@php
  $types = ['text'=>'Tekst','buttons'=>'Knoppen','list'=>'Lijst','collect_text'=>'Verzamel Tekst','dispatch'=>'Verstuur'];
  $opts  = old('options', ['id'=>[], 'label'=>[]]);
  $next  = old('next', ['keys'=>[], 'values'=>[]]);

  $existingOptions = $node->options_json ?? [];
  if (!old('_token') && !empty($existingOptions)) {
      $opts = ['id'=>[], 'label'=>[]];
      foreach ($existingOptions as $o) {
          $opts['id'][]    = $o['id']    ?? '';
          $opts['label'][] = $o['label'] ?? '';
      }
  }

  $existingMap = $node->next_map_json ?? [];
  if (!old('_token') && !empty($existingMap)) {
      $next = ['keys'=>[], 'values'=>[]];
      foreach ($existingMap as $k=>$v) {
          $next['keys'][]   = $k;
          $next['values'][] = $v;
      }
  }
@endphp

@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h6 class="alert-heading mb-2">
      <i class="fas fa-exclamation-triangle me-1"></i>
      Los alstublieft de volgende fouten op:
    </h6>
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row">
  <div class="col-md-6 mb-3">
    <label for="code" class="form-label">
      <i class="fas fa-code me-1 text-muted"></i>
      Code (uniek in stroom) <span class="text-danger">*</span>
    </label>
    <input type="text" 
           class="form-control @error('code') is-invalid @enderror" 
           id="code" 
           name="code" 
           value="{{ old('code', $node->code) }}" 
           placeholder="bijv., welkom_boodschap"
           required>
    @error('code')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6 mb-3">
    <label for="nodeType" class="form-label">
      <i class="fas fa-cog me-1 text-muted"></i>
      Type <span class="text-danger">*</span>
    </label>
    <select name="type" 
            id="nodeType" 
            class="form-select @error('type') is-invalid @enderror" 
            required>
      @foreach($types as $k=>$v)
        <option value="{{ $k }}" @selected(old('type',$node->type)===$k)>{{ $v }}</option>
      @endforeach
    </select>
    @error('type')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <label for="title" class="form-label">
      <i class="fas fa-heading me-1 text-muted"></i>
      Titel (optioneel)
    </label>
    <input type="text" 
           class="form-control @error('title') is-invalid @enderror" 
           id="title" 
           name="title" 
           value="{{ old('title', $node->title) }}" 
           placeholder="bijv., Welkom Boodschap">
    @error('title')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6 mb-3">
    <label for="sort" class="form-label">
      <i class="fas fa-sort-numeric-up me-1 text-muted"></i>
      Sorteervolgorde
    </label>
    <input type="number" 
           class="form-control @error('sort') is-invalid @enderror" 
           id="sort" 
           name="sort" 
           value="{{ old('sort', $node->sort ?? 10) }}" 
           min="1">
    @error('sort')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div class="form-text">Lagere nummers verschijnen eerst</div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-3">
    <label for="body" class="form-label">
      <i class="fas fa-comment me-1 text-muted"></i>
      Berichttekst <span class="text-danger">*</span>
    </label>
    <textarea name="body" 
              id="body"
              rows="5" 
              class="form-control @error('body') is-invalid @enderror" 
              placeholder="Berichttekst weergegeven aan de gebruiker"
              required>{{ old('body', $node->body) }}</textarea>
    @error('body')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div class="form-text">Je kunt variabelen gebruiken zoals {{ '{{' }}user_name{{ '}}' }}, {{ '{{' }}collected.problem{{ '}}' }}, etc.</div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-3">
    <label for="footer" class="form-label">
      <i class="fas fa-info-circle me-1 text-muted"></i>
      Voettekst (optioneel)
    </label>
    <input type="text" 
           class="form-control @error('footer') is-invalid @enderror" 
           id="footer" 
           name="footer" 
           value="{{ old('footer', $node->footer) }}" 
           placeholder="Aanvullende informatie onderaan">
    @error('footer')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

{{-- Opties (voor knoppen/lijst) --}}
<div id="optionsBlock" class="mt-4">
  <div class="card">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="fas fa-list me-2 text-primary"></i>
          Opties (voor knoppen/lijst)
        </h5>
        <button type="button" onclick="addOptionRow()" class="btn btn-sm btn-outline-primary">
          <i class="fas fa-plus me-1"></i> Optie Toevoegen
        </button>
      </div>
    </div>
    <div class="card-body">
      <div id="optionsRows">
        @php $count = max(count($opts['id'] ?? []), 1); @endphp
        @for($i=0; $i<$count; $i++)
          <div class="row mb-2">
            <div class="col-md-6">
              <input name="options[id][]" 
                     value="{{ $opts['id'][$i] ?? '' }}" 
                     class="form-control" 
                     placeholder="ID (sleutel)">
            </div>
            <div class="col-md-6">
              <input name="options[label][]" 
                     value="{{ $opts['label'][$i] ?? '' }}" 
                     class="form-control" 
                     placeholder="Label weergegeven aan gebruiker">
            </div>
          </div>
        @endfor
      </div>
    </div>
  </div>
</div>

{{-- Volgende kaart --}}
<div class="mt-4">
  <div class="card">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="fas fa-route me-2 text-info"></i>
          Routing naar Volgende Knoop
        </h5>
        <button type="button" onclick="addNextRow()" class="btn btn-sm btn-outline-info">
          <i class="fas fa-plus me-1"></i> Regel Toevoegen
        </button>
      </div>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Hoe het werkt:</strong> Kaart gebruikersreacties naar de code van de volgende knoop. Voor <strong>knoppen/lijst</strong> komen de sleutels meestal overeen met optie <strong>id</strong> of het <strong>nummer</strong> (1,2,3...). Voor <strong>verzamel_tekst</strong>, voeg een regel toe met sleutel <code>next</code>.
      </div>
      
      <div id="nextRows">
        @php $ncount = max(count($next['keys'] ?? []), 1); @endphp
        @for($i=0; $i<$ncount; $i++)
          <div class="row mb-2">
            <div class="col-md-6">
              <input name="next[keys][]" 
                     value="{{ $next['keys'][$i] ?? '' }}" 
                     class="form-control" 
                     placeholder="Reactiesleutel (bijv., ja, 1, beschikbaar, volgende)">
            </div>
            <div class="col-md-6">
              <input name="next[values][]" 
                     value="{{ $next['values'][$i] ?? '' }}" 
                     class="form-control" 
                     placeholder="Code van de volgende knoop">
            </div>
          </div>
        @endfor
      </div>
    </div>
  </div>
</div>

<script>
  const typeSelect = document.getElementById('nodeType');
  const optionsBlock = document.getElementById('optionsBlock');

  function toggleOptions() {
    const t = typeSelect.value;
    optionsBlock.style.display = (t === 'buttons' || t === 'list') ? 'block' : 'none';
  }
  toggleOptions();
  typeSelect.addEventListener('change', toggleOptions);

  function addOptionRow() {
    const wrap = document.getElementById('optionsRows');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
      <div class="col-md-6">
        <input name="options[id][]" class="form-control" placeholder="ID (sleutel)">
      </div>
      <div class="col-md-6">
        <input name="options[label][]" class="form-control" placeholder="Label weergegeven aan gebruiker">
      </div>
    `;
    wrap.appendChild(div);
  }

  function addNextRow() {
    const wrap = document.getElementById('nextRows');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
      <div class="col-md-6">
        <input name="next[keys][]" class="form-control" placeholder="Reactiesleutel (bijv., ja, 1, beschikbaar, volgende)">
      </div>
      <div class="col-md-6">
        <input name="next[values][]" class="form-control" placeholder="Code van de volgende knoop">
      </div>
    `;
    wrap.appendChild(div);
  }
</script>

