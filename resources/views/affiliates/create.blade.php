@extends('layouts.app')
@php
    $category_row = 0;
@endphp
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Add New Affiliate</h2>
                </div>
            </div>
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
        <form action="{{ route('affiliates.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Email:</strong>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                            placeholder="abc@gmail.com">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Password:</strong>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                        </select>
                    </div>
                </div>
                <hr>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Phone Number:</strong>
                        <input id="number_country_code" type="hidden" name="number_country_code"/>
                        <input type="tel" id="number" name="number" class="form-control" value="{{ old('number') }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Whatsapp Number:</strong>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code"/>
                        <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Code:</strong>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}"
                            placeholder="Code">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Commission:</strong>
                        <input type="number" name="commission" class="form-control" value="{{ old('commission') }}"
                            placeholder="Commission In %">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Expire after days:</strong>
                        <input type="number" name="expire" class="form-control" value="{{ old('expire') }}"
                            placeholder="Enter days like 20">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Fix Salary:</strong>
                        <input type="number" name="fix_salary" class="form-control" value="{{ old('fix_salary') }}"
                            placeholder="Fix Salary">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Parent Affiliate:</strong>
                        <select name="parent_affiliate_id" class="form-control">
                            <option value=""></option>
                            @foreach ($affiliates as $affiliate)
                                @if($affiliate->affiliate->status == 1)
                                    <option value="{{ $affiliate->id }}" 
                                        {{ old('parent_affiliate_id') == $affiliate->id ? 'selected' : '' }}>
                                        {{ $affiliate->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                       <strong>Parent Affiliate Commission:</strong>
                        <input type="number" name="parent_affiliate_commission" class="form-control" value="{{ old('parent_affiliate_commission') }}"
                            placeholder="Parent Affiliate Commission In %">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Membership Plan:</strong>
                        <select name="membership_plan_id" class="form-control">
                            <option value=""></option>
                            @foreach ($membership_plans as $membership_plan)
                                <option value="{{ $membership_plan->id }}" {{ old('membership_plan_id') == $membership_plan->id ? 'selected' : '' }}>{{ $membership_plan->plan_name }} (AED{{$membership_plan->membership_fee}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- <div class="col-md-12">
                    <div class="form-group">
                        <strong>Expiry Date:</strong>
                        <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}" value={{ old('expiry_date') }}>
                    </div>
                </div> --}}

                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Categories:</strong>
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
                                
                            </tbody>
                        </table>
                        <button id="addCategoryBtn" onclick="addCategoryRow();" type="button" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        var category_row = {{ $category_row }};
    
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
    
        $(document).on('click', '.remove-category', function() {
            $(this).closest('tr').next('tr').remove();
            $(this).closest('tr').remove();
        });
    
        $(document).on('click', '.add-service', function() {
            var categoryRow = $(this).data('category-row');
            var serviceWrapper = $(`#service-container-${categoryRow} .service-wrapper`);
            var serviceIndex = serviceWrapper.find('.service-box').length;
    
            var categoryId = $(`#service-container-${categoryRow}`).prev('tr').find('.category-select').val();
    
            var newServiceRow = `
                <div class="service-box col-md-6 border-bottom mb-3 py-3">
                    <div class="form-group">
                        <select name="categories[${categoryRow}][services][${serviceIndex}][service_id]" class="form-control service-select select2" required>
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
    
            if (categoryId) {
                $.ajax({
                    url: "{{ route('getServicesByCategory') }}",
                    type: "GET",
                    data: { category_id: categoryId },
                    success: function(data) {
                        var $dropdown = serviceWrapper.find('.service-select').last();
                        $dropdown.html('<option value="">Select Service</option>');
                        $.each(data, function(index, service) {
                            $dropdown.append(`<option value="${service.id}">${service.name}</option>`);
                        });
                        $dropdown.select2();
                    }
                });
            } else {
                serviceWrapper.find('.service-select').last().html('<option value="">Select Service</option>').select2();
            }
        });
    
        $(document).on('click', '.remove-service', function() {
            $(this).closest('.service-box').remove();
        });
    
        $(document).on('change', '.category-select', function() {
            var categoryId = $(this).val();
            var categoryRow = $(this).closest('tr').next('tr').attr('id');
            var serviceWrapper = $(`#${categoryRow} .service-wrapper`);
    
            if (categoryId) {
                $.ajax({
                    url: "{{ route('getServicesByCategory') }}",
                    type: "GET",
                    data: { category_id: categoryId },
                    success: function(data) {
                        serviceWrapper.find('.service-select').each(function() {
                            var $dropdown = $(this);
                            $dropdown.html('<option value="">Select Service</option>');
                            $.each(data, function(index, service) {
                                $dropdown.append(`<option value="${service.id}">${service.name}</option>`);
                            });
                            $dropdown.select2();
                        });
                    }
                });
            } else {
                serviceWrapper.find('.service-select').html('<option value="">Select Service</option>').select2();
            }
        });
    </script>
    
@endsection
