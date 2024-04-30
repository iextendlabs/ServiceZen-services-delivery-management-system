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
                <div class="float-left">
                    <h2>Withdraw</h2>
                </div>
                <div class="float-right">
                    @can('withdraw-create')
                        <a class="btn btn-success  float-end" href="{{ route('withdraws.create') }}"> <i
                                class="fa fa-plus"></i></a>
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
            <div class="col-md-12">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('withdraws.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>User:</strong>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option></option>
                                    <option value="Approved" @if ($filter['status'] === 'Approved') selected @endif>
                                        Approved</option>
                                    <option value="Un Approved" @if ($filter['status'] === 'Un Approved') selected @endif>
                                        Un Approved</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <div class="d-flex flex-wrap justify-content-md-end">
                                <div class="col-md-3 mb-3">
                                    <a href="{{ url()->current() }}" class="btn btn-lg btn-secondary">Reset</a>
                                </div>
                                <div class="col-md-9 mb-3">
                                    <button type="submit" class="btn btn-lg btn-block btn-primary">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <h3>withdraw ({{ $total_withdraw }})</h3>
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th class="text-left">User Name</th>
                        <th class="text-left">Amount</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Payment method</th>
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
                                            @if ($withdraw->status == 'Approved')
                                                <a class="btn btn-sm btn-danger"
                                                    href="{{ route('updateWithdrawStatus', $withdraw->id) }}?status=Un Approved">
                                                    <i class="fas fa-thumbs-down"></i>
                                                </a>
                                            @elseif($withdraw->status == 'Un Approved')
                                                <a class="btn btn-sm btn-success"
                                                    href="{{ route('updateWithdrawStatus', $withdraw->id) }}?status=Approved">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </a>
                                            @endif
                                        @endcan
                                        <a class="btn btn-warning" href="{{ route('withdraws.show', $withdraw->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        @can('withdraw-delete')
                                            <button type="button" onclick="confirmDelete('{{ $withdraw->id }}')"
                                                class="btn btn-danger"><i class="fas fa-trash"></i></button>
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
