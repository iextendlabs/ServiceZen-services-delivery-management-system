@extends('site.layout.app')
<link href="{{ asset('css/checkout.css') }}?v={{ config('app.version') }}" rel="stylesheet">
@section('content')
    <div class="album bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-12 py-2 text-center">
                    <h2>Booking</h2>
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
            <div class="col-md-12 errorContainer">
            </div>
            <div class="row" id="selected-booking-staff-slot"
                @if (count($formattedBookings) === 0) style="display:none;" @endif>
                <div class="col-md-12">
                    <div class="form-group">
                        <table class="table table-striped table-bordered selected-services-table">

                            @foreach ($formattedBookings as $booking)
                                <tr>
                                    <th>
                                        <i class="fa fa-calendar m-3"> {{ $booking['date'] }} </i>
                                        <i class="fa fa-user m-3"> {{ $booking['staff'] }} </i>
                                        <i class="fa fa-clock m-3"> {{ $booking['slot'] }}</i>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        @foreach ($booking['services'] as $service)
                                            <div class="row m-3 @if (Session::has('excludedServices') && in_array($service->id, session('excludedServices'))) alert alert-danger @endif"
                                                role="alert" id="{{ $service->id }}">
                                                <div class="col-md-6">
                                                    <div><strong>Name:</strong> {{ $service->name }}</div>
                                                    <div><strong>Price:</strong> 
                                                        <span class="price">
                                                            @if(isset($groupedBookingOption[$service->id]) && $groupedBookingOption[$service->id] != null)
                                                                @php
                                                                    $option = $service->serviceOption->find($groupedBookingOption[$service->id]);
                                                                @endphp
                                                                @if($option)
                                                                    @currency($option->option_price)
                                                                @else
                                                                    @if (isset($service->discount))
                                                                        @currency($service->discount)
                                                                    @else
                                                                        @currency($service->price)
                                                                    @endif
                                                                @endif

                                                            @else
                                                                @if (isset($service->discount))
                                                                    @currency($service->discount)
                                                                @else
                                                                    @currency($service->price)
                                                                @endif
                                                            @endif

                                                        </span>
                                                    </div>
                                                    @if(isset($groupedBookingOption[$service->id]) && $groupedBookingOption[$service->id] != null)
                                                        @php
                                                            $option = $service->serviceOption->find($groupedBookingOption[$service->id]);
                                                        @endphp
                                                        @if($option)
                                                        <div><strong>Option:</strong> {{ $option->option_name }}</div>
                                                        @endif

                                                    @endif
                                                    <div><strong>Duration:</strong> {{ $service->duration }}</div>
                                                </div>
                                                <div class="col-md-6 d-flex justify-content-end align-items-center">
                                                    <button onclick="openBookingPopup('{{ $service->id }}')"
                                                        type="button" class="btn btn-primary edit-booking">Edit</button>
                                                </div>
                                            </div>
                                            <hr class="my-4">
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <div id="booking-step">
                <form id="booking-form" action="draftOrder" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end align-items-center">
                            <a href="{{route('checkBooking')}}" class="btn btn-primary">Add More Services</a>
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
                                    placeholder="Building Name"
                                    value="{{ old('buildingName') ? old('buildingName') : $addresses['buildingName'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Flat / Villa:</strong>
                                <input required type="text" name="flatVilla" id="flatVilla" class="form-control"
                                    placeholder="Flat / Villa"
                                    value="{{ old('flatVilla') ? old('flatVilla') : $addresses['flatVilla'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Street:</strong>
                                <input required type="text" name="street" id="street" class="form-control"
                                    placeholder="Street"
                                    value="{{ old('street') ? old('street') : $addresses['street'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Area:</strong>

                                <select readonly required class="form-control" name="area">
                                    <option value="">-- Select Zone -- </option>
                                    <!-- Loop through the $zones array to generate options -->
                                    @foreach ($zones as $zone)
                                        <option @if (old('area') == $zone || $addresses['area'] == $zone || (session('address') && session('address')['area'] == $zone)) selected @endif
                                            value="{{ $zone }}">
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
                                    placeholder="District"
                                    value="{{ old('district') ? old('district') : $addresses['district'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Landmark:</strong>
                                <input required type="text" name="landmark" id="landmark" class="form-control"
                                    placeholder="Landmark"
                                    value="{{ old('landmark') ? old('landmark') : $addresses['landmark'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>City:</strong>
                                <input required type="text" name="city" id="city" class="form-control"
                                    placeholder="City" value="{{ old('city') ? old('city') : $addresses['city'] }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Custom Location:</strong>
                                <input type="text" name="custom_location" class="form-control"
                                    value="{{ old('custom_location') }}" placeholder="32.3335, 65.23223">
                            </div>
                        </div>
                        <input type="hidden" name="latitude" id="latitude" class="form-control"
                            placeholder="latitude"
                            value="{{ old('latitude') ? old('latitude') : $addresses['latitude'] }}">
                        <input type="hidden" name="longitude" id="longitude" class="form-control"
                            placeholder="longitude"
                            value="{{ old('longitude') ? old('longitude') : $addresses['longitude'] }}">
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <br>
                            <h3><strong>Personal information</strong></h3>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Name:</strong>
                                <input required type="text" name="name" id="name" class="form-control"
                                    placeholder="Name" value="{{ old('name') ? old('name') : $name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Email:</strong>
                                <input required type="email" name="email" id="email" class="form-control"
                                    placeholder="abc@gmail.com" value="{{ old('email') ? old('email') : $email }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Phone Number:</strong>
                                <input id="number_country_code" type="hidden" name="number_country_code" />
                                <input required type="tel" name="number" id="number" class="form-control"
                                    value="{{ old('number') ? old('number') : $addresses['number'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                                <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                                <input required type="tel" name="whatsapp" id="whatsapp" class="form-control"
                                    value="{{ old('whatsapp') ? old('whatsapp') : $addresses['whatsapp'] }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Gender:</strong><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderMale"
                                        value="Male"
                                        {{ old('gender') == 'Male' || $addresses['gender'] == 'Male' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="genderMale">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderFemale"
                                        value="Female"
                                        {{ old('gender') == 'Female' || $addresses['gender'] == 'Female' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="genderFemale">Female</label>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Affiliate Code:</strong>
                                <input type="text" name="affiliate_code" id="affiliate_code" class="form-control"
                                    placeholder="Affiliate Code" {{ $affiliate_code ? 'readonly' : null }}
                                    value="{{ $affiliate_code ?? old('affiliate_code') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Coupon Code:</strong>
                                <div class="input-group">
                                    <input type="text" name="coupon_code" id="coupon_code" class="form-control"
                                        placeholder="Coupon Code"
                                        value="{{ old('coupon_code') ? old('coupon_code') : $coupon_code }}">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" id="applyCouponBtn">Apply
                                            Coupon</button>
                                    </div>
                                </div>
                                <div id="responseMessage"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="checkbox" name="update_profile" id="update-profile" checked
                                    {{ old('update_profile') ? 'checked' : '' }}>

                                <label for="update-profile">
                                    Save Data in Profile
                                </label>
                            </div>
                        </div>
                        <span class="invalid-feedback text-center" id="gender-error" role="alert"
                            style="display: none; font-size: medium;">
                            <strong>Sorry, No Male Services Listed in Our Store.</strong>
                        </span>

                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-block mt-2 mb-2 btn-success">Next</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="confirm-step" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mt-3 mt-3 offset-md-3 ">
                        <h5>Payment Summary</h5>
                        <table class="table">
                            <tr>
                                <td class="text-left"><strong> Service Total:</strong></td>
                                <td><span id="sub_total"></span></td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong> Coupon Discount:</strong></td>
                                <td><span id="discount"></span></td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Staff Charges:</strong></td>
                                <td><span id="staff_charges"></span></td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Transport Charges:</strong></td>
                                <td><span id="transport_charges"></span></td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Total:</strong></td>
                                <td><span id="total_amount"></span></td>
                            </tr>
                        </table>
                    </div>
                    <input type="hidden" name="customer_type" id="customer_type">
                    <div class="col-md-6 offset-md-3">
                        <div class="form-group">
                            <strong>Comment:</strong>
                            <textarea id="order_comment" name="order_comment" class="form-control" cols="30" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button id="confirmOrder" data-order-id="" type="button" class="btn btn-primary">Confirm
                            Order</button><br><br>
                        {{-- @auth
                            <a id="orderEdit" href="">
                                <button type="button" class="btn btn-secondary">Edit Order</button>
                            </a>
                        @endauth --}}
                        <a id="orderCancel" href="">
                            <button type="button" class="btn btn-primary">Cancel Order</button>
                        </a>
                    </div>
                </div>
            </div>
            <div id="success-step" style="display: none;">
                <section class="jumbotron text-center">
                    <div class="container">
                        <h1 class="jumbotron-heading">Your order has been placed!</h1>
                    </div>
                </section>
                <div class="album py-5 bg-light">
                    <div class="container">
                        <li>Your order has been successfully processed!</li>
                        <li>We have send you email with your login credentials.</li>
                        <li>Visit our website for your order detail and book more service</li>
                        @auth
                            <li>You can view your order history by clicking on <a href="/order">Order History</a>.</li>
                        @endauth
                        <li>Please direct any questions you have to the store owner.</li>
                        <li>Thanks for booking our service!</li>
                        <div class="col-md-12 text-right">
                            <a href="/">
                                <button type="button" class="btn btn-primary">Continue</button>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $("#applyCouponBtn").click(function() {
            var couponCode = $("#coupon_code").val();

            $("#responseMessage").html("");
            if(couponCode){
                $.ajax({
                    type: "POST",
                    url: "{{ route('apply.coupon') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        coupon_code: couponCode,
                    },
                    success: function(response) {
                        if(response.error){
                            $("#coupon_code").val("");
                            $("#responseMessage").append('<p class="coupon-message alert alert-danger">' + response.error + '</p>');
                        }else{
                            $("#responseMessage").append('<p class="coupon-message alert alert-success">' + response.message + '</p>');
                        }
                    },
                    error: function(error) {
                        console.log("Error:", error);
                    }
                });
            }else{
                $("#responseMessage").append('<p class="coupon-message alert alert-danger">Please enter coupon code.</p>');
            }
            setTimeout(function() {
                $(".coupon-message").css('display', 'none');
            },6000);

        });
    });
    </script>
    <script>
        $(document).ready(function() {
            $("#booking-form").submit(function(event) {
                event.preventDefault();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var formData = $(this).serialize() + '&_token=' + csrfToken;
                var url = $(this).attr("action");

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    success: function(response) {
                        if (response.excludedServices) {
                            $.each(response.excludedServices, function(key, id) {
                                $("#" + id).addClass("alert alert-danger");
                            });
                        }
                        if (response.errors) {
                            var errorMessages =
                                '<div class="alert alert-danger"><strong>Whoops! There were some problems with your input.</strong><ul>';
                            $.each(response.errors, function(field, errors) {
                                $.each(errors, function(key, error) {
                                    errorMessages += '<li>' + error + '</li>';
                                });
                            });
                            errorMessages += '</ul></div>';
                            $('.errorContainer').html(errorMessages);
                            $('html, body').scrollTop($(".errorContainer").offset().top);
                        } else {
                            $('#booking-step').hide();
                            $('.edit-booking').hide();
                            $('.errorContainer').hide();
                            $('#confirm-step').show();
                            $('html, body').scrollTop(0);

                            $('#sub_total').text(response.sub_total);
                            $('#discount').text(response.discount);
                            $('#staff_charges').text(response.staff_charges);
                            $('#transport_charges').text(response.transport_charges);
                            $('#total_amount').text(response.total_amount);
                            $('#confirmOrder').attr("data-order-id", response.order_ids);
                            $('#customer_type').val(response.customer_type);

                            var editOrderRoute = "{{ route('order.edit', ':orderId') }}";
                            editOrderRoute = editOrderRoute.replace(':orderId', response
                                .order_ids);

                            $('#orderEdit').attr('href', editOrderRoute);

                            var cancelOrderRoute = "{{ route('cancelOrder', ':orderId') }}";
                            cancelOrderRoute = cancelOrderRoute.replace(':orderId', response
                                .order_ids);

                            $('#orderCancel').attr('href', cancelOrderRoute);
                        }
                    },
                    error: function(error) {
                        console.log("Error:", error);
                    }
                });
            });
            $("#confirmOrder").click(function() {
                var order_ids =$(this).attr('data-order-id');
                var comment =$("#order_comment").val();
                var customer_type =$("#customer_type").val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('confirmStep') }}",
                    data: {
                        order_ids: order_ids,
                        comment: comment,
                        customer_type: customer_type,
                    },
                    success: function(response) {
                        $('#confirm-step').hide();
                        $('#selected-booking-staff-slot').hide();
                        $('#success-step').show();
                        $('html, body').scrollTop(0);
                    },
                    error: function(error) {
                        console.log("Error:", error);
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('js/checkout.js') }}?v={{ config('app.version') }}"></script>
@endsection
