@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Staff Group ({{ $total_staffGroup }})</h2>
            </div>
            <div class="col-md-6">
                @can('staff-group-create')
                    <a class="btn btn-success  float-end" href="{{ route('staffGroups.create') }}"> Create New Staff Group</a>
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
        
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class="text-black ml-2 text-decoration-none"
                                href="{{ route('staffGroups.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Staffs</th>
                        <th>Staff Zone</th>
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($staffGroups))
                        @foreach ($staffGroups as $staffGroup)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $staffGroup->name }}</td>
                                <td>
                                    @foreach ($staffGroup->staffs as $staff)
                                        {{ $staff->name }},
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($staffGroup->staffZones as $staff_zone)
                                        {{ $staff_zone->name }},
                                    @endforeach
                                </td>
                                <td>
                                    <form id="deleteForm{{ $staffGroup->id }}"
                                        action="{{ route('staffGroups.destroy', $staffGroup->id) }}" method="POST">
                                        <a class="btn btn-info"
                                            href="{{ route('staffGroups.show', $staffGroup->id) }}">Show</a>
                                        @can('staff-group-edit')
                                            <a class="btn btn-primary"
                                                href="{{ route('staffGroups.edit', $staffGroup->id) }}">Edit</a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('staff-group-delete')
                                            <button type="button" onclick="confirmDelete('{{ $staffGroup->id }}')"
                                                class="btn btn-danger">Delete</button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">There is no staff Group.</td>
                        </tr>
                    @endif
                </table>
                {!! $staffGroups->links() !!}
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
@endsection
