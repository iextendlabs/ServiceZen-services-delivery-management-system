@extends('site.layout.app')
@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-5 text-center">
                <h2>Assigned Service</h2>
            </div>
        </div>
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
            <div class="text-right">
                <a class="btn btn-primary mb-2  float-end no-print" onclick="printDiv()" href=""><i class="fa fa-print"></i>Download PDF</a>
                <a class="btn btn-primary float-end no-print" href="appointmentCSV"><i class="fa fa-download"></i>Export CSV</a>
            </div><br>
            @if(count($staffs) != 0)
            <table class="table table-striped table-bordered album bg-light">
                <tr>
                    <th>Sr#</th>
                    <th>Service</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Staff</th>
                    <th>date</th>
                    <th>Time</th>
                    <th class="no-print">Action</th>
                </tr>
                @foreach($staffs as $staff)
                @foreach($staff->appointments as $booked_service)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $booked_service->service->name }}</td>
                    <td>{{ $booked_service->service->price }}</td>
                    <td>{{ $booked_service->status }}</td>
                    <td>{{ $staff->user->name }}</td>
                    <td>{{ $booked_service->date }}</td>
                    <td>{{ $booked_service->time }}</td>
                    <td class="no-print">
                        <a class="btn btn-primary" href="{{ route('booking.edit',$booked_service->id) }}">Edit</a>
                    </td>
                </tr>
                @endforeach
                @endforeach

            </table>
            @else
            <div class="text-center">
                <h4>There is no assigned services.</h4>
            </div>
            @endif
        </div>
    </div>
</div>
<script>
    function printDiv() {
        window.print();
    }
</script>
@endsection