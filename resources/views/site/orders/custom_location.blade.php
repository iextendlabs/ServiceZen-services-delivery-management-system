@extends('site.layout.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Edit Order (#{{ $order->id }})</h2>
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
                <div class="col-md-12 text-center">
                    <br>
                    <h3><strong>Custom Location</strong></h3>
                    <hr>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Custom Location:</strong>
                        <input type="text" name="custom_location" class="form-control" value="{{ $order->latitude }}, {{ $order->longitude }}">
                    </div>
                </div>
            </div>

            <div class="col-md-12 text-right no-print">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection