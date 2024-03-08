@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Coupon</h2>
        </div>
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
    <form action="{{ route('coupons.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name</strong>
                    <input type="text" name="name" value="{{old('name')}}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Code</strong>
                    <input type="text" name="code" value="{{old('code')}}" class="form-control" placeholder="Code">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Type</strong>
                    <select name="type" class="form-control">
                        <option value="Percentage">Percentage</option>
                        <option value="Fixed Amount">Fixed Amount</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Discount</strong>
                    <input type="text" name="discount" value="{{old('discount')}}" class="form-control" placeholder="Discount">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Minimum Order</strong>
                    <input type="number" name="min_order_value" value="{{old('min_order_value')}}" class="form-control" placeholder="Minimum Order">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date Start</strong>
                    <input type="date" name="date_start" value="{{old('date_start')}}" class="form-control" placeholder="Date Start">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date End</strong>
                    <input type="date" name="date_end" value="{{old('date_end')}}" class="form-control" placeholder="Date End">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Uses Per Coupon</strong>
                    <input type="text" name="uses_total" value="{{old('uses_total')}}" class="form-control" placeholder="Uses Per Coupon">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Status</strong>
                    <select name="status" class="form-control">
                        <option value="1">Enable</option>
                        <option value="0">Disable</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group scroll-div">
                    <strong>Category:</strong>
                    <input type="text" name="categories-search" id="categories-search" class="form-control" placeholder="Search Category By Name">
                    <table class="table table-striped table-bordered categories-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                        </tr>
                        @foreach ($categories as $category)
                        <tr>
                            <td>
                                <input type="checkbox" name="categoriesId[{{ ++$i }}]" value="{{ $category->id }}">
                            </td>
                            <td>{{ $category->title }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group scroll-div">
                    <strong>Service:</strong>
                    <input type="text" name="service-search" id="service-search" class="form-control" placeholder="Search Services By Name">
                    <table class="table table-striped table-bordered service-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Price</th>
                        </tr>
                        @foreach ($services as $service)
                        <tr>
                            <td>
                                <input type="checkbox" name="servicesId[{{ ++$i }}]" value="{{ $service->id }}">
                            </td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->price }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $("#categories-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".categories-table tr").hide();

            $(".categories-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();


                if (name.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });

        $("#service-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".service-table tr").hide();

            $(".service-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var email = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (email.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
    });
    
</script>
@endsection