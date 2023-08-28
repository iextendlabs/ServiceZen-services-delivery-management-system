@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2> Show Affiliate</h2>
        </div>
    </div>
</div>
@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <span>{{ $message }}</span>
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            {{ $affiliate->name }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Email:</strong>
            {{ $affiliate->email }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Code:</strong>
            {{ $affiliate->affiliate->code }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Commission:</strong>
            {{ $affiliate->affiliate->commission }}%
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Fix Salary:</strong>
            @currency($affiliate->affiliate->fix_salary) (Rs.{{ $pkrRateValue * $affiliate->affiliate->fix_salary }})
            <button type="submit" value="salary" name="type" form="pay-transactions" class="btn btn-primary">Pay Salary</button>

        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Roles:</strong>
            @if(!empty($affiliate->getRoleNames()))
            @foreach($affiliate->getRoleNames() as $v)
            <span class="badge rounded-pill bg-dark">{{ $v }}</span>
            @endforeach
            @endif
        </div>
    </div>
</div>
<hr>
<div class="row">
    <p>Current balance is: <b>@currency($total_balance) (Rs.{{ $total_balance_in_pkr }})</b></p>
    @if(count($transactions) != 0)
    <table class="table table-striped table-bordered album bg-light">
        <tr>
            <th>Sr#</th>
            <th>Date Added</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>
        @foreach ($transactions as $transaction)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $transaction->created_at }}</td>
            <td>@if($transaction->order_id) Order ID: #{{ $transaction->order_id }} @else Paid Amount @endif </td>
            <td>@currency($transaction->amount) (Rs.{{ $transaction->formatted_amount }})</td>
            <td>
            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
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
    </table>
    {!! $transactions->links() !!}
    @else
    <div class="text-center">
        <h4>There are no transactions</h4>
    </div>
    @endif
</div>
<hr>
<div class="row">
    <h3>Pay</h3>
    <p>Current balance is: <b>@currency($total_balance) (Rs.{{ $total_balance_in_pkr }})</b></p>
    <form action="{{ route('transactions.store') }}" method="POST" id="pay-transactions">
        @csrf
        <input type="hidden" name="fix_salary" value="{{ $affiliate->affiliate->fix_salary }}">
        <input type="hidden" name="user_id" value="{{ $affiliate->id }}">
        <input type="hidden" name="pay" value="1">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Amount:</strong>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ config('app.currency') }}</span>
                        </div>
                        <input type="text" name="amount" class="form-control" value="{{ old('amount') }}" placeholder="Amount">
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" value="transaction" name="type" class="btn btn-primary" form="pay-transactions">Add Transaction</button>
            </div>
        </div>

    </form>
</div>
@endsection