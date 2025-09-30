@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Usuarios</h1>

    @can('users.create')
      <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Nuevo usuario</a>
    @endcan
  </div>

  <form method="GET" class="mb-3">
    <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Buscar por nombre o email…">
  </form>

  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if($users->isEmpty())
    <div class="alert alert-info">No hay usuarios.</div>
  @else
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th class="d-none d-md-table-cell">Roles</th>
            <th style="width: 180px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $u)
            <tr>
              <td>{{ $u->name }}</td>
              <td>{{ $u->email }}</td>
              <td class="d-none d-md-table-cell">
                @forelse($u->roles as $r)
                  <span class="badge bg-secondary">{{ $r->name }}</span>
                @empty
                  <span class="text-muted">—</span>
                @endforelse
              </td>
              <td>
                @can('users.edit')
                  <a href="{{ route('users.edit',$u) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                @endcan
                @can('users.delete')
                  <form action="{{ route('users.destroy',$u) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar este usuario?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                  </form>
                @endcan
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $users->links() }}
    </div>
  @endif
</div>
@endsection
