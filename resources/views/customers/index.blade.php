@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Customer</h2>
            </div>
            <div class="float-end">
                @can('customer-create')
                <a class="btn btn-success" href="{{ route('customers.create') }}"> Create New Customer</a>
                @endcan
            </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <span>{{ $message }}</span>
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <hr>
    <div class="row">
        <div class="col-md-9">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($customers))
                @foreach ($customers as $customer)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>

                    <td>
                        <form id="deleteForm{{ $customer->id }}" action="{{ route('customers.destroy',$customer->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('customers.show',$customer->id) }}"><i class="fas fa-eye"></i></a>
                            @can('customer-edit')
                            <a class="btn btn-primary" href="{{ route('customers.edit',$customer->id) }}"><i class="fas fa-edit"></i></a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('customer-delete')
                            <button type="button" onclick="confirmDelete('{{ $customer->id }}')" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                            @endcan
                            @can('order-list')
                            <a class="btn btn-info" href="{{ route('orders.index') }}?customer_id={{ $customer->id }}">Order History</a>
                            @endcan
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#couponModal{{ $customer->id }}">
                                <i class="fas fa-gift"></i> Apply Coupon
                            </button>
                        </form>
                        <div class="modal fade" id="couponModal{{ $customer->id }}" tabindex="-1" aria-labelledby="couponModalLabel{{ $customer->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="couponModalLabel{{ $customer->id }}">Select Coupon for {{ $customer->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('coupons.assign', $customer->id) }}" method="post">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="couponSelect{{ $customer->id }}" class="form-label">Select Coupon:</label>
                                                <select class="form-select" id="couponSelect{{ $customer->id }}" name="coupon_id">
                                                    @foreach($coupons as $coupon)
                                                        <option value="{{ $coupon->id }}">{{ $coupon->name }} ({{ $coupon->code }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Coupon</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Customer</td>
                </tr>
                @endif
            </table>
            {!! $customers->links() !!}
        </div>
        <div class="col-md-3">
            <h3>Filter</h3>
            <hr>
            <form action="{{ route('customers.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" value="{{$filter['name']}}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <strong>Email:</strong>
                            <input type="email" name="email" value="{{$filter['email']}}" class="form-control" placeholder="abc@gmail.com">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <strong>Number:</strong>
                            <input type="text" name="number" value="{{$filter['number']}}" class="form-control" placeholder="Enter number with country code">
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function confirmDelete(Id) {
        var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
</script>
<script>
    $(document).ready(function () {
        function checkTableResponsive() {
            var viewportWidth = $(window).width();
            var $table = $('table');

            if (viewportWidth < 768) { 
                $table.addClass('table-responsive');
            } else {
                $table.removeClass('table-responsive');
            }
        }

        checkTableResponsive();

        $(window).resize(function () {
            checkTableResponsive();
        });
    });
</script>
@endsection