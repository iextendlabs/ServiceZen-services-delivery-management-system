@extends('site.layout.app')
<style>
    .table {
        margin-bottom: 0px !important;
    }
</style>
<base href="/public">
@section('content')
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
<div class="container">
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
        <!-- AddToAny BEGIN -->
        <div class="a2a_kit a2a_kit_size_32 a2a_default_style" style="margin-bottom: 20px;">
            <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
            <a class="a2a_button_facebook"></a>
            <a class="a2a_button_twitter"></a>
            <a class="a2a_button_whatsapp"></a>
            <a class="a2a_button_telegram"></a>
        </div>
        <script async src="https://static.addtoany.com/menu/page.js"></script>
        <!-- AddToAny END -->
        <div class="text-right">
            <button type="button" class="btn btn-primary float-end no-print" onclick="printDiv()"><i class="fa fa-print"></i>Download PDF</button>
        </div>
        <table class="table table-bordered album bg-light">
            <td class="text-left" colspan="2">Order Details</td>
            <tr>
                <td>
                    <b>Order ID:</b> #{{ $order->id }} <br><br>
                    <b>Date Added:</b> {{ $order->created_at }} <br><br>
                    <b>Order Status:</b> {{ $order->status }}
                </td>
                <td>
                    <b>Total Amount:</b> ${{ $order->total_amount }} <br><br>
                    <b>Payment Method:</b> {{ $order->payment_method }}
                </td>
            </tr>
        </table>
        <table class="table table-bordered album bg-light">
            <td class="text-left" colspan="2">Time Slot and Staff</td>
            <tr>
                <td>
                    <b>Staff:</b> {{ $order->staff->user->name }} <br><br>
                    <b>Date:</b> {{ $order->date }}
                </td>
                <td>
                    <b>Time Slot:</b> {{ $order->time_slot->name }} <br><br>
                    <b>Time:</b> {{ date('h:i A', strtotime($order->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($order->time_slot->time_end)) }}
                </td>
            </tr>
        </table>
        <table class="table table-bordered album bg-light">
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
        <table class="table table-bordered album bg-light">
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
        <table class="table table-bordered album bg-light">
            <td class="text-left" colspan="4">Services Details</td>
            <tr>
                <th>Service Name</th>
                <th>Status</th>
                <th>Duration</th>
                <th class="text-right">Amount</th>
            </tr>
            @foreach($order->serviceAppointments as $appointment)
            <tr>
                <td>{{ $appointment->service->name }}</td>
                <td>{{ $appointment->status }}</td>
                <td>{{ $appointment->service->duration }}</td>
                <td class="text-right">${{ $appointment->price }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                <td class="text-right">${{ $order->order_total->sub_total }}</td>
            </tr>
            @if($order->order_total->transport_charges != 0)
            <tr>
                <td colspan="3" class="text-right"><strong>Staff Transport Charges:</strong></td>
                <td class="text-right">${{ $order->order_total->transport_charges }}</td>
            </tr>
            @endif
            @if($order->order_total->staff_charges != 0)
            <tr>
                <td colspan="3" class="text-right"><strong>Staff Charges:</strong></td>
                <td class="text-right">${{ $order->order_total->staff_charges }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td class="text-right">${{ $order->total_amount }}</td>
            </tr>
        </table>
        @guest
        @else
        @if(Auth::user()->hasRole('Staff'))
        <fieldset>
            <legend>Update Order</legend>
            <form action="{{ route('order.update',$order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="status" class="form-control">
                                @foreach ($statuses as $status)
                                @if($status == $order->status)
                                <option value="{{ $status }}" selected>{{ $status }}</option>
                                @else
                                <option value="{{ $status }}">{{ $status }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 text-right no-print">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </fieldset>
        @endif
        @endguest

    </div>
</div>
<script>
    function printDiv() {
        window.print();
    }
</script>
@endsection