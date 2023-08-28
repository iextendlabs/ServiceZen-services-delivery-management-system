@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2> Show Affiliate</h2>
        </div>
    </div>
</div>
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
            Rs.{{ $pkrRateValue * $affiliate->affiliate->fix_salary }}
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
        </tr>
        @foreach ($transactions as $transaction)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $transaction->created_at }}</td>
            <td>@if($transaction->order_id) Order ID: #{{ $transaction->order_id }} @else Paid Amount @endif </td>
            <td>@currency($transaction->amount) (Rs.{{ $transaction->formatted_amount }})</td>
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
    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ $affiliate->id }}">
        <input type="hidden" name="pay" value="1">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Amount:</strong>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">AED</span>
                        </div>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" placeholder="Amount">
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </form>
</div>
@endsection