@extends('site.layout.app')
@section('adsense_head')
    @if (!empty($ads['head']))
        {!! $ads['head'] !!}
    @endif
@endsection
<style>
    .box-shadow {
        background: none !important;
    }

    #thumbnails img {
        width: 100px;
        height: 100px;
        object-fit: contain;
        background-color: #fff;
        padding: 5px;
        cursor: pointer;
        margin: 5px;
        border: 1px solid #000000;
        transition: border-color 0.3s;
        display: inline-block;
    }

    #thumbnails img:hover {
        border-color: #007bff;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 5%;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: #000;
        border-radius: 50%;
        padding: 10px;
    }

    /* FAQ styles to match main store/card look */

    .faq-question {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        padding: 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
    }

    .faq-question:hover {
        background: #f1f5f9;
    }

    .faq-answer {
        display: none;
        padding: 0.75rem 1rem 1rem 1rem;
        color: #374151;
    }

    .faq-card {
        border: 1px solid #e6e9ee;
    }

    .faq-toggle-icon {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border-radius: 50%;
        border: 1px solid #e6e9ee;
        color: #6b7280;
        font-weight: 600;
        flex: 0 0 28px;
    }
</style>

@section('content')
    @php
        $rating = $averageRating;
        $fullStars = floor($rating);
        $halfStar = $rating - $fullStars >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    @endphp
    @php
        if ($app_flag === true) {
            $addONsCarousel_chunk = 1;
            $packageCarousel_chunk = 1;
            $reviews_chunk = 1;
        } else {
            $addONsCarousel_chunk = 6;
            $packageCarousel_chunk = 3;
            $reviews_chunk = 3;
        }

        $validPackage = $service->package->filter(function($package) {
            return isset($package->service) && $package->service->status == 1;
        });
        
        $validAddONs = $service->addONs->filter(function($addON) {
            return isset($addON->service) && $addON->service->status == 1;
        });
    @endphp

    @if (isset($lowestPriceOption))
        @php($currentLowestPrice = $lowestPriceOption->option_price)
    @else
        @php($currentLowestPrice = null)
    @endif
    <div class="container">
        {{-- <section class="jumbotron text-center">
            <h1 class="jumbotron-heading">Best In the Town Saloon Services</h1>
            <p class="lead text-muted">Get Your Desired Saloon Beauty service at Your Door, easy to schedule and just few
                clicks away.</p>
        </section> --}}
        <div class="text-center">
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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        @if (!empty($ads['top']))
            {!! $ads['top'] !!}
        @endif
        <div id="serviceDetailContainer" class="album py-5">
            <a href={{ url('/') }} class="flex items-center gap-2 text-purple-900 hover:text-purple-850 mb-6"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left w-5 h-5"><path d="m15 18-6-6 6-6"></path></svg>Back to Services</a>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center w-full flex items-center justify-center bg-indigo-50 border border-gray-200 rounded-md" style="height: 390px;">
                                <img src="{{ asset('service-images/' . $service->image) }}" alt="Main image" class="img-fluid w-full h-96 object-cover rounded-md" id="mainImage">
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            @if ($service->images)
                                <div class="row">
                                    <div class="col">
                                        <div class="d-flex justify-content-center">
                                            <div id="thumbnails" class="d-flex flex-wrap">
                                                @foreach ($service->images as $index => $image)
                                                    <img src="{{ asset('service-images/additional/' . $image->image) }}"
                                                        alt="Thumbnail {{ $index + 1 }}" data-toggle="modal"
                                                        data-target="#imageModal" data-slide-to="{{ $index }}">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog"
                                    aria-labelledby="imageModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                                style="position: absolute; top: 10px; right: 10px; z-index: 1050;">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <div class="modal-body p-0">
                                                <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                                                    <div class="carousel-inner">
                                                        @foreach ($service->images as $index => $image)
                                                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                                <img src="{{ asset('service-images/additional/' . $image->image) }}"
                                                                    class="d-block w-100"
                                                                    alt="Full image {{ $index + 1 }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <a class="carousel-control-prev" href="#imageCarousel" role="button"
                                                        data-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="sr-only">Previous</span>
                                                    </a>
                                                    <a class="carousel-control-next" href="#imageCarousel" role="button"
                                                        data-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="bg-indigo-50 rounded-lg p-6 shadow-sm">
                                <!-- Top row: Studio name + Service title -->
                                <div class="text-center mb-3">
                                    <div class="text-sm text-gray-500">{{ $service->studio_name ?? 'Glam Studio' }}</div>
                                    <h1 class="card-text service-title text-3xl font-extrabold text-purple-700 my-1"><b>{{ $service->name }}</b></h1>
                                </div>

                                <!-- Rating / Duration / Location row -->
                                <div class="d-flex justify-content-center align-items-center flex-wrap text-center" style="gap:18px;">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2 text-yellow-400" aria-hidden="true">
                                            @for ($i = 0; $i < $fullStars; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor
                                            @if ($halfStar)
                                                <i class="fas fa-star-half-alt"></i>
                                            @endif
                                            @for ($i = 0; $i < $emptyStars; $i++)
                                                <i class="far fa-star text-gray-300"></i>
                                            @endfor
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <strong class="text-gray-800">{{ number_format($rating, 1) }}</strong>
                                            <span class="ml-1">({{ count($reviews) }} reviews)</span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center text-sm text-gray-600" style="gap:14px;">
                                        <div class="d-flex align-items-center"><i class="fa fa-clock mr-2"></i><span>{{ $service->duration ?? '—' }}</span></div>
                                        <div class="d-flex align-items-center"><i class="fa fa-map-marker-alt mr-2"></i><span>{{ $service->location ?? 'Location' }}</span></div>
                                    </div>
                                </div>

                                <!-- Short description -->
                                @if(!empty($service->short_description))
                                    <div class="mt-4 text-gray-700 text-center" style="line-height:1.6;">
                                        {!! $service->short_description !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if (count($reviews))
                            <div class="col-md-12">
                                <hr>
                                <h2 class="text-center mt-4 my-4 text-2xl font-bold">Customer Reviews</h2>

                                <div class="mt-6">
                                    @foreach ($reviews as $review)
                                        <div class="bg-white rounded-lg shadow-sm p-4">
                                            <div class="flex">
                                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-purple-800 font-bold mr-3">{{ strtoupper(substr($review->user_name, 0, 1)) }}</div>
                                                <div class="flex-1">
                                                    <div class="flex items-start justify-between">
                                                        <h5 class="font-semibold text-purple-800">{{ $review->user_name }}</h5>
                                                        <div class="text-xs text-gray-500">{{ $review->created_at ? $review->created_at->diffForHumans() : '' }}</div>
                                                    </div>
                                                    <p class="text-sm text-gray-700 mt-2">{{ substr($review->content, 0, $review_char_limit) }}{{ strlen($review->content) > $review_char_limit ? '...' : '' }}</p>
                                                    <div class="mt-3 text-yellow-400">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $review->rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star text-gray-300"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endif
                        @if ($validAddONs->count())
                            <div class="col-md-12">
                                <hr>
                                <h2 class="text-center mt-4 my-4">Add ONs</h2>
                                <div id="myCarousel" class="carousel slide col-md-12" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        @foreach ($validAddONs->chunk($addONsCarousel_chunk) as $key => $addONsChunk)
                                            <li data-target="#myCarousel" data-slide-to="{{ $key }}"
                                                class="{{ $loop->first ? 'active' : '' }}"></li>
                                        @endforeach
                                    </ol>

                                    <div class="carousel-inner">
                                        @foreach ($validAddONs->chunk($addONsCarousel_chunk) as $key => $addONsChunk)
                                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                <div class="row">
                                                    @foreach ($addONsChunk as $addON)
                                                        <div class="col-md-2 col-12 service-box">
                                                            <div class="card mb-2 box-shadow">
                                                                <a href="/service/{{ $addON->service->slug }}">
                                                                    <div class="position-relative">
                                                                        <img src="./service-images/{{ $addON->service->image }}"
                                                                            class="d-block carousel-image"
                                                                            alt="Image {{ $key }}">
                                                                        <p class="card-text text-center service-name">
                                                                            {{ $addON->service->name }}</p>
                                                                    </div>
                                                                </a>
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <small class="text-muted">
                                                                            @if (isset($addON->service->discount))
                                                                                <s>
                                                                            @endif
                                                                            @currency($addON->service->price, false, true)
                                                                            @if (isset($addON->service->discount))
                                                                                </s>
                                                                            @endif
                                                                            @if (isset($addON->service->discount))
                                                                                <b class="discount"> @currency($addON->service->discount, false, true)</b>
                                                                            @endif
                                                                        </small>
                                                                        @if($addON->service->duration)
                                                                            <small class="text-muted"><i class="fa fa-clock"> </i>{{ $addON->service->duration }}</small>
                                                                        @endif
                                                                    </div>
                                                                    @if ($addON->service->quote == 1)
                                                                        <button style="margin-top: 1em;"
                                                                            onclick="openQuotePopup('{{ $addON->service->id }}')" type="button"
                                                                            class="btn btn-sm btn-block btn-warning"> Quote</button>
                                                                    @elseif (count($addON->service->serviceOption) > 0)
                                                                        <a style="margin-top: 1em; color:#fff"
                                                                            href="/service/{{ $addON->service->slug }}"
                                                                            type="button"
                                                                            class="btn btn-sm btn-block btn-primary float-right mt-2"><i
                                                                                class="fa fa-plus"></i></a>
                                                                    @else
                                                                        <button
                                                                            onclick="openBookingPopup('{{ $addON->service->id }}')"
                                                                            type="button" style="color:white"
                                                                            class="btn btn-sm btn-block btn-primary float-right mt-2"><i
                                                                                class="fa fa-plus"></i></button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        @endif

                        
                        @if ($validPackage->count())
                            <div class="col-md-12">
                                <hr>
                                <h2 class="text-center mt-4 my-4">Package Services</h2>
                                <div id="packageCarousel" class="carousel slide col-md-12" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        @foreach ($validPackage->chunk($packageCarousel_chunk) as $key => $packageChunk)
                                            <li data-target="#packageCarousel" data-slide-to="{{ $key }}"
                                                class="{{ $loop->first ? 'active' : '' }}"></li>
                                        @endforeach
                                    </ol>

                                    <div class="carousel-inner">
                                        @foreach ($validPackage->chunk($packageCarousel_chunk) as $key => $packageChunk)
                                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                <div class="row">
                                                    @foreach ($packageChunk as $package)
                                                        <div class="col-md-4 service-box">
                                                            <div class="card mb-4 box-shadow">
                                                                <a href="/service/{{ $package->service->slug }}">
                                                                    <p class="card-text service-box-title text-center">
                                                                        <b>{{ $package->service->name }}</b>
                                                                    </p>
                                                                    <img class="card-img-top"
                                                                        src="./service-images/{{ $package->service->image }}"
                                                                        alt="Card image cap">
                                                                </a>
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <small class="text-muted service-box-price">
                                                                            @if (isset($package->service->discount))
                                                                                <s>
                                                                            @endif
                                                                            @currency($package->service->price, false, true)
                                                                            @if (isset($package->service->discount))
                                                                                </s>
                                                                            @endif
                                                                            @if (isset($package->service->discount))
                                                                                <b class="discount"> @currency($package->service->discount, false, true)</b>
                                                                            @endif
                                                                        </small>
                                                                        @if($package->service->duration)
                                                                        <small all class="text-muted service-box-time"><i
                                                                                class="fa fa-clock">
                                                                            </i> {{ $package->service->duration }}</small>
                                                                        @endif
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <a class="carousel-control-prev" href="#packageCarousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#packageCarousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if (count($FAQs))
                            <hr>
                            <div class="col-md-12">
                                <h2 id="faqs" class="text-center mt-4 my-4 text-2xl font-semibold">Frequently Asked Questions</h2>

                                <div class="row">
                                    <div class="col-md-12">
                                        @foreach ($FAQs as $FAQ)
                                            <div class="mb-4">
                                                <div class="bg-white rounded-lg shadow-sm p-0 faq-card">
                                                    <div class="faq-question" role="button" tabindex="0" aria-expanded="false">
                                                        <span class="faq-toggle-icon">+</span>
                                                        <span class="font-semibold text-gray-800" style="white-space:normal;">{{ $FAQ->question }}</span>
                                                    </div>
                                                    <div class="faq-answer">{!! nl2br(e($FAQ->answer)) !!}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif                        
                    </div>
                </div>
                <div class="w-full md:w-1/3 px-4">
                    <div class="bg-indigo-50 rounded-lg p-6 shadow-sm">
                        <div class="mb-4">
                            <span class="text-gray-800">Price</span>
                            <p id="price" class="text-gray-700">
                                @if ($price)
                                    <span class="text-4xl font-extrabold text-purple-700">@currency($price, false, true)</span>
                                @else
                                    @if (isset($service->discount))
                                        <div class="flex items-baseline space-x-3">
                                            <s class="text-sm text-red-600">@currency($service->price, false, true)</s>
                                            <b class="text-xl text-green-600">@currency($service->discount, false, true)</b>
                                        </div>
                                    @else
                                        <span class="text-2xl font-bold text-purple-700">@currency($service->price, false, true)</span>
                                    @endif
                                @endif
                            </p>
                        </div>

                        <p class="text-sm text-gray-500 mb-4" @if($service->duration == null) style="display:none;" @endif>
                            <b class="inline-flex items-center"><i class="fa fa-clock mr-2"></i>
                                <span id="duration" data-duration="{{ $service->duration }}">{{ $service->duration ?? '' }}</span></b>
                        </p>

                        @if (count($service->serviceOption))
                            <div class="mb-4">
                                <strong class="block text-gray-800 mb-2">Available Options</strong>
                                @foreach ($service->serviceOption as $option)
                                    <label for="option{{ $option->id }}" class="flex items-center space-x-3 py-2 border-b border-indigo-100">
                                        <input type="checkbox" name="option[]" class="option-checkbox h-4 w-4 text-purple-600 rounded" value="{{ $option->id }}" id="option{{ $option->id }}"
                                            data-price="@currency($option->option_price, false, false)" 
                                            data-duration="{{ $option->option_duration }}"
                                            @if (isset($lowestPriceOption) && $option->id === $lowestPriceOption->id) checked @endif
                                            data-image="{{ !empty($option->image) ? asset('service-images/options/' . $option->image) : '' }}">

                                        @if (!empty($option->image))
                                            <img src="{{ asset('service-images/options/' . $option->image) }}" alt="{{ $option->option_name }}" class="w-12 h-12 object-cover rounded-full border" />
                                        @endif

                                        <div class="flex-1 text-sm text-gray-700">
                                            <div class="font-medium">{{ $option->option_name }}</div>
                                            <div class="text-xs text-gray-500">(@currency($option->option_price, false, false)) {{ $option->option_duration ?? '' }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        @if ($service->quote == 1)
                            <button style="margin-top: 1em;" onclick="openQuotePopup('{{ $service->id }}')" type="button" class="w-full bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-3 rounded-lg">Request a Quote</button>
                        @else
                            <button id="bookNowButton" type="button" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold py-3 rounded-lg mb-3"> <i class="far fa-calendar mr-2"></i> Book Now</button>
                        @endif

                        <div class="flex space-x-2 mb-3">
                            @if (count($service->addONs))
                                <button class="flex-1 bg-white border border-indigo-100 text-indigo-700 py-2 rounded-lg" id="add-ons-scroll">Add ONs</button>
                            @endif
                            @if (count($FAQs))
                                <button class="flex-1 bg-white border border-indigo-100 text-indigo-700 py-2 rounded-lg" id="faqs-scroll">FAQs</button>
                            @endif
                        </div>

                        <div class="mb-4">
                            <!-- Share button that opens modal -->
                            <button type="button" class="w-full text-indigo-700 py-2 rounded-lg flex items-center justify-center space-x-2" data-toggle="modal" data-target="#shareModal">
                                <i class="fa fa-share-alt"></i>
                                <span>Share</span>
                            </button>

                            <!-- Modal that displays social icons -->
                            <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="shareModalLabel">Share this service</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="a2a_kit a2a_kit_size_32 a2a_default_style flex items-center justify-center space-x-4 text-gray-600">
                                                <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                                                <a class="a2a_button_facebook"></a>
                                                <a class="a2a_button_twitter"></a>
                                                <a class="a2a_button_whatsapp"></a>
                                                <a class="a2a_button_telegram"></a>
                                                <a class="a2a_button_linkedin"></a>
                                                <a class="a2a_button_email"></a>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script async src="https://static.addtoany.com/menu/page.js"></script>

                        <p class="text-gray-700 mb-4">{!! $service->short_description !!}</p>

                        @if (auth()->check())
                            <button class="w-full bg-white border border-purple-200 text-purple-700 py-2 rounded-lg mb-3" id="review">Write a review</button>
                        @endif

                        <div class="flex items-center space-x-3 mb-2">
                            <div class="flex items-center text-yellow-400">
                                @for ($i = 0; $i < $fullStars; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor

                                @if ($halfStar)
                                    <i class="fas fa-star-half-alt"></i>
                                @endif

                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="far fa-star text-gray-300"></i>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">{{ count($reviews) }} Reviews</span>
                        </div>

                        @if (!empty($ads['right']))
                            {!! $ads['right'] !!}
                        @endif
                    </div>
                    <div class="col-md-12 mb-2">
                        @if (auth()->check())
                            <div id="review-form" style="display: none; ">
                                @include('site.reviews.create')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($ads['bottom']))
            {!! $ads['bottom'] !!}
        @endif
    </div>
    </div>
    <script>
        $(document).ready(function() {
            const $priceElement = $('#price');
            const $durationElement = $('#duration');
            const $options = $('.option-checkbox');

            function parsePrice(priceStr) {
                return parseFloat(priceStr.replace(/[^0-9.-]+/g, ''));
            }

            function parseDuration(durationStr) {
                if (!durationStr) return 0;

                durationStr = durationStr.toLowerCase();

                const match = durationStr.match(/(\d+)\s*(hour|hours|hr|h|min|mins|mints|minute|minutes|m|mint)?/i);
                if (!match) return 0;

                const value = parseInt(match[1], 10);
                const unit = match[2] || 'min';

                switch (unit) {
                    case 'hour':
                    case 'hours':
                    case 'hr':
                    case 'h':
                        return value * 60;
                    case 'min':
                    case 'mins':
                    case 'mints':
                    case 'minute':
                    case 'minutes':
                    case 'm':
                    case 'mint':
                    default:
                        return value;
                }
            }

            function updatePriceAndDuration() {
                if ($options.filter(':checked').length > 0) {
                    let totalPrice = 0;
                    let totalDuration = 0;

                    $options.filter(':checked').each(function() {
                        totalPrice += parsePrice($(this).data('price'));
                        totalDuration += parseDuration($(this).data('duration'));
                    });

                    const hours = Math.floor(totalDuration / 60);
                    const minutes = totalDuration % 60;

                    const formattedDuration = `${hours > 0 ? hours + ' hours ' : ''}${minutes > 0 ? minutes + ' minutes' : ''}`;
                    
                    let currencySymbol = '';
                    $options.filter(':checked').each(function() {
                        let price = $(this).data('price');
                        currencySymbol = price.replace(/[0-9.-]/g, '');
                        return false;
                    });

                    $priceElement.html(
                        `<span class="font-weight-bold">${currencySymbol}${totalPrice.toFixed(2)}</span>`);
                    if (formattedDuration) {
                        $durationElement.parent().parent().show();
                        $durationElement.text(`${formattedDuration}`);
                    } else {
                        if($durationElement.data("duration") == ""){
                            $durationElement.parent().parent().hide();
                        }else{
                            $durationElement.text('{{ $service->duration }}');
                        }   
                    }
                } else {
                    $priceElement.html(`<span class="text-3xl font-bold text-purple-800">@currency($service->price, false, true)</span>`);
                    if($durationElement.data("duration") == ""){
                        $durationElement.parent().parent().hide();
                    }else{
                        $durationElement.text('{{ $service->duration }}');
                    }   
                }
            }

            updatePriceAndDuration();

            $options.on('change', function() {
                updatePriceAndDuration();
            });

            $('#bookNowButton').on('click', function() {

                if ($options.length > 0 && !$options.is(':checked')) {
                    alert('Please select an option before booking.');
                    return false;
                } else if ($options.length > 0 && $options.is(':checked')) {
                    const selectedOptions = $options.filter(':checked').map(function() {
                        return $(this).val();
                    }).get();
                    openBookingPopup('{{ $service->id }}', selectedOptions);
                } else {
                    openBookingPopup('{{ $service->id }}');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            let defaultImage = $("#mainImage").attr("src");

            $(".option-checkbox").change(function () {
                let selectedImage = "";

                $(".option-checkbox:checked").each(function () {
                    let optionImage = $(this).data("image");
                    if (optionImage) {
                        selectedImage = optionImage;
                    }
                });

                if (selectedImage) {
                    $("#mainImage").attr("src", selectedImage);
                } else {
                    $("#mainImage").attr("src", defaultImage);
                }
            });
        });
        $(document).on('change', '#variant-select', function() {
            var selectedOption = $(this).find('option:selected');
            var price = selectedOption.data('price');
            var duration = selectedOption.data('duration');
            var id = selectedOption.data('id');

            $('#price').html(price);
            $('#duration').html(duration);
        });
    </script>
    <script>
        $('#add-ons-scroll').click(() => {
            $('html, body').animate({
                scrollTop: $('#myCarousel').offset().top
            }, 1000);
        });

        $('#faqs-scroll').click(() => {
            $('html, body').animate({
                scrollTop: $('#faqs').offset().top
            }, 1000);
        });

        $(document).on('click', '#review', function() {
            $('#review-form').show();
            $('html, body').animate({
                scrollTop: $('#review-form').offset().top
            }, 1000);
        });

        // FAQ toggle: show/hide answer on question click or Enter/Space
        $(document).on('click', '.faq-question', function() {
            var $q = $(this);
            var $a = $q.next('.faq-answer');
            var expanded = $q.attr('aria-expanded') === 'true';
            $q.attr('aria-expanded', (!expanded).toString());
            $a.slideToggle(180);
            var $icon = $q.find('.faq-toggle-icon');
            if (expanded) {
                $icon.text('+');
            } else {
                $icon.text('-');
            }
        });

        $(document).on('keydown', '.faq-question', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).trigger('click');
            }
        });
    </script>
@endsection
