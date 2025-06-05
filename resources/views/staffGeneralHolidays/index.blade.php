@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Staff General Holidays ({{ $total_Staff_general_holiday }})</h2>
            </div>
            <div class="col-md-6">
                @can('staff-holiday-create')
                    <a class="btn btn-success float-end" href="{{ route('staffGeneralHolidays.create') }}"> Create New Staff
                        General Holiday</a>
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
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('staffGeneralHolidays.index', array_merge(request()->query(), ['sort' => 'day', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">
                        Day
                        </a>
                        @if (request('sort') === 'day')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif
                        </th>
                        <th>Staff Name</th>
                        <th>Status</th>
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($staffGeneralHolidays))
                        @foreach ($staffGeneralHolidays as $staffGeneralHoliday)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $staffGeneralHoliday->day }}</td>
                                <td>{{ $staffGeneralHoliday->staff->name }}</td>
                                <td>
                                    <span class="badge {{ $staffGeneralHoliday->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $staffGeneralHoliday->status == 1 ? 'Enable' : 'Disable' }}
                                    </span>
                                </td>
                                <td>
                                    <form id="deleteForm{{ $staffGeneralHoliday->id }}"
                                        action="{{ route('staffGeneralHolidays.destroy', $staffGeneralHoliday->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        @can('staff-holiday-delete')
                                            <button type="button" onclick="confirmDelete('{{ $staffGeneralHoliday->id }}')"
                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                    @can('staff-holiday-create')
                                        <form action="{{ route('staffGeneralHolidays.toggleStatus', ['id' => $staffGeneralHoliday->id, 'status' => $staffGeneralHoliday->status == 1 ? 0 : 1]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn {{ $staffGeneralHoliday->status == 1 ? 'btn-danger' : 'btn-success' }}" title="{{ $staffGeneralHoliday->status == 1 ? 'Disable' : 'Enable' }}">
                                                <i class="fa {{ $staffGeneralHoliday->status == 1 ? 'fa-thumbs-down' : 'fa-thumbs-up' }}"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">There is no staff General Holiday.</td>
                        </tr>
                    @endif
                </table>
                {!! $staffGeneralHolidays->links() !!}
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
