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
                    <span class="float-end">${{ $sale }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">TOTAL AFFILIATE COMMISSION</div>
                <div class="card-body analytic">
                    <i class="fa fa-dollar-sign"></i>
                    <span class="float-end">${{ $affiliate_commission }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">TOTAL STAFF COMMISSION</div>
                <div class="card-body analytic">
                    <i class="fa fa-dollar-sign"></i>
                    <span class="float-end">${{ $staff_commission }}</span>
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
                <a class="btn btn-primary float-end" href="/orderFilter?status=Pending">Pending Order</a>
                <a class="btn btn-success float-end" href="/orderFilter?status=Complete" style="margin-right: 10px;">Complete Order</a>
                <a class="btn btn-danger float-end" href="/orderFilter?status=Canceled" style="margin-right: 10px;">Canceled Order</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Id</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        @if(count($orders))
                        @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>@if($order->customer){{ $order->customer->name }}@endif</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>${{ $order->total_amount }}</td>
                            <td>
                                <a class="btn btn-info" href="{{ route('orders.show',$order->id) }}">View</a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6" class="text-center">There is no order.</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="py-2"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Appointments</div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>No</th>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        @if(count($appointments))
                        @foreach ($appointments as $appointment)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $appointment->service->name }}</td>
                            <td>{{ $appointment->price }}</td>
                            <td>{{ $appointment->status }}</td>
                            <td>
                                <form action="{{ route('appointments.destroy',$appointment->id) }}" method="POST">
                                    <a class="btn btn-info" href="{{ route('appointments.show',$appointment->id) }}">Show</a>
                                    @can('appointment-edit')
                                    <a class="btn btn-primary" href="{{ route('appointments.edit',$appointment->id) }}">Edit</a>
                                    @endcan
                                    @csrf
                                    @method('DELETE')
                                    @can('appointment-delete')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                    @endcan
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center" >There is no appointment.</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div> -->
</div>
@endsection