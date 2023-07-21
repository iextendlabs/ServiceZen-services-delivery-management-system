@extends('site.layout.app')
<link href="{{ asset('css/checkout.css') }}" rel="stylesheet">
<base href="/public">
@section('content')

<div class="album bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-5 text-center">
                <h2>Booking Step</h2>
                <h3>Your Current Area: {{ $area }}</h3>
            </div>
        </div>
        <div class="text-center" style="margin-bottom: 20px;">
            @if(Session::has('error'))
            <span class="alert alert-danger" role="alert">
                <strong>{{ Session::get('error') }}</strong>
            </span>
            @endif
            @if(Session::has('success'))
            <span class="alert alert-success" role="alert">
                <strong>{{ Session::get('success') }}</strong>
            </span>
            @endif
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
        <form action="timeSlotsSession" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Date:</strong>
                        <input type="date" name="date" id="date" min="{{ date('Y-m-d'); }}" value="{{ date('Y-m-d'); }}" class="form-control" placeholder="Date">
                    </div>
                </div>
                <div class="col-md-12 scroll-div">
                    <strong>Time Slots</strong>
                    <div id="loading" style="display: none;">Loading...</div>
                    <div class="list-group" id="time-slots-container">
                        <input type="hidden" name="city" value="{{ $city }}">
                        <input type="hidden" name="area" value="{{ $area }}">
                        @include('site.checkOut.timeSlots')
                        
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group" id="detail-container">
                        <strong>Selected Time Slot:</strong><span id="selected-time-slot"></span><br>
                        <strong>Selected Staff:</strong><span id="selected-staff"></span>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-block mt-2 mb-2 btn-success">Next</button>
                    <a href="cart">
                        <button type="button" class="btn btn-block btn-secondary">Back</button>
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/checkout.js') }}"></script>
@endsection