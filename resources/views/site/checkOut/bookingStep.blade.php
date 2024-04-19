@extends('site.layout.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
<div class="album bg-light">
    <div class="container">
        <div id="booking-step">
            <div class="row">
                <div class="col-md-12 py-2 text-center">
                    <h2>Booking</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if(Session::has('error') || Session::has('success'))
                    <div class="text-center" style="margin-bottom: 20px;">
                        @if(Session::has('error'))
                        <span class="alert alert-danger" role="alert">
                            <strong>{{ Session::get('error') }}</strong>
                        </span>
                        @endif
                        @if(Session::has('success'))
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
            <form id="booking-form" action="draftOrder" method="POST">
                @csrf
                <div class="row" id="selected-booking-staff-slot" @if(count($formattedBookings) === 0) style="display:none;"  @endif>
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
                                        <div class="row m-3 @if(Session::has('excludedServices') && in_array($service->id, session('excludedServices'))) alert alert-danger @endif" role="alert">
                                            <div class="col-md-6">
                                                <div><strong>Name:</strong> {{ $service->name }}</div>
                                                <div><strong>Price:</strong> <span class="price">@if(isset($service->discount)) 
                                                    @currency($service->discount) @else @currency($service->price) @endif</span></div>
                                                <div><strong>Duration:</strong> {{ $service->duration }}</div>
                                            </div>
                                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                                <button onclick="openBookingPopup('{{ $service->id }}')" type="button" class="btn btn-primary">Edit</button>
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
                <div class="row">
                    <div class="col-md-12 text-center">
                        <br>
                        <h3><strong>Address</strong></h3>
                        <hr>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Building Name:</strong>
                            <input required type="text" name="buildingName" id="buildingName" class="form-control" placeholder="Building Name" value="{{ old('buildingName') ? old('buildingName') : $addresses['buildingName'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Flat / Villa:</strong>
                            <input required type="text" name="flatVilla" id="flatVilla" class="form-control" placeholder="Flat / Villa" value="{{ old('flatVilla') ? old('flatVilla') : $addresses['flatVilla'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Street:</strong>
                            <input required type="text" name="street" id="street" class="form-control" placeholder="Street" value="{{ old('street') ? old('street') : $addresses['street'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Area:</strong>
    
                            <select readonly required class="form-control" name="area" id="area">
                                <option value="">-- Select Zone -- </option>
                                    <!-- Loop through the $zones array to generate options -->
                                @foreach ($zones as $zone)
                                <option @if (old('area')==$zone || $addresses['area']==$zone || (session('address') && session('address')['area']==$zone )) selected @endif value="{{ $zone }}">
                                    {{ $zone }}
                                </option>
                                @endforeach
                            </select>
    
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>District:</strong>
                            <input required type="text" name="district" id="district" class="form-control" placeholder="District" value="{{ old('district') ? old('district') : $addresses['district'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Landmark:</strong>
                            <input required type="text" name="landmark" id="landmark" class="form-control" placeholder="Landmark" value="{{ old('landmark') ? old('landmark') : $addresses['landmark'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>City:</strong>
                            <input required type="text" name="city" id="city" class="form-control" placeholder="City" value="{{ old('city') ? old('city') : $addresses['city'] }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Custom Location:</strong>
                            <input type="text" name="custom_location" class="form-control" value="{{ old('custom_location') }}" placeholder="32.3335, 65.23223">
                        </div>
                    </div>
                    <input type="hidden" name="latitude" id="latitude" class="form-control" placeholder="latitude" value="{{ old('latitude') ? old('latitude') : $addresses['latitude'] }}">
                    <input type="hidden" name="longitude" id="longitude" class="form-control" placeholder="longitude" value="{{ old('longitude') ? old('longitude') : $addresses['longitude'] }}">
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
                            <input required type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{  old('name') ? old('name') : $name }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Email:</strong>
                            <input required type="email" name="email" id="email" class="form-control" placeholder="abc@gmail.com" value="{{  old('email') ? old('email') : $email }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Phone Number:</strong>
                            <input id="number_country_code" type="hidden" name="number_country_code" />
                            <input required type="tel" name="number" id="number" class="form-control" value="{{ old('number') ? old('number') : $addresses['number'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                            <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                            <input required type="tel" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp') ? old('whatsapp') : $addresses['whatsapp'] }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Gender:</strong><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" {{ old('gender') == 'Male' || $addresses['gender'] == 'Male' ? 'checked' : '' }}>
                                <label class="form-check-label" for="genderMale">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female" {{ old('gender') == 'Female' || $addresses['gender'] == 'Female' ? 'checked' : '' }}>
                                <label class="form-check-label" for="genderFemale">Female</label>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Affiliate Code:</strong>
                            <input type="text" name="affiliate_code" id="affiliate_code" class="form-control" placeholder="Affiliate Code" {{ $affiliate_code ? 'readonly': null}} value="{{ $affiliate_code ?? old('affiliate_code') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Coupon Code:</strong>
                            <div class="input-group">
                                <input type="text" name="coupon_code" id="coupon_code" class="form-control" placeholder="Coupon Code" value="{{  old('coupon_code') ? old('coupon_code') : $coupon_code }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="applyCouponBtn">Apply Coupon</button>
                                </div>
                            </div>
                            <div id="responseMessage"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="checkbox" name="update_profile" id="update-profile" checked {{ old('update_profile') ? 'checked' : '' }}>
        
                            <label for="update-profile">
                                Save Data in Profile
                            </label>
                        </div>
                    </div>
                    <span class="invalid-feedback text-center" id="gender-error" role="alert" style="display: none; font-size: medium;">
                        <strong>Sorry, No Male Services Listed in Our Store.</strong>
                    </span>
                    <div class="col-md-12 errorContainer">
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-block mt-2 mb-2 btn-success">Next</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <script>
    $(document).ready(function() {
        $("#applyCouponBtn").click(function() {
            var couponCode = $("#coupon_code").val();
            var selectedServiceIds = [];

            $(".selected-service-checkbox:checked").each(function() {
                selectedServiceIds.push($(this).val());
            });
            $("#responseMessage").html("");
            if(selectedServiceIds.length > 0 && couponCode){
                $.ajax({
                    type: "POST",
                    url: "{{ route('apply.coupon') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        coupon_code: couponCode,
                        selected_service_ids: selectedServiceIds
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
                $("#responseMessage").append('<p class="coupon-message alert alert-danger">There is error with services or coupon input.</p>');
            }
            setTimeout(function() {
                $(".coupon-message").css('display', 'none');
            },6000);

        });
    });
</script> --}}
<script>
    function searchSelectOptions(selectElement, searchString) {
        // Convert the search string to lowercase for case-insensitive comparison
        const searchLower = searchString.toLowerCase();

        // Loop through all the options in the select menu
        for (let i = 0; i < selectElement.options.length; i++) {
            const option = selectElement.options[i];
            const optionValue = option.value.toLowerCase();

            // Check if the search string matches the option value exactly or partially
            if (optionValue === searchLower || optionValue.includes(searchLower)) {
                // Match found, you can perform your desired action here
                // console.log(`Found a match: ${option.value}`);
                return true; // Return true if you want to stop searching after the first match
            }
        }

        // No match found
        // console.log("No match found");
        return false;
    }

    function fillFormAddressFields(place) {
        const buildingNameField = document.getElementById('buildingName');
        const landmarkField = document.getElementById('landmark');
        const areaField = document.getElementById('area');
        const districtField = document.getElementById('district');
        const flatVillaField = document.getElementById('flatVilla');
        const streetField = document.getElementById('street');
        const cityField = document.getElementById('city');
        const latitudeField = document.getElementById('latitude');
        const longitudeField = document.getElementById('longitude');
        const searchField = document.getElementById("searchField");

        const addressComponents = place.address_components;
        // const selectElement = document.getElementById('mySelect');
        // const searchString = "OPT";
        // searchSelectOptions(selectElement, searchString);
        for (let i = 0; i < addressComponents.length; i++) {
            const component = addressComponents[i];
            const types = component.types;

            if (types.includes('premise')) {
                buildingNameField.value = component.long_name;
            } else if (types.includes('point_of_interest')) {
                landmarkField.value = component.long_name;
            } else if (types.includes('neighborhood') || types.includes('sublocality')) {
                areaField.value = component.long_name;
                districtField.value = component.long_name;
            } else if (types.includes('street_number')) {
                flatVillaField.value = component.long_name;
            } else if (types.includes('route')) {
                streetField.value = component.long_name;
            } else if (types.includes('locality')) {
                cityField.value = component.long_name;
            }
            latitudeField.value = place.geometry.location.lat();
            longitude.value = place.geometry.location.lng();
        }
        searchField.value = place["formatted_address"];
    }

    function initMap() {
        $('.location-search-wrapper').show();
        initAutocompleteLocal();
    }

    $(document).ready(function() {
        $("#manualLocationButton").click(function() {
            $("#locationPopup").modal('show');
        });
    });

    function initAutocompleteLocal() {
        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('searchField'));
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();

            if (!place.geometry) {
                return;
            }

            if (marker) {
                marker.setMap(null);
            }

            map.setCenter(place.geometry.location);
            placeMarker(place.geometry.location);

            fillFormAddressFields(place);
        });
    }
    $(document).on('change', '#zone', function() {
        $('#area').val($(this).val());
    });

    $(document).on('change', '#area', function() {
        $('#zone').val($(this).val());
    });
</script>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>
@endsection