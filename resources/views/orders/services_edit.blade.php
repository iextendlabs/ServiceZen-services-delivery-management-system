@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Edit Order Services</h2>
        </div>
    </div>
    <div class="container">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
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
        <form action="{{ route('orders.services_edit',$order->id) }}" method="POST">
            @csrf
            <input type="hidden" name="url" value="{{ url()->previous() }}">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Category:</strong>
                        <select class="form-control" name="category_id" id="category">
                            <option value="0">-- All Services -- </option>
                            @foreach ($servicesCategories as $category)
                            <option @if (old('category_id')==$category->id) selected @endif value="{{ $category->id }}">
                                {{ $category->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group scroll-div">
                        <strong>Services:</strong>
                        <input type="text" name="search-services" id="search-services" class="form-control" placeholder="Search Services By Name">
                        <table class="table table-striped table-bordered services-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($servicesCategories as $category)
                                @foreach ($category->service as $service)
                                @if ($service->status)
                                <tr>
                                    <td>
                                        <input type="checkbox" @if(in_array($service->id,old('service_ids',$serviceIds))) checked @endif class="service-checkbox" name="service_ids[]" value="{{ $service->id }}" data-price="{{ isset($service->discount) ? $service->discount : $service->price }}" data-category="{{ $service->category_id }}">
                                    </td>
                                    <td>{{ $service->name }}</td>
                                    <td><span class="price">{{ isset($service->discount) ? $service->discount : $service->price }}</span></td>
                                    <td>{{ $service->duration }}</td>
                                </tr>
                                @endif
                                @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group" id="selected-services">
                        <strong>Selected Services:</strong>
                        <table class="table table-striped table-bordered selected-services-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($selectedServices))
                                @foreach ($selectedServices as $service)
                                <tr>
                                    <td>
                                        <input type="checkbox" checked class="selected-service-checkbox" name="selected_service_ids[]" @if(in_array('selected_service_ids', [])) checked @endif  value="{{ $service->id }}" data-price="{{ isset($service->discount) ? $service->discount : $service->price }}" data-category="{{ $service->category_id }}">
                                    </td>
                                    <td>{{ $service->name }}</td>
                                    <td><span class="price">{{ isset($service->discount) ? $service->discount : $service->price }}</td>
                                    <td>{{ $service->duration }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-3 offset-md-3 ">
                    <h5>Payment Summary</h5>
                    <table class="table">
                        <tr>
                            <td class="text-left"><strong> Service Total:</strong></td>
                            <td>{{ config('app.currency') }} <span id="sub_total">{{$order->order_total->sub_total ?? 0}}</span></td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Coupon Discount:</strong></td>
                            <td>{{ config('app.currency') }} -<span id="coupon_discount">{{$order->order_total->discount ?? 0}}</span></td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Transport Charges:</strong></td>
                            <td>{{ config('app.currency') }} <span id="transport_charges">{{$order->order_total->transport_charges ?? 0}}</span></td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Staff Charges:</strong></td>
                            <td>{{ config('app.currency') }} <span id="staff_charges">{{$order->order_total->staff_charges ?? 0}}</span></td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Total:</strong></td>
                            <td>{{ config('app.currency') }} <span id="total">{{$order->total_amount}}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-right no-print">
                @can('order-edit')
                <button type="submit" class="btn btn-primary">Update</button>
                @endcan
            </div>
        </form>
    </div>
</div>
</div>
<script>
    $(document).ready(function () {
        

        
    });
</script>
<script>
    function calculateTotal() {
        let total = 0;

        let sub_total = 0;

        $('.selected-service-checkbox:checked').each(function() {
            let price = parseFloat($(this).data('price'));
            sub_total += price;
        });
        const coupon_discount = $('#coupon_discount').text();
        const staff_charges = $('#staff_charges').text();
        const transport_charges = $('#transport_charges').text();
        total = sub_total + parseFloat(staff_charges) + parseFloat(transport_charges) - parseFloat(coupon_discount);

        $('#sub_total').text(sub_total.toFixed(2));
        $('#total').text(total.toFixed(2));
    }

    function checkTableResponsive() {
        var viewportWidth = $(window).width();
        var $table = $('table');

        if (viewportWidth < 768) { 
            $table.addClass('table-responsive');
        } else {
            $table.removeClass('table-responsive');
        }
    }

    $(window).resize(function () {
        checkTableResponsive();
    });
    
    $(document).on("change", ".service-checkbox, .selected-service-checkbox", function() {
        calculateTotal();
    });

    $(document).ready(function() {
        calculateTotal();
    });
</script>
<script>
    $(document).on("change", "#category", function() {
        let value = $(this).val();
        if (value == 0) {
            $(".services-table tr").show();
        } else {
            $(".services-table tr").hide();
            $(".services-table tr").each(function() {
                let $row = $(this);
                let category = $row.find(".service-checkbox").attr("data-category");
                if (category === value) {
                    $row.show();
                }
            });
        }
    });
</script>
<script>
    $(document).on("change", ".service-checkbox", function() {
        let $row = $(this).closest('tr');
        var id = $(this).val();
        var name = $row.find('td:nth-child(2)').text();
        var price = $row.find(".price").text();
        var duration = $row.find("td:last").text();

        if ($(this).prop("checked")) {
            tableHtml = '<tr>';
            tableHtml += '<td>';
            tableHtml += '<input type="checkbox" checked class="selected-service-checkbox" name="selected_service_ids[]" value="' + id + '" data-price="' + price + '">';
            tableHtml += '</td>';
            tableHtml += '<td>' + name + '</td>';
            tableHtml += '<td>' + price + '</td>';
            tableHtml += '<td>' + duration + '</td>';
            tableHtml += '</tr>';

            $('.selected-services-table tbody').append(tableHtml);
        } else {
            $(".selected-services-table tr").each(function() {
                let $row = $(this);
                let selectedName = $row.find("td:nth-child(2)").text();
                if (name === selectedName) {
                    $row.remove();
                }
            });
        }
        calculateTotal();
    });
</script>
<script>
    $(document).on("change", ".selected-service-checkbox", function() {
        let $row = $(this).closest('tr');
        if ($(this).prop("checked") === false) {
            $row.remove();
            calculateTotal();
        }
    });
</script>
<script>
    $(document).ready(function() {
        if ($('.selected-services-table tbody tr').length > 2) {
            $('#no-services').hide();
        } else {
            $('#no-services').show();
        }

        if ($('.selected-services-table tbody tr').length > 5) {
            $('#selected-services').addClass('scroll-div');
        } else {
            $('#selected-services').removeClass('scroll-div');
        }
    });
</script>
<script>
    $(document).on("change", ".service-checkbox,.selected-service-checkbox", function() {
        if ($('.selected-services-table tbody tr').length > 2) {
            $('#no-services').hide();
        } else {
            $('#no-services').show();
        }

        if ($('.selected-services-table tbody tr').length > 5) {
            $('#selected-services').addClass('scroll-div');
        } else {
            $('#selected-services').removeClass('scroll-div');
        }
    });
</script>
<script>
    $(document).on("keyup", "#search-services", function() {
        let value = $(this).val().toLowerCase();

        $(".services-table tbody tr").hide();

        $(".services-table tbody tr").each(function() {
            let $row = $(this);
            let name = $row.find("td:nth-child(2)").text().toLowerCase();
            if (name.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });
</script>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>
@endsection
