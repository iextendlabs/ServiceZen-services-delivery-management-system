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
                <h2>Information</h2>
            </div>
            <div class="float-right">
                @can('information-create')
                <a class="btn btn-success  float-end" href="{{ route('information.create') }}"> <i class="fa fa-plus"></i></a>
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
            <form action="{{ route('information.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Position:</strong>
                            <select name="position" class="form-control">
                                <option></option>
                                <option value="Top Menu" @if($filter['position'] == "Top Menu") selected @endif>Top Menu</option>
                                <option value="Bottom Footer" @if($filter['position'] == "Bottom Footer") selected @endif>Bottom Footer</option>
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
        <h3>Information  ({{ $total_information }})</h3>
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th class="text-left">Name</th>
                    <th class="text-left">Description</th>
                    <th class="text-left">Position</th>
                    <th>Action</th>
                </tr>
                @if(count($information))
                @foreach ($information as $info)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td class="text-left">{{ $info->name }}...</td>
                    <td class="text-left">{{ substr($info->description, 0, 50) }}...</td>
                    <td class="text-left">{{ $info->position }}</td>
                    <td>
                        <form id="deleteForm{{ $info->id }}" action="{{ route('information.destroy',$info->id) }}" method="POST">
                            <a class="btn btn-warning" href="{{ route('information.show',$info->id) }}"><i class="fa fa-eye"></i></a>
                            @can('information-edit')
                            <a class="btn btn-primary" href="{{ route('information.edit',$info->id) }}"><i class="fa fa-edit"></i></a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('information-delete')
                            <button type="button" onclick="confirmDelete('{{ $info->id }}')" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4" class="text-center">There is no information.</td>
                </tr>
                @endif
            </table>
            {!! $information->links() !!}
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