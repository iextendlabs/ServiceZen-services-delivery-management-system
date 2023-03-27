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

    <style>
        .service-box {
            padding: 50px 30px;
            position: relative;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 0 29px 0 rgba(18, 66, 101, 0.08);
            transition: all 0.3s ease-in-out;
            border-radius: 8px;
            z-index: 1;
        }
    </style>
  </head>

  <body>

    <header>
      <div class="collapse bg-dark" id="navbarHeader">
        <div class="container">
          <div class="row">
            <div class="col-sm-8 col-md-7 py-4">
              <h4 class="text-white">About</h4>
              <p class="text-muted">Add some information about the album below, the author, or any other background context. Make it a few sentences long so folks can pick up some informative tidbits. Then, link them off to some social networking sites or contact information.</p>
            </div>
            <div class="col-sm-4 offset-md-1 py-4">
              <h4 class="text-white">Contact</h4>
              <ul class="list-unstyled">
                <li><a href="#" class="text-white">Follow on Twitter</a></li>
                <li><a href="#" class="text-white">Like on Facebook</a></li>
                <li><a href="#" class="text-white">Email me</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
          <a href="/site" class="navbar-brand d-flex align-items-center">
            <strong>Services Delivery Management System</strong>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>
    </header>

    <main role="main">

       @yield('content')

    </main>

    <footer class="text-muted">
      <div class="container">
        <p class="float-right">
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
