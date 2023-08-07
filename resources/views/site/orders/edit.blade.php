@extends('site.layout.app')
<base href="/public">
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Edit Order</h2>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <span>{{ $message }}</span>
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
        <form action="{{ route('order.update',$order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div id="slots-container" class="col-md-12">
                    @include('site.checkOut.timeSlots')
                </div>
                @if(Auth::user()->hasRole('Staff'))
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">
                            @foreach ($statuses as $status)
                            @if($status == $order->status)
                            <option value="{{ $status }}" selected>{{ $status }}</option>
                            @else
                            <option value="{{ $status }}">{{ $status }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-12 text-right no-print">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>

@endsection