@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-6">
        <h2>Cash Collection</h2>
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
        <table class="table table-striped table-bordered">
            <tr>
                <th>Order #</th>
                <th>Staff</th>
                <th>Collected Amount</th>
                <th>Customer</th>
                <th>Order Total</th>
                <th>Description</th>
                <th>Order Comment</th>
                <th>Status</th>
                <th width="280px">Action</th>
            </tr>
            @if(count($cash_collections))
            @foreach ($cash_collections as $cash_collection)
            <tr>
                <td>{{ $cash_collection->order->id }}</td>
                <td>{{ $cash_collection->staff_name }}</td>
                <td>@currency($cash_collection->amount)</td>
                <td>{{ $cash_collection->order->customer->name }}</td>
                <td>@currency( $cash_collection->order->total_amount )</td>
                <td>{{ $cash_collection->description }}</td>

                <td> {{$cash_collection->order->comment}}</td>

                <td>{{ $cash_collection->status }}</td>
                <td>
                    <form action="{{ route('cashCollection.destroy',$cash_collection->id) }}" method="POST">
                        <!-- <a class="btn btn-info" href="{{ route('cashCollection.show',$cash_collection->id) }}">Show</a> -->
                        @can('cash-collection-edit')
                        <a class="btn btn-primary" href="{{ route('cashCollection.edit',$cash_collection->id) }}">Edit</a>
                        @endcan
                        @csrf
                        @method('DELETE')
                        @can('cash-collection-delete')
                        <button type="submit" class="btn btn-danger">Delete</button>
                        @endcan
                    </form>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="8" class="text-center">There is no Cash Collection.</td>
            </tr>
            @endif
        </table>
        {!! $cash_collections->links() !!}
    </div>
    <div class="col-md-3">
        <h3>Filter</h3>
        <hr>
        <form action="{{ route('cashCollection.index') }}" method="GET" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">
                            @if($filter_status == "Not Approved")
                                <option value="Not Approved" selected>Not Approved</option>
                                <option value="Approved">Approved</option>
                            @elseif($filter_status == "Approved")
                                <option value="Not Approved">Not Approved</option>
                                <option value="Approved" selected>Approved</option>
                            @else
                                <option value=""></option>
                                <option value="Approved">Approved</option>
                                <option value="Not Approved">Not Approved</option>
                            @endif
                        </select>
                        <!-- <input type="text" name="name" value="{{}}" class="form-control" placeholder="Name"> -->
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection