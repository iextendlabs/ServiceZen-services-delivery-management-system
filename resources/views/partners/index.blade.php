@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h2>Partners</h2>
        </div>
        <div class="col-md-6">
            @can('partner-create')
            <a class="btn btn-success  float-end" href="{{ route('partners.create') }}"> Create New Partner</a>
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
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Description</th>
            <th width="280px">Action</th>
        </tr>
        @if(count($partners))
        @foreach ($partners as $partner)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $partner->name }}</td>
            <td>{{ $partner->description }}</td>
            <td>
                <form action="{{ route('partners.destroy',$partner->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('partners.show',$partner->id) }}">Show</a>
                    @can('partner-edit')
                    <a class="btn btn-primary" href="{{ route('partners.edit',$partner->id) }}">Edit</a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('partner-delete')
                    <button type="submit" class="btn btn-danger">Delete</button>
                    @endcan
                </form>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="4" class="text-center">There is no partner.</td>
        </tr>
        @endif
    </table>
    {!! $partners->links() !!}
@endsection