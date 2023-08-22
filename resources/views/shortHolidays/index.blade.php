@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="float-start">
            <h2>Short Holidays</h2>
        </div>
        <div class="float-end">
            @can('staff-holiday-create')
            <a class="btn btn-success  float-end" href="{{ route('shortHolidays.create') }}" style="margin-left: 5px;"> <i class="fa fa-plus"></i></a>
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
                <th>Date</th>
                <th>Time Start</th>
                <th>Time End</th>
                <th>Staff Name</th>
                <th>Action</th>
            </tr>
            @if(count($shortHolidays))
            @foreach ($shortHolidays as $shortHoliday)
            <tr>
            <td>
                    <input type="checkbox" class="item-checkbox" value="{{ $shortHoliday->id }}">
                </td>
                <td>{{ ++$i }}</td>
                <td>{{ $shortHoliday->date }}({{ \Carbon\Carbon::parse($shortHoliday->date)->format('l') }})</td>
                <td>{{ date('h:i A', strtotime($shortHoliday->time_start)) }}</td>
                <td>{{ date('h:i A', strtotime($shortHoliday->time_end)) }}</td>
                <td>{{ $shortHoliday->staff->name }}</td>
                <td>
                    <form action="{{ route('shortHolidays.destroy',$shortHoliday->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        @can('staff-holiday-delete')
                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
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
        {!! $shortHolidays->links() !!}
    </div>
</div>
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
@endsection