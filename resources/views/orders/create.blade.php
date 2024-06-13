@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{ config('app.version') }}" rel="stylesheet">
<link href="{{ asset('css/site.css') }}?v=3" rel="stylesheet">

@section('content')
    @php
        $total_amount = 0;
        $staff_charges = 0;
        $transport_charges = 0;
    @endphp
    <div class="album">
        <div class="container">
            <div class="row">
                <div class="col-md-12 py-5 text-center">
                    <h2>Create Order</h2>
                </div>
            </div>
            <div class="text-center" style="margin-bottom: 20px;">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <span>{{ $message }}</span>
                        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <span>{{ $message }}</span>
                        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif
            </div>
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

            <form action="{{ route('orders.store') }}" method="POST">
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

                    <div class="col-md-12">
                        <input type="text" id="search-service" class="form-control" placeholder="Search services...">
                    </div>

                    <div class="col-md-12 scroll-div mb-4">
                        <table class="table table-bordered table-striped">
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
                                                <input type="radio" name="service_id" class="checkout_service_id"
                                                    value="{{ $service->id }}"
                                                    data-options="{{ $service->serviceOption }}"
                                                    data-name="{{ $service->name }}"
                                                    data-price="{{ $service->discount ? $service->discount : $service->price }}"
                                                    data-duration="{{ $service->duration }}">
                                                {{ $service->name }}
                                        </td>
                                        <td>
                                            @if (isset($service->discount))
                                                <s>
                                            @endif
                                            @currency($service->price)
                                            @if (isset($service->discount))
                                                </s>
                                            @endif
                                            @if (isset($service->discount))
                                                <b class="discount"> @currency($service->discount)</b>
                                            @endif
                                        </td>
                                        <td>{{ $service->duration }}</td>
                                        </label>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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
                    <div id="slots-container">
                        @include('site.checkOut.timeSlots')
                    </div>
                    <div class="col-md-12 text-center">
                        <br>
                        <h3><strong>Address</strong></h3>
                        <hr>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Building Name:</strong>
                            <input required type="text" name="buildingName" id="buildingName" class="form-control"
                                placeholder="Building Name" value="{{ old('buildingName') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Flat / Villa:</strong>
                            <input required type="text" name="flatVilla" id="flatVilla" class="form-control"
                                placeholder="Flat / Villa" value="{{ old('flatVilla') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Street:</strong>
                            <input required type="text" name="street" id="street" class="form-control"
                                placeholder="Street" value="{{ old('street') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Area:</strong>

                            <select required class="form-control" name="area" id="area">
                                <option value="">-- Select Zone -- </option>
                                @foreach ($zones as $zone)
                                    <option @if (old('area') == $zone) selected @endif value="{{ $zone }}">
                                        {{ $zone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>District:</strong>
                            <input required type="text" name="district" id="district" class="form-control"
                                placeholder="District" value="{{ old('district') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Landmark:</strong>
                            <input required type="text" name="landmark" id="landmark" class="form-control"
                                placeholder="Landmark" value="{{ old('landmark') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>City:</strong>
                            <input required type="text" name="city" id="city" class="form-control"
                                placeholder="City" value="{{ old('city') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Custom Location:</strong>
                            <input type="text" name="custom_location" class="form-control"
                                value="{{ old('custom_location') }}" placeholder="32.3335, 65.23223">
                        </div>
                    </div>
                    <input type="hidden" name="latitude" id="latitude" class="form-control" placeholder="latitude"
                        value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" class="form-control" placeholder="longitude"
                        value="{{ old('longitude') }}">

                    <div class="col-md-12 text-center">
                        <br>
                        <h3><strong>Personal information</strong></h3>
                        <hr>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Name:</strong>
                            <input required type="text" name="name" id="name" class="form-control"
                                placeholder="Name" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Email:</strong>
                            <input required type="email" name="email" id="email" class="form-control"
                                placeholder="abc@gmail.com" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Phone Number:</strong>
                            <input id="number_country_code" type="hidden" name="number_country_code" />
                            <input required type="tel" name="number" id="number" class="form-control"
                                value="{{ old('number') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                            <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                            <input required type="tel" name="whatsapp" id="whatsapp" class="form-control"
                                value="{{ old('whatsapp') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Gender:</strong><br>
                            <div class="form-check form-check-inline">
                                <input required class="form-check-input" type="radio" name="gender" id="genderMale"
                                    value="Male" {{ old('gender') == 'Male' ? 'checked' : '' }}>
                                <label class="form-check-label" for="genderMale">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input required class="form-check-input" type="radio" name="gender" id="genderFemale"
                                    value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}>
                                <label class="form-check-label" for="genderFemale">Female</label>
                            </div>
                        </div>

                        <!-- Display error message if validation fails -->
                        @if ($errors->has('gender'))
                            <span class="invalid-feedback text-center" role="alert"
                                style="display: block; font-size: medium;">
                                <strong>{{ $errors->first('gender') }}</strong>
                            </span>
                        @endif

                        <hr>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Affiliate Code:</strong>
                            <input type="text" name="affiliate_code" id="affiliate_code" class="form-control"
                                placeholder="Affiliate Code">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Coupon Code:</strong>
                            <div class="input-group">
                                <input type="text" name="coupon_code" id="coupon_code" class="form-control"
                                    placeholder="Coupon Code">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="applyCouponBtn">Apply
                                        Coupon</button>
                                </div>
                            </div>
                            <div id="responseMessage"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Payment Method:</strong>
                            <select name="payment_method" class="form-control">
                                <option value="Cash-On-Delivery">Cash On Delivery</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Comment:</strong>
                            <textarea name="order_comment" class="form-control" cols="30" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3 mt-3 offset-md-3 ">
                        <h5>Payment Summary</h5>
                        <table class="table">
                            <tr>
                                <td class="text-left"><strong> Service Total:</strong></td>
                                <td>{{ config('app.currency') }} <span id="sub_total">0</span></td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Coupon Discount:</strong></td>
                                <td>{{ config('app.currency') }} <span id="coupon-discount">0</span></td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Staff Charges:</strong></td>
                                <td>{{ config('app.currency') }} <span id="staff_charges">0</span></td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Transport Charges:</strong></td>
                                <td>{{ config('app.currency') }} <span id="transport_charges">0</span></td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Total:</strong></td>
                                <td>{{ config('app.currency') }} <span id="total_amount">0</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-block mt-2 mb-2 btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateTotal() {
            let transport_charges = parseFloat($('#zone').find(':selected').data('transport-charges'));

            $('#transport_charges').text(transport_charges);

            let staff_charges = parseFloat($('input[name="service_staff_id"]:checked').data('staff-charges'));

            let coupon_discount = parseFloat($('#coupon-discount').text());

            $('#staff_charges').text(staff_charges);

            let total_amount = 0;

            let sub_total = parseFloat($('#sub_total').text());
            total_amount = sub_total + staff_charges + transport_charges - coupon_discount;

            $('#total_amount').text(total_amount.toFixed(2));
        }

        function applyCoupon() {
            var couponCode = $("#coupon_code").val();
            var selectedServiceId = $('.checkout_service_id:checked').val();
            var selectedOptionId = $('input[name="option_id"]:checked').val() || null;

            $("#responseMessage").html("");

            if (selectedServiceId && couponCode) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('apply.order_coupon') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        coupon_code: couponCode,
                        selected_service_id: selectedServiceId,
                        selected_option_id: selectedOptionId
                    },
                    success: function(response) {
                        var messageClass = response.error ? 'alert-danger' :
                            'alert-success';
                        var message = response.error ? response.error : response.message;
                        $("#coupon_code").val(response.error ? "" : couponCode);
                        $("#responseMessage").append(
                            '<p class="coupon-message alert ' + messageClass + '">' +
                            message + '</p>'
                        );
                        if (response.message) {
                            $('#coupon-discount').text(response.discount);
                            updateTotal();
                        }else if(response.error){
                            $('#coupon-discount').text(0);
                            updateTotal();
                        }
                    },
                    error: function(xhr, status, error) {
                        $("#responseMessage").append(
                            '<p class="coupon-message alert alert-danger">An error occurred while applying the coupon. Please try again.</p>'
                        );
                    }
                });
            } else {
                $("#responseMessage").append(
                    '<p class="coupon-message alert alert-danger">Please select a service and enter a coupon code.</p>'
                );
            }

            setTimeout(function() {
                $(".coupon-message").fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 6000);
        };
    </script>

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

            $('input[name="service_id"]').on('change', function() {
                var serviceName = $(this).data('name');
                var servicePrice = $(this).data('price');
                var serviceDuration = $(this).data('duration');
                var serviceOptions = $(this).data('options');

                $('#selected-service-name').text(serviceName);
                $('#selected-service-price').text(servicePrice);
                $('#selected-service-duration').text(serviceDuration);
                $('#sub_total').text(servicePrice);
                updateTotal();

                var couponCode = $("#coupon_code").val();
                var selectedServiceId = $('.checkout_service_id:checked').val();

                if (couponCode && selectedServiceId) {
                    applyCoupon();
                }

                $('#selected-service').show();

                if (serviceOptions && serviceOptions.length > 0) {
                    var optionsHtml = '';
                    var minPrice = Infinity;
                    var minPriceOption = null;

                    serviceOptions.forEach(function(option) {
                        var optionPrice = parseFloat(option.option_price);
                        optionsHtml += `
                        <div>
                            <label>
                                <input type="radio" name="option_id" value="${option.id}" data-option-price="${optionPrice}" required> ${option.option_name} (AED ${optionPrice})
                            </label>
                        </div>
                    `;
                        if (optionPrice < minPrice) {
                            minPrice = optionPrice;
                            minPriceOption = option.id;
                        }
                    });

                    $('#service-options-list').html(optionsHtml);
                    $('#service-options').show();

                    if (minPriceOption !== null) {
                        $(`input[name="option_id"][value="${minPriceOption}"]`).prop('checked', true);
                        $('#selected-service-price').text(minPrice);
                        $('#sub_total').text(minPrice);
                        console.log(couponCode, selectedServiceId);
                        if (couponCode && selectedServiceId) {
                            applyCoupon();
                        }
                        updateTotal();
                    }

                    $('input[name="option_id"]').on('change', function() {
                        var selectedOptionPrice = $(this).data('option-price');
                        $('#selected-service-price').text(selectedOptionPrice);
                        $('#sub_total').text(selectedOptionPrice);
                        if (couponCode && selectedServiceId) {
                            applyCoupon();
                        }
                        updateTotal();
                    });
                } else {
                    $('#service-options').hide();
                    $('#service-options-list').html('');
                }
            });

            $("#applyCouponBtn").click(function() {
                applyCoupon();
            });

            $(document).on("change", "#zone", function() {
                $("#area").val($(this).val());
            });

            $(document).on("change", "#area", function() {
                $("#zone").val($(this).val());
            });
        });
    </script>
    <script src="{{ asset('js/checkout.js') }}?v={{ config('app.version') }}"></script>
@endsection
