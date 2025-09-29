{{-- resources/views/partials/sidebar.blade.php --}}
@php
use Illuminate\Support\Facades\Route as RouteFacade;

function isActive($patterns){ return request()->routeIs($patterns) ? 'active' : ''; }
@endphp

<aside id="appSidebar" class="sidebar">
  <div class="brand">
    <div class="brand-logo">🅸</div>
    <div class="brand-name">INGETUL</div>
  </div>

  <div class="profile">
    <div class="avatar">👤</div>
    <div class="who">
      <div class="name">{{ auth()->user()->name ?? 'Invitado' }}</div>
      <div class="role">{{ auth()->check() ? 'Usuario' : 'Sin iniciar sesión' }}</div>
    </div>
  </div>

  <nav class="menu">

    <a href="{{ route('home') }}" class="item {{ isActive('home') }}">
      <i class="bi bi-house-door"></i>
      <span>Inicio</span>
    </a>

    {{-- Cotizaciones (con submenú) --}}
    <details class="group" {{ request()->routeIs('cotnom.*') || request()->routeIs('quotations.*') ? 'open' : '' }}>
      <summary class="item">
        <i class="bi bi-file-earmark-text"></i>
        <span>Cotizaciones</span>
        <i class="bi bi-chevron-right caret"></i>
      </summary>
      <div class="sub">
        <a href="{{ route('cotnom.index') }}" class="subitem {{ isActive('cotnom.*') }}">
          <span>Carpetas de clientes</span>
        </a>
        <a href="{{ route('quotations.create') }}" class="subitem {{ isActive('quotations.create') }}">
          <span>Nueva cotización</span>
        </a>
      </div>
    </details>

    {{-- Proyectos (submódulos) --}}
    <details class="group" {{ request()->routeIs('proyectos.*') ? 'open' : '' }}>
      <summary class="item">
        <i class="bi bi-kanban"></i>
        <span>Proyectos</span>
        <i class="bi bi-chevron-right caret"></i>
      </summary>
      <div class="sub">
        @if (RouteFacade::has('proyectos.suelos.index'))
          <a href="{{ route('proyectos.suelos.index') }}" class="subitem {{ isActive('proyectos.suelos.*') }}">Estudio de suelos</a>
        @endif
        @if (RouteFacade::has('proyectos.calculo.index'))
          <a href="{{ route('proyectos.calculo.index') }}" class="subitem {{ isActive('proyectos.calculo.*') }}">Cálculo estructural</a>
        @endif
        @if (RouteFacade::has('proyectos.planos.index'))
          <a href="{{ route('proyectos.planos.index') }}" class="subitem {{ isActive('proyectos.planos.*') }}">Planos estructurales</a>
        @endif
      </div>
    </details>

    {{-- Laboratorio --}}
    <a href="{{ route('laboratorio.index') }}" class="item {{ isActive('laboratorio.index') }}">
      <i class="bi bi-flask"></i>
      <span>Laboratorio</span>
    </a>

    {{-- Proyectos terminados --}}
    @if (RouteFacade::has('proyectos.terminados.index'))
      <a href="{{ route('proyectos.terminados.index') }}" class="item {{ isActive('proyectos.terminados.*') }}">
        <i class="bi bi-check2-circle"></i>
        <span>Proyectos terminados</span>
      </a>
    @endif

    {{-- Usuarios --}}
    @if (RouteFacade::has('users.index'))
      <a href="{{ route('users.index') }}" class="item {{ isActive('users.*') }}">
        <i class="bi bi-people"></i>
        <span>Usuarios</span>
      </a>
    @endif

    {{-- Reportes --}}
    @if (RouteFacade::has('reportes.index'))
      <a href="{{ route('reportes.index') }}" class="item {{ isActive('reportes.*') }}">
        <i class="bi bi-pie-chart"></i>
        <span>Reportes</span>
      </a>
    @endif
  </nav>

  {{-- footer / logout --}}
  <div class="bottom">
    @auth
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="item btn-as-link">
          <i class="bi bi-box-arrow-right"></i>
          <span>Salir</span>
        </button>
      </form>
    @else
      <a href="{{ route('login') }}" class="item">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Ingresar</span>
      </a>
    @endauth
  </div>
</aside>

<style>
/* Layout base del sidebar */
.sidebar {
  position: fixed;
  inset: 0 auto 0 0;
  width: 260px;
  background: #2f3a45;
  color: #e5e7eb;
  display: flex;
  flex-direction: column;
  box-shadow: 0 0 0 1px rgba(255,255,255,.04), 0 8px 24px rgba(0,0,0,.35);
  z-index: 1040;
}
@media (max-width: 991.98px){
  .sidebar { transform: translateX(-100%); transition: transform .25s ease; }
  .sidebar.open { transform: none; }
}

/* Brand */
.sidebar .brand {
  display:flex; align-items:center; gap:.6rem;
  padding: .9rem 1rem; border-bottom: 1px solid rgba(255,255,255,.06);
}
.brand-logo{ width:28px; height:28px; display:grid; place-items:center; background:#16a34a; color:#0b1e12; border-radius:8px; font-weight:700;}
.brand-name{ font-weight:700; letter-spacing:.4px; }

/* Perfil */
.profile { display:flex; gap:.6rem; padding:1rem; border-bottom:1px solid rgba(255,255,255,.06);}
.profile .avatar{ width:36px; height:36px; display:grid; place-items:center; background:#1f2937; border-radius:50%;}
.profile .name{ font-weight:600;}
.profile .role{ font-size:.82rem; color:#9ca3af; }

/* Navegación */
.menu { padding:.5rem .5rem 1rem; overflow-y:auto; }
.item, .btn-as-link{
  display:flex; align-items:center; gap:.65rem;
  padding:.6rem .75rem; margin: .15rem .25rem;
  border-radius:10px; color:#e5e7eb; text-decoration:none;
}
.item:hover{ background:#3a4652; color:#fff; }
.item.active{ background:#1f6feb; color:#fff; }
.item i{ font-size:1.1rem; }

/* Submenús */
.group { margin:.15rem .25rem; }
.group > summary { list-style:none; cursor:pointer; }
.group[open] > summary .caret { transform: rotate(90deg); }
.caret{ margin-left:auto; transition: transform .2s ease; }
.sub{ padding-left:.5rem; border-left:2px solid rgba(255,255,255,.08); margin:.25rem 0 .35rem .5rem; }
.subitem{
  display:block; padding:.45rem .6rem; border-radius:8px; color:#d1d5db; text-decoration:none; font-size:.95rem;
}
.subitem:hover{ background:#384450; color:#fff; }
.subitem.active{ background:#1f6feb; color:#fff; }

/* Footer */
.bottom{ margin-top:auto; padding: .5rem .5rem .75rem; border-top:1px solid rgba(255,255,255,.06);}
.btn-as-link{ background:transparent; border:0; width:100%; text-align:left; cursor:pointer; }

/* para Bootstrap container principal */
.main-with-sidebar{
  margin-left: 260px;
}
@media (max-width: 991.98px){
  .main-with-sidebar{ margin-left:0; }
}
</style>

{{-- Toggle simple en móviles (opcional): agrega data-action="toggle-sidebar" a tu botón --}}
<script>
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('[data-action="toggle-sidebar"]');
  if(btn){ document.getElementById('appSidebar').classList.toggle('open'); }
});
</script>
