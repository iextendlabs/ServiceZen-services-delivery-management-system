@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
<div class="row">
    <div class="col-md-6">
        <h2>Coupons</h2>
    </div>
    <div class="col-md-6">
        @can('coupon-create')
        <a class="btn btn-success  float-end" href="{{ route('coupons.create') }}"><i class="fa fa-plus"></i></a>
        @endcan
    </div>
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <span>{{ $message }}</span>
    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<hr>
<table class="table table-striped table-bordered">
    <tr>
        <th>Sr#</th>
        <th class="text-left">Name</th>
        <th class="text-left">Code</th>
        <th class="text-right">Discount</th>
        <th class="text-left">Date Start</th>
        <th class="text-left">Date End</th>
        <th class="text-left">Status</th>
        <th class="text-right">Action</th>
    </tr>
    @if(count($coupons))
    @foreach ($coupons as $coupon)
    <tr>
        <td>{{ ++$i }}</td>
        <td class="text-left">{{ $coupon->name }}</td>
        <td class="text-left">{{ $coupon->code }}</td>
        <td class="text-right">{{ $coupon->discount }}</td>
        <td class="text-left">{{ $coupon->date_start }}</td>
        <td class="text-left">{{ $coupon->date_end }}</td>
        <td class="text-left">
            @if($coupon->status == 1)Enable @else Disable @endif</td>
        <td class="text-right">
            <form action="{{ route('coupons.destroy',$coupon->id) }}" method="POST">
                <a class="btn btn-warning" href="{{ route('coupons.show',$coupon->id) }}"><i class="fa fa-eye"></i></a>
                @can('coupon-edit')
                <a class="btn btn-primary" href="{{ route('coupons.edit',$coupon->id) }}"><i class="fa fa-edit"></i></a>
                @endcan
                @csrf
                @method('DELETE')
                @can('coupon-delete')
                <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                @endcan
            </form>
        </td>
    </tr>
    @endforeach
    @else
    <tr>
        <td colspan="8" class="text-center">There is no coupon.</td>
    </tr>
    @endif
</table>
{!! $coupons->links() !!}
@endsection