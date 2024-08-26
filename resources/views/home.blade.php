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
            <div class="col-md-6">
                <h2>Dashboard</h2>
            </div>
            <div class="col-md-6">
                @if (auth()->user()->hasRole('Admin'))
                    <a class="btn btn-success float-end" href="{{ route('appData') }}"> Refresh App</a>
                @endif
                @if (auth()->user()->hasRole('Affiliate'))
                    <a class="btn btn-success float-end" href="{{ route('affiliate_dashboard.index') }}">Affiliate DashBorad</a>
                @endif
            </div>
            @if(isset(Auth::user()->affiliate_program) && Auth::user()->affiliate_program == 0)
            <div class="alert alert-warning">
                <span>Your request to join the affiliate program has been submitted and sent to the administrator for review.</span>
            </div>
            @endif
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <span>{{ $message }}</span>
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @can('dashboard-report')
                @if (auth()->user()->hasRole('Admin'))
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">TOTAL SALES</div>
                            <div class="card-body analytic">
                                <i class="fa fa-credit-card"></i>
                                <span class="float-md-end">@currency($sale,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">TOTAL AFFILIATE COMMISSION</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($affiliate_commission,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">TOTAL STAFF COMMISSION</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_commission,true)</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if (auth()->user()->hasRole('Staff'))
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Salary</div>
                            <div class="card-body analytic">
                                <i class="fa fa-credit-card"></i>
                                <span class="float-md-end">@currency(auth()->user()->staff->fix_salary,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Total Balance</div>
                            <div class="card-body analytic">
                                <i class="fa fa-credit-card"></i>
                                <span class="float-md-end">@currency($staff_total_balance,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Product Sale of {{ now()->format('F') }}</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_product_sales,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Total Bonus of {{ now()->format('F') }}</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_bonus,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Total Order Commission of {{ now()->format('F') }}</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_order_commission,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Other Income of {{ now()->format('F') }}</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_other_income,true)</span>
                            </div>
                        </div>
                    </div>
                @endif
            @endcan
        </div>

        @can('order-list')
            <div class="py-2"></div>
            <div class="row">
                <div class="col-md-12 text-center mb-3">
                    <h2>Orders</h2>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="d-flex flex-wrap justify-content-md-end">
                        @if (!auth()->user()->hasRole('Supervisor'))
                            @can('order-download')
                                <a class="btn btn-danger mb-2" href="/orders?print=1"><i class="fa fa-print"></i> PDF</a>
                                <a href="/orders?csv=1" class="btn btn-success mb-2 ms-md-2"><i class="fa fa-download"></i>
                                    Excel</a>
                            @endcan
                        @endif

                        @if (auth()->user()->hasRole('Admin'))
                            <a class="btn btn-secondary mb-2 ms-md-2" href="/orders">
                                <i class="fas fa-list"></i> All
                            </a>
                            <a class="btn btn-danger mb-2 ms-md-2" href="/orders?status=Canceled">
                                <i class="fas fa-times"></i> Canceled
                            </a>
                        @endif

                        @if (!auth()->user()->hasRole('Staff'))
                            <a class="btn btn-primary mb-2 ms-md-2" href="/orders?status=Pending">
                                <i class="fas fa-clock"></i> Pending
                            </a>
                            <a class="btn btn-warning mb-2 ms-md-2" href="/orders?status=Rejected">
                                <i class="fas fa-times"></i> Rejected
                            </a>
                            <a class="btn btn-info mb-2 ms-md-2" href="/orders?status=Inprogress">
                                <i class="fas fa-hourglass-split"></i> Inprogress
                            </a>
                        @endif

                        <a class="btn btn-success mb-2 ms-md-2" href="/orders?status=Complete">
                            <i class="fas fa-check"></i> Complete
                        </a>
                        <a class="btn btn-success mb-2 ms-md-2" href="/orders?status=Accepted">
                            <i class="fas fa-check"></i> Accepted
                        </a>
                        <a class="btn btn-info mb-2 ms-md-2" href="/orders?status=Confirm">
                            <i class="fas fa-check"></i> Confirm
                        </a>

                        @can('order-create')
                            <a class="btn btn-success mb-2 ms-md-2" href="{{ route('orders.create') }}">
                                <i class="fas fa-plus"></i> Create Order
                            </a>
                        @endcan
                        <a class="btn btn-secondary mb-2 ms-md-2"
                            href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}">
                            <i class="fas fa-calendar"></i> Todays Order
                        </a>
                        <a class="btn btn-danger mb-2 ms-md-2"
                            href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Canceled">
                            <i class="fas fa-calendar"></i> Todays Canceled Order
                        </a>
                        <a class="btn btn-success mb-2 ms-md-2"
                            href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Complete">
                            <i class="fas fa-calendar"></i> Todays Complete Order
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
        @endcan
    </div>
@endsection
