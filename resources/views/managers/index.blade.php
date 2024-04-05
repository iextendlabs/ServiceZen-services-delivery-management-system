@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Manager</h2>
            </div>
            <div class="float-end">
                @can('manager-create')
                <a class="btn btn-success" href="{{ route('managers.create') }}"> Create New Manager</a>
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
    <h3>Managers  ({{ $total_manager }})</h3>
    <div class="row">
        <div class="col-md-9">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($managers))
                @foreach ($managers as $manager)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $manager->name }}</td>
                    <td>{{ $manager->email }}</td>
                    <td>
                        <form id="deleteForm{{ $manager->id }}" action="{{ route('managers.destroy',$manager->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('managers.show',$manager->id) }}">Show</a>
                            @can('manager-edit')
                            <a class="btn btn-primary" href="{{ route('managers.edit',$manager->id) }}">Edit</a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('manager-delete')
                            <button type="button" onclick="confirmDelete('{{ $manager->id }}')" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Manager.</td>
                </tr>
                @endif
            </table>
            {!! $managers->links() !!}
        </div>
        <div class="col-md-3">
            <h3>Filter</h3>
            <hr>
            <form action="{{ route('managers.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" value="{{$filter_name}}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
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