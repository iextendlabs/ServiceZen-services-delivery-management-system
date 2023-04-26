@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Orders</h2>
    </div>
</div>
<div class="container">
    <div class="album bg-light">
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
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Customer</th>
            <th>Total Amount</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Date Added</th>
            <th>Action</th>
        </tr>
        
        @foreach ($orders as $order)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $order->customer->name }}</td>
            <td>{{ $order->total_amount }}</td>
            <td>{{ $order->payment_method }}</td>
            <td>{{ $order->status }}</td>
            <td>{{ $order->created_at }}</td>
            <td>
                <a class="btn btn-info" href="{{ route('order.show',$order->id) }}">Show</a>
            </td>
        </tr>
        @endforeach
        @if(count($orders) == 0)
        <tr>
            <td colspan="7" class="text-center" > There is no Order</td>
        </tr>
        @endif
    </table>
    {!! $orders->links() !!}
    
  </div>
</div>
@endsection