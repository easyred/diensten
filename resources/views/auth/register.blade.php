<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <title>Account Aanmaken - diensten.pro</title>
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
      width:100%; max-width:900px;
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
    .pane::-webkit-scrollbar {
      width: 8px;
    }
    .pane::-webkit-scrollbar-track {
      background: rgba(0,0,0,0.05);
      border-radius: 4px;
    }
    .pane::-webkit-scrollbar-thumb {
      background: rgba(0,0,0,0.2);
      border-radius: 4px;
    }
    .pane::-webkit-scrollbar-thumb:hover {
      background: rgba(0,0,0,0.3);
    }
    body.dark .pane::-webkit-scrollbar-track {
      background: rgba(255,255,255,0.05);
    }
    body.dark .pane::-webkit-scrollbar-thumb {
      background: rgba(255,255,255,0.2);
    }
    body.dark .pane::-webkit-scrollbar-thumb:hover {
      background: rgba(255,255,255,0.3);
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
    .control select{ background:transparent; border:none; outline:none; color:var(--text); width:100%; font-size:14px; letter-spacing:.2px; cursor:pointer; }
    body.dark .control select {color:#f9fafb;}
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
    
    .toggle{
      position:absolute; right:10px; top:50%; translate:0 -50%;
      background:transparent; border:none; color:#64748b; cursor:pointer; font-size:12px
    }
    body.dark .toggle {color:#9ca3af}
    .error{color:var(--error); font-size:12px; margin-top:-4px}
    .hint{font-size:12px; color:#64748b; margin-top:2px}
    body.dark .hint {color:#9ca3af}

    .field-grid{ display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:18px; margin-top:12px; }
    @media (max-width: 720px){ .field-grid{ grid-template-columns:1fr; } }
    .span-2{ grid-column: 1 / -1; }

    fieldset.section-card{ 
      border:1px solid rgba(0,0,0,.08); 
      border-radius:12px; 
      background:rgba(248,250,252,.5); 
      padding:20px; 
      margin:16px 0; 
    }
    body.dark fieldset.section-card {
      border-color: rgba(255,255,255,.1);
      background: rgba(0,0,0,.2);
    }
    legend{ 
      font-size:11px; 
      font-weight:800; 
      letter-spacing:.12em; 
      text-transform:uppercase; 
      color:var(--text); 
      padding:0 8px; 
    }

    .input-wrap{position:relative}
    .suggest{ 
      position:absolute;
      top:100%;
      left:0;
      right:0; 
      background:rgba(255,255,255,.98);
      border:1px solid rgba(0,0,0,.1); 
      border-radius:12px;
      margin-top:6px;
      box-shadow:0 10px 24px rgba(0,0,0,.15); 
      z-index:50;
      max-height:240px;
      overflow:auto;
      display:none; 
      backdrop-filter:blur(10px); 
      min-height:50px; 
    }
    body.dark .suggest {
      background: rgba(31,41,55,.98);
      border-color: rgba(255,255,255,.1);
    }
    .s-item{ 
      padding:10px 12px;
      border-bottom:1px solid rgba(0,0,0,.06); 
      cursor:pointer;
      display:flex;
      gap:8px;
      align-items:flex-start; 
      transition:background .2s; 
    }
    body.dark .s-item {border-bottom-color: rgba(255,255,255,.1);}
    .s-item:last-child{border-bottom:0}
    .s-item:hover,.s-item.active{background:rgba(16,185,129,.08)}
    .s-badge{ 
      font-size:10px;
      padding:2px 6px;
      border-radius:6px; 
      border:1px solid rgba(16,185,129,.3);
      background:rgba(16,185,129,.1); 
      color:#059669;
      white-space:nowrap;
      font-weight:700; 
    }
    body.dark .s-badge {
      border-color: rgba(16,185,129,.4);
      background: rgba(16,185,129,.2);
      color: #10b981;
    }
    .s-label{flex:1;color:var(--text);font-size:13px;line-height:1.4}

    .category-select-wrapper{
      margin-bottom:24px;
    }
    .category-label{
      font-size:12px;
      font-weight:600;
      color:#475569;
      margin-bottom:8px;
      display:block;
    }
    body.dark .category-label {color:#9ca3af}
    .category-select-container{
      position:relative;
    }
    .category-select{
      width:100%;
      padding:10px 12px;
      border:1px solid rgba(0,0,0,.1);
      border-radius:10px;
      background:rgba(0,0,0,.02);
      color:var(--text);
      font-size:14px;
      cursor:pointer;
      transition:border .2s, box-shadow .2s, background .2s;
    }
    body.dark .category-select {
      background:rgba(0,0,0,.2);
      border-color:rgba(255,255,255,.1);
      color:#f9fafb;
    }
    .category-select:focus{
      outline:none;
      border-color:var(--ring);
      box-shadow:0 0 0 4px rgba(16,185,129,.15);
      background:rgba(255,255,255,.9);
    }
    body.dark .category-select:focus {
      background:rgba(0,0,0,.3);
    }
    .category-tags{
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      margin-top:12px;
    }
    .category-tag{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:6px 12px;
      background:rgba(16,185,129,.1);
      border:1px solid rgba(16,185,129,.3);
      border-radius:8px;
      font-size:12px;
      font-weight:600;
      color:#059669;
    }
    body.dark .category-tag {
      background:rgba(16,185,129,.2);
      border-color:rgba(16,185,129,.4);
      color:#10b981;
    }
    .category-tag .remove{
      cursor:pointer;
      font-size:16px;
      line-height:1;
      margin-left:4px;
      opacity:.7;
      transition:opacity .2s;
    }
    .category-tag .remove:hover{
      opacity:1;
    }

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
      text-decoration:none;
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
    .ghost{ 
      background:transparent;
      color:var(--text);
      border:2px solid rgba(0,0,0,.12); 
      box-shadow:none; 
      font-weight:600;
      margin-top:12px;
      padding:12px 20px;
    }
    body.dark .ghost {
      border-color: rgba(255,255,255,.2);
      color:var(--text);
    }
    .ghost:hover {
      background: rgba(0,0,0,.04);
      border-color:rgba(0,0,0,.2);
      transform:translateY(-1px);
    }
    body.dark .ghost:hover {
      background: rgba(255,255,255,.08);
      border-color:rgba(255,255,255,.3);
    }
    .ghost i{
      font-size:14px;
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
      <div class="brand" id="brandContainer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 10v7a2 2 0 0 0 2 2h3m11-9v7a2 2 0 0 1-2 2h-3M7 19v-6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6M8 7h8M10 4h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <span>diensten.pro</span>
      </div>
      <h1 class="title" id="headerTitle">Account Aanmaken</h1>
      <p class="subtitle" id="headerSubtitle">Kies uw diensten en vul uw gegevens in om te beginnen</p>

      @if (session('success'))
        <div class="alert" role="status">{{ session('success') }}</div>
      @endif
      @if (isset($errors) && $errors && $errors->any())
        <div class="alert error" role="alert">Gelieve de hieronder gemarkeerde velden te corrigeren.</div>
      @endif

      <form method="POST" action="{{ route('register.store') }}" novalidate>
        @csrf

        <!-- Category Selection -->
        <div class="category-select-wrapper">
          <label class="category-label" for="category_select">Selecteer Diensten *</label>
          <div class="category-select-container">
            <select id="category_select" name="categories[]" multiple size="4" class="category-select" required>
              @foreach($categories as $category)
                <option value="{{ $category->code }}" 
                        data-name="{{ $category->name }}"
                        data-icon="{{ $category->code === 'plumber' ? 'wrench' : ($category->code === 'gardener' ? 'leaf' : 'briefcase') }}"
                        {{ $preselectedCategory === $category->code ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div id="selectedCategories" class="category-tags"></div>
          <input type="hidden" name="categories" id="categories_hidden">
          @error('categories')<div class="error">{{ $message }}</div>@enderror
        </div>

        <!-- JOUW GEGEVENS -->
        <fieldset class="section-card">
          <legend>JOUW GEGEVENS</legend>
          <div class="field-grid">
            <div class="field span-2">
              <label class="label" for="full_name">Volledige naam *</label>
              <div class="control">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm7 9a7 7 0 0 0-14 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input id="full_name" name="full_name" value="{{ old('full_name') }}" placeholder="Jan Jansen" required>
              </div>
              @error('full_name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field" id="company_field">
              <label class="label" for="company_name">Bedrijfsnaam <span class="hint">(optioneel)</span></label>
              <div class="control">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M3 7h18M3 3h18M7 21V7M17 21V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Laat leeg voor persoonlijk account">
              </div>
              @error('company_name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
              <label class="label" for="email">E-mail *</label>
              <div class="control">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 6l8 6 8-6M4 6h16v12H4z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="jij@example.com" required>
              </div>
              @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
              <label class="label" for="password">Wachtwoord *</label>
              <div class="control">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M7 10V8a5 5 0 1 1 10 0v2M6 10h12v9H6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input id="password" type="password" name="password" placeholder="Minimaal 6 tekens" required>
                <button type="button" class="toggle" onclick="togglePass()">Toon</button>
              </div>
              @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
          </div>
        </fieldset>

        <!-- ADRES -->
        <fieldset class="section-card">
          <legend>ADRES</legend>
          <div class="field-grid">
            <div class="field span-2 input-wrap">
              <label class="label" for="address">Straatadres *</label>
              <div class="control">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 10l9-7 9 7v8a2 2 0 0 1-2 2h-4v-6H9v6H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input id="address" name="address" value="{{ old('address') }}" placeholder="Begin met typen straat, nummer, stad…" autocomplete="off" required>
                <input type="hidden" id="address_json" name="address_json" value="{{ old('address_json') }}">
              </div>
              <div id="suggest" class="suggest"></div>
              <div class="hint">Begin te typen en kies een adres om de velden automatisch in te vullen.</div>
            </div>

            <div class="field">
              <label class="label" for="number">Huisnummer</label>
              <div class="control"><input id="number" name="number" value="{{ old('number') }}" placeholder="12A"></div>
              @error('number')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
              <label class="label" for="postal_code">Postcode *</label>
              <div class="control"><input id="postal_code" name="postal_code" value="{{ old('postal_code') }}" placeholder="1000"></div>
              @error('postal_code')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
              <label class="label" for="city">Stad *</label>
              <div class="control"><input id="city" name="city" value="{{ old('city') }}" placeholder="Brussel"></div>
              @error('city')<div class="error">{{ $message }}</div>@enderror
            </div>
          </div>
        </fieldset>

        <!-- CONTACT -->
        <fieldset class="section-card">
          <legend>CONTACT</legend>
          <div class="field-grid">
            <div class="field">
              <label class="label" for="whatsapp_number">WhatsApp-nummer *</label>
              <div class="control phone-control">
                <input id="whatsapp_number" name="whatsapp_number" type="tel" value="{{ old('whatsapp_number') }}" required>
              </div>
              @error('whatsapp_number')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field" id="btw_block" style="display:none">
              <label class="label" for="btw_number">BTW-nummer <span class="hint">(optioneel)</span></label>
              <div class="control">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 12h16M4 6h16M4 18h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input id="btw_number" name="btw_number" value="{{ old('btw_number') }}" placeholder="BE 0123.456.789">
              </div>
              <div class="hint">Vereist voor service providers die klanten factureren.</div>
            </div>
          </div>
        </fieldset>

        <!-- Acties -->
        <button class="btn" type="submit">
          <i class="fas fa-user-plus"></i> Account Aanmaken
        </button>
        <a class="btn ghost" href="{{ route('login') }}">
          <i class="fas fa-sign-in-alt"></i> Ik heb al een account
        </a>
      </form>
    </section>
    </div>
  </div>

  <script>
    // Category selection and dynamic branding
    document.addEventListener('DOMContentLoaded', function() {
      const select = document.getElementById('category_select');
      const selectedDiv = document.getElementById('selectedCategories');
      const hiddenInput = document.getElementById('categories_hidden');
      const brandContainer = document.getElementById('brandContainer');
      const headerTitle = document.getElementById('headerTitle');
      const headerSubtitle = document.getElementById('headerSubtitle');
      const btwBlock = document.getElementById('btw_block');
      const company = document.getElementById('company_field');

      const categoryConfig = {
        plumber: {
          icon: 'wrench',
          title: 'Loodgieter Account',
          subtitle: 'Word onderdeel van ons netwerk van professionals'
        },
        gardener: {
          icon: 'leaf',
          title: 'Tuinman Account',
          subtitle: 'Start met het leveren van tuinservices'
        },
        default: {
          icon: 'briefcase',
          title: 'Account Aanmaken',
          subtitle: 'Kies uw diensten en vul uw gegevens in om te beginnen'
        }
      };

      function updateUI() {
        const selected = Array.from(select.selectedOptions);
        const selectedCodes = selected.map(opt => opt.value);
        const hasServiceProvider = selectedCodes.some(code => ['plumber', 'gardener'].includes(code));

        // Update selected tags display
        if (selected.length === 0) {
          selectedDiv.innerHTML = '';
        } else {
          selectedDiv.innerHTML = selected.map(opt => {
            const code = opt.value;
            const name = opt.dataset.name;
            const icon = opt.dataset.icon;
            return `
              <div class="category-tag ${code}">
                <i class="fas fa-${icon}"></i>
                <span>${name}</span>
                <span class="remove" onclick="removeCategory('${code}')">×</span>
              </div>
            `;
          }).join('');
        }

        // Update hidden input for form submission
        hiddenInput.value = selectedCodes.join(',');

        // Update header based on selection
        if (selected.length === 0) {
          const config = categoryConfig.default;
          brandContainer.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 10v7a2 2 0 0 0 2 2h3m11-9v7a2 2 0 0 1-2 2h-3M7 19v-6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6M8 7h8M10 4h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg><span>diensten.pro</span>`;
          headerTitle.textContent = config.title;
          headerSubtitle.textContent = config.subtitle;
        } else if (selected.length === 1) {
          const code = selected[0].value;
          const config = categoryConfig[code] || categoryConfig.default;
          const iconSvg = code === 'plumber' 
            ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M14 7h4M10 3H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-4M14 3v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V3M14 3h-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>'
            : code === 'gardener'
            ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7l9 5 9-5-9-5zM3 17l9 5 9-5M3 12l9 5 9-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>'
            : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 10v7a2 2 0 0 0 2 2h3m11-9v7a2 2 0 0 1-2 2h-3M7 19v-6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6M8 7h8M10 4h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
          brandContainer.innerHTML = iconSvg + '<span>diensten.pro</span>';
          headerTitle.textContent = config.title;
          headerSubtitle.textContent = config.subtitle;
        } else {
          brandContainer.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 10v7a2 2 0 0 0 2 2h3m11-9v7a2 2 0 0 1-2 2h-3M7 19v-6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6M8 7h8M10 4h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg><span>diensten.pro</span>';
          headerTitle.textContent = 'Multi-Service Account';
          headerSubtitle.textContent = `${selected.length} diensten geselecteerd`;
        }

        // Show BTW field if service provider selected
        if (btwBlock) {
          btwBlock.style.display = hasServiceProvider ? 'block' : 'none';
        }

        // Update company field label
        if (company) {
          const label = company.querySelector('.label');
          if (label) {
            label.innerHTML = hasServiceProvider 
              ? 'Bedrijfsnaam <span class="hint">(optioneel maar aanbevolen)</span>' 
              : 'Bedrijfsnaam <span class="hint">(optioneel)</span>';
          }
        }
      }

      select.addEventListener('change', updateUI);

      // Remove category function
      window.removeCategory = function(code) {
        const option = Array.from(select.options).find(opt => opt.value === code);
        if (option) {
          option.selected = false;
          updateUI();
        }
      };

      // Preselect if category in URL
      @if($preselectedCategory)
        const preselected = Array.from(select.options).find(opt => opt.value === '{{ $preselectedCategory }}');
        if (preselected) {
          preselected.selected = true;
        }
      @endif

      updateUI();
    });

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

    // Password toggle
    function togglePass(){
      const inp = document.getElementById('password');
      if(!inp) return;
      const toShow = inp.type === 'password';
      inp.type = toShow ? 'text' : 'password';
    }

    // Address search functionality (same as client register)
    document.addEventListener('DOMContentLoaded', function() {
      const input = document.querySelector('#address');
      const sugg = document.querySelector('#suggest');
      const number = document.querySelector('#number');
      const zip = document.querySelector('#postal_code');
      const city = document.querySelector('#city');
      const hidden = document.querySelector('#address_json');
      
      if (!input || !sugg || !number || !zip || !city || !hidden) { 
        console.error('Adres zoek elementen niet gevonden'); 
        return; 
      }
      
      let items = []; 
      let activeIndex = -1; 
      let debounceId = null;

      async function fetchJSON(url) {
        const r = await fetch(url);
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      }

      async function searchSmart(qUser) {
        try {
          const results = await fetchJSON(`{{ route('address.suggest') }}?q=${encodeURIComponent(qUser)}&c=10`);
          if (Array.isArray(results) && results.length > 0) {
            const hasVLStructure = results.some(item => item.Suggestion || item._vlLoc);
            return {data: results, src: hasVLStructure ? 'vl' : 'osm'};
          }
          return {data: [], src: 'vl'};
        } catch(e) {
          console.error('Adres zoekfout:', e);
          return {data: [], src: 'vl'};
        }
      }

      function escapeHtml(s) { 
        return (s || '').replace(/[&<>"]/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;"}[c])); 
      }

      function renderList(list, src) {
        if (!list || !list.length) {
          sugg.innerHTML = `<div class="s-item"><span class="s-badge">Ø</span><span class="s-label">Geen resultaten gevonden</span></div>`;
          sugg.style.display = 'block';
          return;
        }
        
        sugg.innerHTML = list.map((it, i) => {
          const label = (src === 'vl') ? (it?.Suggestion?.Label || '') : (it?.display_name || '');
          const b = (src === 'vl') ? 'VL' : 'OSM';
          return `<div class="s-item${i === activeIndex ? ' active' : ''}" data-i="${i}" data-src="${b}">
            <span class="s-badge">[${b}]</span>
            <span class="s-label">${escapeHtml(label)}</span>
          </div>`;
        }).join('');
        
        sugg.style.display = 'block';
        sugg.querySelectorAll('.s-item').forEach(el => {
          el.addEventListener('mousedown', () => choose(parseInt(el.dataset.i, 10), el.dataset.src));
        });
      }

      async function choose(i, srcBadge) {
        if (i < 0 || i >= items.length) return;
        const src = (srcBadge === 'VL') ? 'vl' : 'osm';
        const label = (src === 'vl') ? (items[i]?.Suggestion?.Label || '') : (items[i]?.display_name || '');
        input.value = label;
        sugg.style.display = 'none';

        try {
          if (src === 'vl') {
            if (items[i] && items[i]._osmData) {
              showOSM(items[i]._osmData);
            } else if (items[i] && items[i]._vlLoc) {
              showVL(items[i]._vlLoc.Location, items[i]._vlLoc);
            } else {
              const detailedResults = await fetchJSON(`{{ route('address.suggest') }}?q=${encodeURIComponent(label)}&detailed=1`);
              if (detailedResults && detailedResults.length > 0 && detailedResults[0]._vlLoc) {
                showVL(detailedResults[0]._vlLoc.Location, detailedResults[0]._vlLoc);
              } else {
                const osmResults = await fetchJSON(`{{ route('address.suggest') }}?q=${encodeURIComponent(label)}&osm=1`);
                if (osmResults && osmResults.length > 0) {
                  showOSM(osmResults[0]);
                } else {
                  showVLFromLabel(label);
                }
              }
            }
          } else {
            showOSM(items[i]);
          }
        } catch(e) {
          console.error('Fout bij het ophalen van details:', e);
        }
      }

      function showVL(L, rawArr) {
        if (L) {
          const streetName = L.Thoroughfarename || '';
          const houseNumber = L.Housenumber || '';
          const postalCode = L.Postalcode || '';
          const municipality = L.Municipality || '';
          input.value = [streetName, houseNumber].filter(Boolean).join(' ');
          number.value = houseNumber;
          zip.value = postalCode;
          city.value = municipality;
          hidden.value = JSON.stringify({source: 'vl', location: L, raw: rawArr});
        }
      }

      function showOSM(it) {
        const addr = it?.address || {};
        const streetName = addr.road || addr.pedestrian || addr.path || '';
        const houseNumber = addr.house_number || '';
        const postalCode = addr.postcode || '';
        const cityName = addr.village || addr.town || addr.city || addr.municipality || '';
        input.value = [streetName, houseNumber].filter(Boolean).join(' ');
        number.value = houseNumber;
        zip.value = postalCode;
        city.value = cityName;
        hidden.value = JSON.stringify({source: 'osm', address: it});
      }

      function showVLFromLabel(label) {
        const parts = label.split(', ');
        if (parts.length >= 2) {
          const streetPart = parts[0].trim();
          const cityPart = parts[1].trim();
          input.value = streetPart;
          city.value = cityPart;
        }
      }

      input.addEventListener('input', function(e) {
        const q = e.target.value.trim();
        if (q.length < 2) {
          sugg.style.display = 'none';
          return;
        }
        clearTimeout(debounceId);
        debounceId = setTimeout(async () => {
          const {data, src} = await searchSmart(q);
          items = data;
          activeIndex = -1;
          renderList(data, src);
        }, 300);
      });

      input.addEventListener('blur', function() {
        setTimeout(() => { sugg.style.display = 'none'; }, 200);
      });

      input.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          activeIndex = Math.min(activeIndex + 1, items.length - 1);
          renderList(items, 'vl');
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          activeIndex = Math.max(activeIndex - 1, -1);
          renderList(items, 'vl');
        } else if (e.key === 'Enter' && activeIndex >= 0) {
          e.preventDefault();
          choose(activeIndex, 'VL');
        }
      });
    });
  </script>
</body>
</html>
