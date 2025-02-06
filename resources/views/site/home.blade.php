@extends('site.layout.app')
@section('content')
    @php
        if ($app_flag === true) {
            $reviews_chunk = 1;
            $staffs_chunk = 1;
        } else {
            $reviews_chunk = 3;
            $staffs_chunk = 4;
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
        <div class="col-md-6 col-sm-12 offset-md-3 mt-5">
            <form action="{{ route('storeHome') }}" method="GET" enctype="multipart/form-data">
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
        @if ($slider_images->value && !isset($category))
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
                                    @if ($type === 'category' && !empty($id)) href="?id={{ $id }}"
                      @elseif($type === 'service' && !empty($id))
                          href="/serviceDetail/{{ $id }}"
                      @elseif($type === 'customLink' && !empty($id))
                          href="{{ $id }}" @endif>
                                    <img src="/slider-images/{{ $filename }}" alt="Slide {{ $loop->iteration }}"
                                        class="d-block w-100">
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
                @if (request('search_service'))
                    <p class="lead text-muted"> Search Service:
                        <span @class(['p-4', 'font-bold', 'text-dark' => true])>{{ request('search_service') }}</span>
                    </p>
                @elseif(isset($category))
                    <h1 class="jumbotron-heading">{{ $category->title }}</h1>
                    <p class="lead text-muted">{{ $category->description }}</p>
                @else
                    <h1 class="jumbotron-heading" style="font-family: 'Titillium Web', sans-serif;">Best In the Town Services</h1>
                    <p class="lead text-muted">Get Your Desired service at Your Door, easy to schedule and
                        just few clicks away.</p>
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
                    <div class="owl-carousel">
                        @foreach ($category->services as $service)
                            <div class="item">
                                <div class="card mb-4 box-shadow service-box">
                                    <a href="/serviceDetail/{{ $service->id }}">
                                        <p class="card-text service-box-title text-center"><b>{{ $service->name }}</b></p>
                                        <img class="card-img-top" src="./service-images/{{ $service->image }}"
                                            alt="Card image cap">
                                    </a>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted service-box-price">
                                                @if (isset($service->discount))
                                                    <s>
                                                @endif
                                                @currency($service->price, false, true)
                                                @if (isset($service->discount))
                                                    </s>
                                                @endif
                                                @if (isset($service->discount))
                                                    <b class="discount"> @currency($service->discount, false, true)</b>
                                                @endif
                                            </small>
                                            @if ($service->duration)
                                                <small class="text-muted service-box-time"><i class="fa fa-clock">
                                                    </i>{{ $service->duration }}</small>
                                            @endif
                                        </div>

                                        @if (count($service->serviceOption) > 0)
                                            <a style="margin-top: 1em; color:#fff"
                                                href="/serviceDetail/{{ $service->id }}" type="button"
                                                class="btn btn-block btn-primary">Book Now</a>
                                        @else
                                            <button style="margin-top: 1em;"
                                                onclick="openBookingPopup('{{ $service->id }}')" type="button"
                                                class="btn btn-block btn-primary"> Book Now</button>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <hr>
                </div>
            @endif
        </div>
        <div class="row">
            @foreach ($all_categories as $single_category)
                @if (count($single_category->services) > 0)
                    <div class="col-md-12">
                        <h2 class="font-weight-bold m-3 text-center" style="font-family: 'Titillium Web', sans-serif;">
                            {{ $single_category->title }}</h2>
                        <div class="owl-carousel">
                            @foreach ($single_category->services as $service)
                                <div class="item">
                                    <div class="card mb-4 box-shadow service-box">
                                        <a href="/serviceDetail/{{ $service->id }}">
                                            <p class="card-text service-box-title text-center"><b>{{ $service->name }}</b>
                                            </p>
                                            <img class="card-img-top" src="./service-images/{{ $service->image }}"
                                                alt="Card image cap">
                                        </a>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted service-box-price">
                                                    @if (isset($service->discount))
                                                        <s>
                                                    @endif
                                                    @currency($service->price, false, true)
                                                    @if (isset($service->discount))
                                                        </s>
                                                    @endif
                                                    @if (isset($service->discount))
                                                        <b class="discount"> @currency($service->discount, false, true)</b>
                                                    @endif
                                                </small>
                                                @if ($service->duration)
                                                    <small class="text-muted service-box-time"><i class="fa fa-clock">
                                                        </i>{{ $service->duration }}</small>
                                                @endif
                                            </div>

                                            @if (count($service->serviceOption) > 0)
                                                <a style="margin-top: 1em; color:#fff"
                                                    href="/serviceDetail/{{ $service->id }}" type="button"
                                                    class="btn btn-block btn-primary">Book Now</a>
                                            @else
                                                <button style="margin-top: 1em;"
                                                    onclick="openBookingPopup('{{ $service->id }}')" type="button"
                                                    class="btn btn-block btn-primary"> Book Now</button>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <hr>
                    </div>
                @endif
            @endforeach
        </div>



        <div class="album py-5">
            @if (isset($services))
                <div class="row">
                    <div class="owl-carousel">
                        @foreach ($services as $service)
                            <div class="item">
                                <div class="card mb-4 box-shadow service-box">
                                    <a href="/serviceDetail/{{ $service->id }}">
                                        <p class="card-text service-box-title text-center"><b>{{ $service->name }}</b></p>
                                        <img class="card-img-top" src="./service-images/{{ $service->image }}"
                                            alt="Card image cap">
                                    </a>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted service-box-price">
                                                @if (isset($service->discount))
                                                    <s>
                                                @endif
                                                @currency($service->price, false, true)
                                                @if (isset($service->discount))
                                                    </s>
                                                @endif
                                                @if (isset($service->discount))
                                                    <b class="discount"> @currency($service->discount, false, true)</b>
                                                @endif
                                            </small>
                                            @if ($service->duration)
                                                <small class="text-muted service-box-time"><i class="fa fa-clock">
                                                    </i>{{ $service->duration }}</small>
                                            @endif
                                        </div>

                                        @if (count($service->serviceOption) > 0)
                                            <a style="margin-top: 1em; color:#fff"
                                                href="/serviceDetail/{{ $service->id }}" type="button"
                                                class="btn btn-block btn-primary">Book Now</a>
                                        @else
                                            <button style="margin-top: 1em;"
                                                onclick="openBookingPopup('{{ $service->id }}')" type="button"
                                                class="btn btn-block btn-primary"> Book Now</button>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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
                <div class="col-md-12">
                    <h2 class="text-center">Our Team</h2>
                    <div id="staffCarousel" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach ($staffs->chunk($staffs_chunk) as $key => $chunk)
                                <li data-target="#staffCarousel" data-slide-to="{{ $key }}"
                                    class="{{ $loop->first ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach ($staffs->chunk($staffs_chunk) as $chunk)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="row">
                                        @foreach ($chunk as $staff)
                                            <div class="col-md-3">
                                                <div class="card mb-3">
                                                    <div class="col-md-12 text-center">
                                                        <div class="d-flex justify-content-center align-items-center"
                                                            style="min-height: 230px;">
                                                            <img src="./staff-images/{{ $staff->staff->image }}"
                                                                class="card-img-top img-fluid rounded-circle"
                                                                alt="{{ $staff->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="card-body text-center"
                                                        style="height: 305px; align-content: center;">
                                                        <h6 class="card-title" style="height: 40px; overflow: hidden;">
                                                            {{ $staff->name }}</h6>
                                                        <h6 class="card-title" style="height: 40px; overflow: hidden;">
                                                            {{ $staff->staff->sub_title }}</h6>
                                                        <p class="card-title" style="height: 25px; overflow: hidden;">
                                                            Extra Charges:<b>@currency($staff->staff->charges, false)</b></p>
                                                        @if ($staff->staff->location)
                                                            <p class="card-title" style="height: 50px; overflow: hidden;">
                                                                {{ $staff->staff->location }}</p>
                                                        @endif
                                                        <a href="{{ route('staffProfile.show', $staff->id) }}"
                                                            class="btn btn-block btn-primary">View</a>
                                                        @php
                                                            $rating = $staff->averageRating();
                                                            $fullStars = floor($rating);
                                                            $halfStar = $rating - $fullStars >= 0.5;
                                                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                                        @endphp

                                                        @for ($i = 0; $i < $fullStars; $i++)
                                                            <i class="fas fa-star text-warning fa-xs"></i>
                                                        @endfor

                                                        @if ($halfStar)
                                                            <i class="fas fa-star-half-alt text-warning fa-xs"></i>
                                                        @endif

                                                        @for ($i = 0; $i < $emptyStars; $i++)
                                                            <i class="far fa-star text-muted fa-xs"></i>
                                                        @endfor
                                                        ({{ count($staff->reviews) }} Reviews)
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#staffCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#staffCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <a href="{{ route('staffProfile.index') }}" type="button" class="btn btn-primary">Our Team</a>
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
            $(".owl-carousel").owlCarousel({
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
        });
    </script>
@endsection
