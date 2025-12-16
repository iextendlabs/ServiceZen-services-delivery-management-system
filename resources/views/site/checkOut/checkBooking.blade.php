@extends('site.layout.app')
@section('content')
    <div class="bg-gray-50 py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-purple-800">Check Booking</h2>
            </div>
            <div class="mb-6">
                @if (Session::has('error') || Session::has('success'))
                    @if (Session::has('error'))
                        <div class="rounded-md bg-red-50 p-4 mb-3">
                            <p class="text-red-700">{{ Session::get('error') }}</p>
                        </div>
                    @endif
                    @if (Session::has('success'))
                        <div class="rounded-md bg-green-50 p-4 mb-3">
                            <p class="text-green-700">{{ Session::get('success') }}</p>
                        </div>
                    @endif
                @endif

                @if (Session::has('cart-success'))
                    <div class="rounded-md bg-green-50 p-4 mb-3">
                        <p class="text-green-700">You have added service to your <a class="underline" href="cart">shopping cart</a>! <a class="ml-4 text-purple-800" href="bookingStep">Go and Book Now!</a></p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4">
                        <p class="font-semibold text-red-700">Whoops! There were some problems with your input.</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div>
                <form action="{{ route('addToCartServicesStaff') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Search services by categories</label>
                            <select name="category" id="category-select" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm">
                                <option value="">All</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}> {{ $category->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="search-service" class="w-full rounded-md border-gray-200 shadow-sm p-2" placeholder="Search services...">
                    </div>

                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600">Name</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600">Price</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600">Duration</th>
                                </tr>
                            </thead>
                            <tbody id="services-list" class="divide-y">
                                @foreach ($services as $service)
                                    <tr data-category="{{ json_encode($service->categories->pluck('id')) }}" class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <label class="flex items-center space-x-3 cursor-pointer">
                                                <input required type="radio" name="service_id" class="checkBooking_service_id" value="{{ $service->id }}" data-options='@json($service->serviceOption)' data-name="{{ $service->name }}" data-price="@if($service->discount) @currency($service->discount,false,true) @else @currency($service->price,false,true) @endif" data-duration="{{ $service->duration ?? '' }}">
                                                <span class="text-sm text-gray-800">{{ $service->name }}</span>
                                            </label>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            @if (isset($service->discount))
                                                <s class="text-gray-400 mr-2">@currency($service->price,false,true)</s>
                                                <span class="font-medium text-purple-800">@currency($service->discount,false,true)</span>
                                            @else
                                                @currency($service->price,false,true)
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $service->duration ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div id="selected-service" class="hidden bg-gray-50 rounded-md p-4">
                            <h4 class="font-semibold">Selected Service</h4>
                            <p class="text-sm"><strong>Name:</strong> <span id="selected-service-name"></span></p>
                            <p class="text-sm"><strong>Price:</strong> <span id="selected-service-price"></span></p>
                            <p class="text-sm hidden"><strong>Duration:</strong> <span id="selected-service-duration"></span></p>
                        </div>
                        <div id="service-options" class="hidden bg-gray-50 rounded-md p-4 mt-3">
                            <h4 class="font-semibold">Service Options</h4>
                            <div id="service-options-list" class="mt-2"></div>
                        </div>
                    </div>

                    <div id="slots-container" class="mt-6">
                        @include('site.checkOut.timeSlots')
                    </div>

                    <div class="mt-6 text-center">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-purple-800 text-white rounded-md hover:bg-purple-950">Book Now</button>
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

                if(serviceDuration){
                    $('#selected-service-duration').parent().show();
                    $('#selected-service-duration').text(serviceDuration);
                }else{
                    $('#selected-service-duration').parent().hide();
                }

                $('#selected-service').show();

                if (serviceOptions && serviceOptions.length > 0) {
                    var optionsHtml = '';
                    var minPrice = Infinity;
                    var minPriceOption = null;
                    var minPriceDuration = 0;

                    for (const option of serviceOptions) {
                        var optionPrice = parseFloat(option.option_price) || 0;

                        try {
                            var formattedOptionPrice = await formatCurrencyJS(optionPrice);
                            optionsHtml += `
                                <div>
                                    <label>
                                        <input type="checkbox" class="option-checkbox" name="option_id[]" value="${option.id}" data-price="${formattedOptionPrice}" data-duration="${option.option_duration}"> 
                                        ${option.option_name} (${formattedOptionPrice}) ${option.option_duration ? option.option_duration : ''}
                                    </label>
                                </div>
                            `;

                            if (optionPrice < minPrice) {
                                minPrice = optionPrice;
                                minPriceOption = option.id;
                                minOptionDuration = option.option_duration;
                            }
                        } catch (error) {
                            console.error("Error formatting currency:", error);
                        }
                    }

                    $('#service-options-list').html(optionsHtml);
                    $('#service-options').show();

                    if (minPriceOption !== null) {
                        try {
                            var formattedMinPrice = await formatCurrencyJS(minPrice);
                            $(`input[name="option_id[]"][value="${minPriceOption}"]`).prop('checked', true);
                            $('#selected-service-price').text(formattedMinPrice);
                            if(minOptionDuration || serviceDuration){
                                $('#selected-service-duration').parent().show();
                            }else{
                                $('#selected-service-duration').parent().hide();
                            }
                            $('#selected-service-duration').text(minOptionDuration ? minOptionDuration : serviceDuration);
                        } catch (error) {
                            console.error("Error formatting currency:", error);
                            $('#selected-service-price').text(servicePrice);
                            if(serviceDuration){
                                $('#selected-service-duration').parent().show();
                            }else{
                                $('#selected-service-duration').parent().hide();
                            }
                            $('#selected-service-duration').text(serviceDuration);
                        }
                    }

                    function updatePriceAndDuration() {
                        if ($('.option-checkbox').filter(':checked').length > 0) {
                            let totalPrice = 0;
                            let totalDuration = 0;

                            $('.option-checkbox').filter(':checked').each(function() {
                                totalPrice += parsePrice($(this).data('price'));
                                totalDuration += parseDuration($(this).data('duration'));
                            });

                            const hours = Math.floor(totalDuration / 60);
                            const minutes = totalDuration % 60;

                            const formattedDuration = `${hours > 0 ? hours + ' hours ' : ''}${minutes > 0 ? minutes + ' minutes' : ''}`;
                            
                            let currencySymbol = '';
                            $('.option-checkbox').filter(':checked').each(function() {
                                let price = $(this).data('price');
                                currencySymbol = price.replace(/[0-9.-]/g, '');
                                return false;
                            });
                            $('#selected-service-price').text(`${currencySymbol}${totalPrice.toFixed(2)}`);
                            if(formattedDuration || serviceDuration){
                                $('#selected-service-duration').parent().show();
                            }else{
                                $('#selected-service-duration').parent().hide();
                            }
                            if (formattedDuration) {
                                $('#selected-service-duration').text(formattedDuration);
                            } else {
                                $('#selected-service-duration').text(serviceDuration);
                            }
                        } else {
                            if(serviceDuration){
                                $('#selected-service-duration').parent().show();
                            }else{
                                $('#selected-service-duration').parent().hide();
                            }
                            $('#selected-service-price').text(servicePrice);
                            $('#selected-service-duration').text(serviceDuration);
                        }
                    }

                    $('.option-checkbox').on('change', function() {
                        updatePriceAndDuration();
                    });
                } else {
                    $('#service-options').hide();
                    $('#service-options-list').html('');
                }
            });

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

            function formatCurrencyJS(amount,extra_charges=false) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: "POST",
                        url: '{{ route('format-currency') }}',
                        data: {
                            amount: amount,
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
