@extends('layouts.app')
@section('content')
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h3>Filter</h3>
                        <div class="float-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light dropdown-toggle text-dark mb-2" data-bs-toggle="dropdown" aria-expanded="false">
                                   <i class="fas fa-ellipsis-h"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item" type="button" id="updateAffiliateBtn">
                                            <i class="fa fa-user-plus me-2"></i> Update Affiliate
                                        </button>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ Request::fullUrlWithQuery(['print' => 1]) }}">
                                            <i class="fa fa-print me-2"></i> PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ Request::fullUrlWithQuery(['csv' => 1]) }}">
                                            <i class="fa fa-download me-2"></i> Excel
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item" type="button" id="bulkAssignCouponButton">
                                            <i class="fas fa-gift me-2"></i> Apply Bulk Coupon
                                        </button>
                                    </li>
                                </ul>
                            </div>
        
                            <!-- Modal structure -->
                            <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="couponModalLabel">Select Coupon for Customers</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="couponSelect" class="form-label">Select Coupon:</label>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Select</th>
                                                            <th>Coupon Name</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($coupons as $coupon)
                                                            <tr>
                                                                <td>
                                                                    <input class="form-check-input coupon-checkbox"
                                                                        id="bulk-coupon{{ $coupon->id }}" type="checkbox"
                                                                        name="bulk-coupon[]" value="{{ $coupon->id }}"
                                                                        style="margin-top: -8px">
                                                                </td>
                                                                <td>{{ $coupon->name }} ({{ $coupon->code }})</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button id="assignCouponButton" class="btn btn-primary" type="button"><i
                                                    class=""></i> Assign Coupon</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            {{--  --}}
                            @can('customer-create')
                                <a class="btn text-dark mb-2" href="{{ route('customers.create') }}"> <i class="fa fa-plus"></i>
                                    Create</a>
                            @endcan
                        </div>
                        <div class="modal fade" id="affiliateModal" tabindex="-1" aria-labelledby="affiliateModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="affiliateModalLabel">Update Affiliate</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="affiliateForm">
                                            <div class="mb-3">
                                                <label for="affiliateInput" class="form-label">Affiliate:</label>
                                                <select name="affiliate_id" class="form-control" id="affiliateInput">
                                                    <option></option>
                                                    @foreach ($affiliates as $affiliate)
                                                        @if ($affiliate->affiliate->status == 1)
                                                            <option value="{{ $affiliate->id }}">{{ $affiliate->name }}
                                                                @if ($affiliate->affiliate->code)
                                                                    ({{ $affiliate->affiliate->code }})
                                                                @endif
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="typeInput" class="form-label">Affiliate Commission type:</label>
                                                <select name="type" class="form-control" id="typeInput">
                                                    <option></option>
                                                    <option @if (old('type') == 'F') selected @endif value="F">Fix
                                                    </option>
                                                    <option @if (old('type') == 'P') selected @endif value="P">
                                                        Persentage</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="commissionInput" class="form-label">Commission:</label>
                                                <input type="number" name="commission" class="form-control"
                                                    placeholder="Affiliate Commission" value="{{ old('commission') }}"
                                                    id="commissionInput">
                                            </div>
                                            <div class="mb-3">
                                                <label for="expireDateInput" class="form-label">Expiration Date:</label>
                                                <input type="date" name="expiry_date" class="form-control" id="expireDateInput">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customers.index') }}" method="GET" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Name</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-user text-muted"></i></span>
                                            </div>
                                            <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control"
                                                placeholder="Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group position-relative">
                                        <label class="small text-muted font-weight-medium mb-1">Email</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-envelope text-muted"></i></span>
                                            </div>
                                            <input type="email" name="email" id="email-autocomplete" value="{{ $filter['email'] }}" class="form-control" autocomplete="off">
                                            <ul id="email-suggestions" class="list-group" style="position:absolute; z-index:1000; width:100%; display:none; max-height:180px; overflow-y:auto;"></ul>
                                            <style>
                                                #email-suggestions .list-group-item {
                                                    cursor: pointer !important;
                                                }
                                                #email-suggestions .list-group-item:hover {
                                                    background-color: #f0f0f0;
                                                }
                                            </style>
                                        </div>    
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-phone text-muted"></i></span>
                                            </div>
                                            <input type="text" name="number" value="{{ $filter['number'] }}"
                                                class="form-control" placeholder="Enter number with country code">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Affiliate</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-users text-muted"></i></span>
                                            </div>
                                            <select name="affiliate_id" class="form-control">
                                                <option></option>
                                                @foreach ($affiliates as $affiliate)
                                                    @if ($affiliate->affiliate->status == 1)
                                                        <option value="{{ $affiliate->id }}"
                                                            @if ($filter['affiliate_id'] == $affiliate->id) selected @endif>{{ $affiliate->name }}
                                                            @if ($affiliate->affiliate->code)
                                                                ({{ $affiliate->affiliate->code }})
                                                            @endif
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Zone:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                            </div>
                                            <select name="zone" id="zone" class="form-control">
                                                <option value="">Select a zone</option>
                                                @foreach ($staffZones as $zone)
                                                    <option value="{{ $zone->name }}"
                                                        @if ($zone->name == $filter['zone']) selected @endif>{{ $zone->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Order Count:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-box-open text-muted"></i></span>
                                            </div>
                                            <input type="number" name="order_count" value="{{ $filter['order_count'] }}"
                                            class="form-control" placeholder="Filter by order count">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-3">
                                    <label><i class="fa fa-filter"></i> <strong>Filter Customer by Order Created Date</strong></label>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Date From:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                            </div>
                                            <input type="date" name="date_from" class="form-control"
                                            value="{{ $filter['date_from'] }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Date To:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                            </div>
                                            <input type="date" name="date_to" class="form-control"
                                            value="{{ $filter['date_to'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <button type="submit" class="btn btn-md btn-primary shadow-sm float-end font-weight-bold"><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>    
            </div>            
            <div class="col-md-12 px-1">
                <div class="py-2">
                    <h2>Customer ({{ $total_customer }})</h2>
                </div>
                <table class="table table-bordered">
                    <tr class="bg-white">
                        <td>
                            <input type="checkbox" onclick="$('input[name*=\'ids\']').prop('checked', this.checked);">
                        </td>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('customers.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a></i>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('customers.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a></i>
                            @if (request('sort') === 'email')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Orders</th>
                        <th>Last Order Date</th>
                        <th>Affiliate</th>
                        <th>Action</th>
                    </tr>
                    @if (count($customers))
                        @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <input type="checkbox" class="item-checkbox" name="ids[{{ ++$i }}]"
                                        value="{{ $customer->id }}">
                                </td>
                                <td class="@if ($customer->status == 1) text-success @else text-danger @endif">
                                    {{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->customer_orders_count }}</td>
                                <td>
                                    @if ($customer->customerOrders->isEmpty())
                                        No orders found.
                                    @else
                                        {{ $customer->customerOrders->last()->created_at->toDateString() }}
                                    @endif
                                </td>
                                <td>{{ $customer->userAffiliate->affiliateUser->name ?? '' }}@if (isset($customer->userAffiliate->affiliate->code))
                                        ({{ $customer->userAffiliate->affiliate->code }})
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('customers.show', $customer->id) }}">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </li>
                                            @can('customer-edit')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('customers.edit', $customer->id) }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('order-list')
                                                <li>
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('orders.index') }}?customer_id={{ $customer->id }}&date_from={{ $filter['date_from'] ?? '' }}&date_to={{ $filter['date_to'] ?? '' }}">
                                                        <i class="fa fa-history"></i> Order History
                                                    </a>
                                                </li>
                                            @endcan
                                            <li>
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#couponModal{{ $customer->id }}">
                                                    <i class="fas fa-gift"></i> Apply Coupon
                                                </button>
                                            </li>
                                            @can('customer-delete')
                                                <li>
                                                    <button class="dropdown-item text-danger" type="button" onclick="confirmDelete('{{ $customer->id }}')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>

                                    <form id="deleteForm{{ $customer->id }}" action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <div class="modal fade" id="couponModal{{ $customer->id }}" tabindex="-1" aria-labelledby="couponModalLabel{{ $customer->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="couponModalLabel{{ $customer->id }}">Select Coupon for {{ $customer->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('coupons.assign', $customer->id) }}" method="post">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="couponSelect{{ $customer->id }}" class="form-label">Select Coupon:</label>
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Select</th>
                                                                        <th>Coupon Name</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($coupons as $coupon)
                                                                        <tr>
                                                                            <td>
                                                                                <input class="form-check-input mb-5" id="couponSelect{{ $coupon->id }}" type="checkbox" name="selectItem[]" value="{{ $coupon->id }}" style="margin-top: -8px">
                                                                            </td>
                                                                            <td>{{ $coupon->name }} {{ $coupon->code }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Save Coupon</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">There is no Customer</td>
                        </tr>
                    @endif
                </table>
                {!! $customers->links() !!}
            </div>
        </div>
    </div>

    <script>
        $('#updateAffiliateBtn').click(function() {
            if ($('.item-checkbox:checked').length > 0) {
                $('#affiliateModal').modal('show');
            } else {
                alert('Please select at least one customer.');
            }
        });

        $('#affiliateForm').submit(function(e) {
            e.preventDefault();

            var affiliate = $('#affiliateInput').val();
            var type = $('#typeInput').val();
            var commission = $('#commissionInput').val();
            var expireDate = $('#expireDateInput').val();
            var selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            console.log(affiliate, type, commission, expireDate, selectedItems);
            $.ajax({
                url: '{{ route('customers.updateAffiliate') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    affiliate: affiliate,
                    type: type,
                    commission: commission,
                    expireDate: expireDate,
                    selectedItems: selectedItems
                }),
                success: function(data) {
                    alert(data.message);
                    $('#affiliateModal').modal('hide');
                    window.location.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        alert(errorMessage);
                    } else {
                        console.error('Error:', xhr.responseText);
                        alert('An unexpected error occurred. Please try again later.');
                    }
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#bulkAssignCouponButton').click(function() {
                const selectedItems = $('.item-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedItems.length > 0) {
                    // Manually trigger the modal if items are selected

                    $('#couponModal').modal('show');
                } else {
                    alert('Please select a Customer to Assign Coupon.');
                }
            });
        });

        $('#assignCouponButton').click(function() {
            const selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            const selectedCoupons = $('input[name="bulk-coupon[]"]:checked').map(function() {
                return $(this).val();
            }).get();
            if (selectedItems.length > 0 && selectedCoupons.length > 0) {
                if (confirm("Are you sure you want to assign the selected coupons to customers?")) {
                    bulkAssignCoupon(selectedItems, selectedCoupons);
                }
            } else {
                alert('Please select both customers and coupons to assign.');
            }
        });

        function bulkAssignCoupon(selectedItems, selectedCoupons) {
            $.ajax({
                url: '{{ route('customers.bulkAssignCoupon') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    selectedItems,
                    selectedCoupons
                }),
                success: function(data) {
                    alert(data.message);
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            function checkTableResponsive() {
                var viewportWidth = $(window).width();
                var $table = $('table');

                if (viewportWidth < 768) {
                    $table.addClass('table-responsive');
                } else {
                    $table.removeClass('table-responsive');
                }
            }

            checkTableResponsive();

            $(window).resize(function() {
                checkTableResponsive();
            });

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            var $input = $('#email-autocomplete');
            var $suggestions = $('#email-suggestions');
            $input.on('input', debounce(function() {
                var query = $(this).val();
                if (query.length < 2) {
                    $suggestions.hide();
                    return;
                }
                $.ajax({
                    url: '{{ route('autocomplete.email') }}',
                    data: { role: 'Customer', query: query },
                    success: function(data) {
                        $suggestions.empty();
                        if (Array.isArray(data) && data.length) {
                            data.forEach(function(email) {
                                var $li = $('<li class="list-group-item list-group-item-action"></li>').text(email);
                                $li.on('click', function() {
                                    $input.val(email);
                                    $suggestions.hide();
                                });
                                $suggestions.append($li);
                            });
                            $suggestions.show();
                        } else {
                            var $li = $('<li class="list-group-item text-muted"></li>').text('No data found');
                            $suggestions.append($li);
                            $suggestions.show();
                        }
                    },
                    error: function(xhr) {
                        $suggestions.empty();
                        var $li = $('<li class="list-group-item text-danger"></li>').text('Error fetching data');
                        $suggestions.append($li);
                        $suggestions.show();
                    }
                });
            }, 300));
            $input.on('blur', function() {
                setTimeout(function() { $suggestions.hide(); }, 200);
            });
        });
    </script>
@endsection
