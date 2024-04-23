@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
<div class="container">
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
    <h3>Coupons  ({{ $total_coupons }})</h3>
    <table class="table table-striped table-bordered">
        <tr>
            <th>Sr#</th>
            <th>
                <div class="d-flex">
                    <a class="ml-2 text-black text-decoration-none" href="{{ route('coupons.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                    @if (request('sort') === 'name')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif

                </div>
            </th>
            <th>
                <div class="d-flex">
                    <a class="ml-2 text-black text-decoration-none" href="{{ route('coupons.index', array_merge(request()->query(), ['sort' => 'code', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Code</a>
                    @if (request('sort') === 'code')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif

                </div>
            </th>
            <th>
                <div class="d-flex">
                    <a class="ml-2 text-black text-decoration-none" href="{{ route('coupons.index', array_merge(request()->query(), ['sort' => 'discount', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Discount</a>
                    @if (request('sort') === 'discount')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif

                </div>
            </th>
            <th>
                <div class="d-flex">
                    <a class="ml-2 text-black text-decoration-none" href="{{ route('coupons.index', array_merge(request()->query(), ['sort' => 'date_start', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Date Start</a>
                    @if (request('sort') === 'date_start')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif

                </div>
            </th>
            <th>
                <div class="d-flex">
                    <a class="ml-2 text-black text-decoration-none" href="{{ route('coupons.index', array_merge(request()->query(), ['sort' => 'date_end', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Date End</a>
                    @if (request('sort') === 'date_end')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif

                </div>
            </th>
            <th>
                <div class="d-flex">
                    <a class="ml-2 text-black text-decoration-none" href="{{ route('coupons.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                    @if (request('sort') === 'status')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif

                </div>
            </th>
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
                <form id="deleteForm{{ $coupon->id }}" action="{{ route('coupons.destroy',$coupon->id) }}" method="POST">
                    <a class="btn btn-warning" href="{{ route('coupons.show',$coupon->id) }}"><i class="fa fa-eye"></i></a>
                    @can('coupon-edit')
                    <a class="btn btn-primary" href="{{ route('coupons.edit',$coupon->id) }}"><i class="fa fa-edit"></i></a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('coupon-delete')
                    <button type="button" onclick="confirmDelete('{{ $coupon->id }}')" class="btn btn-danger"><i class="fa fa-trash"></i></button>
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