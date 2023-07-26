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
        <div class="col-md-12 text-center">
            <h1>Dashboard</h1>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">TOTAL SALES</div>
                <div class="card-body analytic">
                    <i class="fa fa-credit-card"></i>
                    <span class="float-end">@currency($sale)</span>
                </div>
            </div>
        </div>
        @if(auth()->user()->getRoleNames() == '["Admin"]')
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
        @endif
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
                    @include('orders.list')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection