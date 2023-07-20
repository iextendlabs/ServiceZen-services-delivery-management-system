@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="album bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-5 text-center">
                <h2>Submit Cash</h2>

                <h3>Total: {{$order->total_amount}}</h3>
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
        <form action="{{ route('cashCollections.store') }}" method="POST">
            @csrf
            <input type="hidden" name="staff_id" value="{{ $order->service_staff_id }}">
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Description:</strong>
                        <textarea name="description" class="form-control" cols="10" rows="5"></textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Amount:</strong>
                        <input type="number" name="amount" class="form-control" placeholder="Amount">
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection