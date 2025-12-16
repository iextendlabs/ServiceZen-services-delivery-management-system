@extends('layouts.app')
@section('content')
@section('page_title')
<h3 class="">Staff Holidays</h3>
@endsection
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Staff Holidays  ({{ $total_staffHoliday }})</h2>
            </div>
            <div class="col-md-6">
                @can('staff-holiday-create')
                    <a class="btn text-dark  float-end" href="{{ route('staffHolidays.create') }}"><i class="fa fa-plus"></i> Create New Staff Holiday</a>
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
                <table class="table table-bordered">
                    <tr class="bg-white">
                        <th>Sr#</th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('staffHolidays.index', array_merge(request()->query(), ['sort' => 'date', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Date</a></i>
                            @if (request('sort') === 'date')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Staff Name</th>
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($staffHolidays))
                        @foreach ($staffHolidays as $staffHoliday)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $staffHoliday->date }}({{ \Carbon\Carbon::parse($staffHoliday->date)->format('l') }})
                                </td>
                                <td>{{ $staffHoliday->staff->name }}</td>
                                <td>
                                    <form id="deleteForm{{ $staffHoliday->id }}"
                                        action="{{ route('staffHolidays.destroy', $staffHoliday->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        @can('staff-holiday-delete')
                                            <button type="button" onclick="confirmDelete('{{ $staffHoliday->id }}')"
                                                class="btn text-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">There is no staff Holiday.</td>
                        </tr>
                    @endif
                </table>
                {!! $staffHolidays->links() !!}
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
