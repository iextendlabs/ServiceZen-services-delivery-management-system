@extends('site.layout.app')
@section('adsense_head')
    @if (!empty($ads['head']))
        {!! $ads['head'] !!}
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
        @if (!empty($ads['top']))
            {!! $ads['top'] !!}
        @endif
        {{-- <section class="jumbotron text-center">
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
        </div> --}}
        <hr>
        <div class="row">
            @if (isset($category) && count($category->services) > 0)
                <div class="col-md-12">
                    <div class="flex items-center justify-center my-6">
                        <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-purple-600 tracking-tight leading-none">
                            {{ $category->title }}
                        </h2>
                    </div>
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
                    <div class="col-md-4">
                        <h2 class="font-weight-bold m-3 text-center" style="font-family: 'Titillium Web', sans-serif;">
                            <a style="text-decoration: none;" href="{{ route('category.show',$single_category->slug) }}">{{ $single_category->title }}</a></h2>
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
                    <div class="hero-area hero-bg mt-4" style="padding: 2.5rem 0; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; width: 100vw; box-sizing: border-box;">
                        <h2 class="text-2xl font-bold text-center text-gray-800 mb-3">What Our Customers Say</h2>
                        <p class="text-center text-gray-600 mb-12">Real reviews from real customers</p>

                        <div id="reviewsCarousel" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                @foreach ($reviews->chunk($reviews_chunk ?? 3) as $key => $chunk)
                                    <li data-target="#reviewsCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                                @endforeach
                            </ol>

                            <div class="carousel-inner">
                                @foreach ($reviews->chunk($reviews_chunk ?? 3) as $chunk)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        <div class="row px-16">
                                            @foreach ($chunk as $review)
                                                <div class="col-md-4 mb-4">
                                                    <div class="card mb-4" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.07); overflow: hidden; height: 100%;">
                                                        <div class="card-body" style="padding: 28px 24px; display:flex; flex-direction:column; gap:12px; box-sizing:border-box; height:200px;">
                                                            @php
                                                                $name = $review->user_name ?? 'User';
                                                                $words = preg_split('/\s+/', trim($name));
                                                                $initials = '';
                                                                foreach ($words as $word) {
                                                                    if (!empty($word)) {
                                                                        $initials .= strtoupper(mb_substr($word, 0, 1));
                                                                    }
                                                                }
                                                                $initials = mb_substr($initials, 0, 2);
                                                                $colors = ['#f472b6', '#7c3aed', '#06b6d4', '#f97316', '#10b981', '#ec4899'];
                                                                $colorIndex = (ord($initials[0] ?? 'U')) % count($colors);
                                                                $bgColor = $colors[$colorIndex];
                                                            @endphp

                                                            <div style="display:flex; align-items:flex-start; gap:14px;">
                                                                <div style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; border-radius:8px; background-color: {{ $bgColor }}; color:#fff; font-weight:700; font-size:16px; flex-shrink:0;">
                                                                    {{ $initials }}
                                                                </div>
                                                                <div style="flex:1;">
                                                                    <h5 style="margin:0 0 4px 0; font-size:15px; font-weight:600; color:#1f2937;">{{ $name }}</h5>
                                                                    @if ($review->service)
                                                                        <p style="margin:0; font-size:13px; color:#6b7280;">{{ $review->service->name }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div style="display:flex; gap:3px; margin-bottom:14px;">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <= $review->rating)
                                                                        <span style="color:#fbbf24; font-size:16px;">★</span>
                                                                    @else
                                                                        <span style="color:#d1d5db; font-size:16px;">★</span>
                                                                    @endif
                                                                @endfor
                                                            </div>

                                                            <p style="margin:0; font-size:14px; color:#4b5563; line-height:1.6; font-style:italic;">
                                                                "{{ substr($review->content, 0, $review_char_limit) }}{{ strlen($review->content) > $review_char_limit ? '...' : '' }}"
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <a href="#reviewsCarousel" data-slide="prev" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/20 flex items-center justify-center text-white shadow-lg transition-all duration-300 hover:bg-purple-300 hover:scale-110 ml-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>

                            <a href="#reviewsCarousel" data-slide="next" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/20 flex items-center justify-center text-white shadow-lg transition-all duration-300 hover:bg-purple-300 hover:scale-110 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
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
                nav: true, // Show navigation arrows
                dots: false, // Hide dots
                autoplay: true,
                autoplayTimeout: 10000,
                items: 4,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    992: {
                        items: 3
                    },
                    1200: {
                        items: 4
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
                nav: true, // Show navigation arrows
                dots: false, // Hide dots
                autoplay: true,
                autoplayTimeout: 10000,
                items: 4,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    992: {
                        items: 3
                    },
                    1200: {
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
