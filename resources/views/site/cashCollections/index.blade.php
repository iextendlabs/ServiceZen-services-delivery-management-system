@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Your Cash Collection</h2>
        </div>
    </div>
    <div class="text-right">
        <a class="btn btn-success float-end" href="{{ route('cashCollections.create') }}"> Create New Cash Collection</a>
    </div><br>
    <div>
        @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(count($cash_collections) != 0)
        <table class="table table-bordered album bg-light">
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Appointment</th>
                <th>Staff</th>
            </tr>
            @foreach ($cash_collections as $cash_collection)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $cash_collection->name }}</td>
                <td>{{ $cash_collection->description }}</td>
                <td>${{ $cash_collection->amount }}</td>
                <td>{{ $cash_collection->status }}</td>
                <td>{{ $cash_collection->appointment->service->name }}</td>
                <td>{{ $cash_collection->staff->name }}</td>
            </tr>
            @endforeach

        </table>
        @else
        <div class="text-center">
            <h4>There is no Cash Collections</h4>
        </div>
        @endif
    </div>
</div>
@endsection