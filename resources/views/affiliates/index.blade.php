@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
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
                    <th>Roles</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($affiliates))
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
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Affiliate.</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="affiliateFilter" method="POST" enctype="multipart/form-data">
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
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
@endsection