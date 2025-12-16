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
                    <h3>Freelancer Groups ({{ $total_freelancer_groups }})</h3>
                </div>
                <div class="float-right">
                    @can('freelancer-group-create')
                        <a class="btn text-dark  float-end" href="{{ route('freelancerGroups.create') }}"> <i class="fa fa-plus"></i> Add Freelancer Group</a>
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
                <table class="table table-bordered">
                    <tr class="bg-white">
                        <th>Sr#</th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('freelancerGroups.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a></i>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    @if (count($freelancer_groups))
                        @foreach ($freelancer_groups as $freelancer_group)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $freelancer_group->name }}</td>
                                <td>{{ substr($freelancer_group->description, 0, 50) }}...</td>

                                <td>
                                    <form id="deleteForm{{ $freelancer_group->id }}" action="{{ route('freelancerGroups.destroy', $freelancer_group->id) }}"
                                        method="POST">
                                        @can('freelancer-group-edit')
                                            <a class="btn text-dark" href="{{ route('freelancerGroups.edit', $freelancer_group->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('freelancer-group-delete')
                                            <button type="button" onclick="confirmDelete('{{ $freelancer_group->id }}')"
                                                class="btn text-danger"><i class="fas fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">There is no Freelancer Group.</td>
                        </tr>
                    @endif
                </table>
                {!! $freelancer_groups->links() !!}
            </div>
            <div class="col-md-3">
                <div class="card bg-white shadow-sm">
                    <div class="card-title p-3 border-bottom">
                        <h3>Filter</h3>
                    </div>
                    <div class="card-body p-3">
                        <form action="{{ route('freelancerGroups.index') }}" method="GET" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Name</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-user text-muted"></i></span>
                                            </div>
                                            <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control"
                                                placeholder="Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end mt-3">
                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light border mr-2 font-weight-medium me-2">Reset</a>
                                    <button type="submit" class="btn btn-sm btn-primary shadow-sm font-weight-bold">Filter</button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
