@extends('site.layout.app')

@section('content')
    <div class="container">
        <div class="row py-5">
            <div class="col-md-6 d-flex align-items-center">
                <h2>Profile</h2>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">
                @if (auth()->user()->hasRole('Affiliate'))
                    <a class="btn btn-success" href="/affiliate_dashboard">Affiliate Dashborad</a>
                @endif
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
            </div>
        @endif
        @if(isset(Auth::user()->affiliate_program) && Auth::user()->affiliate_program == 0)
        <div class="alert alert-warning">
            <span>Your request to join the affiliate program has been submitted and sent to the administrator for review.</span>
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
        <form action="{{ route('customerProfile.update', auth()->user()->id) }}" method="POST" id="customer-form">
            @csrf
            @method('PUT')
            <div class="row bg-light">
                <div class="col-md-12 text-center">
                    <br>
                    <h3><strong>Addresses</strong></h3>
                    <hr>
                </div>
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="address-table">
                        <thead>
                            <tr>
                                <th>Building Name</th>
                                <th>Area</th>
                                <th>Landmark</th>
                                <th>Flat Villa</th>
                                <th>Street</th>
                                <th>City</th>
                                <th>District</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="address-body">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        
            <div class="row bg-light" id="address-input-section" style="display:none;">
                <div class="col-md-12 text-center">
                    <h3><strong>Address Input</strong></h3>
                    <hr>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="buildingName">Building Name</label>
                        <input type="text" class="form-control" id="buildingName" placeholder="Building Name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customerArea">Area</label>
                        <input type="text" class="form-control" id="customerArea" placeholder="Area">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="landmark">Landmark</label>
                        <input type="text" class="form-control" id="landmark" placeholder="Landmark">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="flatVilla">Flat / Villa</label>
                        <input type="text" class="form-control" id="flatVilla" placeholder="Flat / Villa">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="street">Street</label>
                        <input type="text" class="form-control" id="street" placeholder="Street">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" placeholder="City">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="district">District</label>
                        <input type="text" class="form-control" id="district" placeholder="District">
                    </div>
                </div>
                <div class="col-md-12 text-right py-3">
                    <button type="button" class="btn btn-success" id="save-address-btn"><i class="fas fa-save"></i></button>
                    <button type="button" class="btn btn-secondary" id="cancel-btn"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="row bg-light">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-primary" id="add-address-btn"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="row bg-light py-3 mb-4">
                <div class="col-md-12 text-center">
                    <br>
                    <h3><strong>Personal information</strong></h3>
                    <hr>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input required type="text" name="name" id="name" class="form-control" placeholder="Name"
                            value="{{ $user->name }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Email:</strong>
                        <input required type="email" name="email" id="email" class="form-control"
                            placeholder="abc@gmail.com" value="{{ $user->email }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><strong>Password:</strong>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control"
                            placeholder="Confirm Password">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Phone Number:</strong>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input id="number" required type="tel" name="number" class="form-control" 
                            value="{{ optional($user->customerProfiles->first())->number ?? '' }}" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input required type="tel" name="whatsapp" id="whatsapp" class="form-control"
                            value="{{ optional($user->customerProfiles->first())->whatsapp ?? null }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Gender:</strong><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderMale" required
                                value="Male"
                                {{ optional($user->customerProfiles->first())->gender === 'Male' ? 'checked' : '' }}>
                            <label class="form-check-label" for="genderMale">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderFemale" required
                                value="Female"
                                {{ optional($user->customerProfiles->first())->gender === 'Female' ? 'checked' : '' }}>
                            <label class="form-check-label" for="genderFemale">Female</label>
                        </div>
                    </div>
                </div>
                <span class="invalid-feedback text-right" id="gender-error" role="alert" style="display: none;">
                    <strong>Sorry, No Male Services Listed in Our Store.</strong>
                </span>
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
        @if(!auth()->user()->hasRole("Affiliate"))
        <div class="row bg-light py-3 mb-4">
            <div class="col-md-12 text-center">
                <br>
                <h3><strong>Join Affiliate</strong></h3>
                <hr>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <strong>Affiliate Code:</strong>
                    <div class="input-group">
                        <input type="text" name="affiliate_code" id="affiliate_code" class="form-control"
                            placeholder="Affiliate Code" value="{{ $affiliate_code }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="applyAffiliateBtn">Join Affiliate</button>
                        </div>
                    </div>
                    <div id="responseMessage"></div>
                </div>
            </div>
        </div>
        @endif
        <div class="row bg-light py-3 mb-4">
            <div class="col-md-12 text-center">
                <br>
                <h3><strong>Coupon List</strong></h3>
                <hr>
            </div>
            <div class="col-md-12">
                @if (isset($user->coupons))
                    <table class="table table-striped table-bordered album">
                        @if ($coupon_code)
                            <tr>
                                <th colspan="4">Your Selected Coupon code id {{ $coupon_code }}</th>
                            </tr>
                        @endif
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Action</th>
                        </tr>
                        @if (count($user->coupons) != 0)
                            @foreach ($user->coupons as $coupons)
                                <tr>
                                    <td>{{ $coupons->name }}</td>
                                    <td>{{ $coupons->code }}</td>
                                    <td>
                                        @if ($coupons->type == 'Percentage')
                                            {{ $coupons->discount }} %
                                        @else
                                            @currency($coupons->discount,false)
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-primary" href="/applyCoupon?coupon={{ $coupons->code }}"><i
                                                class="fas fa-gift"></i> Apply</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="text-center">
                                <td colspan="4">There are no Coupon Assigned</td>
                            </tr>
                        @endif
                    </table>
                @endif
            </div>
        </div>
    </div>
    <script>
        let addresses = [];
    
        @foreach ($user->customerProfiles as $customerProfile)
        addresses.push({
            buildingName: '{{ $customerProfile->buildingName }}',
            area: '{{ $customerProfile->area }}',
            landmark: '{{ $customerProfile->landmark }}',
            flatVilla: '{{ $customerProfile->flatVilla }}',
            street: '{{ $customerProfile->street }}',
            city: '{{ $customerProfile->city }}',
            district: '{{ $customerProfile->district }}'
        });
        @endforeach
    
        $(document).ready(function() {
            if (addresses.length === 0) {
                $('#address-input-section').show();
                $('#add-address-btn').hide();
                $('#cancel-btn').hide();
            } else {
                addresses.forEach(function(address) {
                    addAddressToTable(address);
                });
            }
        });
    
        $('#add-address-btn').on('click', function() {
            $('#address-input-section').show();
                $('#cancel-btn').show();
                $('#add-address-btn').hide();
            clearAddressInputs();
        });
    
        $('#cancel-btn').on('click', function() {
            $('#address-input-section').hide();
            $('#add-address-btn').show();
            clearAddressInputs();
        });
    
        function saveAddress() {
            const address = {
                buildingName: $('#buildingName').val(),
                area: $('#customerArea').val(),
                landmark: $('#landmark').val(),
                flatVilla: $('#flatVilla').val(),
                street: $('#street').val(),
                city: $('#city').val(),
                district: $('#district').val()
            };
    
            if (!address.buildingName || !address.area || !address.landmark || !address.flatVilla || !address.street || !address.city || !address.district) {
                alert("All fields are required!");
                return false;
            }
    
            addresses.push(address);
            addAddressToTable(address);
    
            $('#address-input-section').hide();
            $('#add-address-btn').show();
            clearAddressInputs();
            return true;
        }
    
        $('#save-address-btn').on('click', function() {
            saveAddress();
        });
    
        function addAddressToTable(address) {
            const row = `<tr>
                            <td>${address.buildingName}</td>
                            <td>${address.area}</td>
                            <td>${address.landmark}</td>
                            <td>${address.flatVilla}</td>
                            <td>${address.street}</td>
                            <td>${address.city}</td>
                            <td>${address.district}</td>
                            <td><button type="button" class="btn btn-danger remove-btn">Remove</button></td>
                            <input type="hidden" name="addresses[]" value='${JSON.stringify(address)}'>
                        </tr>`;
    
            $('#address-body').append(row);
        }
    
        $('#address-body').on('click', '.remove-btn', function() {
            const rowIndex = $(this).closest('tr').index();
            addresses.splice(rowIndex, 1);
            $(this).closest('tr').remove();
        });
    
        function clearAddressInputs() {
            $('#buildingName').val('');
            $('#customerArea').val('');
            $('#landmark').val('');
            $('#flatVilla').val('');
            $('#street').val('');
            $('#city').val('');
            $('#district').val('');
        }
    
        $('#customer-form').on('submit', function(e) {
            if ($('#address-input-section').is(':visible')) {
                const success = saveAddress();
                if (!success) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#applyAffiliateBtn").click(function() {
                var affiliateCode = $("#affiliate_code").val();
    
                $("#responseMessage").html("");
                if(affiliateCode){
                    $.ajax({
                        type: "POST",
                        url: "{{ route('apply.affiliate') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            affiliate_code: affiliateCode,
                        },
                        success: function(response) {
                            if(response.error){
                                $("#affiliate_code").val("");
                                $("#responseMessage").append('<p class="affiliate-message alert alert-danger">' + response.error + '</p>');
                            }else{
                                $("#responseMessage").append('<p class="affiliate-message alert alert-success">' + response.message + '</p>');
                            }
                        },
                        error: function(error) {
                            console.log("Error:", error);
                        }
                    });
                }else{
                    $("#responseMessage").append('<p class="affiliate-message alert alert-danger">There is error with affiliate input.</p>');
                }
                setTimeout(function() {
                    $(".affiliate-message").css('display', 'none');
                },6000);
    
            });
        });
    </script>
    <script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>
@endsection
