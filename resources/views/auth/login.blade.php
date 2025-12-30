<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <title>Inloggen - diensten.pro</title>
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
    .control{
      position:relative; background:rgba(0,0,0,.02);
      border:1px solid rgba(0,0,0,.1); border-radius:10px;
      display:flex; align-items:center; padding:10px 12px; gap:10px;
      transition:border .2s, box-shadow .2s, background .2s;
    }
    .control:focus-within{ border-color:var(--ring); box-shadow:0 0 0 4px rgba(16,185,129,.15); background:rgba(255,255,255,.9) }
    body.dark .control {background:rgba(0,0,0,.2); border-color:rgba(255,255,255,.1);}
    body.dark .control:focus-within {background:rgba(0,0,0,.3);}
    body.dark .control input {color:#f9fafb;}
    .control input{ background:transparent; border:none; outline:none; color:var(--text); width:100%; font-size:14px; letter-spacing:.2px }
    .control svg{opacity:.65}
    .toggle{
      position:absolute; right:10px; top:50%; translate:0 -50%;
      background:transparent; border:none; color:#64748b; cursor:pointer; font-size:12px
    }
    body.dark .toggle {color:#9ca3af}
    .error{color:var(--error); font-size:12px; margin-top:-4px}

    .row{display:flex; align-items:center; justify-content:space-between; gap:10px; margin:6px 0 14px}
    .remember{display:flex; align-items:center; gap:8px; color:#64748b; font-size:13px; cursor:pointer}
    body.dark .remember {color:#9ca3af}
    .remember input[type="checkbox"]{
      width:16px; height:16px; cursor:pointer; accent-color:var(--primary)
    }
    .row a{color:#059669; text-decoration:none; font-size:13px}
    .row a:hover{color:#10b981}
    body.dark .row a{color:#10b981}
    body.dark .row a:hover{color:#34d399}

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
    .btn i{
      font-size:16px;
    }

    .footer{display:flex; justify-content:center; gap:6px; margin-top:14px; font-size:14px; color:#64748b}
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
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 10v7a2 2 0 0 0 2 2h3m11-9v7a2 2 0 0 1-2 2h-3M7 19v-6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6M8 7h8M10 4h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <span>diensten.pro</span>
      </div>
      <h1 class="title">Inloggen</h1>
      <p class="subtitle">Log in met uw e-mail en wachtwoord</p>

      @if (session('status'))
        <div class="alert">{{ session('status') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert error">Controleer uw e-mail en wachtwoord.</div>
      @endif

      <form method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf

        <div class="field">
          <label class="label" for="email">E-mail</label>
          <div class="control">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 6l8 6 8-6M4 6h16v12H4z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="u@example.com" required autofocus autocomplete="username">
          </div>
          @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
          <label class="label" for="password">Wachtwoord</label>
          <div class="control">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M7 10V8a5 5 0 1 1 10 0v2M6 10h12v9H6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
            <button type="button" class="toggle" onclick="togglePass()">Toon</button>
          </div>
          @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="row">
          <label class="remember">
            <input id="remember_me" type="checkbox" name="remember">
            <span>Onthoud mij</span>
          </label>

          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Wachtwoord vergeten?</a>
          @endif
        </div>

        <button class="btn" type="submit">
          <i class="fas fa-sign-in-alt"></i> Inloggen
        </button>

      </form>

      <div class="footer">
        <span>Geen account?</span>
        <a href="{{ route('register') }}">Registreer hier</a>
      </div>
    </section>
    </div>
  </div>

  <script>
    function togglePass(){
      const inp = document.getElementById('password');
      if(!inp) return;
      const btn = event.currentTarget;
      const toShow = inp.type === 'password';
      inp.type = toShow ? 'text' : 'password';
      btn.textContent = toShow ? 'Verberg' : 'Toon';
    }
  </script>
</body>
</html>

