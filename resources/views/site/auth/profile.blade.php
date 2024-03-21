@extends('site.layout.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-5 text-center">
                <h2>Profile</h2>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
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
        <form action="{{ route('customerProfile.update', auth()->user()->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row bg-light py-3 mb-4 card">
                <div class="col-md-12 text-center">
                    <br>
                    <h3><strong>Address</strong></h3>
                    <hr>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Building Name:</strong>
                        <input required type="text" name="buildingName" id="buildingName" class="form-control"
                            placeholder="Building Name" value="{{ $user->customerProfile->buildingName ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Flat / Villa:</strong>
                        <input required type="text" name="flatVilla" id="flatVilla" class="form-control"
                            placeholder="Flat / Villa" value="{{ $user->customerProfile->flatVilla ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Street:</strong>
                        <input required type="text" name="street" id="street" class="form-control"
                            placeholder="Street" value="{{ $user->customerProfile->street ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>District:</strong>
                        <input required type="text" name="district" id="district" class="form-control"
                            placeholder="District" value="{{ $user->customerProfile->district ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Area:</strong>
                        <input required type="text" name="area" class="form-control" placeholder="Area"
                            value="{{ $user->customerProfile->area ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Landmark:</strong>
                        <input required type="text" name="landmark" id="landmark" class="form-control"
                            placeholder="Landmark" value="{{ $user->customerProfile->landmark ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>City:</strong>
                        <input required type="text" name="city" id="city" class="form-control" placeholder="City"
                            value="{{ $user->customerProfile->city ?? null }}">
                    </div>
                </div>
            </div>
            <div class="row bg-light py-3 mb-4 card">
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
                        <input id="number" type="tel" name="number" class="form-control"
                            value="{{ $user->customerProfile->number ?? null }}" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input required type="tel" name="whatsapp" id="whatsapp" class="form-control"
                            value="{{ $user->customerProfile->whatsapp ?? null }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Gender:</strong><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderMale"
                                value="Male"
                                {{ $user->customerProfile && $user->customerProfile->gender == 'Male' ? 'checked' : '' }}>
                            <label class="form-check-label" for="genderMale">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderFemale"
                                value="Female"
                                {{ $user->customerProfile && $user->customerProfile->gender == 'Female' ? 'checked' : '' }}>
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
        <div class="row bg-light py-3 mb-4 card">
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
        <div class="row bg-light py-3 mb-4 card">
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
                                            @currency($coupons->discount)
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
