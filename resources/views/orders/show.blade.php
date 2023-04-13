@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12 py-5 text-center">
        <h2>Order</h2>
    </div>
</div>
<div class="container">
@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
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
    </table>
    <table class="table table-bordered album bg-light">
        <tr>
            <th>Service Name</th>
            <th>Status</th>
            <th>Address</th>
            <th>Duration</th>
            <th>Date</th>
            <th>Time</th>
            <th class="text-right">Amount</th>
        </tr>
        @foreach($order->getServiceData() as $order_detail)
            <tr>
                <td>{{ $order_detail->name }}</td>
                <td>{{ $order_detail->status }}</td>
                <td>{{ $order_detail->address }}</td>
                <td>{{ $order_detail->duration }}</td>
                <td>{{ $order_detail->date }}</td>
                <td>{{ $order_detail->time }}</td>
                <td class="text-right">${{ $order_detail->price }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6" class="text-right"><strong>Total:</strong></td>
            <td class="text-right">${{ $order->total_amount }}</td>
        </tr>
    </table><br>
    <fieldset>
        <legend>Update Order</legend>
    <form action="{{ route('orders.update',$order->id) }}" method="POST">
        @csrf
        @method('PUT')
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group"><br>
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
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
    </fieldset>

  </div>
</div>
@endsection