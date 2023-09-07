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
    @if(count($booked_services) != 0)
    <table class="table table-striped table-bordered album bg-light">
        <tr>
            <th>Sr#</th>
            <th>image</th>
            <th>Service Name</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Action</th>
        </tr>
        @foreach ($booked_services as $booked_service)
        <tr>
            <td>{{ ++$i }}</td>
            <td><img src="service-images/{{ $booked_service->image }}" height="60px" width="60px" style="border: 1px solid #ddd; border-radius: 4px;"></td>
            <td>{{ $booked_service->name }}</td>
            @if(isset($booked_service->discount))
            <td>@currency( $booked_service->discount)</td>
            @else
            <td>@currency( $booked_service->price)</td>
            @endif
            <td>{{ $booked_service->duration }}</td>
            <td>
                <div class="btn-group">
                    <a href="/removeToCart/{{ $booked_service->id }}"><button type="button" class="btn btn-md btn-outline-danger"><i class="fa fa-times-circle"></i></button></a>
                </div>
            </td>
        </tr>
        @if(isset($booked_service->discount))
        @php
        $total_amount += $booked_service->discount;
        @endphp
        @else
        @php
        $total_amount += $booked_service->price;
        @endphp
        @endif
        @endforeach
    </table>
    <div class="row">
        <div class="col-sm-4 offset-sm-8 ">
            <table class="table table-striped table-bordered">
                <tbody>
                    <tr>
                        <td class="text-right"><strong>Total Services:</strong></td>
                        <td class="text-right">{{count($booked_services)}}</td>
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
    @if(count($booked_services))
    <div class="text-center">
        <a href="bookingStep">
            <button type="button" class="btn btn-success">Checkout</button>
        </a>
    </div>
    @endif
</div>
@endsection