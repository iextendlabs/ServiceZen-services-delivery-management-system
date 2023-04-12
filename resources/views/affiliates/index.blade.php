@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="float-start">
                <h2>Affiliate</h2>
            </div>
            <div class="float-end">
                @can('affiliate-create')
                    <a class="btn btn-success" href="{{ route('affiliates.create') }}"> Create New Affiliate</a>
                @endcan
            </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($affiliates as $affiliate)
        @if($affiliate->getRoleNames() == '["Affiliate"]')
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $affiliate->name }}</td>
            <td>{{ $affiliate->email }}</td>
            <td>
                @if(!empty($affiliate->getRoleNames()))
                    @foreach($affiliate->getRoleNames() as $v)
                        <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                    @endforeach
                @endif
            </td>
            <td>
                <form action="{{ route('affiliates.destroy',$affiliate->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('affiliates.show',$affiliate->id) }}">Show</a>
                    @can('affiliate-edit')
                    <a class="btn btn-primary" href="{{ route('affiliates.edit',$affiliate->id) }}">Edit</a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('affiliate-delete')
                    <button type="submit" class="btn btn-danger">Delete</button>
                    @endcan
                </form>
            </td>
        </tr>
        @endif
        @endforeach
    </table>
    {!! $affiliates->links() !!}
@endsection