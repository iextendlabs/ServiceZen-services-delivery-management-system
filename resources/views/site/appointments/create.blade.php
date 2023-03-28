@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="row">
    <div class="col-lg-12 py-5 text-center">
        <h2>Book Your Service</h2>
    </div>
</div>
<div class="album bg-light">
  <div class="container">
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
    <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="service_id" value="{{ $service_id }}">
        <input type="hidden" name="customer_id" value="{{ $customer_id }}">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Date:</strong>
                    <input type="date" name="date" class="form-control" placeholder="Date">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Time:</strong>
                    <input type="time" name="time" class="form-control" placeholder="Time">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Address:</strong>
                    <textarea name="address" class="form-control" cols="10" rows="5"></textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection