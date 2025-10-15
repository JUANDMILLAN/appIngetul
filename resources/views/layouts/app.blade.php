<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <title>Ingetul Cotizaciones</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-dark text-light">
 
  @include('partials.sidebar')

  <div class="main-with-sidebar">
   

    {{-- aquí va tu contenido --}}
    <main class="container-fluid py-3">
      @yield('content')
    </main>
  </div>

  {{-- Bootstrap JS si no lo tienes ya --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>


  

</html>
