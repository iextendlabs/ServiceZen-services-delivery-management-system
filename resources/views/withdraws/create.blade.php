@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <h2>Add New Withdraw</h2>
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
        <form action="{{ route('withdraws.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>User:</strong>
                        <select name="user_id" class="form-control">
                            <option></option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @if (old('user_id') == $user->id) selected @endif>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <input type="hidden" name="user_name" id="user_name">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Amount:</strong>
                        <input type="number" name="amount" value="{{ old('amount') }}" class="form-control"
                            placeholder="Amount">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option value="Approved" @if (old('status') === 'Approved') selected @endif>
                                Approved</option>
                            <option value="Un Approved" @if (old('status') === 'Un Approved') selected @endif>
                                Un Approved</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Payment as:</strong>
                        <select name="payment_method" class="form-control">
                            <option></option>
                            @foreach (config('app.affiliate_payment_method') as $payment_method)
                                <option value="{{ $payment_method }}" @if (old('payment_method') == $payment_method) selected @endif>
                                    {{ $payment_method }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Account Detail:</strong>
                        <textarea class="form-control" style="height:150px" name="account_detail" placeholder="Account Detail">{{ old('account_detail') }}</textarea>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            $('select[name="user_id"]').change(function() {
                var selectedOption = $(this).find(":selected");
                $('#user_name').val(selectedOption.text()); // Extract the user name from the selected option
            });
        });
    </script>
@endsection
