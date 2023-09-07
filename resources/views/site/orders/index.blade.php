@extends('site.layout.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Orders</h2>
        </div>
    </div>
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
    @if(Auth::User()->getRoleNames() == '["Staff"]')

    <div class="text-right mb-2">

        <!-- Pending Order -->
        <a class="btn btn-primary float-end" href="/order?status=Pending" style="margin-right: 10px;">
            <i class="fas fa-clock"></i> Pending Orders
        </a>

        <!-- Complete Order -->
        <a class="btn btn-success float-end" href="/order?status=Complete" style="margin-right: 10px;">
            <i class="fas fa-check"></i> Complete Orders
        </a>

        <a class="btn btn-success float-end" href="/order?status=Accepted" style="margin-right: 10px;">
            <i class="fas fa-check"></i> Accepted Orders
        </a>


        <!-- Canceled Order -->
        <a class="btn btn-danger float-end" href="/order?status=Rejected" style="margin-right: 10px;">
            <i class="fas fa-times"></i> Rejected Orders
        </a>

        <a class="btn btn-success text-right no-print" href="staffOrderCSV"><i class="fa fa-download"></i> Export Excel</a>
        <a class="btn btn-primary text-right no-print" href="cashCollections"><i class="fas fa-money-bill-wave"></i>
            Cash Collection</a>
    </div>

    @endif

    <div class="album bg-light">
        <table class="table table-striped table-bordered">
            <tr>
                <th>Order #</th>
                <th>Staff</th>
                <th><i class="fas fa-clock"></i> Appointment Date</th>
                <th><i class="fas fa-clock"></i> Slots</th>
                <th>Area</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Date Added</th>
                <th class="actions-header">Action</th>
            </tr>

            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->staff_name }}</td>
                <td>{{ $order->date }}</td>
                <td>{{ $order->time_slot_value }}</td>
                <td>{{ $order->area}}</td>
                <td>@currency( $order->total_amount )</td>
                <td>{{ $order->payment_method }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->created_at }}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('order.show', $order->id) }}"><i class="fas fa-eye"></i> </a>

                    @if($order->status == "Pending")
                    <a class="btn btn-primary" href="{{ route('order.edit',$order->id) }}"><i class="fas fa-edit"></i> </a>
                    @endif
                    <a class="btn btn-primary" href="{{ route('order.reOrder',$order->id) }}">ReOrder </a>
                   
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