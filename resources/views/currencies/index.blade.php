@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Currency ({{ $total_currency }})</h2>
                </div>
                <div class="float-end">
                    @can('currency-create')
                        <a class="btn btn-success" href="{{ route('currencies.create') }}"><i class="fa fa-plus"></i></a>
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
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('currencies.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('currencies.index', array_merge(request()->query(), ['sort' => 'symbol', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Symbol</a>
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
                                        <a class="btn btn-warning" href="{{ route('currencies.show', $currency->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('currency-edit')
                                            <a class="btn btn-primary" href="{{ route('currencies.edit', $currency->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('currency-delete')
                                            <button type="button" class="btn btn-danger"
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
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('currencies.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter['name'] }}"
                                    class="form-control" placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Symbol:</strong>
                                <input type="text" name="symbol" value="{{ $filter['symbol'] }}" class="form-control"
                                    placeholder="Symbol">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
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
