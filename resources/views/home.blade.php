@extends('layouts.app')
<style>
    .analytic {
        line-height: 48px;
    }

    .analytic i {
        font-size: 3rem;
        opacity: 0.5;
    }

    .analytic span {
        font-size: 2.5rem;
        display: inline;
    }
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">TOTAL SALES</div>
                <div class="card-body analytic">
                    <i class="fa fa-credit-card"></i>
                    <span class="float-end">@currency($sale)</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">TOTAL AFFILIATE COMMISSION</div>
                <div class="card-body analytic">
                    <i class="fa fa-dollar-sign"></i>
                    <span class="float-end">@currency($affiliate_commission)</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">TOTAL STAFF COMMISSION</div>
                <div class="card-body analytic">
                    <i class="fa fa-dollar-sign"></i>
                    <span class="float-end">@currency($staff_commission)</span>
                </div>
            </div>
        </div>
    </div>
    <div class="py-2"></div>
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Orders</h2>
            </div>
            <div class="float-end">
                <a class="btn btn-secondary ml-2 float-end" href="/orders" style="margin-right: 10px;"> All Orders </a>
                <a class="btn btn-primary float-end" href="/orders?status=Pending">Pending Orders</a>
                <a class="btn btn-success float-end" href="/orders?status=Complete" style="margin-right: 10px;">Complete Orders</a>
                <a class="btn btn-danger float-end" href="/orders?status=Canceled" style="margin-right: 10px;">Canceled Orders</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                <table class="table table-bordered table-responsive">
                <tr>
                    <th>No</th>
                    <th>Order Id</th>
                    <th>Customer</th>
                    <th>Staff</th>
                    <th>Data \ Time Slot</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
                @if(count($orders))
                @foreach ($orders as $order)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>#{{ $order->id }}</td>

                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->staff_name }}</td>
                    <td>{{ $order->date }} \ {{ $order->time_slot_value }}</td>
                    <td>@currency($order->total_amount)</td>
                    <td>{{ $order->payment_method }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->order_comment }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>
                        <form action="{{ route('orders.destroy',$order->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('orders.show',$order->id) }}">Show</a>
                            @can('order-edit')
                            <a class="btn btn-primary" href="{{ route('orders.edit',$order->id) }}">Edit</a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('order-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="11" class="text-center"> There is no Order</td>
                </tr>
                @endif
            </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection