@extends('layouts.app')
<style>
    .table {
        margin-bottom: 0px !important;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-3">
                <h2>Orders</h2>
            </div>
            <div class="col-md-12 mb-3 no-print">
                <div class="d-flex flex-wrap justify-content-md-end">
                    @can('order-booking-edit')
                        <a class="btn btn-success mb-2" href="{{ route('orders.edit', $order->id) }}?edit=booking">Booking
                            Edit</a>
                    @endcan

                    @can('order-status-edit')
                        @if (auth()->user()->hasRole('Supervisor') && $order->status == 'Pending')
                            <a class="btn btn-secondary mb-2 ms-md-2"
                                href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
                        @elseif(!auth()->user()->hasRole('Supervisor'))
                            <a class="btn btn-info mb-2 ms-md-2" href="{{ route('orders.edit', $order->id) }}?edit=status">Status
                                Edit</a>
                        @endif
                    @endcan
                    @can('order-detail-edit')
                        <a class="btn btn-warning mb-2 ms-md-2"
                            href="{{ route('orders.edit', $order->id) }}?edit=address">Address Edit</a>
                    @endcan
                    @can('order-affiliate-edit')
                        <a class="btn btn-primary mb-2 ms-md-2"
                            href="{{ route('orders.edit', $order->id) }}?edit=affiliate">Affiliate Edit</a>
                    @endcan
                    @can('order-comment-edit')
                        <a class="btn btn-success mb-2 ms-md-2"
                            href="{{ route('orders.edit', $order->id) }}?edit=comment">Comment Edit</a>
                    @endcan
                    <a class="btn btn-secondary mb-2 ms-md-2"
                        href="{{ route('orders.edit', $order->id) }}?edit=services">Edit Services</a>
                    @can('order-driver-status-edit')
                        <a class="btn btn-success mb-2 ms-md-2"
                            href="{{ route('orders.edit', $order->id) }}?edit=order_driver_status">Order Driver Status Edit</a>
                        <a class="btn btn-primary mb-2 ms-md-2" href="{{ route('orders.edit', $order->id) }}?edit=driver">Order
                            Driver Edit</a>
                    @endcan
                    <a class="btn btn-secondary mb-2 ms-md-2"
                        href="{{ route('orders.edit', $order->id) }}?edit=custom_location">Add Custom Location</a>
                    @if ($order->customer_id)
                        <a class="btn btn-info mb-2 ms-md-2" href="{{ route('customers.show', $order->customer_id) }}">View
                            Customer</a>
                    @endif
                    @can('order-download')
                        <button type="button" class="btn btn-primary mb-2 ms-md-2" onclick="printDiv()"><i
                                class="fa fa-print"></i>Download PDF</button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="row">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <span>{{ $message }}</span>
                    <button type="button" class="btn-close float-end"data-bs-dismiss="alert" aria-label="Close"></button>
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

            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left font-weight-bold" colspan="2">Order Details</td>
                <tr>
                    <td>
                        <b>Order ID:</b> #{{ $order->id }} <br><br>
                        <b>Date Added:</b> {{ $order->created_at }} <br><br>
                        <b>Order Status:</b> {{ $order->status }}
                    </td>
                    <td>
                        <b>Total Amount:</b> @currency($order->total_amount, true)
                        @if($order->currency_symbol && $order->currency_rate)
                            ({{ $order->currency_symbol }}{{ number_format($order->total_amount * $order->currency_rate, 2) }})
                        @endif <br><br>
                        <b>Payment Method:</b> {{ $order->payment_method }} <br><br>
                        <b>Order Source:</b> {{ $order->order_source }}
                    </td>
                </tr>
            </table>
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left font-weight-bold" colspan="2"><i class="fas fa-clock"></i> Appointment Details</td>
                <tr>
                    <td>
                        <b>Staff:</b>{{ $order->staff_name }}<br><br>
                        <b>Date:</b> {{ $order->date }}
                    </td>
                    <td>
                        <b>Driver:</b>{{ $order->driver->name ?? 'N\A' }}<br><br>
                        <b>Time:</b>{{ $order->time_slot_value }}
                    </td>
                </tr>
            </table>
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left font-weight-bold" colspan="3">Address Details</td>
                <tr>
                    <td>
                        <b>Building Name:</b> {{ $order->buildingName }} <br><br>
                        <b>District:</b> {{ $order->district }} <br><br>
                        <b>Area:</b> {{ $order->area }}
                    </td>
                    <td>
                        <b>Flat / Villa:</b> {{ $order->flatVilla }} <br><br>
                        <b>Land Mark:</b> {{ $order->landmark }}
                    </td>
                    <td>
                        <b>Street:</b> {{ $order->street }} <br><br>
                        <b>City:</b> {{ $order->city }}
                    </td>
                </tr>
            </table>
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left font-weight-bold" colspan="2">Customer Details</td>
                <tr>
                    <td>
                        <b>Name:</b> {{ $order->customer_name }} <br><br>
                        <b>Email:</b> {{ $order->customer_email }} <br><br>
                        <b>Gender:</b> {{ $order->gender }}
                    </td>
                    <td>
                        <b>Phone Number:</b> {{ $order->number }} <br><br>
                        <b>Whatsapp Number:</b> {{ $order->whatsapp }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="text-left">
                        <b>Location of customer:</b> <a
                            href="https://maps.google.com/maps?q={{ $order->latitude }},+{{ $order->longitude }}"
                            target="_blank">click</a><br><br>
                        <a class="btn btn-secondary mr-1"
                            href="{{ route('orders.edit', $order->id) }}?edit=custom_location">Add Custom Location</a>
                    </td>
                </tr>
            </table>
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left font-weight-bold" colspan="4">Services Details</td>
                <tr>
                    <th>Service Name</th>
                    <th>Status</th>
                    <th>Duration</th>
                    <th class="text-right">Amount</th>
                </tr>
                @foreach ($order->orderServices as $orderService)
                    <tr>
                        <td>{{ $orderService->service_name }}@if($orderService->option_name) ({{$orderService->option_name }})@endif</td>
                        <td>{{ $orderService->status }}</td>
                        <td>{{ $orderService->duration ?? ($orderService->service->duration ?? '') }}</td>
                        <td class="text-right">
                            @currency($orderService->price,true) 
                            @if($order->currency_symbol && $order->currency_rate)
                                ({{ $order->currency_symbol }}{{ number_format($orderService->price * $order->currency_rate, 2) }})
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                    <td class="text-right">
                        @currency($order->order_total->sub_total,true)
                        @if($order->currency_symbol && $order->currency_rate)
                            ({{ $order->currency_symbol }}{{ number_format($order->order_total->sub_total * $order->currency_rate, 2) }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Coupon Discount:</strong></td>
                    <td class="text-right">
                        @currency($order->order_total->discount ? '-' . $order->order_total->discount : 0 ,true)
                        @if($order->currency_symbol && $order->currency_rate)
                            ({{ $order->currency_symbol }}{{ number_format(($order->order_total->discount ?? 0) * $order->currency_rate, 2) }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Staff Transport Charges:</strong></td>
                    <td class="text-right">
                        @currency($order->order_total->transport_charges ? $order->order_total->transport_charges : 0 ,true)
                        @if($order->currency_symbol && $order->currency_rate)
                            ({{ $order->currency_symbol }}{{ number_format(($order->order_total->transport_charges ?? 0) * $order->currency_rate, 2) }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Staff Charges:</strong></td>
                    <td class="text-right">
                        @currency( $order->order_total->staff_charges ? $order->order_total->staff_charges : 0,true)
                        @if($order->currency_symbol && $order->currency_rate)
                            ({{ $order->currency_symbol }}{{ number_format(($order->order_total->staff_charges ?? 0) * $order->currency_rate, 2) }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right">
                        @currency($order->total_amount,true)
                        @if($order->currency_symbol && $order->currency_rate)
                            ({{ $order->currency_symbol }}{{ number_format($order->total_amount * $order->currency_rate, 2) }})
                        @endif
                    </td>
                </tr>
                @if ($order->order_total->discount > 0)
                    <tr>
                        <td colspan="4" class="text-right">
                            <a href="{{ route('orders.removeCoupon', $order->id) }}">
                                <button type="button" class="btn btn-danger">Remove Discount</button>
                            </a>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-right">
                        <form action="{{ route('orders.addDiscount', $order->id) }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="input-group me-2 col-4 float-end">
                                <input type="number" required name="discount" placeholder="AED" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Add Discount</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
            @can('order-history')
                @if (count($order->orderHistories))
                    <table class="table table-striped table-bordered album bg-light">
                        <td class="text-left font-weight-bold" colspan="4">Order History</td>
                        <tr>
                            <th>User</th>
                            <th>Status</th>
                            <th>Comment</th>
                            <th>Date Added</th>
                        </tr>
                        @foreach ($order->orderHistories as $orderHistories)
                            <tr>
                                <td>{{ $orderHistories->user }}</td>
                                <td>{{ $orderHistories->status }}</td>
                                <td>{{ $orderHistories->comment }}</td>
                                <td>{{ $orderHistories->created_at }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            @endcan
            @if ($order->order_comment)
                <table class="table table-striped table-bordered album bg-light">
                    <th class="text-left font-weight-bold" colspan="4">Order Comment</th>
                    <tr>
                        <td class="text-left">{!! nl2br($order->order_comment) !!}</td>
                    </tr>
                </table>
            @endif
            @if ($staff_commission)
                <fieldset>
                    <legend>Staff Commission</legend>
                    <table class="table table-striped table-bordered album bg-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Order Status</th>
                            <th>Staff</th>
                            <th>Order Sub Total</th>
                            <th>Commission</th>
                            @if (!auth()->user()->hasRole('Staff'))
                                <th class="no-print">Action</th>
                            @endif
                        </tr>
                        <form action="{{ route('transactions.store') }}" method="POST">
                            @csrf
                            @php
                                $staffTransactionStatus = $order->getTransactionStatus($order->service_staff_id,'Order Staff Commission');
                            @endphp
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="user_id" value="{{ $order->service_staff_id }}">
                            <input type="hidden" name="amount" value="{{ $staff_commission }}">
                            <input type="hidden" name="type" value="Order Staff Commission">
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->status }}</td>
                                <td>{{ $order->staff_name }}</td>
                                <td>@currency($order->order_total->sub_total,true)</td>
                                <td>@currency($staffTransactionStatus->amount ?? $staff_commission,true)</td>
                                @if (!auth()->user()->hasRole('Staff'))
                                    <td class="no-print">

                                        @if (empty($staffTransactionStatus))
                                            @can('order-edit')
                                                <button type="submit" class="btn btn-primary">Approve</button>
                                            @endcan
                                        @else
                                            <a href="{{ route('transactions.Unapprove') }}?id={{ $staffTransactionStatus->id }}"
                                                type="button" class="btn btn-warning">Un Approve</a>
                                            <a href="{{ route('transactions.edit', $staffTransactionStatus->id) }}"
                                                type="button" class="btn btn-primary">Edit</a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        </form>
                    </table>
                </fieldset>
            @endif
            @if ($staff_affiliate_commission)
                <fieldset>
                    <legend>Staff Affiliate Commission</legend>
                    <table class="table table-striped table-bordered album bg-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Staff Commission</th>
                            <th>Affiliate</th>
                            <th>Commission</th>
                            @if (!auth()->user()->hasRole('Staff'))
                                <th class="no-print">Action</th>
                            @endif
                        </tr>
                        <form action="{{ route('transactions.store') }}" method="POST">
                            @csrf
                            @php
                                $staffAffiliateTransactionStatus = $order->getTransactionStatus($order->staff->affiliate_id,'Order Staff Affiliate Commission');
                            @endphp
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="user_id" value="{{ $order->staff->affiliate_id }}">
                            <input type="hidden" name="amount" value="{{ $staff_affiliate_commission }}">
                            <input type="hidden" name="type" value="Order Staff Affiliate Commission">
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $staff_commission }}</td>
                                <td>{{ $order->staff->affiliate->name }}</td>
                                <td>@currency($staffAffiliateTransactionStatus->amount ?? $staff_affiliate_commission,true)</td>
                                @if (!auth()->user()->hasRole('Staff'))
                                    <td class="no-print">

                                        @if (empty($staffAffiliateTransactionStatus))
                                            @can('order-edit')
                                                <button type="submit" class="btn btn-primary">Approve</button>
                                            @endcan
                                        @else
                                            <a href="{{ route('transactions.Unapprove') }}?id={{ $staffAffiliateTransactionStatus->id }}"
                                                type="button" class="btn btn-warning">Un Approve</a>
                                            <a href="{{ route('transactions.edit', $staffAffiliateTransactionStatus->id) }}"
                                                type="button" class="btn btn-primary">Edit</a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        </form>
                    </table>
                </fieldset>
            @endif
            @if (!auth()->user()->hasRole('Staff'))
                @if ($affiliate_commission)
                    <fieldset>
                        <legend>Affiliate Commission</legend>
                        <table class="table table-striped table-bordered album bg-light">
                            <tr>
                                <th>Order Id</th>
                                <th>Order Amount</th>
                                <th>Affiliate</th>
                                <th>Commission</th>
                                <th class="no-print">Action</th>
                            </tr>
                            <form action="{{ route('transactions.store') }}" method="POST">
                                @csrf
                                @php
                                    $affiliateTransactionStatus = $order->getTransactionStatus( $affiliate_id, 'Order Affiliate Commission');
                                @endphp
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="user_id" value="{{ $affiliate_id }}">
                                <input type="hidden" name="amount" value="{{ $affiliate_commission }}">
                                <input type="hidden" name="type" value="Order Affiliate Commission">
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>@currency($order->order_total->sub_total,true)</td>
                                    <td>{{ $affiliate->name }}</td>
                                    <td>@currency($affiliateTransactionStatus->amount ?? $affiliate_commission,true)</td>
                                    <td class="no-print">
                                        @can('order-edit')
                                            @if (empty($affiliateTransactionStatus))
                                                <button type="submit" class="btn btn-primary">Approve</button>
                                            @else
                                                <a href="{{ route('transactions.Unapprove') }}?id={{ $affiliateTransactionStatus->id }}"
                                                    type="button" class="btn btn-warning">Un Approve</a>
                                                <a href="{{ route('transactions.edit', $affiliateTransactionStatus->id) }}"
                                                    type="button" class="btn btn-primary">Edit</a>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            </form>
                        </table>
                    </fieldset>
                @endif
                @if ($parent_affiliate_commission)
                    <fieldset>
                        <legend>Parent Affiliate Commission</legend>
                        <table class="table table-striped table-bordered album bg-light">
                            <tr>
                                <th>Order Id</th>
                                <th>Child Affiliate Commission</th>
                                <th>Affiliate</th>
                                <th>Commission</th>
                                <th class="no-print">Action</th>
                            </tr>
                            <form action="{{ route('transactions.store') }}" method="POST">
                                @csrf
                                @php
                                    $parentAffiliateTransactionStatus = $order->getTransactionStatus($parent_affiliate_id,'Order Parent Affiliate Commission');
                                @endphp
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="user_id" value="{{ $parent_affiliate_id }}">
                                <input type="hidden" name="amount" value="{{ $parent_affiliate_commission }}">
                                <input type="hidden" name="type" value="Order Parent Affiliate Commission">
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>@currency($affiliate_commission,true)</td>
                                    <td>{{ $affiliate->affiliate->parentAffiliate->name }}</td>
                                    <td>@currency($parentAffiliateTransactionStatus->amount ?? $parent_affiliate_commission,true)</td>
                                    <td class="no-print">
                                        @can('order-edit')
                                            @if (empty($parentAffiliateTransactionStatus))
                                                <button type="submit" class="btn btn-primary">Approve</button>
                                            @else
                                                <a href="{{ route('transactions.Unapprove') }}?id={{ $parentAffiliateTransactionStatus->id }}"
                                                    type="button" class="btn btn-warning">Un Approve</a>
                                                <a href="{{ route('transactions.edit', $parentAffiliateTransactionStatus->id) }}"
                                                    type="button" class="btn btn-primary">Edit</a>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            </form>
                        </table>
                    </fieldset>
                @endif
                @if ($driver_commission)
                    <fieldset>
                        <legend>Driver Commission</legend>
                        <table class="table table-striped table-bordered album bg-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Order Status</th>
                                <th>Driver</th>
                                <th>Order Sub Total</th>
                                <th>Commission</th>
                                <th class="no-print">Action</th>
                            </tr>
                            <form action="{{ route('transactions.store') }}" method="POST">
                                @csrf
                                @php
                                    $driverTransactionStatus = $order->getTransactionStatus($order->driver_id,'Order Driver Commission');
                                @endphp
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="user_id" value="{{ $order->driver_id }}">
                                <input type="hidden" name="amount" value="{{ $driver_commission }}">
                                <input type="hidden" name="type" value="Order Driver Commission">
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->driver->name }}</td>
                                    <td>@currency($order->order_total->sub_total,true)</td>
                                    <td>@currency($driverTransactionStatus->amount ?? $driver_commission,true)</td>
                                    <td class="no-print">
                                        @if (empty($driverTransactionStatus))
                                            @can('order-edit')
                                                <button type="submit" class="btn btn-primary">Approve</button>
                                            @endcan
                                        @else
                                            <a href="{{ route('transactions.Unapprove') }}?id={{ $driverTransactionStatus->id }}"
                                                type="button" class="btn btn-warning">Un Approve</a>
                                            <a href="{{ route('transactions.edit', $driverTransactionStatus->id) }}"
                                                type="button" class="btn btn-primary">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            </form>
                        </table>
                    </fieldset>
                @endif
                @if ($driver_affiliate_commission)
                    <fieldset>
                        <legend>Driver Affiliate Commission</legend>
                        <table class="table table-striped table-bordered album bg-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Driver Commission</th>
                                <th>Affiliate</th>
                                <th>Commission</th>
                                <th class="no-print">Action</th>
                            </tr>
                            <form action="{{ route('transactions.store') }}" method="POST">
                                @csrf
                                @php
                                    $driverAffiliateTransactionStatus = $order->getTransactionStatus($order->driver->driver->affiliate_id,'Order Driver Affiliate Commission');
                                @endphp
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="user_id" value="{{ $order->driver->driver->affiliate_id }}">
                                <input type="hidden" name="amount" value="{{ $driver_affiliate_commission }}">
                                <input type="hidden" name="type" value="Order Driver Affiliate Commission">
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $driver_commission }}</td>
                                    <td>{{ $order->driver->driver->affiliate->name }}</td>
                                    <td>@currency($driverAffiliateTransactionStatus->amount ?? $driver_affiliate_commission,true)</td>
                                    @if (!auth()->user()->hasRole('Staff'))
                                        <td class="no-print">

                                            @if (empty($driverAffiliateTransactionStatus))
                                                @can('order-edit')
                                                    <button type="submit" class="btn btn-primary">Approve</button>
                                                @endcan
                                            @else
                                                <a href="{{ route('transactions.Unapprove') }}?id={{ $driverAffiliateTransactionStatus->id }}"
                                                    type="button" class="btn btn-warning">Un Approve</a>
                                                <a href="{{ route('transactions.edit', $driverAffiliateTransactionStatus->id) }}"
                                                    type="button" class="btn btn-primary">Edit</a>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            </form>
                        </table>
                    </fieldset>
                @endif
            @endif
        </div>
    </div>
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
        });
    </script>
    <script>
        function printDiv() {
            window.print();
        }
    </script>
@endsection
