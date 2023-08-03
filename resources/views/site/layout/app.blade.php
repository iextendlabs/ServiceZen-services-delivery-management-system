<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>{{ env('APP_NAME') }}</title>

  <!-- Bootstrap core CSS -->
  <link href="./css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&family=Titillium+Web:wght@300&display=swap" rel="stylesheet">
  <link href="./css/site.css?v=3" rel="stylesheet">
</head>
@if(session()->has('serviceIds'))
@php
$cart_product = count(Session::get('serviceIds'));
@endphp
@else
@php
$cart_product = 0;
@endphp
@endif

<style>
  .navbar-dark .navbar-nav .nav-link {
    color: rgba(255,255,255,1) !important;
}
</style>
<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="background-color:#0c5460!important">
      <a class="navbar-brand" style="font-size: 35px;font-weight:bold;font-family: 'Nunito', sans-serif;font-family: 'Titillium Web', sans-serif;" href="/">{{ env('APP_NAME') }}</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">

          @if(isset($address))
          <li class="nav-item">
            <a class="nav-link" id="change-address"> <i class="fa fa-map-marker "></i> {{$address['area']}} {{$address['city']}}</a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" id="change-address"> <i class="fa fa-map-marker "></i> Set your location</a>
          </li>
          @endif
          <li class="nav-item">
            <a class="nav-link" href="/bookingStep">Booking</a>
          </li>


          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Services
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              @foreach($categories as $category)
              <a class="dropdown-item" href="\?id={{$category->id}}">{{$category->title}}</a>
              @endforeach
              <hr>
              <a class="dropdown-item text-center" href="\"><b>All</b></a>
            </div>
          </li>
          <li class="nav-item">
            <a href="{{ route('cart.index') }}" class="nav-link">View Cart({{$cart_product}})</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Account
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              @guest
              <a class="dropdown-item" href="/customer-login">Login</a>
              <a class="dropdown-item" href="/customer-registration">Register</a>
              @else
              @if(Auth::user()->hasRole('Staff'))
              <a class="dropdown-item" href="{{ route('transactions.index') }}">Transactions</a>
              <a class="dropdown-item" href="{{ route('order.index') }}">My Orders</a>
              <a class="dropdown-item" href="/customer-logout">Logout</a>
              @else
              <a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>
              <a class="dropdown-item" href="/customer-logout">Logout</a>
              @endif
              @endguest
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Contact</a>
          </li>
        </ul>
      </div>
    </nav>
    @include('site.layout.locationPopup')

  </header>

  <main role="main">

    @yield('content')

  </main>

  <footer class="text-muted">
    <div class="container">
      <p class="float-right">
        © 2023 {{ env('APP_NAME') }}

      </p>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript
    ================================================== -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="./js/vendor/popper.min.js"></script>
  <script src="./js/bootstrap.min.js"></script>
  <script src="./js/vendor/holder.min.js"></script>
  <script src="{{ asset('js/popup.js') }}?v=1"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=mapReady&type=address"></script>
</body>

</html>