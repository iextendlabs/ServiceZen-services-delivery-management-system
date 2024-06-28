@extends('site.layout.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Register') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('customer.post-registration') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" autocomplete="name" required autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" autocomplete="email" required>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        autocomplete="new-password" required>

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password-confirm"
                                    class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" autocomplete="new-password" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="type" class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>Login as:</label>

                                <div class="col-md-6">
                                    <select name="type" id="type" class="form-control">
                                        <option value="customer">Customer</option>
                                        <option value="affiliate" @if ($type === 'affiliate') selected @endif>Affiliate</option>
                                        <option value="freelancer" @if ($type === 'freelancer') selected @endif>Freelancer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="number" class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>Phone Number</label>

                                <div class="col-md-6">
                                    <input id="number_country_code" type="hidden" name="number_country_code" />
                                    <input id="number" type="tel" required
                                        class="form-control @error('number') is-invalid @enderror" name="number"
                                        value="{{ old('number') }}" autocomplete="number">

                                    @error('number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="whatsapp" class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>Whatsapp
                                    whatsapp</label>

                                <div class="col-md-6">
                                    <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                                    <input id="whatsapp" type="tel" required
                                        class="form-control @error('whatsapp') is-invalid @enderror" name="whatsapp"
                                        value="{{ old('whatsapp') }}" autocomplete="whatsapp">

                                    @error('whatsapp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3 sub_title">
                                <label for="sub_title"
                                    class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>Sub Title / Designation</label>

                                <div class="col-md-6">
                                    <input id="sub_title" type="text"
                                        class="form-control @error('sub_title') is-invalid @enderror"
                                        name="sub_title" value="{{ old('sub_title') }}"
                                        autocomplete="sub_title">
                                    @error('sub_title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3 membership_plan_id">
                                <label for="membership_plan_id"
                                    class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>Membership Plan</label>
                                    <div class="col-md-6">
                                        <select name="membership_plan_id" id="membership_plan_id" class="form-control">
                                            @foreach ($membership_plans as $membership_plan)
                                                <option value="{{$membership_plan->id}}">{{$membership_plan->plan_name}} (@currency($membership_plan->membership_fee))</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="row mb-3 parent_affiliate_code">
                                <label for="parent_affiliate_code"
                                    class="col-md-4 col-form-label text-md-end">Parent Affiliate</label>

                                <div class="col-md-6">
                                    <input id="parent_affiliate_code" type="text"
                                        class="form-control @error('parent_affiliate_code') is-invalid @enderror"
                                        name="parent_affiliate_code" 
                                        autocomplete="parent_affiliate_code">
                                    @error('parent_affiliate_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3 affiliate_code">
                                <label for="affiliate_code"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Affiliate Code') }}</label>

                                <div class="col-md-6">
                                    <input id="affiliate_code" type="text"
                                        class="form-control @error('affiliate_code') is-invalid @enderror"
                                        name="affiliate_code" {{ $affiliate_code ? 'readonly' : null }}
                                        value="{{ $affiliate_code ? $affiliate_code : old('affiliate_code') }}"
                                        autocomplete="affiliate_code">

                                    @error('affiliate_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>{{ __('Gender') }}</label>

                                <div class="col-md-6">
                                    @if($gender_permission === 'Male')
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('gender') is-invalid @enderror"
                                                   type="radio" name="gender" id="genderMale" value="Male"
                                                   {{ old('gender') == 'Male' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="genderMale">
                                                {{ __('Male') }}
                                            </label>
                                        </div>
                                        <br>
                                        <strong class="text-danger">Sorry, No Female Services Listed in Our Store.</strong>
                                    @elseif ($gender_permission === 'Female')
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('gender') is-invalid @enderror"
                                                   type="radio" name="gender" id="genderFemale" value="Female"
                                                   {{ old('gender') == 'Female' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="genderFemale">
                                                {{ __('Female') }}
                                            </label>
                                        </div>
                                        <br>
                                        <strong class="text-danger">Sorry, No Male Services Listed in Our Store.</strong>
                                    @elseif($gender_permission === 'Both')
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('gender') is-invalid @enderror"
                                                   type="radio" name="gender" id="genderMale" value="Male"
                                                   {{ old('gender') == 'Male' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="genderMale">
                                                {{ __('Male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('gender') is-invalid @enderror"
                                                   type="radio" name="gender" id="genderFemale" value="Female"
                                                   {{ old('gender') == 'Female' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="genderFemale">
                                                {{ __('Female') }}
                                            </label>
                                        </div>
                                    @endif
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            handleTypeChange($("#type").val());
        });
    
        $(document).on("change", "#type", function() {
            handleTypeChange($(this).val());
        });
    
        function handleTypeChange(selectedValue) {
            if (selectedValue == "freelancer") {
                $(".sub_title").show();
                $("#sub_title").attr("required", true);
            } else {
                $(".sub_title").hide();
                $("#sub_title").attr("required", false);
            }

            if (selectedValue == "affiliate") {
                $(".membership_plan_id").show();
                $(".parent_affiliate_code").show();
                $("#membership_plan_id").attr("required", true);
            } else {
                $(".membership_plan_id").hide();
                $(".parent_affiliate_code").hide();
                $("#membership_plan_id").attr("required", false);
            }
            
            if (selectedValue == "customer") {
                $(".affiliate_code").show();
            } else {
                $(".affiliate_code").hide();
            }
        }
    </script>
@endsection
