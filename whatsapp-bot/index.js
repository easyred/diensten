// whatsapp-bot/index.js
require('dotenv').config();

const fs = require('fs');
const path = require('path');
const qrcode = require('qrcode');
const axios = require('axios');
const express = require('express');
const cors = require('cors');

const { default: makeWASocket, useMultiFileAuthState } = require('@whiskeysockets/baileys');

const app = express();
app.use(cors());
app.use(express.json({ limit: '10mb' }));

/* ---------- Config ---------- */
const LARAVEL_API = process.env.LARAVEL_API_URL || 'http://127.0.0.1:8000';
const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || '127.0.0.1';
const AUTH_DIR = './auth_info';
const MAX_RECONNECT = 5;
const SEEN_LIMIT = 500;

/* ---------- State ---------- */
let sock = null;
let qrCodeData = null;
let isConnected = false;
let reconnectAttempts = 0;
let connecting = false; // prevent parallel connect attempts

// Keep recent messages with insertion order to prune efficiently
const seen = new Map();

/* ---------- Helpers ---------- */
function log(...args) { console.log(new Date().toISOString(), ...args); }

function jid(number) {
  if (!number) return null;
  return number.includes('@s.whatsapp.net') ? number : number.replace(/\D/g, '') + '@s.whatsapp.net';
}

function remember(id) {
  if (!id) return false;
  if (seen.has(id)) return false;

  seen.set(id, Date.now());
  // prune if needed
  if (seen.size > SEEN_LIMIT) {
    // remove oldest entry
    const oldestKey = seen.keys().next().value;
    if (oldestKey) seen.delete(oldestKey);
  }
  return true;
}

/* ---------- Axios instance ---------- */
const axiosInstance = axios.create({
  baseURL: LARAVEL_API,
  timeout: 20000,
});

/* ---------- Safe send wrapper ---------- */
async function safeSendMessage(jidTo, messageObj, attempt = 0) {
  if (!sock) {
    throw new Error('Socket not initialized');
  }
  if (!sock.sendMessage) {
    throw new Error('Socket sendMessage not available');
  }

  try {
    return await sock.sendMessage(jidTo, messageObj);
  } catch (err) {
    // Retry once for transient errors
    if (attempt < 1) {
      log('⚠️ sendMessage failed, retrying once...', err?.message || err);
      await new Promise(r => setTimeout(r, 1000));
      return safeSendMessage(jidTo, messageObj, attempt + 1);
    }
    throw err;
  }
}

/* ---------- Connect ---------- */
async function connectToWhatsApp() {
  if (connecting) {
    log('⚠️ connectToWhatsApp already running, skipping duplicate call.');
    return;
  }
  connecting = true;

  try {
    // Ensure auth dir exists
    if (!fs.existsSync(AUTH_DIR)) {
      fs.mkdirSync(AUTH_DIR, { recursive: true });
    }

    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);

    sock = makeWASocket({
      auth: state,
      printQRInTerminal: false,
      browser: [
        process.env.BROWSER_NAME || 'diensten.pro',
        process.env.BROWSER_VERSION || 'Chrome',
        process.env.BROWSER_OS || '1.0.0'
      ],
      syncFullHistory: false,
      connectTimeoutMs: 60000,
      qrTimeout: 60000,
      defaultQueryTimeoutMs: 60000,
      retryRequestDelayMs: 2000,
      markOnlineOnConnect: false
    });

    // Save credentials
    sock.ev.on('creds.update', saveCreds);

    // Connection updates
    sock.ev.on('connection.update', (update) => {
      try {
        const { connection, qr, lastDisconnect, isNewLogin } = update;
        log('Connection update:', JSON.stringify({
          connection,
          hasQR: !!qr,
          isNewLogin,
          err: lastDisconnect?.error?.message || null
        }));

        if (qr) {
          qrCodeData = qr;
          isConnected = false;
          reconnectAttempts = 0;
          log('📲 QR received. Use /get-qr to fetch QR image.');
        }

        if (connection === 'open') {
          qrCodeData = null;
          isConnected = true;
          reconnectAttempts = 0;
          log('✅ WhatsApp connected as', sock?.user?.id || sock?.user?.name || 'unknown');
        }

        if (connection === 'close') {
          isConnected = false;
          const errorMsg = lastDisconnect?.error?.message || 'Unknown';
          const errorCode = lastDisconnect?.error?.output?.statusCode || null;
          log('❌ Connection closed:', errorMsg, 'code:', errorCode);

          // classify
          const isRateLimit = errorCode === 429 || (String(errorMsg).toLowerCase().includes('rate'));
          const isTemporary = [502, 503].includes(Number(errorCode));

          if (reconnectAttempts < MAX_RECONNECT) {
            const baseDelay = isRateLimit ? 60000 : Math.pow(2, reconnectAttempts) * 1000;
            const delay = isTemporary ? 30000 : baseDelay;
            log(`🔄 Reconnect attempt ${reconnectAttempts + 1}/${MAX_RECONNECT} in ${delay}ms`);
            reconnectAttempts++;
            setTimeout(() => {
              connecting = false;
              connectToWhatsApp();
            }, delay);
          } else {
            log('❌ Max reconnection attempts reached. Manual restart required.');
          }
        }

        if (connection === 'connecting') {
          log('🔄 Connecting to WhatsApp...');
        }
      } catch (e) {
        log('⚠️ Error in connection.update handler:', e?.message || e);
      }
    });

    // Incoming messages
    sock.ev.on('messages.upsert', async (m) => {
      try {
        const msg = m?.messages?.[0];
        if (!msg || !msg.message) return;
        if (msg.key?.fromMe) return;

        const id = msg.key?.id;
        if (!remember(id)) return;

        const from = msg.key.remoteJid;

        // extract text (covers common variants)
        const plain =
          msg.message.conversation ||
          msg.message.extendedTextMessage?.text ||
          msg.message?.ephemeralMessage?.message?.extendedTextMessage?.text ||
          '';

        const buttonId = msg.message?.buttonsResponseMessage?.selectedButtonId;
        const listRowId = msg.message?.listResponseMessage?.singleSelectReply?.selectedRowId;

        const inputRaw = (buttonId || listRowId || plain || '').trim();
        if (!inputRaw) return;

        log(`📩 Message from ${from}:`, inputRaw.slice(0, 200));

        // send to Laravel
        try {
          const res = await axiosInstance.post('/api/wa/incoming', {
            from: String(from).replace('@s.whatsapp.net', ''),
            message: inputRaw,
            normalized: inputRaw.toLowerCase()
          });
          const reply = res?.data?.reply;
          if (reply) {
            await sendFromPayload(from, reply);
          }
        } catch (err) {
          log('❌ Error sending incoming to Laravel:', err?.response?.data || err?.message || err);
        }
      } catch (err) {
        log('❌ incoming handler error:', err?.message || err);
      }
    });

    // Generic errors
    sock.ev.on('error', (err) => {
      log('Socket error event:', err?.message || err);
    });

    // finalize
    connecting = false;
    return sock;
  } catch (err) {
    connecting = false;
    log('❌ Error connecting to WhatsApp:', err?.message || err);
    // retry after small delay
    setTimeout(() => connectToWhatsApp(), 5000);
  }
}

/* ---------- Outbound rendering ---------- */
async function sendButtons(jidTo, payload) {
  const { body, options } = payload;
  const lines = [];
  lines.push(body || '');
  if (options && options.length) {
    lines.push('');
    options.forEach((opt, i) => {
      const clean = String(opt.text || `Option ${i + 1}`).replace(/^\s*\d+[\.\)]\s*/, '');
      lines.push(`${i + 1}) ${clean}`);
    });
  }
  await safeSendMessage(jidTo, { text: lines.join('\n') });
}

async function sendList(jidTo, payload) {
  const { body, options } = payload;
  const lines = [];
  lines.push(body || '');
  if (options && options.length) {
    lines.push('');
    options.forEach((section) => {
      if (section.title) lines.push(`${section.title}:`);
      if (section.rows && section.rows.length) {
        section.rows.forEach((row, i) => {
          const clean = String(row.title || `Option ${i + 1}`).replace(/^\s*\d+[\.\)]\s*/, '');
          lines.push(`${i + 1}) ${clean}`);
        });
      }
    });
  }
  await safeSendMessage(jidTo, { text: lines.join('\n') });
}

async function sendFromPayload(jidTo, payload) {
  const type = payload?.type;
  if (!type) {
    // treat as plain text if no type
    const text = payload?.body || '';
    if (!text) return;
    return safeSendMessage(jidTo, { text });
  }

  if (type === 'buttons') return sendButtons(jidTo, payload);
  if (type === 'list') return sendList(jidTo, payload);

  // default text
  const text = payload.body || '';
  if (!text) return;
  await safeSendMessage(jidTo, { text });
}

/* ---------- Admin endpoints ---------- */
app.get('/status', (req, res) => {
  if (isConnected && sock?.user) {
    return res.json({ status: 'Connected', user: sock.user });
  }
  if (qrCodeData) return res.json({ status: 'Awaiting QR scan' });
  return res.json({ status: 'Not connected' });
});

app.get('/get-qr', async (req, res) => {
  try {
    if (qrCodeData) {
      const qrImage = await qrcode.toDataURL(qrCodeData);
      return res.json({ qr: qrImage });
    }
    return res.json({ message: isConnected ? 'WhatsApp is already connected!' : 'QR not available yet' });
  } catch (e) {
    return res.status(500).json({ message: 'Failed to render QR', error: e.message });
  }
});

// Laravel -> send text
app.post('/send-message', async (req, res) => {
  try {
    const { number, message } = req.body;
    if (!number || !message) return res.status(400).json({ error: 'Missing number or message' });

    const to = jid(number);
    if (!to) return res.status(400).json({ error: 'Invalid number' });

    if (!sock) return res.status(500).json({ error: 'WhatsApp socket not initialized' });

    await safeSendMessage(to, { text: message });
    return res.json({ status: 'success', number, message });
  } catch (error) {
    log('❌ send-message error:', error?.message || error);
    return res.status(500).json({ error: 'Failed to send message', details: error?.message || error });
  }
});

// Logout endpoint - clears session and disconnects
app.post('/logout', async (req, res) => {
  try {
    log('🚪 Logout requested...');
    if (sock) {
      try {
        await sock.logout();
      } catch (e) {
        log('⚠️ logout socket call failed:', e?.message || e);
      }
      sock = null;
    }

    try {
      if (fs.existsSync(AUTH_DIR)) {
        fs.rmSync(AUTH_DIR, { recursive: true, force: true });
        log('✅ Auth info directory cleared');
      }

      ['.wwebjs_auth', 'sessions', 'store'].forEach(dir => {
        if (fs.existsSync(dir)) {
          fs.rmSync(dir, { recursive: true, force: true });
          log(`✅ ${dir} directory cleared`);
        }
      });

      fs.readdirSync('.').forEach(file => {
        if ((file.includes('auth') || file.includes('session') || file.includes('store')) && file.endsWith('.json')) {
          fs.unlinkSync(file);
          log(`✅ ${file} file removed`);
        }
      });
    } catch (fe) {
      log('⚠️ Some files could not be cleared:', fe?.message || fe);
    }

    isConnected = false; qrCodeData = null; reconnectAttempts = 0;
    log('✅ Logout completed successfully');
    return res.json({ success: true, message: 'Logged out successfully. Please restart the bot to reconnect.' });
  } catch (error) {
    log('❌ Error during logout:', error?.message || error);
    return res.status(500).json({ success: false, error: 'Failed to logout properly' });
  }
});

/* ---------- Startup & graceful shutdown ---------- */
const server = app.listen(PORT, HOST, () => {
  log(`🚀 WhatsApp bot running on http://${HOST}:${PORT}`);
  connectToWhatsApp().catch(err => log('Startup connect error:', err?.message || err));
});

async function shutdown(signal) {
  log(`\n🛑 Received ${signal}. Shutting down...`);
  try {
    if (sock) {
      try { await sock.logout(); } catch (e) { log('⚠️ logout during shutdown failed:', e?.message || e); }
    }
  } catch (e) {
    log('⚠️ Error during shutdown socket logout:', e?.message || e);
  } finally {
    server.close(() => {
      log('Server closed. Exiting process.');
      process.exit(0);
    });

    // force exit if still not closed within a time
    setTimeout(() => {
      log('Forcing process exit.');
      process.exit(1);
    }, 5000);
  }
}

process.on('SIGINT', () => shutdown('SIGINT'));
process.on('SIGTERM', () => shutdown('SIGTERM'));
process.on('uncaughtException', (err) => {
  log('Uncaught exception:', err?.message || err);
});
process.on('unhandledRejection', (reason) => {
  log('Unhandled rejection:', reason);
});

