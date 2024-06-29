@extends('site.layout.app')
<link href="{{ asset('css/checkout.css') }}?v={{ config('app.version') }}" rel="stylesheet">
<style>
    label {
        display: contents;
    }

    tr:hover {
        background-color: #f5f5f5;
    }
</style>
@section('content')
    <div class="album bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-12 py-2 text-center">
                    <h2>Check Booking</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if (Session::has('error') || Session::has('success'))
                        <div class="text-center" style="margin-bottom: 20px;">
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
                        </div>
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
            </div>

            <div>
                <form action="{{ route('addToCartServicesStaff') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Search services by categories </strong>
                            <select name="category" id="category-select" class="form-control">
                                <option value="">All</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"> {{ $category->title }}</option>
                                @endforeach
                            </select><br>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <input type="text" id="search-service" class="form-control" placeholder="Search services...">
                        </div>
                    </div>

                    <div class="row scroll-div">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody id="services-list">
                                    @foreach ($services as $service)
                                        <tr data-category="{{ json_encode($service->categories->pluck('id')) }}">
                                            <td>
                                                <label style="display: contents;">
                                                    <input required type="radio" name="service_id" class="checkBooking_service_id" 
                                                           value="{{ $service->id }}" data-options="{{ $service->serviceOption }}" data-name="{{ $service->name }}"
                                                           data-price="@if($service->discount) @currency($service->discount,false,true) @else @currency($service->price,false,true) @endif"
                                                           data-duration="{{ $service->duration }}">
                                                    {{ $service->name }}
                                            </td>
                                            <td>
                                                @if (isset($service->discount))
                                                    <s>
                                                @endif
                                                @currency($service->price,false,true)
                                                @if (isset($service->discount))
                                                    </s>
                                                @endif
                                                @if (isset($service->discount))
                                                    <b class="discount"> @currency($service->discount,false,true)</b>
                                                @endif
                                            </td>
                                            <td>{{ $service->duration }}</td>
                                                </label>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div id="selected-service" class="alert alert-secondary" style="display: none;">
                                <h4>Selected Service</h4>
                                <p><strong>Name:</strong> <span id="selected-service-name"></span></p>
                                <p><strong>Price:</strong> <span id="selected-service-price"></span></p>
                                <p><strong>Duration:</strong> <span id="selected-service-duration"></span></p>
                            </div>
                            <div id="service-options" class="alert alert-info" style="display: none;">
                                <h4>Service Options</h4>
                                <div id="service-options-list"></div>
                            </div>
                        </div>
                    </div>
                    <div id="slots-container" class="col-md-12">
                        @include('site.checkOut.timeSlots')
                    </div>
                    <div class="row">
                        <div class="col-md-6 offset-md-3 col-sm-12">
                            <button type="submit" class="btn btn-block mt-2 mb-2 btn-success">Book Now</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function filterServices() {
                var searchValue = $('#search-service').val().toLowerCase();
                var selectedCategory = $('#category-select').val();

                $('#services-list tr').each(function() {
                    var categoryMatch = false;
                    var searchMatch = false;

                    var categories = $(this).data('category');
                    var text = $(this).text().toLowerCase();

                    if (!selectedCategory || categories.includes(parseInt(selectedCategory))) {
                        categoryMatch = true;
                    }

                    if (text.indexOf(searchValue) > -1) {
                        searchMatch = true;
                    }

                    if (categoryMatch && searchMatch) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            $('#search-service').on('keyup', function() {
                filterServices();
            });

            $('#category-select').on('change', function() {
                filterServices();
            });

            $('input[name="service_id"]').on('change', async function() {
                var serviceName = $(this).data('name');
                var servicePrice = $(this).data('price');
                var serviceDuration = $(this).data('duration');
                var serviceOptions = $(this).data('options');
    
                $('#selected-service-name').text(serviceName);
                $('#selected-service-price').text(servicePrice);
                $('#selected-service-duration').text(serviceDuration);

                $('#selected-service').show();
    
                if (serviceOptions && serviceOptions.length > 0) {
                    var optionsHtml = '';
                    var minPrice = Infinity;
                    var minPriceOption = null;
    
                    for (const option of serviceOptions) {
                        var optionPrice = parseFloat(option.option_price);
                        
                        try {
                            var formattedOptionPrice = await formatCurrencyJS(optionPrice,true);
                            
                            optionsHtml += `
                                <div>
                                    <label>
                                        <input type="radio" name="option_id" value="${option.id}" data-option-price="${formattedOptionPrice}" required> ${option.option_name} (${formattedOptionPrice})
                                    </label>
                                </div>
                            `;
                            
                            if (optionPrice < minPrice) {
                                minPrice = optionPrice;
                                minPriceOption = option.id;
                            }
                        } catch (error) {
                            console.error("Error formatting currency:", error);
                        }
                    }

                    $('#service-options-list').html(optionsHtml);
                    $('#service-options').show();

                    if (minPriceOption !== null) {
                        try {
                            var formattedMinPrice = await formatCurrencyJS(minPrice,true);
                            $(`input[name="option_id"][value="${minPriceOption}"]`).prop('checked', true);
                            $('#selected-service-price').text(formattedMinPrice);
                        } catch (error) {
                            console.error("Error formatting currency:", error);
                            $('#selected-service-price').text(minPrice);
                        }
                    }

                    $('input[name="option_id"]').on('change', function() {
                        var selectedOptionPrice = $(this).data('option-price');
                        $('#selected-service-price').text(selectedOptionPrice);
                    });
                } else {
                    $('#service-options').hide();
                    $('#service-options-list').html('');
                }
            });

            function formatCurrencyJS(amount,extra_charges=false) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: "POST",
                        url: '{{ route('format-currency') }}',
                        data: {
                            amount: amount,
                            extra_charges: extra_charges,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            resolve(response.formattedAmount); // Resolve with formatted amount
                        },
                        error: function(error) {
                            reject(error); // Reject with error
                        }
                    });
                });
            }
        });
    </script>
    <script src="{{ asset('js/checkout.js') }}?v={{ config('app.version') }}"></script>
@endsection
