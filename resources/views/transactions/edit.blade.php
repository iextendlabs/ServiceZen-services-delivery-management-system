@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <h2>Edit Transaction</h2>
            </div>
        </div>
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
        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="url" value="{{ url()->previous() }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Order Id:</strong>
                        <input type="text" name="order_id" value="{{ $transaction->order_id }}" class="form-control"
                            disabled>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>User:</strong>
                        <input type="text" name="user_id" value="{{ $transaction->user->name }}" class="form-control"
                            disabled>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Type:</strong>
                        <input type="text" name="type" value="{{ $transaction->type }}" class="form-control" disabled>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Amount:</strong>
                        <input type="text" name="amount" value="{{ $transaction->amount }}" class="form-control"
                            placeholder="Amount">
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
