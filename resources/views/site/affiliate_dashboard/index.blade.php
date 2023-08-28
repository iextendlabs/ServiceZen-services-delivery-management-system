@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Dashboard</h2>
        </div>
    </div>
    <div>
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
        
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('affiliateUrl', ['affiliate_id' => auth()->user()->id]) }}">My Affiliate URL</a>
                <p>{{ route('affiliateUrl', ['affiliate_id' => auth()->user()->id]) }}</p>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <strong>Code:</strong>
                    <input disabled type="text" name="code" id="code" class="form-control" placeholder="Code" value="{{$user->affiliate->code ?? null }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <strong>Commission:</strong>
                    <input disabled type="text" name="commission" id="commission" class="form-control" placeholder="Commission" value="{{$user->affiliate->commission ?? null }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <strong>Fix Salary:</strong>
                    <input disabled type="text" name="fix_salary" id="fix_salary" class="form-control" placeholder="Fix Salary" value="{{'Rs.'.$user->affiliate->fix_salary * $pkrRateValue ?? null }}">
                </div>
            </div>
        </div>
        <p>Your current balance is: <b>Rs.{{ $total_balance }}</b></p>
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
                <td>@if($transaction->order_id) Order ID: #{{ $transaction->order_id }} @else Paid Amount @endif</td>
                <td>Rs.{{ $transaction->formatted_amount }}</td>
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
</div>
@endsection