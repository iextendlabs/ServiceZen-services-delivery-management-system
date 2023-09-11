@extends('site.layout.app')
<style>
    .detail-item .detail-item-label {
        display: block;
        width: 40%;
        color: #80878d;
        font-size: 20px;
    }

    .detail-item .detail-item-value {
        width: 55%;
    }

    ._sb {
        justify-content: space-between;
        display: flex;
    }
</style>


@section('content')

<div class="album bg-light">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Confirm Order</h2>
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
        <div class="row">
            <div class="col-md-12 mt-3">
                <h5>Services</h5>
                <table class="table" style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>image</th>
                            <th>name</th>
                            <th>duration</th>
                            <th>price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $key=>$service)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td><img src="service-images/{{ $service->image }}" height="60px" width="60px" style="border: 1px solid #ddd; border-radius: 4px;"></td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->duration }}</td>
                            @if(isset($service->discount))
                            <td>@currency( $service->discount )</td>
                            @else
                            <td>@currency( $service->price )</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-12 mt-3">
                <h5>Booking Details</h5>
                <div class="detail-item _sb pb-3 pt-3">
                    <div class="detail-item-label pt-5">
                        <div>Address</div>
                    </div>
                    <div class="detail-item-value">
                        <div> <strong>Building Name:</strong> {{ $address['buildingName'] }}</div>
                        <div> <strong>FlatVilla:</strong> {{ $address['flatVilla'] }}</div>
                        <div> <strong>Street:</strong> {{ $address['street'] }}</div>
                        <div> <strong>Area:</strong> {{ $address['area'] }}</div>
                        <div> <strong>City:</strong> {{ $address['city'] }}</div>
                        <div> <strong>Number:</strong> {{ $address['number'] }}</div>
                    </div>
                </div>
                <hr>
                <div class="detail-item _sb pb-3 pt-3">
                    <div class="detail-item-label pt-3">
                        <div>Time Slots And Staff</div>
                    </div>
                    <div class="detail-item-value">
                        <div> <strong>Time Slot:</strong> {{ date('h:i A', strtotime($time_slot->time_start)) }} -- {{ date('h:i A', strtotime($time_slot->time_end)) }}</div>
                        <div> <strong>Staff:</strong> {{ $staff->name }}</div>
                        <div> <strong>Date:</strong> {{ $staff_and_time['date'] }}</div>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mt-3 mt-3 offset-md-3 ">
                <h5>Payment Summary</h5>
                <table class="table">
                    <tr>
                        <td class="text-left"><strong> Service Total:</strong></td>
                        <td>@currency($sub_total)</td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong> Coupon Discount:</strong></td>
                        <td>@currency( '-'.$coupon_discount)</td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong>Staff Charges:</strong></td>
                        <td>@currency($staff_charges)</td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong>Transport Charges:</strong></td>
                        <td>@currency($transport_charges)</td>
                    </tr>
                    <tr>
                        <td class="text-left"><strong>Total:</strong></td>
                        <td>@currency($total_amount)</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <form action="{{ route('order.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Payment Method:</strong>
                                <select name="payment_method" class="form-control">
                                    <option value="Cash-On-Delivery">Cash On Delivery</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Comment:</strong>
                                <textarea name="order_comment" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">Confirm Order</button><br><br>
                            <a href="/bookingStep">
                                <button type="button" class="btn btn-secondary">Edit Order</button>
                            </a>
                            <a href="/">
                                <button type="button" class="btn btn-primary">Continue Shopping</button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection