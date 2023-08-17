@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Your Transactions</h2>
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
        @if(count($transactions) != 0)
        @php
        $total_balance = 0;
        @endphp

        @foreach($transactions as $transaction)
        @php
        $total_balance += $transaction->amount;
        @endphp
        @endforeach
        <p>Your current balance is: <b>@currency( $total_balance )</b></p>
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
                <td>Order ID: #{{ $transaction->id }}</td>
                <td>@currency( $transaction->amount )</td>

            </tr>
            @endforeach

        </table>
        @else
        <div class="text-center">
            <h4>There is no Transactions</h4>
        </div>
        @endif
    </div>
</div>
@endsection