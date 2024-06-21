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
                    <input type="hidden" name="isAdmin" value="true">
                    <div id="slots-container">
                        @include('site.checkOut.timeSlots')
                    </div>
                    <div id="categories-services-container">
                        <!-- Content will be dynamically appended here -->
                    </div>
                    <div id="selected-services-container" class="col-md-12 mt-4" style="display: none;">
                        <input type="hidden" name="selected_service_ids" id="selected_service_ids">
                        <input type="hidden" name="selected_option_ids" id="selected_option_ids">
                        <h4>Selected Services</h4>
                        <div class="mb-4">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody id="selected-services-list">
                                    <!-- Selected services will be appended here -->
                                </tbody>
                            </table>
                        </div>
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

                            <select disabled required class="form-control" name="area" id="area">
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
        $(document).ready(function() {

            function updateTotal() {
                let subTotal = 0;
                let totalAmount = 0;

                let transportCharges = parseFloat($('#zone').find(':selected').data('transport-charges'));

                $('#transport_charges').text(transportCharges);

                let staffCharges = parseFloat($('input[name="service_staff_id"]:checked').data('staff-charges'));

                $('#staff_charges').text(staffCharges);

                let couponDiscount = parseFloat($('#coupon-discount').text());

                $('input[name="service_id"]:checked').each(function() {
                    let serviceId = $(this).val();
                    let serviceOptions = $(this).data('options');
                    let servicePrice = parseFloat($(this).data('price'));

                    if (serviceOptions) {
                        let selectedOptionPrice = parseFloat($(
                            `input[name="service_option_${serviceId}"]:checked`).data('price'));
                        if (!isNaN(selectedOptionPrice)) {
                            servicePrice = selectedOptionPrice;
                        }
                    }

                    subTotal += servicePrice;
                });

                totalAmount = subTotal + staffCharges + transportCharges - couponDiscount;

                $('#sub_total').text(subTotal.toFixed(2));
                $('#total_amount').text(totalAmount.toFixed(2));
            }

            function applyCoupon() {

                let selectedServiceIds = [];
                let selectedOptionIds = {};

                $('input[name="service_id"]:checked').each(function() {
                    let serviceId = $(this).val();
                    selectedServiceIds.push(serviceId);

                    let selectedOptionId = $(`input[name="service_option_${serviceId}"]:checked`).val();
                    if (selectedOptionId) {
                        selectedOptionIds[serviceId] = selectedOptionId;
                    } else {
                        selectedOptionIds[serviceId] =
                            null;
                    }
                });

                var couponCode = $("#coupon_code").val();
                $("#responseMessage").html("");

                if (selectedServiceIds && couponCode) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('apply.order_coupon') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            coupon_code: couponCode,
                            selected_service_ids: selectedServiceIds,
                            selected_option_ids: selectedOptionIds
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
                            } else if (response.error) {
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

            function fetchCategoriesAndServices(serviceIds, categoryIds) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('fetch.staff_categories_services') }}",
                    data: {
                        serviceIds: serviceIds,
                        categoryIds: categoryIds
                    },
                    success: function(response) {
                        let categoriesHtml = '';
                        if (response.categories.length > 0) {
                            categoriesHtml = `
                        <div class="col-md-12">
                            <strong>Search services by categories </strong>
                            <select name="category" id="category-select" class="form-control">
                                <option value="">All</option>
                    `;
                            response.categories.forEach(category => {
                                categoriesHtml +=
                                    `<option value="${category.id}">${category.title}</option>`;
                            });
                            categoriesHtml += `</select><br></div>`;
                        }

                        let servicesHtml = `
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
                            <tbody id="services-list">
                `;
                        response.services.forEach(service => {
                            let categories = JSON.stringify(service.categories.map(category =>
                                category.id));
                            servicesHtml += `
                        <tr data-category='${categories}'>
                            <td>
                                <label style="display: contents;">
                                    <input type="checkbox" name="service_id" class="form-check-input checkout_service_id"
                                        value="${service.id}"
                                        data-options='${JSON.stringify(service.service_option)}'
                                        data-name="${service.name}"
                                        data-price="${service.discount ? service.discount : service.price}"
                                        data-duration="${service.duration}">
                                    ${service.name}
                            </td>
                            <td>
                                ${service.discount ? `<s>${service.price}</s> <b class="discount">${service.discount}</b>` : service.price}
                            </td>
                            <td>${service.duration}</td>
                                </label>
                            </tr>
                    `;
                        });
                        servicesHtml += `</tbody></table></div>`;

                        if (response.categories.length === 0 && response.services.length === 0) {
                            let html = `
                    <div class="col-md-12">
                        <div class="alert alert-danger text-center">
                            There are no services available for this staff.
                        </div>
                    </div>`;
                            $('#categories-services-container').html(html);
                        } else {
                            $('#categories-services-container').html(categoriesHtml + servicesHtml);

                            // Re-attach event handlers if necessary
                            $('#search-service').on('keyup', filterServices);
                            $('#category-select').on('change', filterServices);
                            $('input[name="service_id"]').on('change', serviceSelectionChanged);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("An error occurred while fetching categories and services:",
                            error);
                    }
                });
            }

            function serviceSelectionChanged() {
                let selectedServices = [];

                $('input[name="service_id"]:checked').each(function() {
                    let serviceId = $(this).val();
                    let serviceName = $(this).data('name');
                    let servicePrice = $(this).data('price');
                    let serviceDuration = $(this).data('duration');
                    let serviceOptions = $(this).data('options');

                    let optionsHtml = '';
                    let lowestPrice = servicePrice;

                    if (serviceOptions) {
                        serviceOptions.forEach((option, index) => {
                            if (index === 0 || parseFloat(option.option_price) < lowestPrice) {
                                lowestPrice = parseFloat(option.option_price);
                            }
                            optionsHtml += `
                                <label>
                                    <input type="radio" name="service_option_${serviceId}" value="${option.id}" class="form-check-input"
                                        data-price="${option.option_price}" data-name="${option.option_name}" ${index === 0 ? 'checked' : ''}>
                                    ${option.option_name} (AED ${option.option_price})
                                </label><br>
                            `;
                        });
                    }

                    selectedServices.push({
                        id: serviceId,
                        name: serviceName,
                        price: lowestPrice,
                        duration: serviceDuration,
                        optionsHtml: optionsHtml
                    });
                });

                let selectedServicesHtml = '';

                selectedServices.forEach(service => {
                    selectedServicesHtml += `
                        <tr>
                            <td>${service.name}</td>
                            <td class="service-price">${service.price}</td>
                            <td>${service.duration}</td>
                            <td>${service.optionsHtml}</td>
                        </tr>
                    `;
                });

                $('#selected-services-list').html(selectedServicesHtml);

                // Show or hide the selected services container
                if (selectedServices.length > 0) {
                    $('#selected-services-container').show();
                } else {
                    $('#selected-services-container').hide();
                }

                // Attach event listener to new option radios to update price on change
                $('input[type="radio"]').change(function() {
                    let serviceRow = $(this).closest('tr');
                    let newPrice = $(this).data('price');
                    serviceRow.find('.service-price').text(newPrice);
                    $('#coupon-discount').text(0);
                    $("#coupon_code").val("");
                    handleSelectedServiceOption();
                    updateTotal();
                });
                $('#coupon-discount').text(0);
                $("#coupon_code").val("");
                handleSelectedServiceOption();
                updateTotal();
            }

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

            function handleSelectedServiceOption() {
                let selectedServiceIds = [];
                let selectedOptionIds = {};

                $('input[name="service_id"]:checked').each(function() {
                    let serviceId = $(this).val();
                    selectedServiceIds.push(serviceId);

                    let selectedOptionId = $(`input[name="service_option_${serviceId}"]:checked`).val();
                    if (selectedOptionId) {
                        selectedOptionIds[serviceId] = selectedOptionId;
                    } else {
                        selectedOptionIds[serviceId] =
                            null;
                    }
                });

                $('input[name="selected_service_ids"]').val(JSON.stringify(selectedServiceIds));
                $('input[name="selected_option_ids"]').val(JSON.stringify(selectedOptionIds));

            }

            $("#applyCouponBtn").click(function() {
                applyCoupon();
            });

            function handleZoneAreaChange() {
                var selectedStaffId = $('input[name="service_staff_id"]:checked').val();
                if (selectedStaffId) {
                    $('#selected-services-container').hide();
                    $('#selected-services-list').html("");
                    var serviceIds = $('input[name="service_staff_id"]:checked').data('serviceids');
                    var categoryIds = $('input[name="service_staff_id"]:checked').data('categoryids');
                    fetchCategoriesAndServices(serviceIds, categoryIds);
                    $('input[name="service_id"]').prop('checked', false);
                    $('#coupon-discount').text(0);
                    $("#coupon_code").val("");
                    updateTotal();
                    handleSelectedServiceOption();
                }
            }

            $(document).on("change", "#zone", function() {
                $("#area").val($(this).val());
                setTimeout(handleZoneAreaChange, 3000);
            });

            $(document).on("change", "input[name='service_staff_id']", function() {
                handleZoneAreaChange();
            });
        });
    </script>
    <script src="{{ asset('js/checkout.js') }}?v={{ config('app.version') }}"></script>
@endsection
