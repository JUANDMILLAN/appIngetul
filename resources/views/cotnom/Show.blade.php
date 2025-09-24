@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="{{ route('cotnom.index') }}" class="btn btn-sm btn-outline-primary">Carpetas</a>
    </li>
      <li class="breadcrumb-item active" aria-current="page">{{ $displayName }}</li>
    </ol>
  </nav>

  <h3 class="mb-3">Cotizaciones de: {{ $displayName }}</h3>
  <input id="quoteSearch" class="form-control mb-3" placeholder="Buscar (consecutivo, ciudad, dirigido a)…">

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

    <div class="list-group-item d-flex justify-content-between align-items-center quote-item border-start border-4 {{ $borderClass }} bg-dark text-white mb-3"
         data-haystack="{{ Str::lower("$consec $fecha $q->ciudad $q->dirigido_a $estado") }}">

    <div class="me-3">
      <div class="d-flex align-items-center gap-2">
        <div class="fw-semibold">{{ $consec }}</div>
        <span class="badge {{ $badgeClass }}">{{ ucfirst($estado) }}</span>
        <small class="text-white">{{ $fecha }}</small>
      </div>
      <div class="small text-white mt-1">
        Ciudad: {{ $q->ciudad }} · Dirigido a: {{ $q->dirigido_a }}
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
const q=document.getElementById('quoteSearch');
if(q){
  q.addEventListener('input',()=>{
    const needle=q.value.toLowerCase();
    document.querySelectorAll('.quote-item').forEach(it=>{
      it.style.display=(it.dataset.haystack||'').includes(needle)?'':'none';
    });
  });
}
</script>
@endsection
