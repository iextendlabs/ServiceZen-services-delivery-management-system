@extends('layouts.app')
@section('content')
@section('page_title')
<h3 class="">Currencies</h3>
@endsection
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-title p-3 border-bottom">
                        <div class="float-start">
                           <h3> Filter</h3>
                        </div>
                        <div class="float-end">
                            @can('currency-create')
                                <a class="btn text-dark" href="{{ route('currencies.create') }}"><i class="fa fa-plus"></i> Add Currency</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('currencies.index') }}" method="GET" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <strong>Name:</strong>
                                        <input type="text" name="name" value="{{ $filter['name'] }}"
                                            class="form-control" placeholder="Name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <strong>Symbol:</strong>
                                        <input type="text" name="symbol" value="{{ $filter['symbol'] }}" class="form-control"
                                            placeholder="Symbol">
                                    </div>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <button type="submit" class="btn btn-md btn-primary shadow-sm float-start font-weight-bold"><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>            
            <div class="col-md-12 mt-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h3>Currency ({{ $total_currency }})</h3>
                </div>
                <table class="table table-bordered">
                    <tr class="bg-white">
                        <th>Sr#</th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('currencies.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a></i>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('currencies.index', array_merge(request()->query(), ['sort' => 'symbol', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Symbol</a></i>
                            @if (request('sort') === 'symbol')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Rate</th>
                        <th>Action</th>
                    </tr>
                    @if (count($currencies))
                        @foreach ($currencies as $currency)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $currency->name }}</td>
                                <td>{{ $currency->symbol }}</td>
                                <td>{{ $currency->rate }}</td>
                                <td>
                                    <form id="deleteForm{{ $currency->id }}"
                                        action="{{ route('currencies.destroy', $currency->id) }}" method="POST">
                                        <a class="btn text-dark" href="{{ route('currencies.show', $currency->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('currency-edit')
                                            <a class="btn text-dark" href="{{ route('currencies.edit', $currency->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('currency-delete')
                                            <button type="button" class="btn text-danger"
                                                onclick="confirmDelete('{{ $currency->id }}')"><i
                                                    class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">There is no currency.</td>
                        </tr>
                    @endif
                </table>
                {!! $currencies->links() !!}

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
