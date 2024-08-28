@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Driver</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $driver->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {{ $driver->email }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Roles:</strong>
                @if(!empty($driver->getRoleNames()))
                @foreach($driver->getRoleNames() as $v)
                <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Phone Number:</strong>
                {{ $driver->driver->phone }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Whatsapp Number:</strong>
                {{ $driver->driver->whatsapp }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Commission:</strong>
                {{ $driver->driver->commission }}%
            </div>
        </div>
        @if($driver->driver->affiliate)
        <div class="col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {{ $driver->driver->affiliate->name }}
            </div>
        </div>
        @endif
    </div>
    <hr>
    <div class="row">
        <p>Current balance is: <b>@currency($total_balance,true)</b></p>
        @if(count($transactions) != 0)
        <table class="table table-striped table-bordered album bg-light">
            <tr>
                <th>Sr#</th>
                <th>Date Added</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
            @foreach ($transactions as $transaction)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $transaction->created_at }}</td>
                <td>{{ $transaction->type }}</td>
                <td>@if($transaction->order_id) Order ID: #{{ $transaction->order_id }} @else {{ substr($transaction->description,0,70) }} @endif </td>
                <td>@currency($transaction->amount,true)</td>
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
        <div class="col-md-6">
            <h3>Add Transaction</h3>
            <p>Current balance is: <b>@currency($total_balance,true)</b></p>
            <form action="{{ route('transactions.store') }}" method="POST" id="pay-transactions">
                @csrf
                <input type="hidden" name="user_id" value="{{ $driver->id }}">
                <input type="hidden" name="pay" value="1">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Amount:</strong>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ config('app.currency') }}</span>
                                </div>
                                <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" placeholder="Amount">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Type:</strong>
                            <select name="type" class="form-control">
                                <option value="Credit">Credit</option>
                                <option value="Debit">Debit</option>
                                <option value="Product Sale">Product Sale</option>
                                <option value="Bonus">Bonus</option>
                                <option value="Pay Salary">Pay Salary</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Description:</strong>
                            <textarea name="description" cols="10" rows="5" class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" value="transaction" name="submit_type" class="btn btn-primary" form="pay-transactions">Add Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection