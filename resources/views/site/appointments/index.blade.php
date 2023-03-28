@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="row">
    <div class="col-lg-12 py-5 text-center">
        <h2>Your Booked Service</h2>
    </div>
</div>
<div class="container">
    <div class="album bg-light">
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
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Service</th>
            <th>Price</th>
            <th>date</th>
            <th>Time</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($booked_services as $booked_service)
        @if($booked_service->customer_id == $customer_id)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $booked_service->service->name }}</td>
            <td>{{ $booked_service->service->price }}</td>
            <td>{{ $booked_service->date }}</td>
            <td>{{ $booked_service->time }}</td>
            <td>
                <form action="{{ route('booking.destroy',$booked_service->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Cancel</button>
                </form>
            </td>
        </tr>
        @endif
        @endforeach
    </table>
    {!! $booked_services->links() !!}
    
  </div>
</div>
@endsection