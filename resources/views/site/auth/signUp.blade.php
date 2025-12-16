@extends('site.layout.app')
@section('content')
    <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
        <div class="max-w-3xl w-full grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="hidden md:flex flex-col justify-center bg-gradient-to-tr from-indigo-600 to-indigo-400 rounded-xl p-8 text-white">
                <h3 class="text-2xl font-semibold">Join Lipslay</h3>
                <p class="mt-4 text-sm">Create your account to book services, manage appointments and grow your business. Fast, secure and easy.</p>
                <ul class="mt-6 space-y-2 text-sm">
                    <li>• Simple booking flow</li>
                    <li>• Secure payments</li>
                    <li>• Dedicated support</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Create an account</h2>
                    <p class="text-sm text-gray-500 mt-1">Get started by filling the information below</p>
                </div>

                <form method="POST" action="{{ route('customer.post-registration') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" />
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" />
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                            <input id="password" name="password" type="password" required class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" />
                            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password-confirm" class="block text-sm font-medium text-gray-700">Confirm Password <span class="text-red-500">*</span></label>
                            <input id="password-confirm" name="password_confirmation" type="password" required class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" />
                        </div>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Login as <span class="text-red-500">*</span></label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm">
                            <option value="customer">Customer</option>
                            <option value="Affiliate" @if ($type === 'Affiliate') selected @endif>Affiliate</option>
                            <option value="Freelancer" @if ($type === 'Freelancer') selected @endif>Freelancer</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="number" class="block text-sm font-medium text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                            <input id="number_country_code" type="hidden" name="number_country_code" />
                            <input id="number" name="number" type="tel" required value="{{ old('number') }}" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" />
                            @error('number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-gray-700">Whatsapp <span class="text-red-500">*</span></label>
                            <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                            <input id="whatsapp" name="whatsapp" type="tel" required value="{{ old('whatsapp') }}" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" />
                            @error('whatsapp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="sub_title">
                        <label for="sub_title" class="block text-sm font-medium text-gray-700">Sub Title / Designation <span class="text-red-500">*</span></label>
                        <select id="sub_title" name="sub_titles[]" multiple class="select2 mt-1 block w-full rounded-md border-gray-200 shadow-sm" data-live-search="true" data-actions-box="true">
                            @foreach ($sub_titles as $sub_title)
                                <option value="{{ $sub_title->id }}">{{ $sub_title->name }}</option>
                            @endforeach
                        </select>
                        @error('sub_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="membership_plan_id">
                        <label for="membership_plan_id" class="block text-sm font-medium text-gray-700">Membership Plan <span class="text-red-500">*</span></label>
                        <select name="membership_plan_id" id="membership_plan_id" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm">
                            <option></option>
                            @foreach ($membership_plans as $membership_plan)
                                <option data-type="{{ $membership_plan->type }}" value="{{ $membership_plan->id }}">{{ $membership_plan->plan_name }} (@currency($membership_plan->membership_fee,true))</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="affiliate_code">
                            <label for="affiliate_code" class="block text-sm font-medium text-gray-700">Affiliate Code</label>
                            <input id="affiliate_code" name="affiliate_code" type="text" value="{{ $affiliate_code ? $affiliate_code : old('affiliate_code') }}" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" {{ $affiliate_code ? 'readonly' : '' }} />
                            @error('affiliate_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="parent_affiliate_code hidden">
                            <label class="block text-sm font-medium text-gray-700">Parent Affiliate Code</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gender <span class="text-red-500">*</span></label>
                        <div class="mt-2 space-x-4">
                            @if($gender_permission === 'Male')
                                <label class="inline-flex items-center"><input class="rounded border-gray-200" type="radio" name="gender" id="genderMale" value="Male" {{ old('gender') == 'Male' ? 'checked' : '' }} required><span class="ml-2">Male</span></label>
                                <div class="mt-2 text-sm text-red-600">Sorry, No Female Services Listed in Our Store.</div>
                            @elseif ($gender_permission === 'Female')
                                <label class="inline-flex items-center"><input class="rounded border-gray-200" type="radio" name="gender" id="genderFemale" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }} required><span class="ml-2">Female</span></label>
                                <div class="mt-2 text-sm text-red-600">Sorry, No Male Services Listed in Our Store.</div>
                            @elseif($gender_permission === 'Both')
                                <label class="inline-flex items-center"><input class="rounded border-gray-200" type="radio" name="gender" id="genderMale" value="Male" {{ old('gender') == 'Male' ? 'checked' : '' }} required><span class="ml-2">Male</span></label>
                                <label class="inline-flex items-center"><input class="rounded border-gray-200 ml-4" type="radio" name="gender" id="genderFemale" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }} required><span class="ml-2">Female</span></label>
                            @endif
                            @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Register</button>
                    </div>
                </form>
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

            // Initialize visibility according to current selection
            handleTypeChange($("#type").val());
        });
    </script>
    <script>
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
