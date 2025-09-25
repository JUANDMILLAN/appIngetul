@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h2 class="mb-3">Nueva Cotización</h2>
  @if ($errors->any())
  <div class="alert alert-danger">
    <div class="fw-bold mb-2">Revisa los siguientes errores:</div>
    <ul class="mb-0">
      @foreach ($errors->keys() as $field)
  <li>{{ $field }}: {{ $errors->first($field) }}</li>
@endforeach

    </ul>
  </div>
@endif


  <form method="POST" action="{{ route('quotations.store') }}" id="qform">
    @csrf
@auth
  {{-- Con sesión iniciada no hace falta pedirlo --}}
  <input type="hidden" name="user_id" value="{{ auth()->id() }}">
@else
  <div class="col-md-4">
    <label class="form-label">Cliente</label>
    <select name="user_id" class="form-select" required>
      <option value="">Seleccione…</option>
      @foreach($clients as $c)
        <option value="{{ $c->id }}" @selected(old('user_id') == $c->id)>
          {{ $c->name }} @if($c->email) ({{ $c->email }}) @endif
        </option>
      @endforeach
    </select>
  </div>
@endauth

    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Fecha</label>
        <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Ciudad</label>
        <input name="ciudad" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Departamento</label>
        <input name="departamento" class="form-control" required>
      </div>
      <div class="col-md-3">
 <label class="form-label" for="dirigidoSelect">Dirigido a</label>
<select id="dirigidoSelect" name="dirigido_a" class="form-select" required>
      <option value="">Seleccione o escriba para crear…</option>

    @if(old('dirigido_a'))
      <option value="{{ old('dirigido_a') }}" selected>{{ old('dirigido_a') }}</option>
    @endif
  </select>
</div>
<div class="col-md-3">
  <label class="form-label" for="referenteSelect">Referente</label>
  <select id="referenteSelect" name="referente" class="form-select">
    @if(old('referente'))
      <option value="{{ old('referente') }}" selected>{{ old('referente') }}</option>
    @endif
  </select>
  <div class="form-text">Escribe para buscar o crear…</div>
</div>

      <div class="col-12">
        <label class="form-label">Objeto</label>
        <textarea name="objeto" class="form-control" rows="2"></textarea>
      </div>

      {{-- Checkboxes de tipos de estudio --}}
      <div class="col-12">
        <label class="form-label d-block">Tipos de estudio (elige uno o varios)</label>

        <div id="studyChecks" class="row row-cols-1 row-cols-md-2 g-2">
          @foreach($studyTypes as $st)
            <div class="col">
              <div class="form-check">
                <input
                  class="form-check-input study-check"
                  type="checkbox"
                  id="st-{{ $st->key }}"
                  name="studyTypes[]"
                  value="{{ $st->key }}"
                  data-label="{{ $st->label }}"
                >
                <label class="form-check-label" for="st-{{ $st->key }}">
                  {{ $st->label }}
                </label>
              </div>
            </div>
          @endforeach
        </div>

        <div class="form-text">Al marcar o desmarcar, se agregarán o quitarán ítems (editables).</div>
      </div>
    </div>

    <hr class="my-4">

    {{-- Tabla de ítems --}}
    <div class="table-responsive">
      <table class="table table-bordered align-middle" id="itemsTable">
        <thead class="table-light">
          <tr>
            <th style="width:70px">ITEM</th>
            <th>DESCRIPCIÓN</th>
            <th style="width:90px">UND</th>
            <th style="width:110px">CANT</th>
            <th style="width:160px">VR. UNITARIO</th>
            <th style="width:160px">VR. TOTAL</th>
            <th style="width:50px"></th>
          </tr>
        </thead>
        <tbody id="itemsBody">
          <!-- filas dinámicas -->
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-end fw-bold">Total</td>
            <td id="grandTotal" class="fw-bold">$ 0</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <button type="button" class="btn btn-outline-primary mb-3" id="addRowBtn">Agregar ítem manual</button>

    {{-- Notas --}}
    <div class="mb-2"><b>Notas:</b></div>
    <div id="notesBox" class="mb-3">
      @foreach($defaultNotes as $i => $note)
        <div class="input-group mb-2">
          <span class="input-group-text">•</span>
          <input name="notas[]" class="form-control" value="{{ $note }}">
          <button type="button" class="btn btn-outline-danger btn-sm del-note" tabindex="-1">X</button>
        </div>
      @endforeach
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary" id="addNoteBtn">Añadir nota</button>

    <div class="mt-4">
      <button class="btn btn-success">Guardar cotización</button>
    </div>
  </form>
</div>

{{-- Bootstrap 5 (rápido) --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


<script>
const money = n => new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n);

function recalc() {
  let total = 0;
  document.querySelectorAll('#itemsBody tr').forEach((tr, idx) => {
    tr.querySelector('.item-num').innerText = idx + 1;
    const qty = parseInt(tr.querySelector('[name$="[cantidad]"]').value || 0);
    const unit = parseInt(tr.querySelector('[name$="[vr_unitario]"]').value || 0);
    const line = qty * unit;
    tr.querySelector('.vr-total').innerText = money(line);
    tr.querySelector('[name$="[vr_total]"]').value = line;
    total += line;
  });
  document.getElementById('grandTotal').innerText = money(total);
}

function addRow(data = {descripcion:'', und:'UND', cantidad:1, vr_unitario:0, vr_total:0}) {
  const tbody = document.getElementById('itemsBody');
  const idx = tbody.children.length;
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td class="item-num">${idx+1}</td>
    <td><textarea class="form-control" name="items[${idx}][descripcion]" rows="4">${data.descripcion ?? ''}</textarea></td>
    <td><input class="form-control" name="items[${idx}][und]" value="${data.und ?? 'UND'}"></td>
    <td><input class="form-control" type="number" min="1" name="items[${idx}][cantidad]" value="${data.cantidad ?? 1}"></td>
    <td><input class="form-control" type="number" min="0" name="items[${idx}][vr_unitario]" value="${data.vr_unitario ?? 0}"></td>
    <td class="vr-total">${money(data.vr_total ?? 0)}</td>
    <td><button type="button" class="btn btn-sm btn-outline-danger del">X</button></td>
    <input type="hidden" name="items[${idx}][vr_total]" value="${data.vr_total ?? 0}">
  `;
  tbody.appendChild(tr);
  tr.querySelectorAll('input,textarea').forEach(el => el.addEventListener('input', recalc));
  tr.querySelector('.del').addEventListener('click', () => { tr.remove(); recalc(); });
  recalc();
}

document.getElementById('addRowBtn').addEventListener('click', () => addRow());

document.getElementById('addNoteBtn').addEventListener('click', () => {
  const div = document.createElement('div');
  div.className = 'input-group mb-2';
  div.innerHTML = `<span class="input-group-text">•</span>
    <input name="notas[]" class="form-control" value="">
    <button type="button" class="btn btn-outline-danger btn-sm del-note" tabindex="-1">X</button>`;
  document.getElementById('notesBox').appendChild(div);
  attachDeleteNoteEvents();
});

function attachDeleteNoteEvents() {
  document.querySelectorAll('.del-note').forEach(btn => {
    btn.onclick = function() {
      btn.closest('.input-group').remove();
    };
  });
}
// engancha las notas precargadas al cargar
attachDeleteNoteEvents();

// Debounce sencillo
const debounce = (fn, ms=200) => { let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args),ms); }; };

// Cargar items desde checkboxes marcados (reconstruye tabla)
document.getElementById('studyChecks').addEventListener('change', debounce(async () => {
  const keys = Array.from(document.querySelectorAll('.study-check:checked')).map(el => el.value);
  const tbody = document.getElementById('itemsBody');

  if (keys.length === 0) {
    tbody.innerHTML = '';
    recalc();
    return;
  }

  const params = new URLSearchParams();
  keys.forEach(k => params.append('keys[]', k));

  const url = `{{ route('api.study-items') }}?` + params.toString();
  const res = await fetch(url);
  const items = await res.json();

  tbody.innerHTML = '';
  items.forEach(it => addRow(it)); // addRow ya invoca recalc()
}, 200));

// --- Tom Select para "Dirigido a"
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('dirigidoSelect');
  if (!el) return;

  new TomSelect(el, {
  valueField: 'value',
  labelField: 'text',
  searchField: ['text'],
  create: true,
  createOnBlur: true,
  persist: false,
  maxOptions: 10,
  preload: 'focus',
  shouldLoad: () => true,
  loadThrottle: 250,
  placeholder: 'Escribe para buscar o crear…',
  render: {
    option_create: (data, escape) =>
      '<div class="create">➕ Crear: <strong>' + escape(data.input) + '</strong></div>',
    no_results: () =>
      '<div class="no-results text-muted px-2 py-1">Sin resultados… escribe para crear</div>'
  },
  load: function(query, callback) {
    const url = '{{ route('ajax.dirigidos') }}?q=' + encodeURIComponent(query || '');
    fetch(url, { headers: { 'Accept': 'application/json' } })
      .then(r => r.json())
      .then(json => {
        console.log('dirigidos:', json);   // ← útil para depurar
        callback(json);
      })
      .catch(err => {
        console.error('error dirigidos:', err);
        callback();
      });
  },
 
});
});
document.addEventListener('DOMContentLoaded', () => {
  const refEl = document.getElementById('referenteSelect');
  if (refEl) {
    new TomSelect(refEl, {
      valueField: 'value',
      labelField: 'text',
      searchField: ['text'],
      create: true,
      createOnBlur: true,
      persist: false,
      maxOptions: 10,
      preload: 'focus',
      shouldLoad: () => true,  // carga aunque no haya tecleo
      loadThrottle: 250,
      placeholder: 'Escribe para buscar o crear…',
      render: {
        option_create: (data, escape) =>
          '<div class="create">➕ Crear: <strong>' + escape(data.input) + '</strong></div>',
        no_results: () =>
          '<div class="no-results text-muted px-2 py-1">Sin resultados… escribe para crear</div>'
      },
      load: function(query, callback) {
        const url = '{{ route('ajax.referentes') }}?q=' + encodeURIComponent(query || '');
        fetch(url, { headers: { 'Accept': 'application/json' } })
          .then(r => r.json())
          .then(json => callback(json))
          .catch(() => callback());
      },
      
    });
  }
});
</script>
@endsection
