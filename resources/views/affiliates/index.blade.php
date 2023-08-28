@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Affiliate</h2>
            </div>
            <div class="float-end">
                @can('affiliate-create')
                    <a class="btn btn-success" href="{{ route('affiliates.create') }}"><i class="fa fa-plus"></i></a>
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
    @php
        $i = 0;
    @endphp
    <div class="row">
        <div class="col-md-9">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Commission</th>
                    <th>Code</th>
                    <th>Salary</th>
                    <th>Action</th>
                </tr>
                @if(count($affiliates))
                @foreach ($affiliates as $affiliate)
                @if($affiliate->getRoleNames() == '["Affiliate"]')
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $affiliate->name }}</td>
                    <td>{{ $affiliate->email }}</td>
                    <td>{{ $affiliate->affiliate->commission }}%</td>
                    <td>{{ $affiliate->affiliate->code }}</td>
                    <td>Rs.{{$pkrRateValue * $affiliate->affiliate->fix_salary }}</td>
                  
                    <td>
                        <form action="{{ route('affiliates.destroy',$affiliate->id) }}" method="POST">
                            <a class="btn btn-warning" href="{{ route('affiliates.show',$affiliate->id) }}"><i class="fa fa-eye"></i></a>
                            @can('affiliate-edit')
                            <a class="btn btn-primary" href="{{ route('affiliates.edit',$affiliate->id) }}"><i class="fa fa-edit"></i></a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('affiliate-delete')
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
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
            <form action="{{ route('affiliates.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" value="{{$filter_name}}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
@endsection