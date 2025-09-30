@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('content')
<div class="container py-4 bg-dark text-light rounded-3">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-dark text-light">
      <li class="breadcrumb-item">
        <a href="{{ route('cotnom.index') }}" class="btn btn-sm btn-outline-primary">Carpetas</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">{{ $displayName }}</li>
    </ol>
  </nav>

  @if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  <h3 class="mb-3">Cotizaciones del referente: <span class="fw-semibold">{{ $displayName }}</span></h3>

  <input id="quoteSearch" class="form-control mb-3 bg-secondary text-light border-0" placeholder="Buscar (consecutivo, fecha, ciudad, dirigido a, estado)…">



  @if($quotations->isEmpty())
    <div class="alert alert-info">No hay cotizaciones.</div>
  @else
    <div class="list-group" id="quotesList">
      
      @foreach ($quotations as $q)
        @php
          $consec = $q->consecutivo ? 'COT-'.str_pad($q->consecutivo, 6, '0', STR_PAD_LEFT) : 'ID-'.$q->id;
          $fecha  = optional($q->fecha)->format('Y-m-d');
          $total  = number_format($q->items->sum('vr_total'), 0, ',', '.');

          $estado = $q->estado ?? 'pendiente';
          $badgeClass = match ($estado) {
            'aceptada'  => 'bg-success',
            'cancelada' => 'bg-danger',
            default     => 'bg-warning text-dark', // pendiente
          };
          $borderClass = match ($estado) {
            'aceptada'  => 'border-estado-aceptada',
            'cancelada' => 'border-estado-cancelada',
            default     => 'border-estado-pendiente',
          };
        @endphp

        <div
  class="bg-dark list-group-item d-flex justify-content-between align-items-center quote-item border-start border-4 {{ $borderClass }} mb-3"
  data-haystack="{{ \Illuminate\Support\Str::of("$consec $fecha $q->ciudad $q->dirigido_a $estado ".($q->referente ?? ''))->lower() }}"
  style="color:#fff;">

            <div class="me-3 text-white">
            <div class="d-flex align-items-center gap-2">
              <div class="fw-semibold text-white">{{ $consec }}</div>
              <span class="badge {{ $badgeClass }}">{{ ucfirst($estado) }}</span>
              <small class="text-white-50">{{ $fecha }}</small>
            </div>
            <div class="small mt-1">
              <span class="text-white-50">
              Referente: <span class="text-white">{{ $q->referente ?: '—' }}</span> ·
              Ciudad: <span class="text-white">{{ $q->ciudad }}</span> ·
              Dirigido a: <span class="text-white">{{ $q->dirigido_a }}</span>
              </span>
            </div>
            </div>

          <div class="text-end">
            <div class="fw-bold mb-2">$ {{ $total }}</div>

            {{-- Botonera de estado (tres pequeños formularios PATCH con @csrf) --}}
            <div class="btn-group btn-group-sm mb-2" role="group" aria-label="Cambiar estado">
              {{-- Pendiente --}}
              <form action="{{ route('cotnom.updateEstado', ['slug' => $slug, 'quotation' => $q->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="estado" value="pendiente">
                <button type="submit"
                        class="btn btn-outline-warning @if($estado==='pendiente') active disabled @endif"
                        title="Marcar como pendiente">
                  Pendiente
                </button>
              </form>

              {{-- Aceptada --}}
              <form action="{{ route('cotnom.updateEstado', ['slug' => $slug, 'quotation' => $q->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="estado" value="aceptada">
                <button type="submit"
                        class="btn btn-outline-success @if($estado==='aceptada') active disabled @endif"
                        title="Marcar como aceptada">
                  Aceptada
                </button>
              </form>

              {{-- Cancelada --}}
              <form action="{{ route('cotnom.updateEstado', ['slug' => $slug, 'quotation' => $q->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="estado" value="cancelada">
                <button type="submit"
                        class="btn btn-outline-danger @if($estado==='cancelada') active disabled @endif"
                        title="Marcar como cancelada">
                  Cancelada
                </button>
              </form>
            </div>

            <div>
              <a class="btn btn-sm btn-primary"
                 href="{{ route('cotnom.pdf', ['slug' => $slug, 'quotation' => $q->id]) }}">
                PDF
              </a>
              <a class="btn btn-sm btn-secondary" href="{{ route('quotations.edit', $q) }}">Editar</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

<style>
.border-estado-pendiente { border-color: #f59f00 !important; } /* amarillo */
.border-estado-aceptada  { border-color: #198754 !important; } /* verde */
.border-estado-cancelada { border-color: #dc3545 !important; } /* rojo */
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('quoteSearch');
  if (!input) return;

  // Normaliza: minúsculas + sin tildes
  const normalize = (s = '') =>
    (s || '')
      .toString()
      .normalize('NFD')                // separa base + tilde
      .replace(/[\u0300-\u036f]/g, '') // borra tildes
      .toLowerCase()
      .trim();

  const debounce = (fn, ms = 150) => {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  // Pre-normaliza el haystack de cada tarjeta
  document.querySelectorAll('.quote-item').forEach(it => {
    const raw = it.dataset.haystack || '';
    it.dataset.norm = normalize(raw);
  });

  const applyFilter = () => {
    const q = normalize(input.value);
    document.querySelectorAll('.quote-item').forEach(it => {
      const hay = it.dataset.norm || '';
      it.style.display = hay.includes(q) ? '' : 'none';
    });
  };

  input.addEventListener('input', debounce(applyFilter, 150));

  // Por si el navegador recuerda el valor del input al recargar
  applyFilter();
});
</script>

@endsection
