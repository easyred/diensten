<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <title>Verifieer OTP - diensten.pro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            emerald: {
              50: '#ecfdf5',
              100: '#d1fae5',
              500: '#10b981',
              600: '#059669',
              700: '#047857',
            }
          }
        }
      }
    }
  </script>
  <style>
    :root{
      --bg:#ffffff; --card:#f8fafc; --muted:#64748b; --text:#1e293b;
      --primary:#10b981; --primary-600:#059669; --ring:#10b981;
      --error:#ef4444; --ok:#22c55e;
    }
    *{box-sizing:border-box}
    html,body{
      min-height:100vh;
      margin:0;
    }
    body{
      font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial,sans-serif;
      background: #ffffff;
      color:var(--text);
      padding:24px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    body.dark {
      background: #111827;
      --bg:#111827; --card:#1f2937; --muted:#9ca3af; --text:#f9fafb;
    }
    .container-wrapper{
      display:flex;
      align-items:flex-start;
      justify-content:center;
      min-height:calc(100vh - 48px);
      padding:20px 0;
    }
    .shell{
      width:100%; max-width:480px;
      background:linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.9));
      border:1px solid rgba(0,0,0,0.08);
      border-radius:20px; box-shadow:0 30px 60px rgba(0,0,0,.1), inset 0 1px 0 rgba(255,255,255,.8);
      transition: all 0.3s ease;
    }
    body.dark .shell {
      background: linear-gradient(180deg, rgba(31,41,55,0.95), rgba(31,41,55,0.9));
      border-color: rgba(255,255,255,0.1);
    }
    .pane{
      padding:34px 28px 28px;
      background:rgba(255,255,255,.8); 
      transition: background 0.3s ease;
      overflow-y:auto;
    }
    body.dark .pane {background:rgba(31,41,55,.8);}
    @media (max-width: 768px) {
      .pane {
        padding:24px 20px 20px;
      }
      body {
        padding: 12px;
      }
      .container-wrapper {
        padding: 10px 0;
        min-height: calc(100vh - 24px);
      }
    }
    .brand{
      display:flex; gap:10px; align-items:center; justify-content:center; margin-bottom:6px;
      color:#059669; font-weight:700; letter-spacing:.3px;
    }
    body.dark .brand {color:#10b981;}
    .title{margin:6px 0 2px; text-align:center; font-size:26px; font-weight:800; letter-spacing:.2px; color:var(--text);}
    .subtitle{color:var(--muted); text-align:center; font-size:14px; margin-bottom:18px}

    .alert{
      padding:10px 12px; border-radius:10px; font-size:14px; margin-bottom:12px;
      border:1px solid rgba(34,197,94,.2); background:rgba(34,197,94,.1); color:#166534;
    }
    .alert.error{background:rgba(239,68,68,.1); color:#dc2626; border-color:rgba(239,68,68,.2)}
    body.dark .alert {background:rgba(34,197,94,.2); color:#86efac; border-color:rgba(34,197,94,.3)}
    body.dark .alert.error {background:rgba(239,68,68,.2); color:#fca5a5; border-color:rgba(239,68,68,.3)}

    .field{display:flex; flex-direction:column; gap:8px; margin:10px 0}
    .label{font-size:12px; color:#475569}
    body.dark .label {color:#9ca3af}
    .otp-input{
      display:flex; gap:8px; justify-content:center; margin:20px 0;
    }
    .otp-digit{
      width:50px; height:60px; 
      text-align:center; font-size:24px; font-weight:700;
      background:rgba(0,0,0,.02);
      border:2px solid rgba(0,0,0,.1); border-radius:10px;
      color:var(--text);
      transition:border .2s, box-shadow .2s, background .2s;
    }
    .otp-digit:focus{
      border-color:var(--ring); 
      box-shadow:0 0 0 4px rgba(16,185,129,.15); 
      background:rgba(255,255,255,.9);
      outline:none;
    }
    body.dark .otp-digit {
      background:rgba(0,0,0,.2); 
      border-color:rgba(255,255,255,.1);
      color:#f9fafb;
    }
    body.dark .otp-digit:focus {
      background:rgba(0,0,0,.3);
    }

    .error{color:var(--error); font-size:12px; margin-top:-4px}
    .error a{font-weight:bold; text-decoration:underline; color:var(--text); transition:color 0.2s}
    .error a:hover{color:var(--primary)}

    .btn{
      appearance:none; width:100%; border:none; cursor:pointer;
      background:linear-gradient(180deg, var(--primary), var(--primary-600));
      color:#ffffff; font-weight:700; padding:14px 20px; border-radius:12px;
      letter-spacing:.3px; box-shadow:0 4px 12px rgba(16,185,129,.3);
      transition:all .2s ease; margin-top:24px;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      font-size:15px;
    }
    .btn:hover{
      background:linear-gradient(180deg, var(--primary-600), #047857);
      box-shadow:0 6px 16px rgba(16,185,129,.4);
      transform:translateY(-1px);
    }
    .btn:active{
      transform:translateY(0);
      box-shadow:0 2px 8px rgba(16,185,129,.3);
    }
    .btn:disabled{opacity:0.6; cursor:not-allowed;}
    .btn-secondary{
      background:transparent; border:2px solid rgba(0,0,0,.12); color:var(--text);
      box-shadow:none; margin-top:12px;
    }
    .btn-secondary:hover{
      background:rgba(0,0,0,.04);
      border-color:rgba(0,0,0,.2);
      transform:translateY(-1px);
    }
    body.dark .btn-secondary {
      border-color:rgba(255,255,255,.2);
      color:#f9fafb;
    }
    body.dark .btn-secondary:hover {
      background:rgba(255,255,255,.08);
      border-color:rgba(255,255,255,.3);
    }

    .footer{display:flex; justify-content:center; gap:6px; margin-top:14px; font-size:14px; color:#64748b; flex-wrap:wrap;}
    .footer a{color:#059669; text-decoration:none}
    .footer a:hover{color:#10b981}
    body.dark .footer{color:#9ca3af}
    body.dark .footer a{color:#10b981}
    body.dark .footer a:hover{color:#34d399}

    .whatsapp-number{
      text-align:center; color:var(--muted); font-size:13px; margin-bottom:8px;
    }
    
    .dark-mode-container {
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 1000;
    }
    @media (max-width: 768px) {
      .dark-mode-container {
        top: 12px;
        right: 12px;
      }
    }
  </style>
</head>
<body class="bg-white dark:bg-gray-900">
  <div class="dark-mode-container">
    <x-dark-mode-toggle />
  </div>
  <div class="container-wrapper">
    <div class="shell">
    <section class="pane">
      <div class="brand">
        <i class="fab fa-whatsapp text-lg"></i>
        <span>diensten.pro</span>
      </div>
      <h1 class="title">Voer verificatiecode in</h1>
      <p class="subtitle">We hebben een code naar uw WhatsApp gestuurd</p>

      <div class="whatsapp-number">
        <i class="fab fa-whatsapp mr-1"></i>
        {{ preg_replace('/(\d{2})(\d{3})(\d{3})(\d{3})/', '+$1 $2 $3 $4', $whatsapp_number) }}
      </div>

      @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert error">
          @foreach ($errors->all() as $error)
            {!! $error !!}<br>
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('otp.verify.submit') }}" novalidate id="otpForm">
        @csrf

        <div class="field">
          <label class="label" for="otp_code">6-cijferige code</label>
          <div class="otp-input" id="otpContainer">
            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="0" required>
            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="1" required>
            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="2" required>
            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="3" required>
            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="4" required>
            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="5" required>
          </div>
          <input type="hidden" name="otp_code" id="otp_code" required>
          @error('otp_code') 
            <div class="error">{!! $message !!}</div> 
          @enderror
        </div>

        <button class="btn" type="submit" id="submitBtn">
          <i class="fas fa-check"></i> Verifieer en log in
        </button>
      </form>

      <form method="POST" action="{{ route('otp.resend') }}" style="margin-top: 8px;">
        @csrf
        <button type="submit" class="btn btn-secondary" id="resendBtn">
          <i class="fas fa-redo"></i> Code opnieuw verzenden
        </button>
      </form>

      <div class="footer">
        <a href="{{ route('login') }}">← Ander nummer gebruiken</a>
      </div>
    </section>
    </div>
  </div>

  <script>
    const otpInputs = document.querySelectorAll('.otp-digit');
    const hiddenInput = document.getElementById('otp_code');
    const form = document.getElementById('otpForm');

    otpInputs.forEach((input, index) => {
      input.addEventListener('input', function(e) {
        const value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
          e.target.value = value[0];
          if (index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
          }
        }
        updateHiddenInput();
      });

      input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
          otpInputs[index - 1].focus();
        }
      });

      input.addEventListener('paste', function(e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const digits = paste.replace(/\D/g, '').substring(0, 6);
        digits.split('').forEach((digit, i) => {
          if (otpInputs[i]) {
            otpInputs[i].value = digit;
          }
        });
        updateHiddenInput();
        if (digits.length === 6) {
          otpInputs[5].focus();
        } else if (digits.length > 0) {
          otpInputs[digits.length].focus();
        }
      });
    });

    function updateHiddenInput() {
      const code = Array.from(otpInputs).map(input => input.value).join('');
      hiddenInput.value = code;
    }

    otpInputs[5].addEventListener('input', function() {
      if (hiddenInput.value.length === 6) {
        setTimeout(() => {
          form.requestSubmit();
        }, 300);
      }
    });

    form?.addEventListener('submit', function() {
      const btn = document.getElementById('submitBtn');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifiëren...';
      }
    });

    otpInputs[0].focus();
  </script>
</body>
</html>

