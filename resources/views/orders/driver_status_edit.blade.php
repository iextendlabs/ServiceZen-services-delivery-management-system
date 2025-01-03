@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Edit Order Driver Status</h2>
        </div>
    </div>
    <div class="container">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
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
            <form action="{{ route('orders.driver_status_edit',$order->id) }}" method="POST">
                @csrf
                <input type="hidden" name="url" value="{{ url()->previous() }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="driver_status" class="form-control">
                                @foreach ($driver_statuses as $status)
                                <option value="{{ $status }}" {{ old('driver_status', $order->driver_status) == $status ? 'selected' : "" }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 text-right no-print">
                        @can('order-edit')
                        <button type="submit" class="btn btn-primary">Update</button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>
@endsection