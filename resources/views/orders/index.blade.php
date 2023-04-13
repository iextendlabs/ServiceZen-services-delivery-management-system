    @extends('layouts.app')
    @section('content')
    <div class="row">
        <div class="col-6">
            <h2>Orders</h2>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
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
                <form action="{{ route('orders.destroy',$order->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('orders.show',$order->id) }}">Show</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
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
@endsection