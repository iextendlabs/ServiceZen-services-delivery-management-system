@extends('site.layout.app')

@section('content')
<div class="container">
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Your Booked Service</h2>
    </div>
</div>
@php
$total_amount = 0;
@endphp
    <div class="text-center" style="margin-bottom: 20px;">
        @if(Session::has('error'))
        <span class="alert alert-danger" role="alert">
            <strong>{{ Session::get('error') }}</strong>
        </span>
        @endif
        @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            <span>{{ Session::get('success') }}</span><br>
            <span>To add more service<a href="/"> Continue</a></span>
        </div>
        </span>
        @endif
    </div>
    @if(count($formattedBookings) != 0)
    <table class="table table-striped table-bordered album bg-light">
        <tr>
            <th>Image</th>
            <th>Service Name</th>
            <th>
                <span>Price</span><br>
                <span>Duration</span>
            </th>
            <th>
                Booking Detail
            </th>
            <th>Action</th>
        </tr>
        @foreach ($formattedBookings as $key => $booking)
        <tr>
            <td><img src="service-images/{{ $booking['service']->image }}" height="60px" width="60px" style="border: 1px solid #ddd; border-radius: 4px;"></td>
            <td>{{ $booking['service']->name }}</td>
            <td>
                @if($booking['option'] !== null)
                    <span>@currency($booking['option']->option_price)</span>
                @else
                    <span>
                        @if(isset($booking['service']->discount))  
                            @currency($booking['service']->discount) 
                        @else 
                            @currency($booking['service']->price)
                        @endif
                    </span>
                @endif
                <br><span>{{ $booking['service']->duration }}</span><br>
                @if($booking['option'] !== null)
                <span>{{ $booking['option']->option_name }}</span>
                @endif
            </td>
            <td>
                <i class="fa fa-calendar"></i> {{ $booking['date'] }} <br>
                <i class="fa fa-user"></i> {{ $booking['staff'] }} <br>
                <i class="fa fa-clock"></i>  {{ $booking['slot'] }} <br>
            </td>
            <td>
                <div class="btn-group">
                    <a href="/removeToCart/{{ $booking['service']->id }}"><button type="button" class="btn btn-md btn-outline-danger"><i class="fa fa-times-circle"></i></button></a>
                </div>
            </td>
        </tr>
        @if($booking['option'] !== null)
        @php
        $total_amount += $booking['option']->option_price;
        @endphp
        @else
        @if(isset($booking['service']->discount))
        @php
        $total_amount += $booking['service']->discount;
        @endphp
        @else
        @php
        $total_amount += $booking['service']->price;
        @endphp
        @endif
        @endif
        @endforeach
    </table>
    <div class="row">
        <div class="col-sm-4 offset-sm-8 ">
            <table class="table table-striped table-bordered">
                <tbody>
                    <tr>
                        <td class="text-right"><strong>Total Services:</strong></td>
                        <td class="text-right">{{count($formattedBookings)}}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><strong>Total:</strong></td>
                        <td class="text-right">@currency($total_amount)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="text-center">
        <h4>Cart is Empty</h4>
    </div>
    @endif
    @if(count($formattedBookings))
    <div class="text-center">
        <a href="bookingStep">
            <button type="button" class="btn btn-success">Checkout</button>
        </a>
    </div>
    @endif
</div>
@endsection