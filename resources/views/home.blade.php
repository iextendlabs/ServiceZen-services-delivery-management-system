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
                <!-- All Orders -->
                <a class="btn btn-secondary ml-2 float-end" href="/orders" style="margin-right: 10px;">
                    <i class="fas fa-list"></i> All Orders
                </a>

                <!-- Pending Order -->
                <a class="btn btn-primary float-end" href="/orders?status=Pending" style="margin-right: 10px;">
                    <i class="fas fa-clock"></i> Pending Order
                </a>

                <!-- Complete Order -->
                <a class="btn btn-success float-end" href="/orders?status=Complete" style="margin-right: 10px;">
                    <i class="fas fa-check"></i> Complete Order
                </a>

                <!-- Canceled Order -->
                <a class="btn btn-danger float-end" href="/orders?status=Canceled" style="margin-right: 10px;">
                    <i class="fas fa-times"></i> Canceled Order
                </a>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-responsive">
                        <tr>
                            <th>Order #</th>
                            <th>Staff</th>
                            <th>Data \ Time Slot</th>
                            @if(auth()->user()->getRoleNames() == '["Supervisor"]')
                            <th>Landmark</th>
                            <th>Area</th>
                            <th>City</th>
                            <th>Building name</th>
                            @else
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Comment</th>
                            @endif
                            <th>Status</th>
                            <th>Date Added</th>
                            <th style="min-width:160px">Action</th>
                        </tr>
                        @if(count($orders))
                        @foreach ($orders as $order)
                        <tr>
                            <th>#{{ $order->id }}</th>
                            <td>{{ $order->staff_name }}</td>
                            <td>{{ $order->date }} \ {{ $order->time_slot_value }}</td>
                            @if(auth()->user()->getRoleNames() == '["Supervisor"]')
                            <td>{{ $order->landmark }}</td>
                            <td>{{ $order->area }}</td>
                            <td>{{ $order->city }}</td>
                            <td>{{ $order->buildingName }}</td>
                            @else
                            <td>{{ $order->customer_name }}</td>
                            <td>@currency($order->total_amount)</td>
                            <td>{{ $order->payment_method }}</td>
                            <td>{{ $order->order_comment }}</td>
                            @endif
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>
                                <form action="{{ route('orders.destroy',$order->id) }}" method="POST">
                                    <a class="btn btn-info" href="{{ route('orders.show',$order->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @can('order-edit')
                                    <a class="btn btn-primary" href="{{ route('orders.edit',$order->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @csrf
                                    @method('DELETE')
                                    @can('order-delete')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>

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