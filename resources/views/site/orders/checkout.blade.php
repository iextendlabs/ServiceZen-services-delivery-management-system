@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="row">
    <div class="col-lg-12 py-5 text-center">
        <h2>Checkout</h2>
    </div>
</div>
<div class="album bg-light">
  <div class="container">
  <table class="table table-bordered">
        <tr>
            <th>Service</th>
            <th>date</th>
            <th>Time</th>
            <th>Address</th>
            <th class="text-right">Price</th>
        </tr>
        <tr>
            <td>{{ $appointment->service->name }}</td>
            <td>{{ $appointment->date }}</td>
            <td>{{ $appointment->time }}</td>
            <td>{{ $appointment->address }}</td>
            <td class="text-right">${{ $appointment->service->price }}</td>
        </tr>
        <tr>
        <tr>
            <td colspan="4" class="text-right"><strong>Sub-Total:</strong></td>
            <td class="text-right">${{ $appointment->service->price }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-right"><strong>Total:</strong></td>
            <td class="text-right">${{ $appointment->service->price }}</td>
        </tr>
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
        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
        <input type="hidden" name="customer_id" value="{{ $customer_id }}">
        <input type="hidden" name="total_amount" value="{{ $appointment->service->price }}">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Payment Method:</strong>
                    <select name="payment_method" class="form-control">
                        <option></option>
                        <option value="Cash-On-Delivery">Cash On Delivery</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Confirm Order</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection