@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Service Staff</h2>
            </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <span>{{ $message }}</span>
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
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
            <div class="form-group">
                <strong>Name:</strong>
                {{ $serviceStaff->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {{ $serviceStaff->email }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Phone Number:</strong>
                {{ $serviceStaff->staff->phone }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Commission:</strong>
                {{ $serviceStaff->staff->commission }}%
            </div>
        </div>
        @if(isset( $serviceStaff->staff->charges ))
        <div class="col-md-12">
            <div class="form-group">
                <strong>Additional Charges:</strong>
                @currency($serviceStaff->staff->charges,true)
            </div>
        </div>
        @endif
        <div class="col-md-12">
            <div class="form-group">
                <strong>Commission Salary:</strong>
                @currency($serviceStaff->staff->fix_salary,true)
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Bonus of {{ now()->format('F') }}:</strong>
                @currency($bonus,true)
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Product Sales of {{ now()->format('F') }}:</strong>
                @currency($product_sales,true)
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Roles:</strong>
                @if(!empty($serviceStaff->getRoleNames()))
                @foreach($serviceStaff->getRoleNames() as $v)
                <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                @endforeach
                @endif
            </div>
        </div>
        @foreach($documents as $field => $label)
            <div class="col-md-12">
                <div class="form-group">
                    <strong>{{ $label }}:</strong>
                    @if($serviceStaff->document && $serviceStaff->document->$field)
                        <p>
                            <a href="{{ asset('staff-document/' . $serviceStaff->document->$field) }}" target="_blank">{{ $serviceStaff->document->$field }}</a>
                        </p>
                    @else
                        <p>No {{ $label }} uploaded.</p>
                    @endif
                </div>
            </div>
        @endforeach
        @if($freelancer_join)
        <div class="col-md-12">
            <div class="form-group">
                <strong>Expir at:</strong>
                {{ $serviceStaff->staff->expiry_date }}
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
            <p>Current balance with salary is: <b>@currency($total_balance,true)+ @currency($serviceStaff->staff->fix_salary,true)</b></p>
            <form action="{{ route('transactions.store') }}" method="POST" id="pay-transactions">
                @csrf
                <input type="hidden" name="user_id" value="{{ $serviceStaff->id }}">
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
                                <option value="Credit" {{ old('type') == 'Credit' ? 'selected' : '' }}>Credit</option>
                                <option value="Debit" {{ old('type') == 'Debit' ? 'selected' : '' }}>Debit</option>
                                <option value="Product Sale" {{ old('type') == 'Product Sale' ? 'selected' : '' }}>Product Sale</option>
                                <option value="Bonus" {{ old('type') == 'Bonus' ? 'selected' : '' }}>Bonus</option>
                                <option value="Pay Salary" {{ old('type') == 'Pay Salary' ? 'selected' : '' }}>Pay Salary</option>
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