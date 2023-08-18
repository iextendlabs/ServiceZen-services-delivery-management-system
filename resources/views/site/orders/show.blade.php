@extends('site.layout.app')
<style>
    .table {
        margin-bottom: 0px !important;
    }
</style>
<base href="/public">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Order</h2>
        </div>
    </div>
    @php
    $sub_total = 0;
    $staff_charges = 0;
    $total_amount = 0;
    $staff_transport_charges = 0;
    @endphp
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

        <div class="text-right mb-2">
            <a class="btn btn-primary float-end no-print" href="{{ route('order.edit',$order->id) }}">Edit</a>
            <button type="button" class="btn btn-danger float-end no-print" onclick="printDiv()"><i class="fa fa-print"></i> Download PDF</button>
        </div>
        <table class="table table-striped table-bordered album bg-light">
            <td class="text-left" colspan="2"><i class="fas fa-shopping-cart"></i> Order Details</td>
            <tr>
                <td>
                    <b>Order ID:</b> #{{ $order->id }} <br><br>
                    <b>Date Added:</b> {{ $order->created_at }} <br><br>
                    <b>Order Status:</b> {{ $order->status }}
                </td>
                <td>
                    <b>Total Amount:</b> @currency( $order->total_amount ) <br><br>
                    <b>Payment Method:</b> {{ $order->payment_method }}
                </td>
            </tr>
        </table>
        <table class="table table-striped table-bordered album bg-light">
            <td class="text-left" colspan="2"><i class="fas fa-clock"></i> Appointment Details</td>
            <tr>
                <td>
                    <b>Staff:</b> {{ $order->staff_name }}

                </td>
                <td>
                    <b>Date:</b> {{ $order->date }}
                </td>
                <td>
                    <b>Time:</b> {{ date('h:i A', strtotime($order->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($order->time_slot->time_end)) }}
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
                    <b>Flat / Villa:</b> {{ $order->total_amount }} <br><br>
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
                    <b>Name:</b> {{ $order->customer->name }} <br><br>
                    <b>Email:</b> {{ $order->customer->email }}
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
            <td class="text-left" colspan="4"><i class="fas fa-spa"></i> Services Details</td>
            <tr>
                <th>Service Name</th>
                <th>Status</th>
                <th>Duration</th>
                <th class="text-right">Amount</th>
            </tr>
            @foreach($order->orderServices as $orderService)
            <tr>
                <td>{{ $orderService->service->name }}</td>
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
    </div>
</div>
<script>
    function printDiv() {
        window.print();
    }
</script>
@endsection