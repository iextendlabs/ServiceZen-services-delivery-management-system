@extends('site.layout.app')
<base href="/public">
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
                        <input required type="text" name="buildingName" id="buildingName" class="form-control" placeholder="Building Name" value="{{ $order->buildingName ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Flat / Villa:</strong>
                        <input required type="text" name="flatVilla" id="flatVilla" class="form-control" placeholder="Flat / Villa" value="{{ $order->flatVilla ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Street:</strong>
                        <input required type="text" name="street" id="street" class="form-control" placeholder="Street" value="{{ $order->street ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Area:</strong>
                        <select required class="form-control" name="area" id="area">
                            <option value="">-- Select Zone -- </option>
                            <!-- Loop through the $zones array to generate options -->
                            @foreach ($zones as $zone)
                            <option @if (old('area')==$zone || $order->area ==$zone) selected @endif value="{{ $zone }}">
                                {{ $zone }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Landmark:</strong>
                        <input required type="text" name="landmark" id="landmark" class="form-control" placeholder="Landmark" value="{{ $order->landmark ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>City:</strong>
                        <input required type="text" name="city" id="city" class="form-control" placeholder="City" value="{{ $order->city ?? null }}">
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
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ config('app.country_code') }}</span>
                            </div>
                            <input required type="text" name="number" id="number" class="form-control" placeholder="Phone Number" value="{{ str_replace('+971', '', $order->number ?? null) }}" pattern="[0-9]{7,9}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ config('app.country_code') }}</span>
                            </div>
                            <input required type="text" name="whatsapp" id="whatsapp" class="form-control" placeholder="Whatsapp Number" value="{{ str_replace('+971', '', $order->whatsapp ?? null) }}" pattern="[0-9]{7,9}">
                        </div>
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
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Services:</strong>
                        <input type="text" name="search-services" id="search-services" class="form-control" placeholder="Search Services By Name, Price And Duration">
                        <div class="scroll-div">
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
                                        @if(in_array($service->id,$order_service))
                                        <input type="checkbox" class="service-checkbox" checked name="service_ids[]" value="{{ $service->id }}" data-price="{{ isset($service->discount) ? 
                                 $service->discount : $service->price }}">
                                        @else
                                        <input type="checkbox" class="service-checkbox" name="service_ids[]" value="{{ $service->id }}" data-price="{{ isset($service->discount) ? 
                                 $service->discount : $service->price }}">
                                        @endif
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
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Comment:</strong>
                        <textarea name="order_comment" class="form-control" cols="30" rows="5">{{ $order->order_comment }}</textarea>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-3 offset-md-3 ">
                    <h5>Payment Summary</h5>
                    <table class="table">
                        <tr>
                            <td class="text-left"><strong> Service Total:</strong></td>
                            <td>{{ config('app.currency') }} <span id="sub_total">{{ $order->order_total->sub_total }}</span></td>
                            <input type="hidden" name="sub_total" value="{{ $order->order_total->sub_total }}">
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Coupon Discount:</strong></td>
                            <td>{{ config('app.currency') }} <span id="coupon-discount">{{ $order->order_total->discount ? '-'.$order->order_total->discount : 0 }}</span></td>
                            <input type="hidden" name="discount" value="{{ $order->order_total->discount ? $order->order_total->discount : 0 }}">
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Staff Charges:</strong></td>
                            <td>{{ config('app.currency') }} <span id="staff_charges">{{ $order->staff->charges ? $order->staff->charges : 0 }}</span></td>
                            <input type="hidden" name="staff_charges" value="{{ $order->staff->charges ? $order->staff->charges : 0 }}">
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Transport Charges:</strong></td>
                            <td>{{ config('app.currency') }} <span id="transport_charges">{{ $staffZone->transport_charges ? $staffZone->transport_charges : 0 }}</span></td>
                            <input type="hidden" name="transport_charges" value="{{ $staffZone->transport_charges ? $staffZone->transport_charges : 0 }}">
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Total:</strong></td>
                            <td>{{ config('app.currency') }} <span id="total_amount">{{ $order->total_amount}}</span></td>
                            <input type="hidden" name="total_amount" value="{{ $order->total_amount}}">
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-right no-print">
                <button type="submit" class="btn btn-primary">Update</button>
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
</script>
<script>
    function updateTotal() {
        let transport_charges = parseFloat($('#zone').find(':selected').data('transport-charges'));

        $('input[name="transport_charges"]').val(transport_charges);
        $('#transport_charges').text(transport_charges);

        let staff_charges = parseFloat($('input[name="service_staff_id"]:checked').data('staff-charges'));

        let coupon_discount = parseFloat($('#coupon-discount').text());

        $('input[name="staff_charges"]').val(staff_charges);
        $('#staff_charges').text(staff_charges);

        let total_amount = 0;

        let sub_total = parseFloat($('#sub_total').text());
        total_amount = sub_total + staff_charges + transport_charges + coupon_discount;

        $('input[name="total_amount"]').val(total_amount.toFixed(2));
        $('#total_amount').text(total_amount.toFixed(2));
    }
</script>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>

@endsection