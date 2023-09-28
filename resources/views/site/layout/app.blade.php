<!doctype html>
<html lang="en">

<head>
  <base href="{{ env('APP_URL') }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>{{ env('APP_NAME') }}</title>

  <!-- Bootstrap core CSS -->
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk&family=Titillium+Web:wght@300&display=swap" rel="stylesheet">
  <link href="{{ asset('css/site.css') }}?v=3" rel="stylesheet">
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-TEMW2WSQE1">
  </script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-TEMW2WSQE1');
  </script>

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
    color: rgba(255, 255, 255, 1) !important;
  }

  .sub-item {
    padding-left: 40px;
    /* Add indentation for subcategories */
    color: #888;
    /* Apply different color to subcategories */
  }

  .sub_category {
    display: none;
  }
</style>

<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="background-color:#0c5460!important">
      <a class="navbar-brand" style="font-size: 30px;font-weight:bold;font-family: 'Titillium Web', sans-serif;" href="/">{{ env('APP_NAME') }}</a>
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
              @if($category->status == '1')
              @if($category->id == 10 || $category->id == 11)
              @continue
              @endif
              @if(count($category->childCategories) == 0)
              @if(!$category->parentCategory)
              <a class="dropdown-item" href="\?id={{$category->id}}">{{$category->title}}</a>
              @endif
              @else
              <a class="dropdown-item dropdown-toggle" href="#" id="subDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{$category->title}}
              </a>
              <div class="dropdown-menu" aria-labelledby="subDropdown" style="position: relative;">
                @foreach($category->childCategories as $subcategory)
                @if($subcategory->status == '1')
                <a class="dropdown-item" href="\?id={{$subcategory->id}}">- {{$subcategory->title}}</a>
                @endif
                @endforeach
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="\?id={{$category->id}}">Show All {{$category->title}}</a>
              </div>
              @endif
              @endif
              @endforeach
              <a class="dropdown-item text-center" href="{{ route('categories.index') }}"><b>All</b></a>
            </div>
          </li>
          <li class="nav-item">
            <a href="{{ config('app.packageUrl') }}" class="nav-link">Packages</a>
          </li>
          <li class="nav-item">
            <a href="{{ config('app.addOnUrl') }}" class="nav-link">Beauty Add-Ons</a>
          </li>


          <li class="nav-item">
            <a href="{{ route('cart.index') }}" class="nav-link">Cart({{$cart_product}})</a>
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
              <a class="dropdown-item" href="{{ route('customerProfile.edit', auth()->user()->id) }}">Profile</a>
              @if(Auth::user()->hasRole('Affiliate'))
              <a class="dropdown-item" href="{{ route('affiliate_dashboard.index') }}">Affiliate Dashboard</a>
              @endif
              <a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>
              <a class="dropdown-item" href="/customer-logout">Logout</a>
              @endguest
            </div>
          </li>
          <li class="nav-item">
            <a href="{{ route('siteFAQs.index')}}" class="nav-link">FAQs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('siteReviews.index') }}">Reviews</a>
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
  <script src="./js/vendor/popper.min.js"></script>
  <script src="./js/bootstrap.min.js"></script>
  <script src="./js/vendor/holder.min.js"></script>
  <script src="{{ asset('js/popup.js') }}?v={{config('app.version')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=mapReady&type=address"></script>
  <script>
    var Dropdowns = function() {
      var t = $(".dropdown"),
        e = $(".dropdown-menu"),
        r = $(".dropdown-menu .dropdown-menu");
      $(".dropdown-menu .dropdown-toggle").on("click", function() {
          var a;
          return (a = $(this)).closest(t).siblings(t).find(e).removeClass("show"),
            a.next(r).toggleClass("show"),
            !1
        }),
        t.on("hide.bs.dropdown", function() {
          var a, t;
          a = $(this),
            (t = a.find(r)).length && t.removeClass("show")
        })
    }()
  </script>

</body>

</html>