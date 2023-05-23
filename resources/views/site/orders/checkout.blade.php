@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Checkout</h2>
    </div>
</div>
  <div class="container">
  <table class="table table-bordered album bg-light">
        <tr>
            <th>Service</th>
            <th>date</th>
            <th>Time</th>
            <th>Address</th>
            <th class="text-right">Price</th>
        </tr>
        @foreach($appointments as $appointment)
        <tr>
            <td>{{ $appointment->service->name }}</td>
            <td>{{ $appointment->date }}</td>
            <td>{{ date('h:i A', strtotime($appointment->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($appointment->time_slot->time_end)) }}</td>
            <td>{{ $appointment->address }}</td>
            @if(isset($appointment->service->discount))
                <td class="text-right">${{ $appointment->service->discount }}</td>
            @else
                <td class="text-right">${{ $appointment->service->price }}</td>
            @endif
        </tr>
        @endforeach
        @php
            $total_amount = 0;
        @endphp

        @foreach($appointments as $appointment)
            @if(isset($appointment->service->discount))
            @php
                $total_amount += $appointment->service->discount;
            @endphp
            @else
            @php
                $total_amount += $appointment->service->price;
            @endphp
            @endif
        @endforeach
        <tr>
            <td colspan="4" class="text-right"><strong>Total:</strong></td>
            <td class="text-right">${{ $total_amount }}</td>
        </tr>
    </table>
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
    
    <form action="{{ route('order.store') }}" method="POST">
        @csrf
        @foreach($appointments as $appointment)
            <input type="hidden" name="appointment_id[]" value="{{ $appointment->id }}">
        @endforeach
        <input type="hidden" name="customer_id" value="{{ Auth::id() }}">
        <input type="hidden" name="total_amount" value="{{ $total_amount }}">
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Payment Method:</strong>
                    <select name="payment_method" class="form-control">
                        <option></option>
                        <option value="Cash-On-Delivery">Cash On Delivery</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Confirm Order</button>
            </div>
        </div>
    </form>
  </div>
@endsection