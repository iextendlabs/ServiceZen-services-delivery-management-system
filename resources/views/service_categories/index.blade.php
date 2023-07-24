@extends('layouts.app')
<style>
  a {
    text-decoration: none !important;
  }
</style>
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h2>Service Categories</h2>
        </div>
        <div class="col-md-6">
            @can('service-category-create')
            <a class="btn btn-success  float-end" href="{{ route('serviceCategories.create') }}"> Create New Service Category</a>
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
    <table class="table table-striped table-bordered">
        <tr>
            <th>No</th>
            <th>Title</th>
            <th>Description</th>
            <th width="280px">Action</th>
        </tr>
        @if(count($service_categories))
        @foreach ($service_categories as $service_category)
        <tr>
            <td>{{ ++$i }}</td>
            <td><a href="/serviceFilterCategory?category_id={{$service_category->id}}">{{ $service_category->title }}</a></td>
            <td>{{ $service_category->description }}</td>
            <td>
                <form action="{{ route('serviceCategories.destroy',$service_category->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('serviceCategories.show',$service_category->id) }}">Show</a>
                    @can('service-category-edit')
                    <a class="btn btn-primary" href="{{ route('serviceCategories.edit',$service_category->id) }}">Edit</a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('service-category-delete')
                    <button type="submit" class="btn btn-danger">Delete</button>
                    @endcan
                </form>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="4" class="text-center">There is no service category.</td>
        </tr>
        @endif
    </table>
    {!! $service_categories->links() !!}
@endsection