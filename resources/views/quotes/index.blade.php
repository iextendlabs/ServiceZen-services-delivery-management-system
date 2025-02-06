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
                <h2>Quotes</h2>
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
            <form action="{{ route('quotes.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>User:</strong>
                            <select name="user_id" class="form-control">
                                <option></option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}" @if ($filter['user_id']==$user->id) selected @endif>
                                    {{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Services:</strong>
                            <select name="service_id" class="form-control">
                                <option></option>
                                @foreach ($services as $service)
                                <option value="{{ $service->id }}" @if ($filter['service_id']==$service->id) selected
                                    @endif>
                                    {{ $service->name }}</option>
                                @endforeach
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
        <h3>Quote ({{ $total_quote }})</h3>
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>User</th>
                    <th>Service</th>
                    <th>Action</th>
                </tr>
                @if (count($quotes))
                @foreach ($quotes as $quote)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td class="text-left">{{ $quote->user->name ?? "" }}</td>
                    <td class="text-left">{{ $quote->service_name }}</td>
                    <td>
                        <form id="deleteForm{{ $quote->id }}" action="{{ route('quotes.destroy', $quote->id) }}"
                            method="POST">
                            <a class="btn btn-warning" href="{{ route('quotes.show', $quote->id) }}">
                                <i class="fa fa-eye"></i>
                            </a>
                            @csrf
                            @method('DELETE')
                            @can('quote-delete')
                            <button type="button" onclick="confirmDelete('{{ $quote->id }}')" class="btn btn-danger"><i
                                    class="fas fa-trash"></i></button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6" class="text-center">There is no quote.</td>
                </tr>
                @endif
            </table>
            {!! $quotes->links() !!}
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