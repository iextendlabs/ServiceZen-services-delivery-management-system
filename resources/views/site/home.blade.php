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
    <style>
        /* Scoped to hero-area to avoid overriding global .card styles */
        .hero-area {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            padding: 2rem;
            min-height: 360px;
        }

        /* Hero background */
        .hero-area.hero-bg {
            background: linear-gradient(135deg, #fbf1ff 0%, #fff6fb 50%, #f6f0ff 100%);
        }

        /* Keyframes: straight up (no sideways drift) */
        @keyframes floatUp {
            0% {
                transform: translateY(100vh) scale(0.92);
                opacity: 0;
            }
            10% {
                opacity: 0.95;
            }
            90% {
                opacity: 0.6;
            }
            100% {
                transform: translateY(-120vh) scale(1.02);
                opacity: 0;
            }
        }

        /* Card base style similar to provided screenshot (scoped) */
        .hero-area .card-floating {
            position: absolute;
            bottom: -18%;
            width: 96px;
            height: 96px;
            border-radius: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #ffffff99;
            border: 1px solid #8b5cf61e;
            box-shadow: 0 8px 30px #6366f10f;
            backdrop-filter: blur(6px);
            animation-name: floatUp;
            animation-timing-function: linear;
            animation-fill-mode: forwards;
            pointer-events: none;
        }

        /* Icon inside card */
        .hero-area .card-floating svg {
            width: 34px;
            height: 34px;
            display: block;
            flex: 0 0 auto;
            filter: drop-shadow(0 6px 12px #0000000f);
        }

        /* Ensure sprite strokes inherit current color */
        .hero-area .card-floating svg * {
            stroke: currentColor;
        }

        /* Label */
        .hero-area .card-floating .label {
            font-size: 12px;
            font-weight: 600;
            color: #6b21a8;
            transform: translateY(2px);
            letter-spacing: 0.2px;
        }

        /* glow uses currentColor for tint */
        .hero-area .card-floating.glow {
            box-shadow:
                0 8px 30px #0000000f,
                0 0 18px calc(0.25rem) currentColor;
        }

        /* responsive adjust */
        @media (max-width: 640px) {
            .hero-area .card-floating { width: 76px; height: 76px; border-radius: 14px; }
            .hero-area .card-floating svg { width: 28px; height: 28px; }
            .hero-area .card-floating .label { font-size: 11px; }
        }

        /* small helper to keep the main content above decorations */
        .hero-area .hero-main { position: relative; z-index: 2; }
        .hero-area .decor { z-index: 1; }
    </style>
        @if (!empty($ads['top']))
            {!! $ads['top'] !!}
        @endif
        <div class="col-md-12 col-sm-12">
            <!-- Inline SVG sprite so <use href="#icon-..."> can reference symbols in the same document -->
            <svg width="0" height="0" class="hidden" aria-hidden="true" style="position: absolute; width: 0; height: 0; overflow: hidden;">
                <defs>
                  <!-- Haircut / Scissors -->
                  <symbol id="icon-scissors" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.6 7.6L14.4 12.4" />
                    <path d="M14.4 7.6L9.6 12.4" />
                    <circle cx="5" cy="19" r="2" />
                    <circle cx="19" cy="19" r="2" />
                    <path d="M5 17L19 5" />
                  </symbol>

                  <!-- Hair Dryer -->
                  <symbol id="icon-hairdryer" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 10h14l-2 4h-6l-3 5H4l1-3H2z" />
                    <path d="M16 8v2" />
                    <path d="M19 8v2" />
                  </symbol>

                  <!-- Brush -->
                  <symbol id="icon-brush" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21s2-2 4-2 3-1 3-1 0-2-1-3-4-1-4-1-1 2-2 4 0 3 0 3z" />
                    <path d="M14 3l7 7-7 7-7-7 7-7z" />
                  </symbol>

                  <!-- Perfume / Bottle -->
                  <symbol id="icon-perfume" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="6" y="7" width="12" height="12" rx="2" />
                    <path d="M12 7V4h4" />
                  </symbol>

                  <!-- Lipstick -->
                  <symbol id="icon-lipstick" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="9" y="8" width="6" height="10" rx="1" />
                    <path d="M9 8V4a3 3 0 016 0v4" />
                  </symbol>

                  <!-- Facial / Spa (mask-like icon) -->
                  <symbol id="icon-facial" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="8" />
                    <path d="M9 10h.01M15 10h.01M8 15s1.5-2.5 4-2.5 4 2.5 4 2.5" />
                  </symbol>

                  <!-- Nail polish -->
                  <symbol id="icon-nail" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 21l7-7" />
                    <rect x="12" y="3" width="4" height="7" rx="1" />
                    <path d="M11 13l6 6" />
                  </symbol>

                  <!-- Spray bottle -->
                  <symbol id="icon-spray" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 22h8" />
                    <path d="M7 8c0-1.5 1-3 3-3h4c2 0 3 1.6 3 3v9a2 2 0 01-2 2H9a2 2 0 01-2-2z" />
                    <path d="M12 4v2" />
                  </symbol>
                </defs>
            </svg>
            <section class="hero-area hero-bg text-center">
                <!-- Decorative floating cards (position and delays can be adjusted) -->
                <div class="decor">
                    <div class="card-floating glow" style="left: 6%; animation-duration: 14s; animation-delay: 0s; color: #f472b6;">
                        <svg aria-hidden="true" viewBox="0 0 24 24" role="img">
                            <use href="#icon-facial"></use>
                        </svg>
                        <div class="label">Verified</div>
                    </div>

                    <div class="card-floating" style="left: 28%; animation-duration: 16s; animation-delay: 2s; color: #7c3aed;">
                        <svg aria-hidden="true" viewBox="0 0 24 24" role="img">
                            <use href="#icon-scissors"></use>
                        </svg>
                        <div class="label">Book</div>
                    </div>

                    <div class="card-floating glow" style="right: 10%; animation-duration: 18s; animation-delay: 1s; color: #06b6d4;">
                        <svg aria-hidden="true" viewBox="0 0 24 24" role="img">
                            <use href="#icon-spray"></use>
                        </svg>
                        <div class="label">Secure</div>
                    </div>

                    <div class="card-floating" style="right: 30%; animation-duration: 15s; animation-delay: 3s; color: #f97316;">
                        <svg aria-hidden="true" viewBox="0 0 24 24" role="img">
                            <use href="#icon-hairdryer"></use>
                        </svg>
                        <div class="label">Fast</div>
                    </div>
                </div>

                <!-- Main content kept above decorations -->
                <form action="{{ route('search') }}" method="GET" enctype="multipart/form-data" class="hero-main">
                    <main class="relative z-10 text-center mx-auto">
                        <div class="text-4xl sm:text-5xl md:text-5xl font-extrabold text-black mb-4">
                            Professional Beauty Services at Your Home
                        </div>
                        <p class="text-lg sm:text-xl text-dark mb-8">
                            Book trusted salon and beauty experts for haircare, makeup, skincare, and more.
                        </p>

                        <div class="d-flex flex-column flex-sm-row mx-auto mb-3" style="max-width: 620px;">
                            <input
                                type="search"
                                id="search_product"
                                name="search_service"
                                value="{{ request('search_service') }}"
                                placeholder="Search services or professionals..."
                                class="form-control px-3 py-3 rounded-lg border border-purple-200 bg-white text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-purple-300 w-100"
                                aria-label="Search services or professionals"
                                aria-describedby="search-button"
                            />
                            <button
                                type="submit"
                                id="search-button"
                                class="btn btn-purple-900 px-3 py-3 rounded-lg border-purple-200  focus:ring-2 focus:ring-purple-300"
                                style="background: linear-gradient(45deg, #6b21a8, #6b21a8); border: none;"
                            >
                                <i class="fa fa-search" aria-hidden="true"></i> Search
                            </button>
                        </div>

                        <div class="d-flex justify-content-center gap-3 text-muted small">
                            <div class="w-3 h-3 mt-1 bg-purple-600 rounded-full"></div>
                            <span class="d-flex align-items-center"><span class="badge " style="background:#fbcfe8;border-radius:50%;width:8px;height:8px;"></span>Verified Professionals</span>
                            <div class="w-3 h-3 mt-1 bg-purple-600 rounded-full"></div>
                            <span class="d-flex align-items-center"><span class="badge " style="background:#fbcfe8;border-radius:50%;width:8px;height:8px;"></span>Same-Day Booking</span>
                            <div class="w-3 h-3 mt-1 bg-purple-600 rounded-full"></div>
                            <span class="d-flex align-items-center"><span class="badge " style="background:#fbcfe8;border-radius:50%;width:8px;height:8px;"></span>Secure Payments</span>
                        </div>
                    </main>
                </form>
            </section>
        </div>
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
        {{-- @if ($slider_images->value)
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
        @endif --}}
    <div class="container mt-4">    
        <section class="text-center mt-5">
            <div class="container">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Browse Services</h2>
            </div>
        </section>
        <div class="row mb-5" id="categories">
            @foreach ($all_categories as $single_category)
                @include('site.categories.category_card', ['category' => $single_category])
            @endforeach
        </div>
        @if (!empty($ads['center']))
            {!! $ads['center'] !!}
        @endif
        
            <div class="text-center mb-4">
                <a href="{{ route('categories.index') }}" class="btn btn-primary " style="background: linear-gradient(45deg, #6b21a8, #6b21a8); border: none;">View All Categories Services</a>
            </div>
        <hr>
        
        @if (!empty($featured_services) && count($featured_services) > 0)
        <section class="hero-area hero-bg mt-5" style="padding: 2.5rem 0; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; width: 100vw; box-sizing: border-box;">
            <div class="container">
            <section class="text-center">
                <div class="container">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Featured Services</h2>
                <p class="text-center text-gray-600 mb-8">Our most popular and highly-rated services</p>
                </div>
            </section>
            <div class="row mb-5">
                @foreach ($featured_services as $service)
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm" style="border-radius:10px; overflow:hidden; border:1px solid #eef2f7; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)'; this.style.transform='translateY(-4px)';" onmouseout="this.style.boxShadow=''; this.style.transform='translateY(0)';">
                        <a href="/service/{{ $service->slug }}" style="text-decoration:none; color:inherit; display:block; height:100%;">
                            <div style="position:relative; height:200px; background:#fff; overflow:hidden;">
                            @if ($service->image)
                                <img
                                src="{{ url('img/service-images/' . $service->image) }}?w=800&h=600&q=80&f=webp"
                                srcset="{{ url('img/service-images/' . $service->image) }}?w=400&h=300&q=80&f=webp 1x,
                                    {{ url('img/service-images/' . $service->image) }}?w=800&h=600&q=80&f=webp 2x"
                                alt="{{ $service->name }}"
                                style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.4s ease;"
                                loading="lazy" decoding="async"
                                onmouseover="this.style.transform='scale(1.08)';"
                                onmouseout="this.style.transform='scale(1)';"
                                />
                            @else
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f8fafc;">
                                <i class="fa fa-image" style="font-size:40px; color:#cbd5e1;"></i>
                                </div>
                            @endif

                            </div>

                            <div class="card-body d-flex flex-column" style="padding:16px;">
                            <h5 style="margin:0 0 6px 0; font-size:16px; font-weight:700; color:#0f172a; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $service->name }}
                            </h5>

                            @php
                                // provider name fallback
                                $providerName = $service->provider->name ?? $service->shop_name ?? $service->vendor_name ?? 'Provider';
                                // rating and counts - try common properties, fallback to 0
                                $rating = method_exists($service, 'averageRating') ? $service->averageRating() : ($service->rating ?? 0);
                                $rating = $rating ? round($rating, 1) : 0;
                                $ratingCount = $service->reviews_count ?? $service->ratings_count ?? ($service->review_count ?? 0);
                                // lowest price
                                $lowestPrice = null;
                                if (!empty($service->serviceOption)) {
                                foreach ($service->serviceOption as $option) {
                                    if (is_null($lowestPrice) || $option->option_price < $lowestPrice) {
                                    $lowestPrice = $option->option_price;
                                    }
                                }
                                }
                            @endphp

                            <div style="font-size:13px; color:#6b7280; margin-bottom:8px;">{{ $providerName }}</div>

                            <div class="d-flex align-items-center" style="gap:8px; margin-bottom:10px;">
                                <div style="display:flex; align-items:center; gap:2px;">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($rating))
                                    <span style="color:#fbbf24; font-size:14px;">★</span>
                                    @elseif ($i - 0.5 <= $rating)
                                    <span style="color:#fbbf24; font-size:14px;">☆</span>
                                    @else
                                    <span style="color:#e5e7eb; font-size:14px;">★</span>
                                    @endif
                                @endfor
                                </div>
                                <div style="font-size:13px; color:#374151; font-weight:600;">{{ $rating }}</div>
                                <div style="font-size:13px; color:#9ca3af;">({{ $ratingCount }})</div>
                            </div>

                            <div style="display:flex; gap:14px; color:#6b7280; font-size:13px; margin-top:auto;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                <i class="fa fa-map-marker" aria-hidden="true" style="color:#9ca3af;"></i>
                                <span>{{ $service->distance ?? '2.5 km away' }}</span>
                                </div>
                                <div style="display:flex; align-items:center; gap:6px;">
                                <i class="fa fa-clock-o" aria-hidden="true" style="color:#9ca3af;"></i>
                                <span>{{ $service->duration ?? '30 mins' }}</span>
                                </div>
                            </div>

                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
                                @if (!is_null($lowestPrice))
                                @php
                                    $priceDisplay = (floor($lowestPrice) == $lowestPrice) ? number_format($lowestPrice, 0) : number_format($lowestPrice, 2);
                                @endphp
                                <div style="color:#6b21a8; font-weight:800; font-size:18px;">
                                    ${{ $priceDisplay }}
                                </div>
                                @else
                                <div style="color:#6b21a8; font-weight:800; font-size:18px;">
                                    --
                                </div>
                                @endif

                                <div>
                                <a href="/service/{{ $service->slug }}" class="btn" style="background:#6b21a8; color:#fff; padding:8px 12px; border-radius:6px; font-weight:600; font-size:14px; text-decoration:none;">
                                    Book
                                </a>
                                </div>
                            </div>
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            </div>
        </section>
        <hr>
        @endif
        {{-- <div class="row">
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
        </div> --}}
    </div>
    <div class="container mt-4" style="linear-gradient(135deg, #fbf1ff 0%, #fff6fb 50%, #f6f0ff 100%)">
        <div class="album">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center mb-4">
                        <h3 class="text-2xl font-bold text-center text-gray-800 mb-4">Our Expert Team</h3>
                        <p class="text-center text-gray-600 mb-12">Meet our highly trained and certified professionals</p>
                    </div>
                    <div class="owl-carousel owl-carousel-staff">
                        @foreach ($staffs as $staff)
                            <div class="item">
                                @include('site.staff.card')
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>            
            <div class="row">
                <div class="hero-area hero-bg mt-5" style="padding: 2.5rem 0; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; width: 100vw; box-sizing: border-box;">
                    @if (count($FAQs))
                        <div style="text-align: center; margin: 50px 0 30px 0;">
                            <h1 id="faqs" class="text-2xl font-bold text-center text-gray-800 mb-4">Frequently Asked Questions</h1>
                            <p class="text-center text-gray-600 mb-12">Find answers to common questions about our services</p>
                        </div>
                        <div style="max-width: 900px; margin: 0 auto;">
                            <div class="faq-accordion" id="faqAccordion">
                                @foreach ($FAQs as $FAQ)
                                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 16px; overflow: hidden;">
                                        <button type="button" 
                                                class="faq-toggle-btn" 
                                                style="padding: 20px; background-color: #fff; cursor: pointer; display: flex; justify-content: space-between; align-items: center; width: 100%; border: none; font-family: inherit; transition: background-color 0.3s ease; outline: none;" 
                                                data-faq-id="{{ $FAQ->id }}"
                                                aria-expanded="false"
                                                aria-controls="collapse{{ $FAQ->id }}">
                                            <h5 style="margin: 0; font-size: 1.05rem; font-weight: 500; color: #1a365d; text-align: left;">{{ $FAQ->question }}</h5>
                                            <span class="faq-arrow" style="color: #718096; font-size: 1.5rem; min-width: 24px; text-align: right; margin-left: 12px; transition: transform 0.3s ease; flex-shrink: 0;">▼</span>
                                        </button>
                                        <div id="collapse{{ $FAQ->id }}" class="faq-content" style="display: none; max-height: 0; overflow: hidden; transition: max-height 0.3s ease;"
                                            aria-labelledby="heading{{ $FAQ->id }}">
                                            <div style="padding: 20px; background-color: #f7fafc; border-top: 1px solid #e2e8f0; color: #2d3748; line-height: 1.6;">
                                                {{ $FAQ->answer }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div style="text-align: center; margin-top: 40px;">
                                <a href="{{ route('siteFAQs.index') }}" style="display: inline-block; padding: 12px 32px; border: 2px solid #7c3aed; color: #7c3aed; text-decoration: none; border-radius: 6px; font-weight: 500; transition: all 0.3s ease;">See All FAQs</a>
                            </div>
                        </div>
                        <style>
                            .faq-toggle-btn:focus {
                                outline: 2px solid #7c3aed;
                                outline-offset: 2px;
                            }
                            
                            .faq-toggle-btn:focus-visible {
                                outline: 2px solid #7c3aed;
                                outline-offset: 2px;
                            }
                        </style>
                        <script>
                            (function() {
                                const faqToggles = document.querySelectorAll('.faq-toggle-btn');
                                
                                faqToggles.forEach(button => {
                                    button.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const faqId = this.getAttribute('data-faq-id');
                                        const content = document.getElementById('collapse' + faqId);
                                        const arrow = this.querySelector('.faq-arrow');
                                        const isExpanded = this.getAttribute('aria-expanded') === 'true';
                                        
                                        // Close all other open accordions
                                        faqToggles.forEach(otherBtn => {
                                            if (otherBtn !== this && otherBtn.getAttribute('aria-expanded') === 'true') {
                                                const otherId = otherBtn.getAttribute('data-faq-id');
                                                const otherContent = document.getElementById('collapse' + otherId);
                                                otherContent.style.display = 'none';
                                                otherContent.style.maxHeight = '0';
                                                otherBtn.setAttribute('aria-expanded', 'false');
                                                otherBtn.querySelector('.faq-arrow').style.transform = 'rotate(0deg)';
                                                otherBtn.style.backgroundColor = '#fff';
                                            }
                                        });
                                        
                                        // Toggle current accordion
                                        if (isExpanded) {
                                            content.style.display = 'none';
                                            content.style.maxHeight = '0';
                                            this.setAttribute('aria-expanded', 'false');
                                            arrow.style.transform = 'rotate(0deg)';
                                            this.style.backgroundColor = '#fff';
                                        } else {
                                            content.style.display = 'block';
                                            content.style.maxHeight = content.scrollHeight + 'px';
                                            this.setAttribute('aria-expanded', 'true');
                                            arrow.style.transform = 'rotate(180deg)';
                                            this.style.backgroundColor = '#f9fafb';
                                        }
                                    });
                                });
                            })();
                        </script>
                    @endif
                    @if (!empty($ads['bottom']))
                        {!! $ads['bottom'] !!}
                    @endif     
                </div>   
            </div>
            <div class="col-md-12 mt-4">
                <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">How It Works</h1>
                <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
                    Booking beauty services at home is easy with our platform. Follow these simple steps to get started also give review and check customer reviews.
                </p>
                <div class="flex flex-wrap justify-content-around items-center gap-7 my-6" role="list" aria-label="Quick links">
                    <a role="listitem" href="{{ route('siteReviews.index') }}"
                       class="group flex flex-col items-center gap-2 w-36 text-center no-underline focus:outline-none">
                        <span class="flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-pink-50 via-white to-indigo-50 border border-indigo-100 shadow-md text-2xl text-indigo-700 transform transition duration-150 hover:-translate-y-1 hover:shadow-lg" aria-hidden="true">
                            <i class="fa fa-comments" aria-hidden="true"></i>
                        </span>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm">Read Testimonials</div>
                            <div class="text-xs text-gray-500">Real customer reviews</div>
                        </div>
                    </a>

                    <a role="listitem" href="{{ route('staffProfile.index') }}"
                       class="group flex flex-col items-center gap-2 w-36 text-center no-underline focus:outline-none">
                        <span class="flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-cyan-50 via-white to-cyan-100 border border-cyan-100 shadow-md text-2xl text-cyan-500 transform transition duration-150 hover:-translate-y-1 hover:shadow-lg" aria-hidden="true">
                            <i class="fa fa-users"></i>
                        </span>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm">Our Members</div>
                            <div class="text-xs text-gray-500">Meet the experts</div>
                        </div>
                    </a>

                    <a role="listitem" href="{{ route('categories.index') }}"
                       class="group flex flex-col items-center gap-2 w-36 text-center no-underline focus:outline-none">
                        <span class="flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-orange-50 via-white to-orange-100 border border-orange-100 shadow-md text-2xl text-orange-500ctransform transition duration-150 hover:-translate-y-1 hover:shadow-lg" aria-hidden="true">
                            <i class="fa fa-th-large"></i>
                        </span>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm">Browse Services</div>
                            <div class="text-xs text-gray-500">Find what you need</div>
                        </div>
                    </a>

                    @if (auth()->check())
                        <button role="listitem" id="openReviewModal" type="button" aria-haspopup="dialog"
                                class="group flex flex-col items-center gap-2 w-36 bg-transparent border-0 p-0 cursor-pointer focus:outline-none">
                            <span class="flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-pink-50 to-purple-50 border border-pink-100 shadow-md text-2xl text-pink-600 transform transition duration-150 hover:-translate-y-1 hover:shadow-lg" aria-hidden="true">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800 text-sm">Write a Review</div>
                                <div class="text-xs text-gray-500">Share your experience</div>
                            </div>
                        </button>

                        <!-- Review Modal -->
                        <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content rounded-xl shadow-2xl border-0">
                                    <div class="modal-header bg-gradient-to-r from-purple-700 to-purple-600 p-6 border-0">
                                        <h5 id="reviewModalLabel" class="modal-title text-white font-bold text-xl">
                                            <i class="fa fa-star mr-2" aria-hidden="true"></i>Leave a Review
                                        </h5>
                                        <button type="button" class="close text-white text-2xl opacity-90" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body p-8 bg-white">
                                        @include('site.reviews.create')
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>                    
                <div class="hero-area hero-bg mt-4" style="padding: 2.5rem 0; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; width: 100vw; box-sizing: border-box;">
                    <h2 class="text-2xl font-bold text-center text-gray-800 mb-3">What Our Customers Say</h2>
                    <p class="text-center text-gray-600 mb-12">Real reviews from real customers</p>
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
                                    <div class="row px-16">
                                        @foreach ($chunk as $review)
                                            <div class="col-md-4 mb-4">
                                                <div class="card mb-4" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07); overflow: hidden; height: 100%;">
                                                    <div class="card-body" style="padding: 28px 24px; display:flex; flex-direction:column; gap:12px; box-sizing:border-box; height:200px;">
                                                        <!-- Header with Avatar and Info -->
                                                        <div style="display: flex; align-items: flex-start; gap: 14px;">
                                                            @php
                                                                $name = $review->user_name ?? 'User';
                                                                $words = explode(' ', $name);
                                                                $initials = '';
                                                                foreach ($words as $word) {
                                                                    if (!empty($word)) {
                                                                        $initials .= strtoupper(substr($word, 0, 1));
                                                                    }
                                                                }
                                                                $initials = substr($initials, 0, 2);
                                                                
                                                                $colors = ['#f472b6', '#7c3aed', '#06b6d4', '#f97316', '#10b981', '#ec4899'];
                                                                $colorIndex = (ord($initials[0]) ?? 0) % count($colors);
                                                                $bgColor = $colors[$colorIndex];
                                                            @endphp
                                                            
                                                            <!-- Avatar Badge -->
                                                            <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 8px; background-color: {{ $bgColor }}; color: white; font-weight: 700; font-size: 16px; flex-shrink: 0;">
                                                                {{ $initials }}
                                                            </div>
                                                            
                                                            <!-- Name and Service Info -->
                                                            <div style="flex: 1;">
                                                                <h5 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 600; color: #1f2937;">{{ $name }}</h5>
                                                                @if ($review->service)
                                                                    <p style="margin: 0; font-size: 13px; color: #6b7280;">{{ $review->service->name }}</p>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Rating Stars -->
                                                        <div style="display: flex; gap: 3px; margin-bottom: 14px;">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= $review->rating)
                                                                    <span style="color: #fbbf24; font-size: 16px;">★</span>
                                                                @else
                                                                    <span style="color: #d1d5db; font-size: 16px;">★</span>
                                                                @endif
                                                            @endfor
                                                        </div>

                                                        <!-- Review Text -->
                                                        <p style="margin: 0; font-size: 14px; color: #4b5563; line-height: 1.6; font-style: italic;">
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
                        <!-- Prev Button -->
                        <a href="#reviewsCarousel"
                        data-slide="prev"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/20
                                flex items-center justify-center text-white shadow-lg transition-all duration-300 
                                hover:bg-purple-300 hover:scale-110 ml-4">

                            <!-- Heroicon: Chevron Left -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <!-- Next Button -->
                        <a href="#reviewsCarousel"
                        data-slide="next"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/20 
                                flex items-center justify-center text-white shadow-lg transition-all duration-300 
                                hover:bg-purple-300 hover:scale-110 mr-4">

                            <!-- Heroicon: Chevron Right -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Open modal when button is clicked
        $(document).on('click', '#openReviewModal', function() {
            $('#reviewModal').modal('show');
        });

        // Handle form submission via AJAX
        $(document).on('submit', '#reviewForm', function(e) {
            e.preventDefault();
            
            var form = this;
            var $submitBtn = $(form).find('button[type="submit"]');
            var originalBtnText = $submitBtn.html();
            var formData = new FormData(form);

            // Disable submit button and show loading state
            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
            
            // Hide previous errors
            $('#reviewFormErrors').fadeOut().find('#errorList').empty();

            $.ajax({
                url: $(form).attr('action'),
                method: $(form).attr('method') || 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Close modal with fade effect
                    $('#reviewModal').modal('hide');
                    
                    // Show success message
                    var successAlert = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 20px; border-radius: 8px; border-left: 4px solid #10b981; background-color: #f0fdf4;">
                            <strong style="color: #065f46;"><i class="fa fa-check-circle" style="margin-right: 8px;"></i>Success!</strong>
                            <span style="color: #047857;">Thank you for your review. It will be displayed after admin verification.</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `;
                    
                    // Prepend success alert to the reviews section
                    $('.album').prepend(successAlert);
                    
                    // Reset form
                    form.reset();
                    
                    // Reset button
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                    
                    // Auto-hide success message after 5 seconds
                    setTimeout(function() {
                        $('.alert-success').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 5000);
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                    
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Show validation errors
                        var errors = xhr.responseJSON.errors;
                        var errorHtml = '';
                        
                        $.each(errors, function(key, messages) {
                            errorHtml += '<li style="margin-bottom: 6px;">' + messages[0] + '</li>';
                        });
                        
                        $('#errorList').html(errorHtml);
                        $('#reviewFormErrors').fadeIn();
                        
                        // Scroll to error message
                        $('html, body').animate({
                            scrollTop: $('#reviewFormErrors').offset().top - 100
                        }, 300);
                    } else {
                        // Generic error message
                        $('#errorList').html('<li>An unexpected error occurred. Please try again.</li>');
                        $('#reviewFormErrors').fadeIn();
                    }
                }
            });
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

            $(".owl-carousel-staff").owlCarousel({
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