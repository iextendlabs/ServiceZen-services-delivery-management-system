@extends('site.layout.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-semibold text-gray-900">Profile</h2>
            @if (auth()->user()->hasRole('Affiliate'))
                <a href="/affiliate_dashboard" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition">Affiliate Dashboard</a>
            @endif
        </div>

        @if ($message = Session::get('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4">
                <p class="text-sm text-green-700">{{ $message }}</p>
            </div>
        @endif

        @if(isset(Auth::user()->affiliate_program) && Auth::user()->affiliate_program == 0)
            <div class="mb-4 rounded-md bg-yellow-50 p-4">
                <p class="text-sm text-yellow-700">Your request to join the affiliate program has been submitted and sent to the administrator for review.</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-4">
                <p class="font-semibold text-red-800">Whoops! There were some problems with your input.</p>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customerProfile.update', auth()->user()->id) }}" method="POST" id="customer-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar -->
                <aside class="col-span-1 bg-white border rounded-lg p-6 shadow-sm">
                    <div class="flex flex-col items-center">
                        <div class="w-28 h-28 rounded-full bg-indigo-500 flex items-center justify-center text-white text-3xl font-bold">{{ strtoupper(substr($user->name,0,1) ?? 'U') }}</div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="button" id="add-address-btn" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"> <i class="fas fa-plus"></i> Add Address</button>
                        <a href="#coupon-list" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 rounded-md"> View Coupons</a>
                    </div>
                </aside>

                <!-- Main content -->
                <div class="col-span-1 lg:col-span-3 space-y-6">
                    <!-- Addresses Card -->
                    <section class="bg-white border rounded-lg p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Addresses</h3>
                        </div>

                        <div class="mt-4">
                            <table class="min-w-full table-auto" id="address-table">
                                <thead class="bg-gray-50">
                                    <tr class="text-left text-sm text-gray-600">
                                        <th class="px-3 py-2">Building</th>
                                        <th class="px-3 py-2">Area</th>
                                        <th class="px-3 py-2">Landmark</th>
                                        <th class="px-3 py-2">Flat</th>
                                        <th class="px-3 py-2">Street</th>
                                        <th class="px-3 py-2">City</th>
                                        <th class="px-3 py-2">District</th>
                                        <th class="px-3 py-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="address-body" class="divide-y">
                                    {{-- Filled by JS --}}
                                </tbody>
                            </table>
                        </div>

                        <!-- Address input (toggle) -->
                        <div id="address-input-section" style="display:none;" class="mt-6 border-t pt-4">
                            <h4 class="text-sm font-medium text-gray-800">Address Input</h4>
                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm text-gray-700">Building Name</label>
                                    <input type="text" id="buildingName" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="Building Name">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700">Area</label>
                                    <select id="customerArea" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm">
                                        <option value="">-- Select Zone -- </option>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone }}">{{ $zone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700">Landmark</label>
                                    <input type="text" id="landmark" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="Landmark">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700">Flat / Villa</label>
                                    <input type="text" id="flatVilla" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="Flat / Villa">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700">Street</label>
                                    <input type="text" id="street" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="Street">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700">City</label>
                                    <input type="text" id="city" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="City">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700">District</label>
                                    <input type="text" id="district" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="District">
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end gap-3">
                                <button type="button" id="save-address-btn" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save</button>
                                <button type="button" id="cancel-btn" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- Personal Info Card -->
                    <section class="bg-white border rounded-lg p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-700">Name <span class="text-red-500">*</span></label>
                                <input required type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="Name" value="{{ old('name',$user->name) }}">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Email <span class="text-red-500">*</span></label>
                                <input required type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="abc@gmail.com" value="{{ old('email',$user->email) }}">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Password</label>
                                <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="Password">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Confirm Password</label>
                                <input type="password" name="confirm-password" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" placeholder="Confirm Password">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                                <input id="number_country_code" type="hidden" name="number_country_code" />
                                <input id="number" required type="tel" name="number" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" value="{{ optional($user->customerProfiles->first())->number ?? '' }}" />
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Whatsapp Number <span class="text-red-500">*</span></label>
                                <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                                <input required type="tel" name="whatsapp" id="whatsapp" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" value="{{ optional($user->customerProfiles->first())->whatsapp ?? null }}">
                            </div>
                        </div>

                        <div class="mt-4">
                            <span class="block text-sm text-gray-700">Gender <span class="text-red-500">*</span></span>
                            <div class="mt-2 flex items-center gap-6">
                                <label class="inline-flex items-center gap-2">
                                    <input class="form-radio text-indigo-600" type="radio" name="gender" id="genderMale" required value="Male" {{ optional($user->customerProfiles->first())->gender === 'Male' ? 'checked' : '' }}> <span class="text-sm">Male</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input class="form-radio text-indigo-600" type="radio" name="gender" id="genderFemale" required value="Female" {{ optional($user->customerProfiles->first())->gender === 'Female' ? 'checked' : '' }}> <span class="text-sm">Female</span>
                                </label>
                            </div>
                        </div>

                        <span class="invalid-feedback text-right" id="gender-error" role="alert" style="display: none;">
                            <strong>Sorry, No Male Services Listed in Our Store.</strong>
                        </span>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update</button>
                        </div>
                    </section>

                    @if(!auth()->user()->hasRole("Affiliate"))
                        <section class="bg-white border rounded-lg p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">Join Affiliate</h3>
                            <div class="mt-4 md:flex md:items-center md:gap-3">
                                <input type="text" name="affiliate_code" id="affiliate_code" class="mt-1 block w-full md:flex-1 rounded-md border-gray-200 shadow-sm" placeholder="Affiliate Code" value="{{ $affiliate_code }}">
                                <button type="button" id="applyAffiliateBtn" class="mt-2 md:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Join Affiliate</button>
                            </div>
                            <div id="responseMessage" class="mt-3"></div>
                        </section>
                    @endif

                    <!-- Coupon List -->
                    <section id="coupon-list" class="bg-white border rounded-lg p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Coupon List</h3>
                        <div class="mt-4">
                            @if (isset($user->coupons))
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-auto">
                                        @if ($coupon_code)
                                            <tr>
                                                <th colspan="4" class="text-left py-2">Your Selected Coupon code id {{ $coupon_code }}</th>
                                            </tr>
                                        @endif
                                        <tr class="text-left text-sm text-gray-600">
                                            <th class="px-3 py-2">Name</th>
                                            <th class="px-3 py-2">Code</th>
                                            <th class="px-3 py-2">Discount</th>
                                            <th class="px-3 py-2">Action</th>
                                        </tr>
                                        @if (count($user->coupons) != 0)
                                            @foreach ($user->coupons as $coupons)
                                                <tr class="border-t">
                                                    <td class="px-3 py-2">{{ $coupons->name }}</td>
                                                    <td class="px-3 py-2">{{ $coupons->code }}</td>
                                                    <td class="px-3 py-2">
                                                        @if ($coupons->type == 'Percentage')
                                                            {{ $coupons->discount }} %
                                                        @else
                                                            @currency($coupons->discount,false)
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <a class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-600 text-white rounded-md" href="/applyCoupon?coupon={{ $coupons->code }}">Apply</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">There are no Coupon Assigned</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </form>
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
                            <td class="px-3 py-2">${address.buildingName}</td>
                            <td class="px-3 py-2">${address.area}</td>
                            <td class="px-3 py-2">${address.landmark}</td>
                            <td class="px-3 py-2">${address.flatVilla}</td>
                            <td class="px-3 py-2">${address.street}</td>
                            <td class="px-3 py-2">${address.city}</td>
                            <td class="px-3 py-2">${address.district}</td>
                            <td class="px-3 py-2"><button type="button" class="btn btn-danger remove-btn">Remove</button></td>
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
    <script src="{{ asset('js/checkout.js') }}?v={{ config('app.version') }}"></script>
@endsection
