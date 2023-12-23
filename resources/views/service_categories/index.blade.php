@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="float-left">
                <h2>Service Categories</h2>
            </div>
            <div class="float-right">
                @can('service-category-create')
                <a class="btn btn-success" href="{{ route('serviceCategories.create') }}"><i class="fa fa-plus"></i></a>
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
    <table class="table table-striped table-bordered">
        <tr>
            <th>Sr#</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th width="280px">Action</th>
        </tr>
        @if(count($service_categories))
        @foreach ($service_categories as $service_category)
        <tr>
            <td>{{ ++$i }}</td>
            <td><a href="{{ route('services.index', ['category_id' => $service_category->id]) }}">{{ $service_category->title }}</a></td>
            <td>{{ $service_category->description }}</td>
            <td>@if($service_category->status) Enable @else Disable @endif</td>
            <td>
                <form id="deleteForm{{ $service_category->id }}" action="{{ route('serviceCategories.destroy',$service_category->id) }}" method="POST">
                    @can('FAQs-create')
                    <a class="btn btn-primary" href="{{ route('FAQs.create', ['category_id' => $service_category->id]) }}">Add FAQs</a>
                    @endcan
                    <a class="btn btn-warning" href="{{ route('serviceCategories.show',$service_category->id) }}"><i class="fa fa-eye"></i></a>
                    @can('service-category-edit')
                    <a class="btn btn-primary" href="{{ route('serviceCategories.edit',$service_category->id) }}"><i class="fa fa-edit"></i></a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('service-category-delete')
                    <button type="button" onclick="confirmDelete('{{ $service_category->id }}')" class="btn btn-danger"><i class="fa fa-trash"></i></button>
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