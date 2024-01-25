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
                <h2>FAQs</h2>
            </div>
            <div class="float-right">
                @can('FAQs-create')
                <a class="btn btn-success  float-end" href="{{ route('FAQs.create') }}"> <i class="fa fa-plus"></i></a>
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
            <form action="{{ route('FAQs.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Question:</strong>
                            <input type="text" name="question" value="{{ $filter['question'] }}" class="form-control" placeholder="Question">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Services:</strong>
                            <select name="service_id" class="form-control">
                                <option></option>
                                @foreach($services as $service)
                                @if($service->id == $filter['service_id'])
                                <option value="{{$service->id}}" selected>{{$service->name}}</option>
                                @else
                                <option value="{{$service->id}}">{{$service->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Category:</strong>
                            <select name="category_id" class="form-control">
                                <option></option>
                                @foreach($categories as $category)
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
                    <th>Sr#</th>
                    <th class="text-left">Question</th>
                    <th class="text-left">Answer</th>
                    <th class="text-left">Status</th>
                    <th>Action</th>
                </tr>
                @if(count($FAQs))
                @foreach ($FAQs as $FAQ)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td class="text-left">{{ substr($FAQ->question, 0, 50) }}...</td>
                    <td class="text-left">{{ substr($FAQ->answer, 0, 50) }}...</td>
                    <td class="text-left">@if($FAQ->status == 1) Enable @else Disable @endif</td>
                    <td>
                        <form id="deleteForm{{ $FAQ->id }}" action="{{ route('FAQs.destroy',$FAQ->id) }}" method="POST">
                            <a class="btn btn-warning" href="{{ route('FAQs.show',$FAQ->id) }}"><i class="fa fa-eye"></i></a>
                            @can('FAQs-edit')
                            <a class="btn btn-primary" href="{{ route('FAQs.edit',$FAQ->id) }}"><i class="fa fa-edit"></i></a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('FAQs-delete')
                            <button type="button" onclick="confirmDelete('{{ $FAQ->id }}')" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4" class="text-center">There is no FAQs.</td>
                </tr>
                @endif
            </table>
            {!! $FAQs->links() !!}
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
@endsection