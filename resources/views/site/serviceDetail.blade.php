@extends('site.layout.app')
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

    .modal-dialog-centered {
        max-width: 700px !important;
        max-height: 700px !important;
        margin: auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: transparent;
        border: none;
        box-shadow: none;
        max-width: 700px !important;
        max-height: 700px !important;
    }

    .modal-body {
        padding: 0;
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
</style>

@section('content')

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
    @endphp

    @if (isset($lowestPriceOption))
        @php($currentLowestPrice = $lowestPriceOption->option_price)
    @else
        @php($currentLowestPrice = null)
    @endif
    <div class="container">
        <section class="jumbotron text-center">
            <h1 class="jumbotron-heading">Best In the Town Saloon Services</h1>
            <p class="lead text-muted">Get Your Desired Saloon Beauty service at Your Door, easy to schedule and just few
                clicks away.</p>
        </section>
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
        <div id="serviceDetailContainer" class="album py-5">
            <h1 class="card-text text-center service-title"><b>{{ $service->name }}</b></h1>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <img src="{{ asset('service-images/' . $service->image) }}" alt="Main image" height="auto"
                                width="100%">
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
                            <h3 class="text-center mt-3"
                                style="font-family: 'Titillium Web', sans-serif;font-weight: bold;">
                                Description</h3>
                            {!! $service->description !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-body">
                        <p id="price" class="text-muted">
                            @if ($price)
                                <span class="font-weight-bold">@currency($price, false, true)</span>
                            @else
                                @if (isset($service->discount))
                                    <s class="text-danger">@currency($service->price, false, true)</s>
                                    <b class="discount text-success">@currency($service->discount, false, true)</b>
                                @else
                                    <span class="font-weight-bold">@currency($service->price, false, true)</span>
                                @endif
                            @endif
                        </p>

                        <p class="text-muted">
                            <b><i class="fa fa-clock mr-2"></i><span id="duration">{{ $service->duration }}</span></b>
                        </p>

                        @if (count($service->serviceOption))
                            <div class="mb-3">
                                <strong>Available Options</strong>
                                @foreach ($service->serviceOption as $option)
                                    <div class="form-check">
                                        <input type="checkbox" name="option[]" class="form-check-input option-checkbox"
                                            value="{{ $option->id }}" id="option{{ $option->id }}"
                                            data-price="@currency($option->option_price, false, false)" data-duration="{{ $option->option_duration }}"
                                            @if (isset($lowestPriceOption) && $option->id === $lowestPriceOption->id)
                                        checked
                                @endif>
                                <label class="form-check-label" for="option{{ $option->id }}">{{ $option->option_name }}
                                    (@currency($option->option_price, false, false))
                                    {{ $option->option_duration ?? '' }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- @if (count($service->variant))
                    <div class="form-group">
                      <strong>Service Variants</strong>
                      <select name="variant" id="variant-select" class="form-control mb-2">
                        <option value="{{ $service->name }}" data-id="{{ $service->id }}" data-name="{{ $service->name }}" data-duration="{{ $service->duration }}" data-price="@currency(isset($service->discount,true) ? $service->discount : $service->price)">
                          {{ $service->name }}
                        </option>
                        @foreach ($service->variant as $variant)
                        <option value="{{ $variant->service->name }}" data-id="{{ $variant->service->id }}" data-name="{{ $variant->service->name }}" data-duration="{{ $variant->service->duration }}" data-price="@currency(isset($variant->service->discount) ? $variant->service->discount : $variant->service->price,true)">
                          {{ $variant->service->name }}
                        </option>
                        @endforeach
                      </select>
                    </div>
                    @endif --}}

                    <button id="bookNowButton" type="button" class="btn btn-primary btn-block mb-2">Book Now</button>

                    @if (count($service->addONs))
                        <button class="btn btn-secondary btn-block mb-2" id="add-ons-scroll">Add ONs</button>
                    @endif
                    @if (count($FAQs))
                        <button class="btn btn-secondary btn-block mb-2" id="faqs-scroll">FAQs</button>
                    @endif

                    <!-- AddToAny BEGIN -->
                    <div
                        class="a2a_kit a2a_kit_size_32 a2a_default_style service-social-icon d-flex justify-content-around mt-3 mb-3">
                        <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                        <a class="a2a_button_facebook"></a>
                        <a class="a2a_button_twitter"></a>
                        <a class="a2a_button_whatsapp"></a>
                        <a class="a2a_button_telegram"></a>
                    </div>
                    <script async src="https://static.addtoany.com/menu/page.js"></script>
                    <!-- AddToAny END -->

                    <p class="card-text">{!! $service->short_description !!}</p>

                    @if (auth()->check())
                        <button class="btn btn-primary btn-block mb-3" id="review">Write a review</button>
                    @endif

                    <div class="rating mb-3">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $averageRating)
                                <span class="text-warning">&#9733;</span>
                            @else
                                <span class="text-muted">&#9734;</span>
                            @endif
                        @endfor
                        <span>{{ count($reviews) }} Reviews</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @if (count($reviews))
                <div class="col-md-12">
                    <hr>
                    <h2 class="text-center mt-4 my-4">Customer Reviews</h2>
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
            @endif
            <div class="col-md-5 offset-md-4 mb-2">
                @if (auth()->check())
                    <div id="review-form" style="display: none;">
                        @include('site.reviews.create')
                    </div>
                @endif
            </div>
            @if (count($service->addONs))
                <div class="col-md-12">
                    <hr>
                    <h2 class="text-center mt-4 my-4">Add ONs</h2>
                    <div id="myCarousel" class="carousel slide col-md-12" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach ($service->addONs->chunk($addONsCarousel_chunk) as $key => $addONsChunk)
                                <li data-target="#myCarousel" data-slide-to="{{ $key }}"
                                    class="{{ $loop->first ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>

                        <div class="carousel-inner">
                            @foreach ($service->addONs->chunk($addONsCarousel_chunk) as $key => $addONsChunk)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="row">
                                        @foreach ($addONsChunk as $addON)
                                            <div class="col-md-2 col-12 service-box">
                                                <div class="card mb-2 box-shadow">
                                                    <a href="/serviceDetail/{{ $addON->service->id }}">
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
                                                            <small class="text-mutede">
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
                                                            <small class="text-muted"><i class="fa fa-clock"> </i>
                                                                {{ $addON->service->duration }}</small>
                                                        </div>
                                                        @if (count($addON->service->serviceOption) > 0)
                                                            <a style="margin-top: 1em; color:#fff"
                                                                href="/serviceDetail/{{ $addON->service->id }}"
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

            @if (count($service->package))
                <div class="col-md-12">
                    <hr>
                    <h2 class="text-center mt-4 my-4">Package Services</h2>
                    <div id="packageCarousel" class="carousel slide col-md-12" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach ($service->package->chunk($packageCarousel_chunk) as $key => $packageChunk)
                                <li data-target="#packageCarousel" data-slide-to="{{ $key }}"
                                    class="{{ $loop->first ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>

                        <div class="carousel-inner">
                            @foreach ($service->package->chunk($packageCarousel_chunk) as $key => $packageChunk)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="row">
                                        @foreach ($packageChunk as $package)
                                            <div class="col-md-4 service-box">
                                                <div class="card mb-4 box-shadow">
                                                    <a href="/serviceDetail/{{ $package->service->id }}">
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

                                                            <small class="text-muted service-box-time"><i
                                                                    class="fa fa-clock">
                                                                </i> {{ $package->service->duration }}</small>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <a class="carousel-control-prev" href="#  " role="button" data-slide="prev">
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
                    <h2 id="faqs" class="text-center mt-4 my-4">Frequently Asked Questions</h2>
                    <div id="accordion">
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
                </div>
            @endif
        </div>
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
                let duration = 0;
                if (durationStr) {
                    const minMatch = durationStr.match(/(\d+)\s*(min|mint|MINT)/i);
                    if (minMatch) {
                        duration = parseInt(minMatch[1], 10);
                    }
                }
                return duration;
            }

            function updatePriceAndDuration() {
                if ($options.filter(':checked').length > 0) {
                    let totalPrice = 0;
                    let totalDuration = 0;

                    $options.filter(':checked').each(function() {
                        totalPrice += parsePrice($(this).data('price'));
                        totalDuration += parseDuration($(this).data('duration'));
                    });

                    let currencySymbol = '';
                    $options.filter(':checked').each(function() {
                        let price = $(this).data('price');
                        currencySymbol = price.replace(/[0-9.-]/g, '');
                        return false;
                    });

                    $priceElement.html(
                        `<span class="font-weight-bold">${currencySymbol}${totalPrice.toFixed(2)}</span>`);
                    if (totalDuration == 0) {
                        $durationElement.text('{{ $service->duration }}');
                    } else {
                        $durationElement.text(`${totalDuration} MINT`);
                    }
                } else {
                    $priceElement.html(`<span class="font-weight-bold">@currency($service->price, false, true)</span>`);
                    $durationElement.text('{{ $service->duration }}');
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
    </script>
@endsection
