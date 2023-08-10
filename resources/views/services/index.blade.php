@extends('layouts.app')
@section('content')
    <div class="row">
    <div class="col-md-8">
        <h2>Services</h2>
    </div>
    <div class="col-md-4 ">
        @can('service-create')
        <a class="btn btn-success  float-end" href="{{ route('services.create') }}"> Create New Service</a>
        @endcan

        @can('service-delete')
        <button id="bulkDeleteBtn" class="btn btn-danger">Delete Selected</button>
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
        <div class="col-md-9">
            <table class="table table-striped table-bordered">
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Price</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($services))
                @foreach ($services as $service)
                <tr>
                <td>
                    <input type="checkbox" class="item-checkbox" value="{{ $service->id }}">
                </td>
                    <td>{{ $service->name }}</td>
                    <td>@currency( $service->price )</td>
                    <td>
                        <form action="{{ route('services.destroy',$service->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('services.show',$service->id) }}">Show</a>
                            @can('service-edit')
                            <a class="btn btn-primary" href="{{ route('services.edit',$service->id) }}">Edit</a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('service-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
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
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="{{ route('services.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Price:</strong>
                            <input type="number" name="price" value="{{ $filter['price'] }}" class="form-control" placeholder="Price">
                        </div>
                    </div>
                    <div class="col-md-12">
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
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('bulkDeleteBtn').addEventListener('click', function () {
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
            fetch('{{ route('service.bulkDelete') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ selectedItems })
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