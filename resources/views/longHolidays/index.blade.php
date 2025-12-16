@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-start">
                    <h2>Long Holidays ({{ $total_longHoliday }})</h2>
                </div>
                <div class="float-end">
                    @can('staff-holiday-create')
                        <a class="btn text-dark  float-end" href="{{ route('longHolidays.create') }}" style="margin-left: 5px;">
                            <i class="fa fa-plus"></i> Add</a>
                    @endcan
                    @can('staff-holiday-delete')
                        <button id="bulkDeleteBtn" class="btn text-danger"><i class="fa fa-trash"></i> Delete</button>
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
                <table class="table table-bordered">
                    <tr class="bg-white">
                        <th></th>
                        <th>Sr#</th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('longHolidays.index', array_merge(request()->query(), ['sort' => 'date_start', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Date
                                Start</a></i>
                            @if (request('sort') === 'date_start')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('longHolidays.index', array_merge(request()->query(), ['sort' => 'date_end', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Date
                                End</a></i>
                            @if (request('sort') === 'date_end')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Staff Name</th>
                        <th>Action</th>
                    </tr>
                    @if (count($longHolidays))
                        @foreach ($longHolidays as $longHoliday)
                            <tr>
                                <td>
                                    <input type="checkbox" class="item-checkbox" value="{{ $longHoliday->id }}">
                                </td>
                                <td>{{ ++$i }}</td>
                                <td>{{ $longHoliday->date_start }}</td>
                                <td>{{ $longHoliday->date_end }}</td>
                                <td>{{ $longHoliday->staff->name }}</td>
                                <td>
                                    <form id="deleteForm{{ $longHoliday->id }}"
                                        action="{{ route('longHolidays.destroy', $longHoliday->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        @can('staff-holiday-delete')
                                            <button type="button" onclick="confirmDelete('{{ $longHoliday->id }}')"
                                                class="btn text-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">There is no staff Holiday.</td>
                        </tr>
                    @endif
                </table>
                {!! $longHolidays->links() !!}
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
                fetch('{{ route('longHolidays.bulkDelete') }}', {
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
@endsection
