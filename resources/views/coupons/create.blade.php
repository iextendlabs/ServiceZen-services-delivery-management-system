    @extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Coupon</h2>
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
    <form action="{{ route('coupons.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name</strong>
                    <input type="text" name="name" value="{{old('name')}}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Code</strong>
                    <input type="text" name="code" value="{{old('code')}}" class="form-control" placeholder="Code">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Type</strong>
                    <select name="type" class="form-control">
                        <option value="Percentage">Percentage</option>
                        <option value="Fixed Amount">Fixed Amount</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Discount</strong>
                    <input type="number" name="discount" value="{{old('discount')}}" class="form-control" placeholder="Discount">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Minimum Order</strong>
                    <input type="number" name="min_order_value" value="{{old('min_order_value')}}" class="form-control" placeholder="Minimum Order">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date Start</strong>
                    <input type="date" name="date_start" value="{{old('date_start')}}" class="form-control" placeholder="Date Start">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date End</strong>
                    <input type="date" name="date_end" value="{{old('date_end')}}" class="form-control" placeholder="Date End">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Uses Per Coupon</strong>
                    <input type="text" name="uses_total" value="{{old('uses_total')}}" class="form-control" placeholder="Uses Per Coupon">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Status</strong>
                    <select name="status" class="form-control">
                        <option value="1">Enable</option>
                        <option value="0">Disable</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Coupon For:</strong>
                    <select name="coupon_for" id="coupon-for" class="form-control">
                        <option value="public" {{ old('coupon_for') == 'public' ? 'selected' : '' }}>Public</option>
                        <option value="customer" {{ old('coupon_for') == 'customer' ? 'selected' : '' }}>Customer</option>
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
                <div class="form-group" id="selected-customer-list" style="display:none;">
                    <strong>Selected Customers:</strong>
                    <table class="table table-striped table-bordered selected-customers-table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody id="selected-customers-body">
                            <!-- Selected customers will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group scroll-div">
                    <strong>Category:</strong>
                    <input type="text" name="categories-search" id="categories-search" class="form-control" placeholder="Search Category By Name">
                    <table class="table table-striped table-bordered categories-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                        </tr>
                        @foreach ($categories as $category)
                        <tr>
                            <td>
                                <input type="checkbox" name="categoriesId[{{ ++$i }}]" value="{{ $category->id }}">
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
                                <input type="checkbox" name="servicesId[{{ ++$i }}]" value="{{ $service->id }}">
                            </td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->price }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
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
                // Check if customer is already selected
                const isAlreadySelected = $selectedCustomersBody.find(`input[value="${customerId}"]`).length > 0;

                if (!isAlreadySelected) {
                    // Add row to selected customers list
                    $selectedRow.find('input[type="checkbox"]').prop('checked', true).attr('name', 'selected_customer_ids[]');
                    $selectedCustomersBody.append($selectedRow);
                }
            } else {
                // Remove row from selected customers list
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
                // Check the corresponding checkbox in customer list
                $customerBody.find(`tr`).each(function() {
                    const id = $(this).find('input[type="checkbox"]').val();
                    if (id == customerId) {
                        $(this).find('input[type="checkbox"]').prop('checked', true);
                    }
                });
            } else {
                // Uncheck the corresponding checkbox in customer list
                $customerBody.find(`tr`).each(function() {
                    const id = $(this).find('input[type="checkbox"]').val();
                    if (id == customerId) {
                        $(this).find('input[type="checkbox"]').prop('checked', false);
                    }
                });
                $row.remove();
            }
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