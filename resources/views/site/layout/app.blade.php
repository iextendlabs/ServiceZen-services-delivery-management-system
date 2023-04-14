<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ config('app.name', 'Services Delivery Management System') }}</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/site.css" rel="stylesheet">

  </head>
  
  <body>

    <header>
          <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="/">Saloon X UAE</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                  <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
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
                  <a href="{{ route('booking.index') }}" class="nav-link">Checkout</a>
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
                    <a class="dropdown-item" href="{{ route('transactions.index') }}">Transactions</a>
                    <a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>
                    <a class="dropdown-item" href="/customer-logout">Logout</a>
                  @endguest
                  </div>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Contact</a>
                </li>
              </ul>
            </div>
          </nav>
          

    </header>

    <main role="main">

       @yield('content')

    </main>

    <footer class="text-muted">
      <div class="container">
        <p class="float-right">
    © 2023 Saloon X UAE

        </p>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <script>window.jQuery || document.write('<script src="./js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="./js/vendor/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/vendor/holder.min.js"></script>
  </body>
</html>
