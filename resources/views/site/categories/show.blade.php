@extends('site.layout.app')
@section('adsense_head')
    @if (!empty($ads['category']['head']))
        {!! $ads['category']['head'] !!}
    @endif
@endsection
@section('content')
    <div class="container">
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
        </div>
        @if (!empty($ads['category']['top']))
            {!! $ads['category']['top'] !!}
        @endif
        <section class="jumbotron text-center">
            <div class="container">
                @if (isset($category))
                    <h1 class="jumbotron-heading">{{ $category->title }}</h1>
                    <p class="lead text-muted">{{ $category->description }}</p>
                @endif
            </div>
        </section>
        <div class="row" id="categories">
            @foreach ($all_categories as $single_category)
                @include('site.categories.category_card', ['category' => $single_category])
            @endforeach
        </div>
        <hr>
        <div class="row">
            @if (isset($category) && count($category->services) > 0)
                <div class="col-md-12">
                    <h2 class="font-weight-bold m-3 text-center" style="font-family: 'Titillium Web', sans-serif;">
                        {{ $category->title }}</h2>
                </div>
                @foreach ($category->services as $service)
                    @if ($service->status == 1)
                        <div class="col-md-4">
                            @include('site.services.card')
                        </div>
                    @endif
                @endforeach
                <hr>
            @endif
        </div>
        <div class="row">
            @foreach ($all_categories as $single_category)
                @if (count($single_category->services->where('status', 1)->take(10)) > 0)
                    <div class="col-md-12">
                        <h2 class="font-weight-bold m-3 text-center" style="font-family: 'Titillium Web', sans-serif;">
                            <a style="text-decoration: none;" href="{{ route('category.show',$single_category->slug) }}">{{ $single_category->title }}</a></h2>
                        <div class="owl-carousel owl-carousel-category-service">
                            @foreach ($single_category->services->where('status', 1)->take(10) as $service)
                                @if ($service->status == 1)
                                    <div class="item">
                                        @include('site.services.card')
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <hr>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="album py-5">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center">Customer Reviews</h2>
                    <div class="owl-carousel owl-carousel-review">
                        @foreach ($reviews as $review)
                            <div class="item">
                                <div class="card mb-4 text-center">
                                    <div class="card-body" style="height: 215px !important">
                                        <h5 class="card-title">{{ $review->user_name }}</h5>
                                        <p class="card-text">
                                            {{ substr($review->content, 0, $review_char_limit) }}...</p>
                                        <p class="card-text">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $review->rating)
                                                    <span class="text-warning">&#9733;</span>
                                                @else
                                                    <span class="text-muted">&#9734;</span>
                                                @endif
                                            @endfor
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-12 text-center mb-2">
                    <a class="btn btn-primary" href="{{ route('siteReviews.index') }}">All Reviews</a>
                </div>
                @if (auth()->check())
                    <div class="col-md-12 text-center">
                        <button class="btn btn-primary" id="review">Write a Review</button>
                    </div>
                    <div class="col-md-6" id="review-form" style="display: none;">
                        @include('site.reviews.create')
                    </div>
                @endif
            </div>
            @if (!empty($ads['category']['bottom']))
                {!! $ads['category']['bottom'] !!}
            @endif
        </div>
    </div>
    <script>
        $(document).on('click', '#review', function() {
            $('#review-form').show();
            $('html, body').animate({
                scrollTop: $('#review-form').offset().top
            }, 1000);
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".owl-carousel-category-service").owlCarousel({
                loop: false,
                margin: 15,
                nav: true, // Hide navigation arrows
                dots: false, // Show dots only
                autoplay: true,
                autoplayTimeout: 10000,
                items: 3,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 3
                    },
                    1000: {
                        items: 3
                    }
                },
                navText: [
                    '<i class="fa fa-chevron-left"></i>', // Left navigation arrow
                    '<i class="fa fa-chevron-right"></i>' // Right navigation arrow
                ]
            });

            $(".owl-carousel-review").owlCarousel({
                loop: false,
                margin: 15,
                nav: true, // Hide navigation arrows
                dots: false, // Show dots only
                autoplay: true,
                autoplayTimeout: 10000,
                items: 4,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 4
                    },
                    1000: {
                        items: 4
                    }
                },
                navText: [
                    '<i class="fa fa-chevron-left"></i>', // Left navigation arrow
                    '<i class="fa fa-chevron-right"></i>' // Right navigation arrow
                ]
            });
        });
    </script>
@endsection
