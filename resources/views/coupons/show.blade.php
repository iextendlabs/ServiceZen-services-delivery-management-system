@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Coupon</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $coupon->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Code:</strong>
                {{ $coupon->code }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Type:</strong>
                {{ $coupon->type }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Discount:</strong>
                {{ $coupon->discount }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date Start:</strong>
                {{ $coupon->date_start }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date End:</strong>
                {{ $coupon->date_end }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                @if($coupon->status == 1)Enable @else Disable @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Coupon For:</strong>
                {{ $coupon->coupon_for }}
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        @if(isset($coupon->customers) && $coupon->coupon_for == "customer")
        <h3>Coupon Customers</h3>
        <div class="col-md-12">
            @if(count($coupon->customers) != 0)
            <input type="text" id="customer-search" placeholder="Search customer by name and email..." class="form-control">
            <div class="scroll-div">
                <table class="table table-striped table-bordered album bg-light customers-table">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                    @foreach ($coupon->customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @else
            <div class="text-center">
                <p>There are no Customer Assigned</p>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#customer-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".customers-table tr").hide();

            $(".customers-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();
                var code = $row.find("td:first").next().next().text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (code.indexOf(value) != -1) {
                    $(this).show();
                }

            });
        });
    });
</script>
@endsection