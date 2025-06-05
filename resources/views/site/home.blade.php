@extends('site.layout.app')
@section('adsense_head')
    @if (!empty($ads['head']))
        {!! $ads['head'] !!}
    @endif
@endsection
@section('content')
    @php
        if ($app_flag === true) {
            $reviews_chunk = 1;
        } else {
            $reviews_chunk = 3;
        }
    @endphp
    <style>
        #staffCarousel img {
            height: 200px !important;
            width: 200px;
        }

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
        @if (!empty($ads['top']))
            {!! $ads['top'] !!}
        @endif
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
        </div>
        @if ($slider_images->value)
            <div class="row">
                <div id="imageSlider" class="carousel slide mt-3" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach (explode(',', $slider_images->value) as $index => $imagePath)
                            <li data-target="#imageSlider" data-slide-to="{{ $index }}"
                                class="@if ($index === 0) active @endif"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach (explode(',', $slider_images->value) as $index => $imagePath)
                            @php
                                [$type, $id, $filename] = explode('_', $imagePath);
                            @endphp
                            <div class="carousel-item @if ($loop->first) active @endif">
                                <a
                                    @if ($type === 'category' && !empty($id)) href="{{ route('category.show', $id) }}"
                      @elseif($type === 'service' && !empty($id))
                          href="/service/{{ $id }}"
                      @elseif($type === 'customLink' && !empty($id))
                          href="{{ $id }}" @endif>
                                    @php
                                        $imagePath = 'slider-images/' . $filename;
                                        $altText = $filename_alt ?? 'Lipslay Slider Image';
                                        $width = 1140;
                                        $height = 500;
                                    @endphp

                                    <img class="d-block w-100"
                                        src="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp"
                                        srcset="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp 1x,
                                 {{ url('img/' . $imagePath) }}?w={{ $width * 2 }}&h={{ $height * 2 }}&q=80&f=webp 2x"
                                        alt="{{ $altText }}" loading="lazy" decoding="async">

                                    {{-- <img src="{{ url('img/slider-images/' . $filename) }}"
                                        src="{{ asset('slider-images/' . $filename) }}" alt="Slide {{ $loop->iteration }}"
                                        class="d-block w-100"> --}}
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#imageSlider" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#imageSlider" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        @endif
        <section class="jumbotron text-center">
            <div class="container">
                <h1 class="jumbotron-heading" style="font-family: 'Titillium Web', sans-serif;">Best In the Town
                    Services</h1>
                <p class="lead text-muted">Get Your Desired service at Your Door, easy to schedule and
                    just few clicks away.</p>
            </div>
        </section>
        <div class="row" id="categories">
            @foreach ($all_categories as $single_category)
                @include('site.categories.category_card', ['category' => $single_category])
            @endforeach
        </div>
        @if (!empty($ads['center']))
            {!! $ads['center'] !!}
        @endif
        <hr>
        <div class="row">
            @foreach ($all_categories as $single_category)
                @if (count($single_category->services->where('status', 1)->take(10)) > 0)
                    <div class="col-md-12">
                        <h2 class="font-weight-bold m-3 text-center" style="font-family: 'Titillium Web', sans-serif;">
                            <a style="text-decoration: none;"
                                href="{{ route('category.show', $single_category->slug) }}">{{ $single_category->title }}</a>
                        </h2>
                        <div class="owl-carousel owl-carousel-category-service">
                            @foreach ($single_category->services->where('status', 1)->take(10) as $service)
                                <div class="item">
                                    @include('site.services.card')
                                </div>
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
                    <div id="reviewsCarousel" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach ($reviews->chunk($reviews_chunk) as $key => $chunk)
                                <li data-target="#reviewsCarousel" data-slide-to="{{ $key }}"
                                    class="{{ $loop->first ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach ($reviews->chunk($reviews_chunk) as $chunk)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="row">
                                        @foreach ($chunk as $review)
                                            <div class="col-md-4">
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
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#reviewsCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#reviewsCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
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
            <div class="row pt-4">
                <div class="col-md-12">
                    <h2 class="text-center">Our Members</h2>
                    <div class="owl-carousel owl-carousel-staff">
                        @foreach ($staffs as $staff)
                            <div class="item">
                                @include('site.staff.card')
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <a href="{{ route('staffProfile.index') }}" type="button" class="btn btn-primary">Our Members</a>
                </div>
            </div>
            @if (count($FAQs))
                <div class="row">
                    <div class="col-12">
                        <h1 id="faqs">Frequently Asked Questions</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-3" id="accordion">
                        @foreach ($FAQs as $FAQ)
                            <div class="card">
                                <div class="card-header" id="heading{{ $FAQ->id }}">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse"
                                            data-target="#collapse{{ $FAQ->id }}" aria-expanded="true"
                                            aria-controls="collapse{{ $FAQ->id }}">
                                            <div style="white-space: normal;">{{ $FAQ->question }}</div>
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse{{ $FAQ->id }}" class="collapse"
                                    aria-labelledby="heading{{ $FAQ->id }}" data-parent="#accordion">
                                    <div class="card-body">
                                        {{ $FAQ->answer }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-12 text-center mt-3">
                        <a href="{{ route('siteFAQs.index') }}" class="btn btn-primary">More..</a>
                    </div>
                </div>
            @endif
            @if (!empty($ads['bottom']))
                {!! $ads['bottom'] !!}
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

            $(".owl-carousel-staff").owlCarousel({
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
