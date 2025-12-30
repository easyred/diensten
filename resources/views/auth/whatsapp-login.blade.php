<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <title>Inloggen met WhatsApp - diensten.pro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- intl-tel-input for country code selector -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
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
    .control{
      position:relative; background:rgba(0,0,0,.02);
      border:1px solid rgba(0,0,0,.1); border-radius:10px;
      display:flex; align-items:center; padding:10px 12px; gap:10px;
      transition:border .2s, box-shadow .2s, background .2s;
      width: 100%;
      min-width: 0;
    }
    .control.phone-control {
      padding: 10px 12px !important;
      align-items: stretch;
    }
    .control:focus-within{ border-color:var(--ring); box-shadow:0 0 0 4px rgba(16,185,129,.15); background:rgba(255,255,255,.9) }
    body.dark .control {background:rgba(0,0,0,.2); border-color:rgba(255,255,255,.1);}
    body.dark .control:focus-within {background:rgba(0,0,0,.3);}
    body.dark .control input {color:#f9fafb;}
    .control input{ background:transparent; border:none; outline:none; color:var(--text); width:100%; font-size:14px; letter-spacing:.2px }
    .control svg{opacity:.65}
    
    /* intl-tel-input styling integration */
    .control.phone-control {
      padding: 0 !important;
      align-items: stretch;
    }
    .phone-control {
      display: flex !important;
      flex: 1 1 100% !important;
      width: 100% !important;
      min-width: 0 !important;
      align-items: stretch !important;
      margin: 0 !important;
    }
    .phone-control .iti {
      width: 100% !important;
      flex: 1 1 auto !important;
      display: flex !important;
      min-width: 0 !important;
      padding: 10px 12px;
      box-sizing: border-box;
    }
    .phone-control .iti__flag-container {
      position: absolute;
      left: 22px;
      top: 50%;
      transform: translateY(-50%);
      z-index: 2;
    }
    .phone-control .iti__selected-flag {
      padding: 0 8px 0 0;
      background: transparent;
      border: none;
    }
    .phone-control .iti__selected-flag:hover {
      background: transparent;
    }
    .phone-control .iti__arrow {
      margin-left: 4px;
      border-top-color: #64748b;
      opacity: 0.7;
    }
    .phone-control .iti input {
      padding-left: 70px !important;
      width: 100% !important;
      min-width: 0 !important;
      flex: 1 1 auto !important;
      box-sizing: border-box !important;
    }
    .phone-control .iti__selected-dial-code {
      margin-left: 4px;
    }
    body.dark .phone-control .iti__arrow {
      border-top-color: #9ca3af;
    }
    
    /* intl-tel-input dropdown dark mode support */
    body.dark .iti__country-list {
      background-color: #1f2937;
      border-color: #374151;
      color: #f9fafb;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    body.dark .iti__country {
      color: #f9fafb;
    }
    body.dark .iti__country:hover,
    body.dark .iti__country.iti__highlight {
      background-color: #374151;
    }
    body.dark .iti__search-input {
      background-color: #1f2937;
      color: #f9fafb;
      border-color: #374151;
    }
    
    .error{color:var(--error); font-size:12px; margin-top:-4px}

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

    .footer{display:flex; justify-content:center; gap:6px; margin-top:14px; font-size:14px; color:#64748b; flex-wrap:wrap;}
    .footer a{color:#059669; text-decoration:none}
    .footer a:hover{color:#10b981}
    body.dark .footer{color:#9ca3af}
    body.dark .footer a{color:#10b981}
    body.dark .footer a:hover{color:#34d399}
    
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

    .whatsapp-info {
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.2);
      border-radius: 10px;
      padding: 12px;
      margin-bottom: 12px;
      font-size: 13px;
      color: #166534;
    }
    body.dark .whatsapp-info {
      background: rgba(16, 185, 129, 0.15);
      border-color: rgba(16, 185, 129, 0.3);
      color: #86efac;
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
      <h1 class="title">Inloggen met WhatsApp</h1>
      <p class="subtitle">Voer uw WhatsApp-nummer in om een verificatiecode te ontvangen</p>

      @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert error">
          @foreach ($errors->all() as $error)
            {{ $error }}<br>
          @endforeach
        </div>
      @endif

      <div class="whatsapp-info">
        <i class="fab fa-whatsapp mr-2"></i>
        <strong>Hoe het werkt:</strong> We sturen u een 6-cijferige code via WhatsApp. Deze code is 10 minuten geldig.
      </div>

      <form method="POST" action="{{ route('whatsapp.login.send') }}" novalidate>
        @csrf

        <div class="field">
          <label class="label" for="whatsapp_number">WhatsApp-nummer</label>
          <div class="control phone-control">
            <input 
              id="whatsapp_number" 
              type="tel" 
              name="whatsapp_number" 
              value="{{ old('whatsapp_number') }}" 
              required 
              autofocus
              autocomplete="tel"
            >
          </div>
          @error('whatsapp_number') 
            <div class="error">{{ $message }}</div> 
          @enderror
        </div>

        <button class="btn" type="submit" id="submitBtn">
          <i class="fab fa-whatsapp"></i> Verstuur verificatiecode
        </button>

        <div class="footer">
          <span>Geen account?</span>
          <a href="{{ route('client.register') }}">Registreer hier</a>
        </div>

      </form>
    </section>
    </div>
  </div>

  <script>
    // Initialize intl-tel-input for WhatsApp number
    document.addEventListener('DOMContentLoaded', function() {
      const whatsappInput = document.getElementById('whatsapp_number');
      if (whatsappInput) {
        const iti = window.intlTelInput(whatsappInput, {
          initialCountry: "be", // Belgium default
          preferredCountries: ["be", "nl", "fr", "de", "uk"],
          separateDialCode: true,
          utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js"
        });

        // On form submit, use the full international number
        const form = whatsappInput.closest('form');
        if (form) {
          form.addEventListener('submit', function(e) {
            const fullNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164);
            if (fullNumber) {
              whatsappInput.value = fullNumber;
            }
          });
        }
      }
    });

    // Disable submit button on submit to prevent double submission
    document.querySelector('form')?.addEventListener('submit', function() {
      const btn = document.getElementById('submitBtn');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verzenden...';
      }
    });
  </script>
</body>
</html>

