@extends('site.layout.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-5 text-center">
                <h2>Your Booked Service</h2>
            </div>
        </div>
        <div class="text-center" style="margin-bottom: 20px;">
            @if (Session::has('error'))
                <span class="alert alert-danger" role="alert">
                    <strong>{{ Session::get('error') }}</strong>
                </span>
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">
                    <span>{{ Session::get('success') }}</span><br>
                    <span>To add more service<a href="/"> Continue</a></span>
                </div>
                </span>
            @endif
        </div>
        @if (count($formattedBookings) != 0)
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
                        <td><a href="/service/{{ $booking['service']->slug }}">
                                <img src="service-images/{{ $booking['service']->image }}" height="60px" width="60px"
                                    style="border: 1px solid #ddd; border-radius: 4px;"></a></td>
                        <td><a href="/service/{{ $booking['service']->slug }}"> {{ $booking['service']->name }}</a></td>
                        <td>
                            @if ($booking['option_total_price'] > 0)
                                <span>@currency($booking['option_total_price'], false, true)</span>
                            @else
                                <span>
                                    @if (isset($booking['service']->discount))
                                        @currency($booking['service']->discount, false, true)
                                    @else
                                        @currency($booking['service']->price, false, true)
                                    @endif
                                </span>
                            @endif
                            @if ($booking['option_total_duration'] != null)
                                <br><span>{{ $booking['option_total_duration'] }}</span>
                            @elseif($booking['service']->duration)
                                <br><span>{{ $booking['service']->duration }}</span>
                            @endif
                            @if (count($booking['option']) != 0)
                                <br>
                                @foreach ($booking['option'] as $option)
                                    <span>
                                        {{ $option->option_name }}
                                    </span><br>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <i class="fa fa-calendar"></i> {{ $booking['date'] }} <br>
                            <i class="fa fa-user"></i> {{ $booking['staff'] }} <br>
                            <i class="fa fa-clock"></i> {{ $booking['slot'] }} <br>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="/removeToCart/{{ $booking['service']->id }}"><button type="button"
                                        class="btn btn-md btn-outline-danger"><i
                                            class="fa fa-times-circle"></i></button></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            <div class="text-center">
                <h4>Cart is Empty</h4>
            </div>
        @endif
        @if (count($formattedBookings))
            <div class="text-center">
                <a href="bookingStep">
                    <button type="button" class="btn btn-success">Checkout</button>
                </a>
            </div>
        @endif
    </div>
@endsection
