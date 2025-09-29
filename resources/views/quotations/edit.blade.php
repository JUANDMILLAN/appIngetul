@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Editar Cotización</h2>
    <a href="{{ route('cotnom.index') }}" class="btn btn-sm btn-outline-primary">Carpetas</a>
  </div>

  {{-- Errores de validación --}}
  @if ($errors->any())
    <div class="alert alert-danger my-3">
      <div class="fw-bold mb-2">Revisa los siguientes errores:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $msg)
          <li>{{ $msg }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('quotations.update', $quotation) }}" id="qform">
    @csrf
    @method('PUT')

    @auth
      {{-- Con sesión iniciada no hace falta pedirlo --}}
      <input type="hidden" name="user_id" value="{{ auth()->id() }}">
    @else
      <div class="col-md-4 mb-3">
        <label class="form-label">Cliente</label>
        <select name="user_id" class="form-select" required>
          <option value="">Seleccione…</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" @selected(old('user_id', $quotation->user_id) == $c->id)>
              {{ $c->name }} @if($c->email) ({{ $c->email }}) @endif
            </option>
          @endforeach
        </select>
      </div>
    @endauth

    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Fecha</label>
        <input type="date" name="fecha" class="form-control"
               value="{{ old('fecha', optional($quotation->fecha)->format('Y-m-d')) }}" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Departamento</label>
        <input name="departamento" class="form-control"
               value="{{ old('departamento', $quotation->departamento) }}" required>
        @error('departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">Ciudad</label>
        <input name="ciudad" class="form-control"
               value="{{ old('ciudad', $quotation->ciudad) }}" required>
        @error('ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label" for="dirigidoSelect">Dirigido a</label>
        <select id="dirigidoSelect" name="dirigido_a" class="form-select" required>
          <option value="{{ old('dirigido_a', $quotation->dirigido_a) }}" selected>
            {{ old('dirigido_a', $quotation->dirigido_a) }}
          </option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label" for="referenteSelect">Referente</label>
        @php $ref = old('referente', $quotation->referente) @endphp
        <select id="referenteSelect" name="referente" class="form-select">
          @if($ref)
            <option value="{{ $ref }}" selected>{{ $ref }}</option>
          @endif
        </select>
        <div class="form-text">Escribe para buscar o crear…</div>
      </div>

      <div class="col-12">
        <label class="form-label">Objeto</label>
        <textarea name="objeto" class="form-control @error('objeto') is-invalid @enderror" rows="2"
                  placeholder="Describe el objeto de la cotización">{{ old('objeto', $quotation->objeto) }}</textarea>
        @error('objeto') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      {{-- Checkboxes de tipos de estudio (en edición NO reconstruyen tabla) --}}
      <div class="col-12">
        <label class="form-label d-block">Tipos de estudio (opcional)</label>
        <div id="studyChecks" class="row row-cols-1 row-cols-md-2 g-2">
          @foreach($studyTypes as $st)
            <div class="col">
              <div class="form-check">
                <input class="form-check-input study-check"
                       type="checkbox"
                       id="st-{{ $st->key }}"
                       name="studyTypes[]"
                       value="{{ $st->key }}"
                       data-label="{{ $st->label }}"
                       @checked(in_array($st->key, old('studyTypes', [])))>
                <label class="form-check-label" for="st-{{ $st->key }}">{{ $st->label }}</label>
              </div>
            </div>
          @endforeach
        </div>
        <div class="form-text">En edición no se reemplazarán los ítems automáticamente.</div>
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
          @foreach($quotation->items as $idx => $it)
            <tr>
              <td class="item-num">{{ $loop->iteration }}</td>

              <td>
                <textarea class="form-control"
                          name="items[{{ $idx }}][descripcion]"
                          rows="4">{{ old("items.$idx.descripcion", $it->descripcion) }}</textarea>
              </td>

              <td>
                <input class="form-control"
                       name="items[{{ $idx }}][und]"
                       value="{{ old("items.$idx.und", $it->und) }}">
              </td>

              <td>
                <input class="form-control" type="number" min="1"
                       name="items[{{ $idx }}][cantidad]"
                       value="{{ old("items.$idx.cantidad", $it->cantidad) }}">
              </td>

              <td>
                <input class="form-control" type="number" min="0"
                       name="items[{{ $idx }}][vr_unitario]"
                       value="{{ old("items.$idx.vr_unitario", $it->vr_unitario) }}">
              </td>

              <td class="vr-total"></td>

              <td>
                <button type="button" class="btn btn-sm btn-outline-danger del">X</button>
              </td>

              {{-- Importante: id del item existente --}}
              <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $it->id }}">
              <input type="hidden" name="items[{{ $idx }}][vr_total]" value="{{ (int)$it->vr_total }}">
            </tr>
          @endforeach
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
    @php
      $notes = old('notas', $quotation->notas ?? []);
    @endphp

    <div class="mb-2"><b>Notas:</b></div>
    <div id="notesBox" class="mb-3">
      @foreach($notes as $note)
        <div class="input-group mb-2">
          <span class="input-group-text">•</span>
          <input name="notas[]" class="form-control" value="{{ $note }}">
          <button type="button" class="btn btn-outline-danger btn-sm del-note" tabindex="-1">X</button>
        </div>
      @endforeach
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary" id="addNoteBtn">Añadir nota</button>

    <div class="mt-4">
      <button class="btn btn-success">Guardar cambios</button>
    </div>
  </form>
</div>

{{-- Tom Select (estilos + script). Bootstrap ya viene de tu layout --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>
const IS_EDIT = true; // ← estamos en edición
const money = n => new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n);

function recalc() {
  let total = 0;
  document.querySelectorAll('#itemsBody tr').forEach((tr, idx) => {
    tr.querySelector('.item-num').innerText = idx + 1;

    const qty  = parseInt(tr.querySelector('[name$="[cantidad]"]').value || 0);
    const unit = parseInt(tr.querySelector('[name$="[vr_unitario]"]').value || 0);
    const line = qty * unit;

    tr.querySelector('.vr-total').innerText = money(line);
    const hidden = tr.querySelector('[name$="[vr_total]"]');
    if (hidden) hidden.value = line;

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

function attachDeleteNoteEvents() {
  document.querySelectorAll('.del-note').forEach(btn => {
    btn.onclick = function() { btn.closest('.input-group').remove(); };
  });
}

document.addEventListener('DOMContentLoaded', () => {
  // Enganchar eventos a filas existentes
  document.querySelectorAll('#itemsBody tr input, #itemsBody tr textarea').forEach(el => {
    el.addEventListener('input', recalc);
  });
  document.querySelectorAll('#itemsBody tr .del').forEach(btn => {
    btn.addEventListener('click', (e) => { e.target.closest('tr').remove(); recalc(); });
  });
  recalc();

  // Botones
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
  attachDeleteNoteEvents();

  // Desactivar reconstrucción de items por "Tipos de estudio" en edición
  const checks = document.getElementById('studyChecks');
  if (checks) {
    checks.addEventListener('change', () => {
      if (IS_EDIT) {
        // Si quieres permitir, aquí podrías mostrar un confirm y, si aceptan,
        // cargar plantillas como en create. Por defecto: no hacemos nada.
        return;
      }
    });
  }

  // TomSelect: Dirigido a
  const dirigidoEl = document.getElementById('dirigidoSelect');
  if (dirigidoEl) {
    new TomSelect(dirigidoEl, {
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
          .then(json => callback(json))
          .catch(() => callback());
      },
    });
  }

  // TomSelect: Referente
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
