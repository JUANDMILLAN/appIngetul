@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">Editar usuario</h1>

  @if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold mb-2">Revisa los errores:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-datos" type="button" role="tab">Datos</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-roles" type="button" role="tab">Roles</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-permisos" type="button" role="tab">Permisos</button>
    </li>
  </ul>

  <div class="tab-content">
    {{-- Datos --}}
    <div class="tab-pane fade show active" id="tab-datos" role="tabpanel">
      <form method="POST" action="{{ route('users.update',$user) }}">
        @csrf @method('PUT')

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Nombre</label>
            <input name="name" class="form-control" value="{{ old('name',$user->name) }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Nueva contraseña (opcional)</label>
            <input type="password" name="password" class="form-control">
            <small class="text-muted">Déjalo vacío para no cambiarla.</small>
          </div>
          <div class="col-md-4">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="form-control">
          </div>
        </div>

        <div class="mt-4">
          <button class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>

    {{-- Roles --}}
    <div class="tab-pane fade" id="tab-roles" role="tabpanel">
      <form method="POST" action="{{ route('users.syncRoles',$user) }}">
        @csrf @method('PUT')

        <div class="mb-3">
          @foreach($roles as $r)
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="roles[]"
                     value="{{ $r->name }}" id="r-{{ $r->id }}"
                     @checked($user->roles->pluck('name')->contains($r->name))>
              <label class="form-check-label" for="r-{{ $r->id }}">{{ $r->name }}</label>
            </div>
          @endforeach
        </div>

        <button class="btn btn-primary btn-sm">Actualizar roles</button>
      </form>
    </div>

    {{-- Permisos --}}
    <div class="tab-pane fade" id="tab-permisos" role="tabpanel">
      <form method="POST" action="{{ route('users.syncPermissions',$user) }}">
        @csrf @method('PUT')

        <div class="row g-2">
          @foreach($permissions as $p)
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permissions[]"
                       value="{{ $p->name }}" id="p-{{ $p->id }}"
                       @checked($user->permissions->pluck('name')->contains($p->name))>
                <label class="form-check-label" for="p-{{ $p->id }}">{{ $p->name }}</label>
              </div>
            </div>
          @endforeach
        </div>

        <button class="btn btn-primary btn-sm mt-3">Actualizar permisos</button>
      </form>
    </div>
  </div>
</div>
@endsection
