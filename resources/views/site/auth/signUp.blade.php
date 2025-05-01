@extends('site.layout.app')
@section('content')
<style>
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        /* Match Bootstrap form-control height */
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single:focus {
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        /* Bootstrap focus shadow */
    }

    .select2-container .select2-search__field {
        width: 100% !important;
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }
</style>
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
                                        <option value="Affiliate" @if ($type === 'Affiliate') selected @endif>Affiliate</option>
                                        <option value="Freelancer" @if ($type === 'Freelancer') selected @endif>Freelancer</option>
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
                                    class="col-md-4 col-form-label text-md-end"><span style="color: red;">*</span>Sub Title / Designation:</label>

                                <div class="col-md-6">
                                    <select id="sub_title" class="form-control selectpicker" name="sub_titles[]"
                                    multiple data-live-search="true" data-actions-box="true">
                                        @foreach ($sub_titles as $sub_title)
                                            <option value="{{ $sub_title->id }}">
                                                {{ $sub_title->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                            <option></option>
                                            @foreach ($membership_plans as $membership_plan)
                                                <option data-type="{{ $membership_plan->type }}" value="{{ $membership_plan->id }}">{{ $membership_plan->plan_name }} (@currency($membership_plan->membership_fee,true))</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="row mb-3">
                                <label for="affiliate_code" class="col-md-4 col-form-label text-md-end affiliate_code">{{ __('Affiliate Code') }}</label>
                                <label for="affiliate_code" class="col-md-4 col-form-label text-md-end parent_affiliate_code">Parent Affiliate Code</label>

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
            $('.select2').select2({
                placeholder: 'Search...',
                allowClear: true,
                width: '100%',
                language: {
                    searching: function() {
                        return "Type to search...";
                    }
                }
            }).on('select2:open', function() {
                setTimeout(() => {
                    let searchBox = document.querySelector('.select2-search__field');
                    if (searchBox) {
                        searchBox.placeholder = "Type to search...";
                        searchBox.focus();
                    }
                }, 100);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            handleTypeChange($("#type").val());
        });
    
        $(document).on("change", "#type", function() {
            handleTypeChange($(this).val());
        });
    
        function handleTypeChange(selectedValue) {
            if (selectedValue == "Freelancer") {
                $(".sub_title").show();
                $("#sub_title").attr("required", true);
            } else {
                $(".sub_title").hide();
                $("#sub_title").attr("required", false);
            }

            if (selectedValue == "Affiliate") {
                $(".affiliate_code").hide();
                $(".parent_affiliate_code").show();
            } else {
                $(".affiliate_code").show();
                $(".parent_affiliate_code").hide();
            }
            
            if (selectedValue == "customer") {
                $(".membership_plan_id").hide();
                $("#membership_plan_id").attr("required", false);
            } else {
                $(".membership_plan_id").show();
                $("#membership_plan_id").attr("required", true);
                filterMembershipPlans(selectedValue);
            }
            $("#membership_plan_id").val('');
        }

        function filterMembershipPlans(selectedValue) {
            $("#membership_plan_id option").each(function() {
                if ($(this).data("type") == selectedValue) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    </script>
@endsection
