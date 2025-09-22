@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Cotización #{{ $quotation->id }}</h4>
    <a class="btn btn-primary" href="{{ route('quotations.pdf', $quotation) }}">Descargar PDF</a>
  </div>

  <p><b>Fecha:</b> {{ $quotation->fecha->format('Y-m-d') }}</p>
  <p><b>Ciudad:</b> {{ $quotation->ciudad }} — <b>Departamento:</b> {{ $quotation->departamento }}</p>
  <p><b>Dirigido a:</b> {{ $quotation->dirigido_a }}</p>
  <p><b>Objeto:</b> {{ $quotation->objeto }}</p>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>ITEM</th><th>DESCRIPCIÓN</th><th>UND</th><th>CANT</th><th>VR. UNITARIO</th><th>VR. TOTAL</th>
      </tr>
    </thead>
    <tbody>
      @foreach($quotation->items as $i => $it)
      <tr>
        <td>{{ $i+1 }}</td>
        <td style="white-space:pre-line">{{ $it->descripcion }}</td>
        <td>{{ $it->und }}</td>
        <td>{{ $it->cantidad }}</td>
        <td>$ {{ number_format($it->vr_unitario,0,',','.') }}</td>
        <td>$ {{ number_format($it->vr_total,0,',','.') }}</td>
      </tr>
      @endforeach
      <tr>
        <td colspan="5" class="text-end fw-bold">Total</td>
        <td class="fw-bold">$ {{ number_format($quotation->total(),0,',','.') }}</td>
      </tr>
    </tbody>
  </table>

  <div>
    <b>Notas:</b>
    <ul>
      @foreach($quotation->notas ?? [] as $n)
        <li>{{ $n }}</li>
      @endforeach
    </ul>
  </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection
