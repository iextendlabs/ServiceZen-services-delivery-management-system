@extends('site.layout.app')
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
    <table class="table table-bordered album bg-light">
        <td class="text-left" colspan="2">Order Details</td>
        <tr>
            <td>
                <b>Order ID:</b>#{{ $order->id }} <br><br>
                <b>Date Added:</b>{{ $order->created_at }}
            </td>
            <td>
                <b>Total Amount:</b>${{ $order->total_amount }} <br><br>
                <b>Payment Method:</b>{{ $order->payment_method }}
            </td>
        </tr>
        <td class="text-left" colspan="2">Address Details</td>
        <tr>
            <td colspan="2">
                <b>Building Name:</b> {{ $order->buildingName }} <br>
                <b>Area:</b> {{ $order->area }} <br>
                <b>Flat / Villa:</b> {{ $order->flatVilla }} <br>
                <b>Street:</b> {{ $order->street }} <br>
                <b>City:</b> {{ $order->city }} <br>
                <b>Number:</b> {{ $order->number }}
            </td>
        </tr>
    </table>
    <table class="table table-bordered album bg-light">
        <tr>
            <th>Service Name</th>
            <th>Status</th>
            <th>Duration</th>
            <th>Date</th>
            <th>Time</th>
            <th class="text-right">Amount</th>
        </tr>
        @foreach($order->serviceAppointments as $appointment)
            <tr>
                <td>{{ $appointment->service->name }}</td>
                <td>{{ $appointment->status }}</td>
                <td>{{ $appointment->service->duration }}</td>
                <td>{{ $appointment->date }}</td>
                <td>{{ date('h:i A', strtotime($appointment->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($appointment->time_slot->time_end)) }}</td>
                <td class="text-right">${{ $appointment->price }}</td>
            </tr>

            @php
                $sub_total = $appointment->price + $sub_total;
            @endphp
            
            @if($appointment->staffData)
            @php
                $staff_charges = $appointment->staffData->charges;
            @endphp
            @else
            @php
                $staff_charges = 0;
            @endphp
            @endif

            @if($appointment->time_slot->staffGroup->staffZone->transport_charges)
            @php
                $staff_transport_charges = $appointment->time_slot->staffGroup->staffZone->transport_charges;
            @endphp
            @else
            @php
                $staff_transport_charges = 0;
            @endphp
            @endif
        @endforeach
        <tr>
            <td colspan="5" class="text-right"><strong>Sub Total:</strong></td>
            <td class="text-right">${{ $sub_total }}</td>
            @php
                $total_amount = $sub_total +$total_amount;
            @endphp
        </tr>
        @if($staff_transport_charges != 0)
        <tr>
            <td colspan="5" class="text-right"><strong>Staff Transport Charges:</strong></td>
            <td class="text-right">${{ $staff_transport_charges }}</td>
            @php
                $total_amount = $staff_transport_charges +$total_amount;
            @endphp
        </tr>
        @endif
        @if($staff_charges != 0)
        <tr>
            <td colspan="5" class="text-right"><strong>Staff Charges:</strong></td>
            <td class="text-right">${{ $staff_charges }}</td>
            @php
                $total_amount = $staff_charges +$total_amount;
            @endphp
        </tr>
        @endif
        <tr>
            <td colspan="5" class="text-right"><strong>Total:</strong></td>
            <td class="text-right">${{ $total_amount }}</td>
        </tr>
    </table>
    
  </div>
</div>
@endsection