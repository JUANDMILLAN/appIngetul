<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  * { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
  .box { border: 2px solid #000; padding: 6px; }
  .title { text-align:center; font-weight:700; border:2px solid #000; padding:6px; margin-bottom:8px; }
  table { width:100%; border-collapse: collapse; }
  th, td { border:1px solid #000; padding:6px; vertical-align: top; }
  .right { text-align: right; }
  .notes { margin-top: 8px; }
  .desc { white-space: pre-line; }
</style>
</head>
<body>
<div class="title">COTIZACIÓN</div>

<table>
  <tr>
    <td style="width:120px"><b>FECHA:</b></td><td>{{ $quotation->fecha->translatedFormat('l, d \d\e F \d\e Y') }}</td>
  </tr>
  <tr>
    <td><b>CIUDAD:</b></td><td>{{ $quotation->ciudad }}</td>
  </tr>
  <tr>
    <td><b>DEPARTAMENTO:</b></td><td>{{ $quotation->departamento }}</td>
  </tr>
  <tr>
    <td><b>DIRIGIDO A:</b></td><td>{{ $quotation->dirigido_a }}</td>
  </tr>
  <tr>
    <td><b>OBJETO:</b></td><td>{{ $quotation->objeto }}</td>
  </tr>
</table>

<br>

<table>
  <thead>
    <tr>
      <th style="width:50px">ITEM</th>
      <th>DESCRIPCIÓN</th>
      <th style="width:60px">UND</th>
      <th style="width:60px">CANT</th>
      <th style="width:110px">VR. UNITARIO</th>
      <th style="width:110px">VR. TOTAL</th>
    </tr>
  </thead>
  <tbody>
  @foreach($quotation->items as $i => $it)
    <tr>
      <td class="right">{{ $i + 1 }}</td>
      <td class="desc">{{ $it->descripcion }}</td>
      <td class="right">{{ $it->und }}</td>
      <td class="right">{{ $it->cantidad }}</td>
      <td class="right">$ {{ number_format($it->vr_unitario,0,',','.') }}</td>
      <td class="right">$ {{ number_format($it->vr_total,0,',','.') }}</td>
    </tr>
  @endforeach
    <tr>
      <td colspan="5" class="right"><b>Total</b></td>
      <td class="right"><b>$ {{ number_format($quotation->total(),0,',','.') }}</b></td>
    </tr>
  </tbody>
</table>

<div class="notes">
  <b>NOTAS:</b>
  <ul>
    @foreach(($quotation->notas ?? []) as $n)
      <li>{{ $n }}</li>
    @endforeach
  </ul>
</div>
<div  style="margin-top:20px; font-size:10px;">
    <img src="" alt="">
    <div style="margin-top: 40px; text-align: left;">
  <p>Atentamente,</p>
  <img src="{{ public_path('images/firmajulio.png') }}" style="width:120px;">
  <p><b>Ing. Julio Tocoche</b><br>Gerente INGETUL</p>
</div>



</div>

</body>
</html>
