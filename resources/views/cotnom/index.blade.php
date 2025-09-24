@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <h2 class="mb-0">Carpetas</h2>
    <a href="{{ route('quotations.create') }}" class="btn btn-success">Nueva Cotización</a>
  </div>

  <input id="clientSearch" class="form-control mb-3" placeholder="Buscar nombre…">

  @if($clientes->isEmpty())
    <div class="alert alert-info">No hay cotizaciones.</div>
  @else
    <div class="row g-3" id="clientsGrid">
      @foreach($clientes as $c)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 client-card" data-name="{{ Str::lower($c->display_name) }}">
          <a href="{{ route('cotnom.show', $c->slug) }}" class="text-decoration-none">
            <div class="folder card h-100 border-0 shadow-sm">
              <div class="folder-tab"></div>
              <div class="card-body d-flex flex-column pt-4">
                <div class="d-flex align-items-start justify-content-between mb-2">
                  <div class="folder-icon" aria-hidden="true">📁</div>
                  <span class="badge count-badge">{{ $c->total }}</span>
                </div>
                <div class="folder-name fw-semibold text-truncate text-white" title="{{ $c->display_name }}">
                  {{ $c->display_name }}
                </div>
                <div class="small text-muted mt-1">Carpeta</div>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  @endif
</div>

<style>
/* Tarjeta estilo carpeta */
.folder {
  position: relative;
  border-radius: 16px;
  transition: transform .15s ease, box-shadow .2s ease;
  background: #ffffff;
}
.folder:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(0,0,0,.12); }

/* Pestaña de carpeta */
.folder-tab {
  position: absolute; top: -8px; left: 14px;
  width: 48px; height: 14px;
  background: #ffd34d; border-top-left-radius: 10px; border-top-right-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,.1);
}

/* Icono & nombre */
.folder-icon { font-size: 1.25rem; opacity: .9; }
.folder-name { line-height: 1.2; }

/* Badge de conteo */
.count-badge {
  background: linear-gradient(90deg, #7c5cff, #3bc6ff);
  color: #fff; border-radius: 999px; padding: .25rem .5rem; font-weight: 600;
}

/* Modo oscuro si tu layout lo usa */
@media (prefers-color-scheme: dark) {
  .folder { background: #151a23; }
  .count-badge { background: linear-gradient(90deg, #6b5cff, #33b6ff); }
  .small.text-muted { color: #9aa3af !important; }
}
</style>

<script>
const inp = document.getElementById('clientSearch');
if (inp) {
  inp.addEventListener('input', () => {
    const q = inp.value.trim().toLowerCase();
    let visible = 0;
    document.querySelectorAll('#clientsGrid .client-card').forEach(el => {
      const show = (el.dataset.name || '').includes(q);
      el.style.display = show ? '' : 'none';
      if (show) visible++;
    });
  });
}
</script>
@endsection
