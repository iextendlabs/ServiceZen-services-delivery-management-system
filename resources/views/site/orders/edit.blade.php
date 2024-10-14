@extends('site.layout.app')

<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">

@section('content')
@php
$total_amount = 0;
$staff_charges = 0;
$transport_charges = 0;
@endphp
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
                <div id="slots-container" class="col-md-12">
                    @include('site.checkOut.timeSlots')
                </div>
                @if(Auth::user() && Auth::user()->hasRole('Staff'))
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">
                            @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ old('status',$order->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <br>
                    <h3><strong>Address</strong></h3>
                    <hr>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Building Name:</strong>
                        <input required type="text" name="buildingName" id="buildingName" class="form-control" placeholder="Building Name" value="{{ old('buildingName',$order->buildingName ?? null) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Flat / Villa:</strong>
                        <input required type="text" name="flatVilla" id="flatVilla" class="form-control" placeholder="Flat / Villa" value="{{ old('flatVilla',$order->flatVilla ?? null) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Street:</strong>
                        <input required type="text" name="street" id="street" class="form-control" placeholder="Street" value="{{ old('street',$order->street ?? null) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Area:</strong>
                        <select required class="form-control" name="area" id="area">
                            <option value="">-- Select Zone -- </option>
                            <!-- Loop through the $zones array to generate options -->
                            @foreach ($zones as $zone)
                            <option {{ old('area',$order->area) == $zone ? 'selected' : '' }} value="{{ $zone }}">
                                {{ $zone }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Landmark:</strong>
                        <input required type="text" name="landmark" id="landmark" class="form-control" placeholder="Landmark" value="{{ old('landmark',$order->landmark ?? null) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>City:</strong>
                        <input required type="text" name="city" id="city" class="form-control" placeholder="City" value="{{ old('city',$order->city ?? null) }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <br>
                    <h3><strong>Personal information</strong></h3>
                    <hr>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Phone Number:</strong>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input required type="tel" name="number" id="number" class="form-control" value="{{ $order->number ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input required type="tel" name="whatsapp" id="whatsapp" class="form-control" value="{{ $order->whatsapp ?? null }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <br>
                    <h3><strong>Services</strong></h3>
                    <hr>
                </div>
                <div class="col-md-12">
                    <div class="form-group scroll-div">
                        <span style="color: red;">*</span><strong>Services:</strong>
                        <input type="text" name="search-services" id="search-services" class="form-control" placeholder="Search Services By Name, Price And Duration">
                        <table class="table table-striped table-bordered services-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Duration</th>
                            </tr>
                            @foreach ($services as $service)
                            <tr>
                                <td>
                                    <input type="checkbox" class="service-checkbox checkout-services" name="service_ids[]" value="{{ $service->id }}" data-price="{{ isset($service->discount) ? $service->discount : $service->price }}" {{ in_array($service->id, old('service_ids', $order_service)) ? 'checked' : '' }}>
                                </td>
                                <td>{{ $service->name }}</td>

                                <td>{{ isset($service->discount) ? 
                                 $service->discount : $service->price }}</td>
                                <td>{{ $service->duration }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Comment:</strong>
                        <textarea name="order_comment" class="form-control" cols="30" rows="5">{{ old('order_comment',$order->order_comment) }}</textarea>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-3 offset-md-3 ">
                    <h5>Payment Summary</h5>
                    <table class="table">
                        <tr>
                            <td class="text-left"><strong> Service Total:</strong></td>
                            <td>{{ config('app.currency') }} <span id="sub_total">{{ $order->order_total->sub_total }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Coupon Discount:</strong></td>
                            <td>{{ config('app.currency') }} <span id="coupon-discount">{{ $order->order_total->discount ? '-'.$order->order_total->discount : 0 }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Staff Charges:</strong></td>
                            <td>{{ config('app.currency') }} <span id="staff_charges">{{ isset($order->staff->charges) ? $order->staff->charges : 0 }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Transport Charges:</strong></td>
                            <td>{{ config('app.currency') }} <span id="transport_charges">{{ isset($staffZone->transport_charges) ? $staffZone->transport_charges : 0 }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Total:</strong></td>
                            <td>{{ config('app.currency') }} <span id="total_amount">{{ $order->total_amount}}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-right no-print">
                <button type="submit" class="btn btn-primary">
                    @if($order->status =="Draft")Save & Confirm Order @else Update @endif</button>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#search-services").keyup(function() {
            let value = $(this).val().toLowerCase();

            $(".services-table tr").hide();

            $(".services-table tr").each(function() {
                let $row = $(this);

                let name = $row.find("td:nth-child(2)").text().toLowerCase();
                let price = $row.find("td:nth-child(3)").text().toLowerCase();
                let duration = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) !== -1 || price.indexOf(value) !== -1 || duration.indexOf(value) !== -1) {
                    $row.show();
                }
            });
        });

        $('.service-checkbox').on('change', function() {

            let sub_total = 0;

            $('.service-checkbox:checked').each(function() {
                let price = parseFloat($(this).data('price'));
                sub_total += price;
            });

            $('input[name="sub_total"]').val(sub_total.toFixed(2));
            $('#sub_total').text(sub_total.toFixed(2));
            updateTotal();
        });
    });

    $(document).on('change', '#zone', function() {
        $('#area').val($(this).val());
    });

    $(document).on('change', '#area', function() {
        $('#zone').val($(this).val());
    });
</script>
<script>
    function updateTotal() {
        let transport_charges = parseFloat($('#zone').find(':selected').data('transport-charges'));

        $('#transport_charges').text(transport_charges);

        let staff_charges = parseFloat($('input[name="service_staff_id"]:checked').data('staff-charges'));

        let coupon_discount = parseFloat($('#coupon-discount').text());

        $('#staff_charges').text(staff_charges);

        let total_amount = 0;

        let sub_total = parseFloat($('#sub_total').text());
        total_amount = sub_total + staff_charges + transport_charges + coupon_discount;

        $('#total_amount').text(total_amount.toFixed(2));
    }
</script>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>

@endsection