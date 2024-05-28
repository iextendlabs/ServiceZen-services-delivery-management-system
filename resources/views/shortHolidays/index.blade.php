@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-start">
                    <h2>Short Holidays ({{ $total_shortHoliday }})</h2>
                </div>
                <div class="float-end">
                    @can('staff-holiday-create')
                        <a class="btn btn-success  float-end" href="{{ route('shortHolidays.create') }}" style="margin-left: 5px;">
                            <i class="fa fa-plus"></i></a>
                    @endcan
                    @can('staff-holiday-delete')
                        <button id="bulkDeleteBtn" class="btn btn-danger"><i class="fa fa-trash"></i></button>
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
                <table class="table table-striped table-bordered">
                    <tr>
                        <th></th>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('shortHolidays.index', array_merge(request()->query(), ['sort' => 'date', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Date</a>
                            @if (request('sort') === 'date')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('shortHolidays.index', array_merge(request()->query(), ['sort' => 'time_start', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Time
                                Start</a>
                            @if (request('sort') === 'time_start')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('shortHolidays.index', array_merge(request()->query(), ['sort' => 'hours', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Hours</a>
                            @if (request('sort') === 'hours')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('shortHolidays.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                            @if (request('sort') === 'status')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Staff Name</th>
                        <th>Action</th>
                    </tr>
                    @if (count($shortHolidays))
                        @foreach ($shortHolidays as $shortHoliday)
                            <tr>
                                <td>
                                    <input type="checkbox" class="item-checkbox" value="{{ $shortHoliday->id }}">
                                </td>
                                <td>{{ ++$i }}</td>
                                <td>{{ $shortHoliday->date }}({{ \Carbon\Carbon::parse($shortHoliday->date)->format('l') }})
                                </td>
                                <td>{{ date('h:i A', strtotime($shortHoliday->time_start)) }}</td>
                                <td>{{ $shortHoliday->hours }}</td>
                                <td>
                                    @if ($shortHoliday->status == 1)
                                        Enable
                                    @else
                                        Disable
                                    @endif
                                </td>
                                <td>{{ $shortHoliday->staff->name }}</td>
                                <td>
                                    <a class="btn btn-sm btn-success mb-2"
                                        href="{{ route('updateStatus', $shortHoliday->id) }}?status=1">
                                        <i class="fas fa-thumbs-up"></i>
                                    </a>

                                    <a class="btn btn-sm btn-danger mb-2"
                                        href="{{ route('updateStatus', $shortHoliday->id) }}?status=0">
                                        <i class="fas fa-thumbs-down"></i>
                                    </a>
                                    <form id="deleteForm{{ $shortHoliday->id }}"
                                        action="{{ route('shortHolidays.destroy', $shortHoliday->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        @can('staff-holiday-delete')
                                            <button type="button" onclick="confirmDelete('{{ $shortHoliday->id }}')"
                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">There is no staff Holiday.</td>
                        </tr>
                    @endif
                </table>
                {!! $shortHolidays->links() !!}
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
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
                const selectedItems = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                    .map(checkbox => checkbox.value);

                if (selectedItems.length > 0) {
                    if (confirm('Are you sure you want to delete the selected items?')) {
                        deleteSelectedItems(selectedItems);
                    }
                } else {
                    alert('Please select items to delete.');
                }
            });

            function deleteSelectedItems(selectedItems) {
                fetch('{{ route('shortHolidays.bulkDelete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            selectedItems
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
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
