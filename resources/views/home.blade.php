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
        @can('dashboard-report')
        @if(auth()->user()->getRoleNames() == '["Admin"]')
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
        @endif
        @if(auth()->user()->getRoleNames() == '["Staff"]')
        <div class="col-md-4 py-2">
            <div class="card">
                <div class="card-header">Salary</div>
                <div class="card-body analytic">
                    <i class="fa fa-credit-card"></i>
                    <span class="float-end">@currency(auth()->user()->staff->fix_salary)</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 py-2">
            <div class="card">
                <div class="card-header">Total Balance</div>
                <div class="card-body analytic">
                    <i class="fa fa-credit-card"></i>
                    <span class="float-end">@currency($staff_total_balance)</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 py-2">
            <div class="card">
                <div class="card-header">Product Sale of {{ now()->format('F') }}</div>
                <div class="card-body analytic">
                    <i class="fa fa-dollar-sign"></i>
                    <span class="float-end">@currency($staff_product_sales)</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 py-2">
            <div class="card">
                <div class="card-header">Total Bonus of {{ now()->format('F') }}</div>
                <div class="card-body analytic">
                    <i class="fa fa-dollar-sign"></i>
                    <span class="float-end">@currency($staff_bonus)</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 py-2">
            <div class="card">
                <div class="card-header">Total Order Commission of {{ now()->format('F') }}</div>
                <div class="card-body analytic">
                    <i class="fa fa-dollar-sign"></i>
                    <span class="float-end">@currency($staff_order_commission)</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 py-2">
            <div class="card">
                <div class="card-header">Other Income of {{ now()->format('F') }}</div>
                <div class="card-body analytic">
                    <i class="fa fa-dollar-sign"></i>
                    <span class="float-end">@currency($staff_other_income)</span>
                </div>
            </div>
        </div>
        @endif
        @endcan
    </div>
    @can('order-list')
    <div class="py-2"></div>
    <div class="row">
        <div class="col-md-12 margin-tb text-center">
            <div>
                <h2>Orders</h2>
            </div>
        </div>
        <div class="col-md-12 margin-tb mb-3">

            <div class="float-end">
                @if(auth()->user()->getRoleNames() != '["Supervisor"]')
                @can('order-download')
                <a class="btn btn-danger float-end" href="/orders?print=1"><i class="fa fa-print"></i> PDF</a>
                <a href="/orders?csv=1" class="btn btn-success float-end mr-1"><i class="fa fa-download"></i> Excel</a>
                @endcan
                @endif

                @if(auth()->user()->getRoleNames() == '["Admin"]')
                <a class="btn btn-secondary mr-1 float-end" href="/orders">
                    <i class="fas fa-list"></i> All
                </a>

                <a class="btn btn-danger float-end mr-1" href="/orders?status=Canceled">
                    <i class="fas fa-times"></i> Canceled
                </a>
                @endif
                @if(auth()->user()->getRoleNames() != '["Staff"]')
                <a class="btn btn-primary float-end mr-1" href="/orders?status=Pending">
                        <i class="fas fa-clock"></i> Pending
                </a>

                <a class="btn btn-warning float-end mr-1" href="/orders?status=Rejected">
                    <i class="fas fa-times"></i> Rejected
                </a>

                <a class="btn btn-info float-end mr-1" href="/orders?status=Inprogress">
                    <i class="fas fa-hourglass-split"></i> Inprogress
                </a>
                @endif
                <a class="btn btn-success float-end mr-1" href="/orders?status=Complete">
                    <i class="fas fa-check"></i> Complete
                </a>

                <a class="btn btn-success float-end mr-1" href="/orders?status=Accepted">
                    <i class="fas fa-check"></i> Accepted
                </a>

                <a class="btn btn-info float-end mr-1" href="/orders?status=Confirm">
                    <i class="fas fa-check"></i> Confirm
                </a>

                <a class="btn btn-secondary float-end mr-1" href="{{route('orders.index')}}?appointment_date={{date('Y-m-d')}}">
                    <i class="fas fa-calendar"></i>Todays Order
                </a>

                @can('order-create')
                <div class="float-end">
                    <a class="btn btn-success mt-2" href="{{route('orders.create')}}">
                        <i class="fas fa-plus"></i> Create Order
                    </a>
                </div>
                @endcan
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
    @endcan
</div>
@endsection