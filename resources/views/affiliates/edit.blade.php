@extends('layouts.app') 
@php
    $category_row = 0;
@endphp
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Edit Affiliate</h2>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br /><br />
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('affiliates.update', $affiliate->id) }}" method="POST">
            <input type="hidden" value="{{ $affiliate->affiliate->id ?? '' }}" name="affiliate_id" />
            <input type="hidden" value="{{ $affiliate_join }}" name="affiliate_join" />
            <input type="hidden" name="url" value="{{ url()->previous() }}" />
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{ old( 'name', $affiliate->name ) }}" class="form-control"
                            placeholder="Name" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Email:</strong>
                        <input type="email" name="email" value="{{ old('email', $affiliate->email) }}" class="form-control"
                            placeholder="abc@gmail.com" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control" placeholder="Password" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control"
                            placeholder="Confirm Password" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $affiliate->affiliate->status ?? '') == "1" ? 'selected' : '' }}>Enable</option>
                            <option value="0"  {{ old('status', $affiliate->affiliate->status ?? '') == "0" ? 'selected' : '' }}>Disable</option>
                        </select>
                    </div>
                </div>
                <hr>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Phone Number:</strong>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input type="tel" id="number" name="number" value="{{ old( 'number' ,$affiliate->affiliate->number ?? '' ) }}"
                            class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Whatsapp Number:</strong>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input type="tel" id="whatsapp" name="whatsapp"
                            value="{{ old('whatsapp', $affiliate->affiliate->whatsapp ?? '') }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Code:</strong>
                        <input type="text" name="code" value="{{ old( 'code', $affiliate->affiliate->code ?? '' ) }}"
                            class="form-control" placeholder="Code" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Commission:</strong>
                        <input type="number" name="commission" value="{{ old( 'commission' , $affiliate->affiliate->commission ?? '' )  }}"
                            class="form-control" placeholder="Commission In %" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Expire after days:</strong>
                        <input type="number" name="expire" class="form-control"
                            value="{{ old( 'expire' , $affiliate->affiliate->expire ?? '') }}" placeholder="Enter days like 20" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Fix Salary:</strong>
                        <input type="number" name="fix_salary" value="{{ old( 'fix_salary' , $affiliate->affiliate->fix_salary ?? '' ) }}"
                            class="form-control" placeholder="Fix Salary" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Parent Affiliate:</strong>
                        <select name="parent_affiliate_id" class="form-control">
                            <option value=""></option>
                            @foreach ($affiliates as $single_affiliate)
                                @if ($single_affiliate->affiliate->status == 1 && $single_affiliate->id !== $affiliate->id)
                                    <option value="{{ $single_affiliate->id }}"
                                        {{ old('parent_affiliate_id', $affiliate->affiliate->parent_affiliate_id ?? '') == $single_affiliate->id ? 'selected' : '' }}>
                                        {{ $single_affiliate->name }}
                                    </option>                                
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Parent Affiliate Commission:</strong>
                        <input type="number" name="parent_affiliate_commission" value="{{ old('parent_affiliate_commission', $affiliate->affiliate->parent_affiliate_commission ?? '') }}"
                            class="form-control" placeholder="Parent Affiliate Commission In %" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Membership Plan:</strong>
                        <select name="membership_plan_id" class="form-control">
                            <option value=""></option>
                            @foreach ($membership_plans as $membership_plan)
                                <option value="{{ $membership_plan->id }}" 
                                    {{ old('membership_plan_id', optional($affiliate->affiliate)->membership_plan_id) == $membership_plan->id ? 'selected' : '' }}>
                                    {{ $membership_plan->plan_name }} (AED{{ $membership_plan->membership_fee }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Categories base commission:</strong>
                        <table id="categoryTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Category Commission</th>
                                    <th>Services</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($affiliate->affiliateCategories)
                                    @foreach ($affiliate->affiliateCategories as $index => $affiliateCategory)
                                    <tr>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <select name='categories[{{ $index }}][category_id]' class="form-control category-select" required>
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}" 
                                                                @if($affiliateCategory->category_id == $category->id) selected @endif>
                                                                {{ $category->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="form-group d-flex">
                                                    <input type="number" name="categories[{{ $index }}][category_commission]" 
                                                        value="{{ $affiliateCategory->commission }}" class="form-control category-commission" 
                                                        placeholder="Commission" required min="1">
                                                    <select name="categories[{{ $index }}][commission_type]" class="form-control commission-type">
                                                        <option value="percentage" {{ $affiliateCategory->commission_type == 'percentage' ? 'selected' : '' }}>%</option>
                                                        <option value="fixed" {{ $affiliateCategory->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success add-service" data-category-row="{{ $index }}">
                                                <i class="fa fa-plus-circle"></i> Add Service
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-category">
                                                <i class="fa fa-minus-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="service-container-{{ $index }}">
                                        <td colspan="4">
                                            <div class="service-wrapper d-flex flex-wrap">
                                                @if($affiliateCategory->services)
                                                    @foreach ($affiliateCategory->services as $serviceIndex => $service)
                                                    <div class="service-box col-md-6 border-bottom mb-3 py-3">
                                                        <div class="form-group">
                                                            <select name="categories[{{ $index }}][services][{{ $serviceIndex }}][service_id]" 
                                                                class="form-control service-select select2" required>
                                                                <option value="">Select Service</option>
                                                                @foreach ($services as $serviceOption)
                                                                    <option value="{{ $serviceOption->id }}" 
                                                                        @if($service->service_id == $serviceOption->id) selected @endif>
                                                                        {{ $serviceOption->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group d-flex">
                                                            <input type="number" name="categories[{{ $index }}][services][{{ $serviceIndex }}][service_commission]" 
                                                                value="{{ $service->commission }}" class="form-control service-commission" required min="1">
                                                            <select name="categories[{{ $index }}][services][{{ $serviceIndex }}][commission_type]" class="form-control commission-type">
                                                                <option value="percentage" {{ $service->commission_type == 'percentage' ? 'selected' : '' }}>%</option>
                                                                <option value="fixed" {{ $service->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                            </select>
                                                        </div>
                                                        <button type="button" class="btn btn-danger remove-service"><i class="fa fa-minus-circle"></i></button>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button id="addCategoryBtn" onclick="addCategoryRow();" type="button" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
                    </div>
                </div>
                {{-- <div class="col-md-12">
                    <div class="form-group">
                        <strong>Expiry Date:</strong>
                        <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}" value={{ $affiliate->affiliate->expiry_date ?? "" }}>
                    </div>
                </div> --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Customer Display:</strong>
                        <select name="display_type" id="display_type" class="form-control">
                            <option value="1" @if ($affiliate->affiliate && $affiliate->affiliate->display_type == 1) selected @endif>Enable
                            </option>
                            <option value="0" @if ($affiliate->affiliate && $affiliate->affiliate->display_type == 0) selected @endif>Disable
                            </option>
                            <option value="2" @if ($affiliate->affiliate && $affiliate->affiliate->display_type == 2) selected @endif>Selected Customer
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12" style="display: none" id="customer">
                    @if (count($affiliateUser) > 0)
                        <div class="form-group @if (count($affiliateUser) > 6) scroll-div @endif">
                            <span style="color: red">*</span><strong>Select Customer To Display:</strong>
                            <input type="text" name="customer-search" id="customer-search" class="form-control"
                                placeholder="Search Customer By Name or Email" />
                            <table class="table table-striped table-bordered customer-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                                @foreach ($affiliateUser as $user)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="customer_checkbox"
                                                @if ($user->display == '1') checked @endif name="customerId[]"
                                                value="{{ $user->user_id }}"/>
                                        </td>
                                        <td>{{ $user->customer->name }}</td>
                                        <td>{{ $user->customer->email }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="form-group">
                            <span style="color: red">*</span><strong>Selected Customer:</strong>
                            <table class="table table-striped table-bordered selected-customer-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                                    @if ($affiliateUser->where('display', 1)->count() > 0)
                                        @foreach ($affiliateUser->where('display', 1) as $user)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="selected_customer_checkbox" checked
                                                        name="selectedCustomerId[]" value="{{ $user->user_id }}"/>
                                                </td>
                                                <td>{{ $user->customer->name }}</td>
                                                <td>{{ $user->customer->email }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                            </table>
                        </div>
                    @else
                        <div class="text-center">
                            <h4>There is no customer</h4>
                        </div>
                    @endif
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $('.service-select').select2();

            $('.category-select').each(function () {
                var categoryId = $(this).val();
                var categoryRow = $(this).closest('tr').next('tr').attr('id');
                var serviceWrapper = $(`#${categoryRow} .service-wrapper`);

                if (categoryId) {
                    serviceWrapper.find('.service-select').each(function () {
                        var selectedServiceId = $(this).val();
                        var dropdown = $(this);
                        
                        fetchServices(categoryId, dropdown, selectedServiceId);
                    });
                }
            });
        });
        var category_row = {{ $affiliate->affiliateCategories->count() ?? 0 }};
        

        function addCategoryRow() {
            var newRow = `
                <tr>
                    <td>
                        <div class="col-md-12">
                            <div class="form-group">
                                <select name='categories[${category_row}][category_id]' class="form-control category-select" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            <div class="form-group d-flex">
                                <input type="number" name="categories[${category_row}][category_commission]" class="form-control category-commission" placeholder="Commission" required min="1">
                                <select name="categories[${category_row}][commission_type]" class="form-control commission-type">
                                    <option value="percentage">%</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-success add-service" data-category-row="${category_row}">
                            <i class="fa fa-plus-circle"></i> Add Service
                        </button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-category">
                            <i class="fa fa-minus-circle"></i>
                        </button>
                    </td>
                </tr>
                <tr id="service-container-${category_row}">
                    <td colspan="4">
                        <div class="service-wrapper d-flex flex-wrap"></div>
                    </td>
                </tr>
            `;

            $('#categoryTable tbody').append(newRow);
            category_row++;
        }

        $(document).on('click', '.remove-category', function () {
            $(this).closest('tr').next('tr').remove();
            $(this).closest('tr').remove();
        });

        $(document).on('click', '.add-service', function () {
            var categoryRow = $(this).data('category-row');
            var serviceWrapper = $(`#service-container-${categoryRow} .service-wrapper`);
            var serviceIndex = serviceWrapper.find('.service-box').length;
            var categoryId = $(`select[name="categories[${categoryRow}][category_id]"]`).val();

            var newServiceRow = `
                <div class="service-box col-md-6 border-bottom mb-3 py-3">
                    <div class="form-group">
                        <select name="categories[${categoryRow}][services][${serviceIndex}][service_id]" class="form-control service-select" required>
                            <option value="">Select Service</option>
                        </select>
                    </div>
                    <div class="form-group d-flex">
                        <input type="number" name="categories[${categoryRow}][services][${serviceIndex}][service_commission]" class="form-control service-commission" placeholder="Service Commission" required min="1">
                        <select name="categories[${categoryRow}][services][${serviceIndex}][commission_type]" class="form-control commission-type">
                            <option value="percentage">%</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-danger remove-service"><i class="fa fa-minus-circle"></i></button>
                </div>
            `;

            serviceWrapper.append(newServiceRow);

            serviceWrapper.find('.service-select').last().select2();

            if (categoryId) {
                fetchServices(categoryId, serviceWrapper.find('.service-select').last());
            }
        });

        $(document).on('click', '.remove-service', function () {
            $(this).closest('.service-box').remove();
        });

        $(document).on('change', '.category-select', function () {
            var categoryId = $(this).val();
            var categoryRow = $(this).closest('tr').next('tr').attr('id');
            var serviceWrapper = $(`#${categoryRow} .service-wrapper`);

            if (categoryId) {
                serviceWrapper.find('.service-select').each(function () {
                    fetchServices(categoryId, $(this));
                });
            } else {
                serviceWrapper.find('.service-select').html('<option value="">Select Service</option>').select2();
            }
        });

        function fetchServices(categoryId, dropdown, selectedServiceId = null) {
            $.ajax({
                url: "{{ route('getServicesByCategory') }}",
                type: "GET",
                data: { category_id: categoryId },
                success: function (data) {
                    dropdown.html('<option value="">Select Service</option>');
                    $.each(data, function (index, service) {
                        var selected = (selectedServiceId && selectedServiceId == service.id) ? "selected" : "";
                        dropdown.append(`<option value="${service.id}" ${selected}>${service.name}</option>`);
                    });
                    dropdown.select2();
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            if ($('.selected-customer-table tr').length > 6) {
                $('.selected-customer-table').parent().addClass('scroll-div');
            }
            $(document).on('change', '.customer_checkbox', function() {
                if ($('.selected-customer-table tr').length > 6) {
                    $('.selected-customer-table').parent().addClass('scroll-div');
                }
                var row = $(this).closest('tr').clone();
                if ($(this).is(':checked')) {
                    row.find('.customer_checkbox')
                        .removeClass('customer_checkbox')
                        .addClass('selected_customer_checkbox')
                        .attr('name', function(i, name) {
                            return name.replace(/^customerId/, 'selectedCustomerId');
                        });
                    $('.selected-customer-table').append(row);
                } else {
                    var userId = $(this).val();
                    $('.selected_customer_checkbox[value="' + userId + '"]').closest('tr').remove();
                }
            });

            // Handler for selected customers table checkboxes
            $(document).on('change', '.selected_customer_checkbox', function() {
                if ($('.selected-customer-table tr').length > 6) {
                    $('.selected-customer-table').parent().addClass('scroll-div');
                }
                var userId = $(this).val();
                if (!$(this).is(':checked')) {
                    // Remove from selected customers table
                    $(this).closest('tr').remove();
                    // Uncheck in customer table
                    $('.customer_checkbox[value="' + userId + '"]').prop('checked', false);
                }
            });
        });
    </script>

    <script>
        $("#display_type").on("change", function() {
            var selectedValue = $(this).val();
            if (selectedValue == 2) {
                $("#customer").show();
            } else {
                $("#customer").hide();
            }
        });
        $(document).ready(function() {
            var selectedValue = $("#display_type").val();
            if (selectedValue == 2) {
                $("#customer").show();
            } else {
                $("#customer").hide();
            }
            $("#customer-search").keyup(function() {
                var value = $(this).val().toLowerCase();

                $(".customer-table tr").hide();

                $(".customer-table tr").each(function() {
                    $row = $(this);

                    var name = $row.find("td:first").next().text().toLowerCase();

                    var email = $row.find("td:last").text().toLowerCase();

                    if (name.indexOf(value) != -1) {
                        $(this).show();
                    } else if (email.indexOf(value) != -1) {
                        $(this).show();
                    }
                });
            });
        });
    </script>
@endsection
