@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Update Coupon</h2>
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
    <form action="{{ route('coupons.update',$coupon->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">History</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Name</strong>
                            <input type="text" name="name" value="{{ old('name', $coupon->name) }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Code</strong>
                            <input type="text" name="code" value="{{ 'code', $coupon->code }}" class="form-control" placeholder="Code">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Type</strong>
                            <select name="type" class="form-control">
                                @if($coupon->type == "Percentage")
                                <option value="Percentage" {{ old('type') == 'Percentage' ? 'selected' : '' }} selected>Percentage</option>
                                <option value="Fixed Amount" {{ old('type') == 'Fixed Amount' ? 'selected' : '' }}>Fixed Amount</option>
                                @elseif($coupon->type == "Fixed Amount")
                                <option value="Percentage" {{ old('type') == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="Fixed Amount" {{ old('type') == 'Fixed Amount' ? 'selected' : '' }} selected>Fixed Amount</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Discount</strong>
                            <input type="number" name="discount" value="{{ old('discount', $coupon->discount) }}" class="form-control" placeholder="Discount">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Minimum Order</strong>
                            <input type="text" name="min_order_value" value="{{ old( 'min_order_value', $coupon->min_order_value ) }}" class="form-control" placeholder="Minimum Order">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Date Start</strong>
                            <input type="date" name="date_start" value="{{ old( 'date_start', $coupon->date_start ) }}" class="form-control" placeholder="Date Start">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Date End</strong>
                            <input type="date" name="date_end" value="{{ old( 'date_end', $coupon->date_end ) }}" class="form-control" placeholder="Date End">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Uses Per Coupon</strong>
                            <input type="text" name="uses_total" value="{{ old( 'uses_total',$coupon->uses_total) }}" class="form-control" placeholder="Uses Per Coupon">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Status</strong>
                            <select name="status" class="form-control">
                                @if($coupon->status == 1)
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}selected>Enable</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                                @elseif($coupon->status == 0)
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }} selected>Disable</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Coupon For:</strong>
                            <select name="coupon_for" id="coupon-for" class="form-control">
                                <option value="public" {{ old('coupon_for', $coupon->coupon_for) == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="customer" {{ old('coupon_for', $coupon->coupon_for) == 'customer' ? 'selected' : '' }}>Customer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group scroll-div" id="customer-list" style="display:none;">
                            <strong>Select Customers:</strong>
                            <input type="text" id="customer-search" placeholder="Search Customers..." class="form-control">
                            <table class="table table-striped table-bordered customer-table">
                                <tbody id="customer-body">
                                    <!-- Initial 10 customers will be loaded here via AJAX -->
                                </tbody>
                            </table>
                            <h3 id="loading" style="display: none; text-align: center">Loading...</h3>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group scroll-div" id="selected-customer-list" style="display:none;">
                            <strong>Selected Customers:</strong>
                            <input type="text" id="selected-customer-search" placeholder="Search Customers..." class="form-control">
                            <table class="table table-striped table-bordered selected-customers-table">
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                                <tbody id="selected-customers-body">
                                    <!-- Selected customers will be added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <span style="color: red;">*</span><strong>Category:</strong>
                            <input type="text" name="categories-search" {{ old('categories-search') }} id="categories-search" class="form-control" placeholder="Search Category By Name">
                            <table class="table table-striped table-bordered categories-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                </tr>
                                @foreach ($categories as $category)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="categoriesId[{{ ++$i }}]" value="{{ $category->id }}" @if(in_array($category->id, old( 'categoriesId',$category_ids))) checked @endif>
                                    </td>
                                    <td>{{ $category->title }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <strong>Service:</strong>
                            <input type="text" name="service-search" id="service-search" class="form-control" placeholder="Search Services By Name">
                            <table class="table table-striped table-bordered service-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Price</th>
                                </tr>
                                @foreach ($services as $service)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="servicesId[{{ ++$i }}]" value="{{ $service->id }}"   @if(in_array($service->id, old('servicesId', $service_ids))) checked @endif>
                                    </td>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->price }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-block btn-primary">Save</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <table class="table table-striped table-bordered history-table">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date Added</th>
                                </tr>
                                @if(count($coupon->couponHistory))
                                @foreach ($coupon->couponHistory as $history)
                                <tr>
                                    <td>{{ $history->order_id }}</td>
                                    <td>{{ $history->customer_id }}</td>
                                    <td>{{ $history->discount_amount }}</td>
                                    <td>{{ $history->created_at }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="4" class="text-center"> There is no History</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
<script>
    $(document).ready(function () {
        const $couponForElement = $('#coupon-for');
        const $customerListElement = $('#customer-list');
        const $selectedCustomerListElement = $('#selected-customer-list');
        const $customerBody = $('#customer-body');
        const $selectedCustomersBody = $('#selected-customers-body');
        const $loading = $('#loading');
        let page = 1;
        let searchQuery = '';

        function loadCustomers(page, searchQuery = '') {
            $.ajax({
                url: "{{ route('customers.load') }}",
                method: "GET",
                data: { page: page, search: searchQuery },
                beforeSend: function() {
                    $loading.show();
                },
                success: function(data) {
                    $customerBody.append(data.html);
                    $loading.hide();
                }
            });
        }

        function initializeSelectedCustomers(selectedCustomers) {
            selectedCustomers.forEach(function(customer) {
                const row = `
                    <tr>
                        <td><input type="checkbox" name="selected_customer_ids[]" value="${customer.id}" checked></td>
                        <td>${customer.name}</td>
                        <td>${customer.email}</td>
                    </tr>
                `;
                $selectedCustomersBody.append(row);

                $customerBody.find(`input[value="${customer.id}"]`).prop('checked', true);
            });
        }

        $couponForElement.on('change', function () {
            if ($(this).val() === 'customer') {
                $customerListElement.show();
                $selectedCustomerListElement.show();
                $customerBody.empty();
                $selectedCustomersBody.empty();
                page = 1;
                loadCustomers(page);
            } else {
                $customerListElement.hide();
                $selectedCustomerListElement.hide();
                $selectedCustomersBody.empty();
            }
        });

        $couponForElement.trigger('change');

        $('#customer-search').on('keyup', function() {
            searchQuery = $(this).val().toLowerCase();
            page = 1;
            $customerBody.empty();
            loadCustomers(page, searchQuery);
        });

        $customerListElement.on('scroll', function() {
            if ($customerListElement[0].scrollHeight - $customerListElement.scrollTop() <= $customerListElement.outerHeight()) {
                page++;
                loadCustomers(page, searchQuery);
            }
        });

        $(document).on('change', '.customer-table input[type="checkbox"]', function() {
            const $row = $(this).closest('tr');
            const customerId = $(this).val();
            const $selectedRow = $row.clone();

            if ($(this).is(':checked')) {
                const isAlreadySelected = $selectedCustomersBody.find(`input[value="${customerId}"]`).length > 0;

                if (!isAlreadySelected) {
                    $selectedRow.find('input[type="checkbox"]').prop('checked', true).attr('name', 'selected_customer_ids[]');
                    $selectedCustomersBody.append($selectedRow);
                }
            } else {
                $selectedCustomersBody.find(`tr`).each(function() {
                    const selectedId = $(this).find('input[type="checkbox"]').val();
                    if (selectedId == customerId) {
                        $(this).remove();
                    }
                });
            }
        });

        $(document).on('change', '.selected-customers-table input[type="checkbox"]', function() {
            const customerId = $(this).val();
            const $row = $(this).closest('tr');

            if ($(this).is(':checked')) {
                $customerBody.find(`tr`).each(function() {
                    const id = $(this).find('input[type="checkbox"]').val();
                    if (id == customerId) {
                        $(this).find('input[type="checkbox"]').prop('checked', true);
                    }
                });
            } else {
                $customerBody.find(`tr`).each(function() {
                    const id = $(this).find('input[type="checkbox"]').val();
                    if (id == customerId) {
                        $(this).find('input[type="checkbox"]').prop('checked', false);
                    }
                });
                $row.remove();
            }
        });

        const selectedCustomers = @json($coupon->customers);
        initializeSelectedCustomers(selectedCustomers);

        $("#selected-customer-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".selected-customers-table tr").hide();

            $(".selected-customers-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();


                if (name.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        $("#categories-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".categories-table tr").hide();

            $(".categories-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();


                if (name.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });

        $("#service-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".service-table tr").hide();

            $(".service-table tr").each(function() {

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