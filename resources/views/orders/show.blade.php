@extends('layouts.app')
<style>
    .table {
        margin-bottom: 0px !important;
    }
</style>
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Order</h2>
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
        @can('order-download')
        <button type="button" class="btn mb-2 btn-primary float-end no-print" onclick="printDiv()"><i class="fa fa-print"></i>Download PDF</button>
        @endcan
        <table class="table table-striped table-bordered album bg-light">
            <td class="text-left" colspan="2">Order Details</td>
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
            <td class="text-left" colspan="2"><i class="fas fa-clock"></i> Appointment Details</td>
            <tr>
                <td>
                    <b>Staff:</b>{{ $order->staff_name }}<br><br>
                    <b>Date:</b> {{ $order->date }}
                </td>
                <td>
                    <b>Time:</b>{{ $order->time_slot_value }}
                </td>
            </tr>
        </table>
        <table class="table table-striped table-bordered album bg-light">
            <td class="text-left" colspan="3">Address Details</td>
            <tr>
                <td>
                    <b>Building Name:</b> {{ $order->buildingName }} <br><br>
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
            <td class="text-left" colspan="2">Customer Details</td>
            <tr>
                <td>
                    <b>Name:</b> {{ $order->customer_name }} <br><br>
                    <b>Email:</b> {{ $order->customer_email }}
                </td>
                <td>
                    <b>Phone Number:</b> {{ $order->number }} <br><br>
                    <b>Whatsapp Number:</b> {{ $order->whatsapp }}
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-left">
                    <b>Location of customer:</b> <a href="https://maps.google.com/maps?q={{ $order->latitude }},+{{ $order->longitude }}" target="_blank">click</a>
                </td>
            </tr>
        </table>
        <table class="table table-striped table-bordered album bg-light">
            <td class="text-left" colspan="4">Services Details</td>
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
                <td>{{ $orderService->service->duration }}</td>
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
        @if($order->order_comment)
        <table class="table table-striped table-bordered album bg-light">
            <th class="text-left" colspan="4">Order Comment</th>
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
                            <button type="submit" class="btn btn-primary" disabled>Approved</button>
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
                    <input type="hidden" name="amount" value="{{ ($order->total_amount * $order->affiliate->affiliate->commission) / 100 }}">
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>@currency($order->total_amount)</td>
                        <td>{{ $order->affiliate->name }}</td>
                        <td>@currency(($order->total_amount * $order->affiliate->affiliate->commission) / 100)</td>
                        <td class="no-print">
                            @if(empty($order->getAffiliateTransactionStatus()))
                            @can('order-edit')
                            <button type="submit" class="btn btn-primary">Approve</button>
                            @endcan
                            @else
                            <button type="submit" class="btn btn-primary" disabled>Approved</button>
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
<script>
    function printDiv() {
        window.print();
    }
</script>
@endsection