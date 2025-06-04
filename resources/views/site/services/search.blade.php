@extends('site.layout.app')
@section('content')
    <style>
        .input-group {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 50px;
            overflow: hidden;
        }

        #search_product {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            padding: 0.75rem 1.25rem;
        }

        #search_product:focus {
            border-color: transparent;
            box-shadow: none;
        }

        #search-button {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            background: linear-gradient(45deg, #1f91a5, #0c5460);
            border: none;
            color: white;
            padding: 0.75rem 1.25rem;
            transition: background 0.3s ease;
        }

        #search-button:hover {
            background: linear-gradient(45deg, #1f91a5, #0c5460);
        }

        .fa-search {
            margin-right: 5px;
        }
    </style>
    <div class="container">
        <div class="col-md-6 col-sm-12 offset-md-3 mt-5">
            <form action="{{ route('search') }}" method="GET" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="search" id="search_product" class="form-control border-right-0" placeholder="Search Services"
                        aria-label="Search Product" name="search_service" value="{{ request('search_service') }}"
                        aria-describedby="search-button">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary" id="search-button">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="text-center mt-3">
            @if (Session::has('error'))
                <span class="alert alert-danger" role="alert">
                    <strong>{{ Session::get('error') }}</strong>
                </span>
            @endif
            @if (Session::has('success'))
                <span class="alert alert-success" role="alert">
                    <strong>{{ Session::get('success') }}</strong>
                </span>
            @endif
            @if (Session::has('cart-success'))
                <div class="alert alert-success" role="alert">
                    <span>You have added service to your <a href="cart">shopping cart!</a></span><br>
                    <span><a href="bookingStep">Go and Book Now!</a></span><br>
                    <span>To add more service<a href="/"> Continue</a></span>
                </div>
            @endif
            @if (isset($search) && count($services) == 0)
                <span class="alert alert-danger text-center w-75 " role="alert" style="padding:10px 200px">
                    <strong>Service not found</strong>
                </span>
            @endif
        </div>
        <section class="jumbotron text-center">
            <div class="container">
                @if (request('search_service'))
                    <p class="lead text-muted"> Search Service:
                        <span @class(['p-4', 'font-bold', 'text-dark' => true])>{{ request('search_service') }}</span>
                    </p>
                @else
                    <h1 class="jumbotron-heading" style="font-family: 'Titillium Web', sans-serif;">Best In the Town
                        Services</h1>
                    <p class="lead text-muted">Get Your Desired service at Your Door, easy to schedule and
                        just few clicks away.</p>
                @endif
            </div>
        </section>
        <hr>
        <div class="album py-5">
            @if (isset($services))
                <div class="row">
                    @foreach ($services as $service)
                        <div class="col-md-4">
                            @include('site.services.card')
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
