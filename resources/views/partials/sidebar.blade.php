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

    {{-- Módulo: Inicio --}}
    <a href="{{ route('home') }}" class="item module {{ isActive('home') }}">
      <i class="bi bi-house-door"></i>
      <span>Inicio</span>
    </a>

    {{-- Módulo: Cotizaciones (con submódulos) --}}
    <details class="group" {{ request()->routeIs('cotnom.*') || request()->routeIs('quotations.*') ? 'open' : '' }}>
      <summary class="item module">
        <i class="bi bi-file-earmark-text"></i>
        <span>Cotizaciones</span>
        <i class="bi bi-chevron-right caret"></i>
      </summary>
      <div class="sub">
        <a href="{{ route('cotnom.index') }}" class="subitem {{ isActive('cotnom.*') }}">
          <span class="dot">•</span><span>Carpetas de clientes</span>
        </a>
        <a href="{{ route('quotations.create') }}" class="subitem {{ isActive('quotations.create') }}">
          <span class="dot">•</span><span>Nueva cotización</span>
        </a>
      </div>
    </details>

    {{-- Módulo: Proyectos (con submódulos) --}}
    <details class="group" {{ request()->routeIs('proyectos.*') ? 'open' : '' }}>
      <summary class="item module">
        <i class="bi bi-kanban"></i>
        <span>Proyectos</span>
        <i class="bi bi-chevron-right caret"></i>
      </summary>
      <div class="sub">
        @if (RouteFacade::has('proyectos.suelos.index'))
          <a href="{{ route('proyectos.suelos.index') }}" class="subitem {{ isActive('proyectos.suelos.*') }}">
            <span class="dot">•</span><span>Estudio de suelos</span>
          </a>
        @endif
        @if (RouteFacade::has('proyectos.calculo.index'))
          <a href="{{ route('proyectos.calculo.index') }}" class="subitem {{ isActive('proyectos.calculo.*') }}">
            <span class="dot">•</span><span>Cálculo estructural</span>
          </a>
        @endif
        @if (RouteFacade::has('proyectos.planos.index'))
          <a href="{{ route('proyectos.planos.index') }}" class="subitem {{ isActive('proyectos.planos.*') }}">
            <span class="dot">•</span><span>Planos estructurales</span>
          </a>
        @endif
      </div>
    </details>

    {{-- Módulo: Laboratorio --}}
    <a href="{{ route('laboratorio.index') }}" class="item module {{ isActive('laboratorio.index') }}">
      <i class="bi bi-flask"></i>
      <span>Laboratorio</span>
    </a>

    {{-- Módulo: Proyectos terminados --}}
    @if (RouteFacade::has('proyectos.terminados.index'))
      <a href="{{ route('proyectos.terminados.index') }}" class="item module {{ isActive('proyectos.terminados.*') }}">
        <i class="bi bi-check2-circle"></i>
        <span>Proyectos terminados</span>
      </a>
    @endif

    {{-- Módulo: Usuarios --}}
    @if (RouteFacade::has('users.index'))
      <a href="{{ route('users.index') }}" class="item module {{ isActive('users.*') }}">
        <i class="bi bi-people"></i>
        <span>Usuarios</span>
      </a>
    @endif

    {{-- Módulo: Reportes --}}
    @if (RouteFacade::has('reportes.index'))
      <a href="{{ route('reportes.index') }}" class="item module {{ isActive('reportes.*') }}">
        <i class="bi bi-pie-chart"></i>
        <span>Reportes</span>
      </a>
    @endif
  </nav>

  <div class="bottom">
    @auth
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="item module btn-as-link">
          <i class="bi bi-box-arrow-right"></i>
          <span>Salir</span>
        </button>
      </form>
    @else
      <a href="{{ route('login') }}" class="item module">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Ingresar</span>
      </a>
    @endauth
  </div>
</aside>

<style>
/* Base */
.sidebar {
  position: fixed; inset: 0 auto 0 0; width: 260px;
  background: #2f3a45; color: #e5e7eb;
  display: flex; flex-direction: column;
  box-shadow: 0 0 0 1px rgba(255,255,255,.04), 0 8px 24px rgba(0,0,0,.35);
  z-index: 1040;
}
@media (max-width: 991.98px){
  .sidebar { transform: translateX(-100%); transition: transform .25s ease; }
  .sidebar.open { transform: none; }
}
.brand{ display:flex; align-items:center; gap:.6rem; padding:.9rem 1rem; border-bottom:1px solid rgba(255,255,255,.06); }
.brand-logo{ width:28px; height:28px; display:grid; place-items:center; background:#16a34a; color:#0b1e12; border-radius:8px; font-weight:700;}
.brand-name{ font-weight:700; letter-spacing:.4px; }
.profile { display:flex; gap:.6rem; padding:1rem; border-bottom:1px solid rgba(255,255,255,.06);}
.profile .avatar{ width:36px; height:36px; display:grid; place-items:center; background:#1f2937; border-radius:50%;}
.profile .name{ font-weight:600;}
.profile .role{ font-size:.82rem; color:#9ca3af; }

.menu { padding:.5rem .5rem 1rem; overflow-y:auto; }

/* Enlaces */
.item, .btn-as-link{
  display:flex; align-items:center; gap:.65rem;
  padding:.6rem .75rem; margin:.15rem .25rem;
  border-radius:10px; color:#e5e7eb; text-decoration:none;
}

/* ===== Diferencia MÓDULO vs SUBMÓDULO ===== */

/* Nivel 1: módulo */
.item.module{
  font-weight: 700;
  font-size: .98rem;
  background: transparent;
  position: relative;
}
.item.module:hover{ background:#36414d; color:#fff; }
.item.module.active{
  background: linear-gradient(90deg, rgba(31,111,235,.22), rgba(31,111,235,.06));
  color:#fff;
}
.item.module::before{
  content:""; position:absolute; left:6px; top:8px; bottom:8px; width:3px;
  border-radius: 3px; background: transparent;
}
.item.module:hover::before,
.item.module.active::before{
  background:#1f6feb;  /* barrita de acento para módulos */
}

/* Cabecera del details como módulo */
.group > summary.item.module { list-style:none; cursor:pointer; }

/* Caret animado */
.group[open] > summary .caret { transform: rotate(90deg); }
.caret{ margin-left:auto; transition: transform .2s ease; }

/* Contenedor submódulos */
.sub{
  padding-left:.75rem; border-left:2px solid rgba(255,255,255,.10);
  margin:.25rem 0 .5rem .5rem;
}

/* Nivel 2: submódulo */
.subitem{
  display:flex; align-items:center; gap:.45rem;
  padding:.45rem .6rem; border-radius:8px;
  color:#d1d5db; text-decoration:none; font-size:.92rem; font-weight:500;
}
.subitem .dot{ opacity:.65; transform: translateY(-1px); }
.subitem:hover{ background:#384450; color:#fff; }
.subitem.active{
  background:#1f6feb; color:#fff;
}

/* Footer */
.bottom{ margin-top:auto; padding:.5rem .5rem .75rem; border-top:1px solid rgba(255,255,255,.06); }
.btn-as-link{ background:transparent; border:0; width:100%; text-align:left; cursor:pointer; }

/* Empuje del contenido principal */
.main-with-sidebar{ margin-left: 260px; }
@media (max-width: 991.98px){ .main-with-sidebar{ margin-left:0; } }
</style>

<script>
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('[data-action="toggle-sidebar"]');
  if(btn){ document.getElementById('appSidebar').classList.toggle('open'); }
});
</script>
