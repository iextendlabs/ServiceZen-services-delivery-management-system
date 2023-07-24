@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Customer</h2>
            </div>
            <div class="float-end">
                @can('customer-create')
                    <a class="btn btn-success" href="{{ route('customers.create') }}"> Create New Customer</a>
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
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($customers))
                @foreach ($customers as $customer)
                @if($customer->getRoleNames() == '["Customer"]')
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                   
                    <td>
                        <form action="{{ route('customers.destroy',$customer->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('customers.show',$customer->id) }}">Show</a>
                            @can('customer-edit')
                            <a class="btn btn-primary" href="{{ route('customers.edit',$customer->id) }}">Edit</a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('customer-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endif
                @endforeach
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Customer</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="customerFilter" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" @if(isset($name)) value="{{$name}}"  @endif class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
@endsection