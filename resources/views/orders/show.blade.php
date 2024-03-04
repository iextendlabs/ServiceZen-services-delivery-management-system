@extends('layouts.app')
<style>
    .table {
        margin-bottom: 0px !important;
    }
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 float-start">
            <h2>Orders</h2>
        </div>
        <div class="col-md-12 float-end no-print">
            @can('order-booking-edit')
            <a class="btn btn-success float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=booking">Booking Edit</a>
            @endcan
            @can('order-status-edit')
            @if(auth()->user()->getRoleNames() == '["Supervisor"]' && $order->status == 'Pending')
            <a class="btn btn-secondary float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
            @elseif(auth()->user()->getRoleNames() != '["Supervisor"]')
            <a class="btn btn-info float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
            @endif
            @endcan
            @can('order-detail-edit')
            <a class="btn btn-warning float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=address">Address Edit</a>
            @endcan
            @can('order-affiliate-edit')
            <a class="btn btn-primary float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=affiliate">Affiliate Edit</a>
            @endcan
            @can('order-comment-edit')
            <a class="btn btn-success float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=comment">Comment Edit</a>
            @endcan
            <a class="btn btn-secondary float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=custom_location">Add Custom Location</a>
            @can('order-driver-status-edit')
            <a class="btn btn-success float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=order_driver_status">Order Driver Status Edit</a>
            <a class="btn btn-primary float-end mr-1" href="{{ route('orders.edit', $order->id) }}?edit=driver">Order Driver Edit</a>
            @endcan
        </div>
        <div class="col-md-12 float-end no-print mt-2 mb-2">
            @if($order->customer_id)
            <a class="btn btn-info float-end mr-1" href="{{ route('customers.show',$order->customer_id) }}">View Customer</a>
            @endif
            @can('order-download')
            <button type="button" class="btn mr-1 btn-primary float-end" onclick="printDiv()"><i class="fa fa-print"></i>Download PDF</button>
            @endcan
        </div>
    </div>
    <div class="container">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div>
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
                        <b>Total Amount:</b> @currency($order->total_amount) <br><br>
                        <b>Payment Method:</b> {{ $order->payment_method }}
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
                        <b>Driver:</b>{{ isset($order->driver->name) ? $order->driver->name : "N\A" }}<br><br>
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
                        <b>Flat / Villa:</b> {{ $order->flatVilla}} <br><br>
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
                        <b>Location of customer:</b> <a href="https://maps.google.com/maps?q={{ $order->latitude }},+{{ $order->longitude }}" target="_blank">click</a><br><br>
                        <a class="btn btn-secondary mr-1" href="{{ route('orders.edit', $order->id) }}?edit=custom_location">Add Custom Location</a>
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
                @foreach($order->orderServices as $orderService)
                <tr>
                    <td>{{ $orderService->service_name }}</td>
                    <td>{{ $orderService->status }}</td>
                    <td>{{ $orderService->duration ?? $orderService->service->duration ?? '' }}</td>
                    <td class="text-right">@currency($orderService->price)</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                    <td class="text-right">@currency($order->order_total->sub_total)</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Coupon Discount:</strong></td>
                    <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->discount ? '-'.$order->order_total->discount : 0 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Staff Transport Charges:</strong></td>
                    <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->transport_charges ? $order->order_total->transport_charges : 0 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Staff Charges:</strong></td>
                    <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->staff_charges ? $order->order_total->staff_charges : 0 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right">@currency($order->total_amount)</td>
                </tr>
            </table>
            @can('order-history')
            @if(count($order->orderHistories))
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left font-weight-bold" colspan="4">Order History</td>
                <tr>
                    <th>User</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th>Date Added</th>
                </tr>
                @foreach($order->orderHistories as $orderHistories)
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
            @if($order->order_comment)
            <table class="table table-striped table-bordered album bg-light">
                <th class="text-left font-weight-bold" colspan="4">Order Comment</th>
                <tr>
                    <td class="text-left">{!! nl2br($order->order_comment) !!}</td>
                </tr>
            </table>
            @endif
            @if(isset($order->staff))
            <fieldset>
                <legend>Staff Commission</legend>
                <table class="table table-striped table-bordered album bg-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Status</th>
                        <th>Staff</th>
                        <th>Order Sub Total</th>
                        <th>Staff Commission Amount</th>
                        @if(auth()->user()->getRoleNames() != '["Staff"]')

                        <th class="no-print">Action</th>
                        @endif
                    </tr>
                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="user_id" value="{{ $order->service_staff_id }}">
                        <input type="hidden" name="amount" value="{{ ($order->order_total->sub_total * $order->staff->commission) / 100 }}">
                        @php
                        $staff_commission = ($order->order_total->sub_total * $order->staff->commission) / 100;
                        @endphp
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->staff_name }}</td>
                            <td>@currency($order->order_total->sub_total)</td>
                            <td>@currency(($order->order_total->sub_total * $order->staff->commission) / 100)</td>
                            @if(auth()->user()->getRoleNames() != '["Staff"]')
                            <td class="no-print">
                                @if(empty($order->getStaffTransactionStatus()))
                                @can('order-edit')
                                <button type="submit" class="btn btn-primary">Approve</button>
                                @endcan
                                @else
                                <a href="{{ route('transactions.Unapprove') }}?order_id={{$order->id}}&user_id={{ $order->service_staff_id }}" type="button" class="btn btn-primary">Un Approve</a>
                                @endif
                            </td>
                            @endif
                        </tr>
                    </form>
                </table>
            </fieldset>
            @endif
            @if(auth()->user()->getRoleNames() != '["Staff"]')
            @if(isset($order->affiliate->affiliate))
            <fieldset>
                <legend>Affiliate Commission</legend>
                <table class="table table-striped table-bordered album bg-light">
                    <tr>
                        <th>Order Id</th>
                        <th>Order Amount</th>
                        <th>Affiliate</th>
                        <th>Staff Commission</th>
                        <th class="no-print">Action</th>
                    </tr>
                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="user_id" value="{{ $order->affiliate->id}}">
                        <input type="hidden" name="amount" value="{{ ((($order->order_total->sub_total - $order->order_total->staff_charges - $order->order_total->transport_charges - $order->order_total->discount - $staff_commission) * $order->affiliate->affiliate->commission) / 100) }}">
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>@currency($order->order_total->sub_total)</td>
                            <td>{{ $order->affiliate->name }}</td>
                            <td>@currency((($order->order_total->sub_total - $order->order_total->staff_charges - $order->order_total->transport_charges - $order->order_total->discount - $staff_commission) * $order->affiliate->affiliate->commission) / 100)</td>
                            <td class="no-print">
                                @if(empty($order->getAffiliateTransactionStatus()))
                                @can('order-edit')
                                <button type="submit" class="btn btn-primary">Approve</button>
                                @endcan
                                @else
                                <a href="{{ route('transactions.Unapprove') }}?order_id={{$order->id}}&user_id={{ $order->affiliate->id }}" type="button" class="btn btn-primary">Un Approve</a>
                                @endif
                            </td>
                        </tr>
                    </form>
                </table>
            </fieldset>
            @endif
            @endif
        </div>
    </div>
</div>
<script>
    function printDiv() {
        window.print();
    }
</script>
@endsection