@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h2>Services</h2>
        </div>
        <div class="col-md-6">
            @can('service-create')
            <a class="btn btn-success  float-end" href="{{ route('services.create') }}"> Create New Service</a>
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
            <table class="table table-bordered">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($services))
                @foreach ($services as $service)
                <tr>
                    <td>{{ ++$i }}</td>
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
            <form action="serviceFilter" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" @if(isset($name)) value="{{$name}}"  @endif class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Price:</strong>
                            <input type="number" name="price" @if(isset($price)) value="{{$price}}"  @endif class="form-control" placeholder="Price">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Category:</strong>
                            <select name="category_id" class="form-control">
                                <option></option>
                                @foreach($service_categories as $category)
                                    @if($category->id == $category_id)
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
    
@endsection