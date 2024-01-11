@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Companies</h2>
        </div>
        <div class="col-md-6">
            @can('company-create')
            <a class="btn btn-success  float-end" href="{{ route('companies.create') }}"><i class="fa fa-plus"></i></a>
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
            <th>Sr#</th>
            <th class="text-left">Title</th>
            <th class="text-left">Body</th>
            <!-- <th class="text-right">Action</th> -->
        </tr>
        @if(count($companies))
        @foreach ($companies as $company)
        <tr>
            <td>{{ ++$i }}</td>
            <td class="text-left">{{ $company->title }}</td>
            <td class="text-left">{{ $company->body }}</td>
            <!-- <td class="text-right">
                <form id="deleteForm{{ $company->id }}" action="{{ route('companies.destroy',$company->id) }}" method="POST">
                    <a class="btn btn-warning" href="{{ route('companies.show',$company->id) }}"><i class="fa fa-eye"></i></a>
                    @can('company-edit')
                    <a class="btn btn-primary" href="{{ route('companies.edit',$company->id) }}"><i class="fa fa-edit"></i></a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('company-delete')
                    <button type="button" onclick="confirmDelete('{{ $company->id }}')" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                    @endcan
                </form>
            </td> -->
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="8" class="text-center">There is no company.</td>
        </tr>
        @endif
    </table>
    {!! $companies->links() !!}
</div>
<!-- <script>
    function confirmDelete(Id) {
        var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
</script> -->
@endsection