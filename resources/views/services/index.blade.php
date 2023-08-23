@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2>Services</h2>
        </div>
        <div class="float-end">
            @can('service-create')
            <a class="btn btn-primary " href="{{ route('services.create') }}"><i class="fa fa-plus"></i></a>
            @endcan

            <button id="bulkCopyBtn" class="btn btn-secondary"><i class="fa fa-copy"></i></button>

            @can('service-delete')
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
        <h3>Filter</h3>
        <hr>
        <form action="{{ route('services.index') }}" method="GET" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control" placeholder="Name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Price:</strong>
                        <input type="number" name="price" value="{{ $filter['price'] }}" class="form-control" placeholder="Price">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Category:</strong>
                        <select name="category_id" class="form-control">
                            <option></option>
                            @foreach($service_categories as $category)
                            @if($category->id == $filter['category_id'])
                            <option value="{{$category->id}}" selected>{{$category->title}}</option>
                            @else
                            <option value="{{$category->id}}">{{$category->title}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="offset-6 col-md-3 text-center">
                        <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                    </div>
                    <div class="col-md-3 text-center">
                        <button type="submit" class="btn btn-block btn-primary">Filter</button>
                    </div>
            </div>
        </form>
    </div>
    <div class="col-md-12">
        <table class="table table-striped table-bordered">
            <tr>
                <th></th>
                <th class="text-left">Name</th>
                <th class="text-right">Price</th>
                <th class="text-right">Duration</th>
                <th class="text-right">Action</th>
            </tr>
            @if(count($services))
            @foreach ($services as $service)
            <tr>
                <td>
                    <input type="checkbox" class="item-checkbox" value="{{ $service->id }}">
                </td>
                <td class="text-left">{{ $service->name }}</td>
                <td class="text-right">@currency( $service->price )</td>
                <td class="text-right">{{ $service->duration }}</td>
                <td class="text-right">
                    <!-- <form action="{{ route('services.destroy',$service->id) }}" method="POST"> -->
                        <a class="btn btn-primary" href="{{ route('FAQs.create', ['service_id' => $service->id]) }}">Add FAQs</a>
                        <a class="btn btn-warning" href="{{ route('services.show',$service->id) }}"><i class="fa fa-eye"></i></a>
                        @can('service-edit')
                        <a class="btn btn-primary" href="{{ route('services.edit',$service->id) }}"><i class="fa fa-edit"></i></a>
                        @endcan
                        <!-- @csrf
                        @method('DELETE')
                        @can('service-delete')
                        <button type="submit" class="btn btn-danger">Delete</button>
                        @endcan
                    </form> -->
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="4" class="text-center">There is no service.</td>
            </tr>
            @endif
        </table>
        {!! $services->links() !!}
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

        document.getElementById('bulkCopyBtn').addEventListener('click', function() {
            const selectedItems = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                .map(checkbox => checkbox.value);

            if (selectedItems.length > 0) {
                if (confirm('Are you sure you want to Copy the selected items?')) {
                    copySelectedItems(selectedItems);
                }
            } else {
                alert('Please select items to Copy.');
            }
        });

        function deleteSelectedItems(selectedItems) {
            fetch('{{ route('services.bulkDelete') }}', {
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

        function copySelectedItems(selectedItems) {
            fetch('{{ route('services.bulkCopy') }}', {
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