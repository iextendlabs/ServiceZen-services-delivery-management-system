@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
           @section('page_title')
            <h3 class="text-bold">Withdraw</h3>
            @endsection
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
            <div class="card mb-4 col-md-12 p-0 shadow-sm rounded-3 bg-white ">
                <div class="card-body">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="float-right">
                            @can('withdraw-create')
                                <a class="btn btn-outline-dark btn-sm rounded-2 float-end" href="{{ route('withdraws.create') }}"> <i
                                        class="fa fa-plus"></i> New Withdraw</a>
                            @endcan
                        </div>                        
                        <h3 class="text-muted">Filter</h3>
                        <hr>
                        <form action="{{ route('withdraws.index') }}" method="GET" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">User:</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-user text-muted"></i></span>
                                            <select name="user_id" class="form-control">
                                                <option></option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        @if ($filter['user_id'] == $user->id) selected @endif>
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Status</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-clock text-muted"></i></span>
                                        <select name="status" class="form-control" style="border-left: 0;">
                                            <option></option>
                                            <option value="Approved" @if ($filter['status'] === 'Approved') selected @endif>
                                                Approved</option>
                                            <option value="Un Approved" @if ($filter['status'] === 'Un Approved') selected @endif>
                                                Un Approved</option>
                                        </select>
                                        </div>  
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 mt-4">
                                    <div class="d-flex flex-wrap justify-content-md-start">
                                        <div class="me-2 mb-3">
                                            <a href="{{ url()->current() }}" class="btn btn-md btn-outline-secondary"><i class="fa fa-undo"></i> Reset</a>
                                        </div>
                                        <div class=" mb-3">
                                            <button type="submit" class="btn btn-md btn-primary"><i class="fa fa-filter"></i> Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
            </div>
            </div>
            <h3 class="text-dark">Withdraw ({{ $total_withdraw }})</h3>
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                            href="{{ route('withdraws.index', array_merge(request()->query(), ['sort' => 'user_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">User Name</a>
                        @if (request('sort') === 'user_name')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif
                    </th>
                    <th><a class=" ml-2 text-decoration-none"
                        href="{{ route('withdraws.index', array_merge(request()->query(), ['sort' => 'amount', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Amount</a>
                    @if (request('sort') === 'amount')
                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif
                </th>
                <th><a class=" ml-2 text-decoration-none"
                    href="{{ route('withdraws.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                @if (request('sort') === 'status')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                @endif
            </th>
            <th><a class=" ml-2 text-decoration-none"
                href="{{ route('withdraws.index', array_merge(request()->query(), ['sort' => 'payment_method', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Payment method</a>
            @if (request('sort') === 'payment_method')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
        </th>
                        {{-- <th class="text-left">Payment method</th> --}}
                        <th>Action</th>
                    </tr>
                    @if (count($withdraws))
                        @foreach ($withdraws as $withdraw)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td class="text-left">{{ $withdraw->user_name }}</td>
                                <td class="text-left">{{ $withdraw->amount }}</td>
                                <td class="text-left">{{ $withdraw->status }}</td>
                                <td class="text-left">{{ $withdraw->payment_method }}</td>
                                <td>
                                    <form id="deleteForm{{ $withdraw->id }}"
                                        action="{{ route('withdraws.destroy', $withdraw->id) }}" method="POST">
                                        @can('withdraw-edit')
                                            {{-- <a class="btn btn-primary" href="{{ route('withdraws.edit', $withdraw->id) }}"><i
                                                    class="fa fa-edit"></i></a> --}}
                                            @if($withdraw->user_id)
                                            @if ($withdraw->status == 'Approved')
                                                <a class="btn btn-sm"
                                                    href="{{ route('updateWithdrawStatus', $withdraw->id) }}?status=Un Approved">
                                                    <i class="fas fa-thumbs-down"></i>
                                                </a>
                                            @elseif($withdraw->status == 'Un Approved')
                                                <a class="btn btn-sm"
                                                    href="{{ route('updateWithdrawStatus', $withdraw->id) }}?status=Approved">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </a>
                                            @endif
                                            @endif
                                        @endcan
                                        <a class="btn " href="{{ route('withdraws.show', $withdraw->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        @can('withdraw-delete')
                                            <button type="button" onclick="confirmDelete('{{ $withdraw->id }}')"
                                                class="btn text-danger"><i class="fas fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">There is no withdraw.</td>
                        </tr>
                    @endif
                </table>
                {!! $withdraws->links() !!}
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
        $(document).ready(function() {
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

            $(window).resize(function() {
                checkTableResponsive();
            });
        });
    </script>
@endsection
