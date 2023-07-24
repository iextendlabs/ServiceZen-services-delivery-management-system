@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-6">
        <h2>Cash Collection</h2>
    </div>
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <span>{{ $message }}</span>
    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<hr>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Slot</th>
                <th>Order Status</th>
                <th>Date Added</th>
                <th>Collection Status</th>
            </tr>
            @foreach ($orders as $order)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $order->customer->name }}</td>
                <td>@currency( $order->total_amount )</td>
                <td>{{ $order->payment_method }}</td>
                <td>{{ $order->date }} \ {{ $order->time_slot_value }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->created_at }}</td>
                <td>
                    @if(!$order->cashCollection)
                    <a class="btn btn-info" href="{{ route('cashCollection.create',$order->id) }}">Create</a>
                    @else
                    {{$order->cashCollection->status}} : 
                    @currency($order->cashCollection->amount)

                    @endif
                </td>
            </tr>
            @endforeach
            @if(count($orders) == 0)
            <tr>
                <td colspan="7" class="text-center"> There is no Order</td>
            </tr>
            @endif
        </table>
        {!! $orders->links() !!}
    </div>
</div>

@endsection