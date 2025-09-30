@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">Nuevo usuario</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold mb-2">Revisa los errores:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('users.store') }}">
    @csrf

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Nombre</label>
        <input name="name" class="form-control" value="{{ old('name') }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Roles</label>
        <select name="roles[]" class="form-select" multiple>
          @foreach($roles as $r)
            <option value="{{ $r->name }}" @selected(collect(old('roles',[]))->contains($r->name))>{{ $r->name }}</option>
          @endforeach
        </select>
        <div class="form-text">Ctrl/Cmd + clic para seleccionar varios.</div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Permisos directos</label>
        <select name="permissions[]" class="form-select" multiple>
          @foreach($permissions as $p)
            <option value="{{ $p->name }}" @selected(collect(old('permissions',[]))->contains($p->name))>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="mt-4">
      <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
      <button class="btn btn-primary">Crear</button>
    </div>
  </form>
</div>
@endsection
