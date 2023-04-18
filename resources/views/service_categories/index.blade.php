@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-6">
            <h2>Service Categories</h2>
        </div>
        <div class="col-6">
            <a class="btn btn-success  float-end" href="{{ route('serviceCategories.create') }}"> Create New Service Category</a>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Title</th>
            <th>Description</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($service_categories as $service_category)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $service_category->title }}</td>
            <td>{{ $service_category->description }}</td>
            <td>
                <form action="{{ route('serviceCategories.destroy',$service_category->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('serviceCategories.show',$service_category->id) }}">Show</a>
                    <a class="btn btn-primary" href="{{ route('serviceCategories.edit',$service_category->id) }}">Edit</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
    {!! $service_categories->links() !!}
@endsection