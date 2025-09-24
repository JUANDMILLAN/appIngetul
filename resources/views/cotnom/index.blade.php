@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('content')
<div class="container py-4">
  <a href="{{ route('quotations.create') }}" class="btn btn-success mb-3">Nueva Cotización</a>
  <h2 class="mb-3">usuarios</h2>
  <input id="clientSearch" class="form-control mb-3" placeholder="Buscar nombre…">



  @if($clientes->isEmpty())
    <div class="alert alert-info">No hay cotizaciones.</div>
  @else
    <div class="row" id="clientsGrid">
      @foreach($clientes as $c)
        <div class="col-12 col-md-6 col-lg-4 client-card" data-name="{{ Str::lower($c->display_name) }}">
          <a href="{{ route('cotnom.show', $c->slug) }}"
             class="list-group-item list-group-item-action d-flex justify-content-between align-items-center mb-2 rounded shadow-sm">
            <span class="text-truncate" title="{{ $c->display_name }}">📁 {{ $c->display_name }}</span>
            <span class="badge bg-primary rounded-pill">{{ $c->total }}</span>
          </a>
        </div>
      @endforeach
    </div>
  @endif
</div>

<script>
const inp=document.getElementById('clientSearch');
if(inp){
  inp.addEventListener('input',()=>{
    const q=inp.value.toLowerCase();
    document.querySelectorAll('#clientsGrid .client-card').forEach(el=>{
      el.style.display=(el.dataset.name||'').includes(q)?'':'none';
    });
  });
}
</script>
@endsection
