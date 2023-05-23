@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Appointments</h2>
            </div>
            <div class="float-end">
                <a class="btn btn-primary float-end no-print" href="appointmentDetailCSV"><i class="fa fa-download"></i>Export CSV</a>
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
                    <th>Service</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>date</th>
                    <th>Time</th>
                    <th>Staff</th>
                    <th>Group</th>
                    <th>Action</th>
                </tr>
                @if(count($appointments))
                @foreach ($appointments as $appointment)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $appointment->service->name }}</td>
                    <td>{{ $appointment->service->duration }}</td>
                    <td>${{ $appointment->price }}</td>
                    <td>{{ $appointment->status }}</td>
                    <td>{{ $appointment->date }}</td>
                    <td>{{ date('h:i A', strtotime($appointment->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($appointment->time_slot->time_end)) }}</td>
                    <td>{{ $appointment->staff->name }}</td>
                    <td>{{ $appointment->time_slot->group->name }}</td>
                    <td>
                        <form action="{{ route('appointments.destroy',$appointment->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('appointments.show',$appointment->id) }}">Show</a>
                            <a class="btn btn-primary" href="{{ route('appointments.edit',$appointment->id) }}">Edit</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="10" class="text-center" >There is no appointment.</td>
                </tr>
                @endif
            </table>
            {!! $appointments->links() !!}
        </div>
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="appointmentFilter" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="row">
                <div class="col-md-12">
                        <div class="form-group">
                            <strong>Service:</strong>
                            <select name="service_id" class="form-control">
                                <option></option>
                                @foreach ($services as $service)
                                    @if($service->id == $filter['service'])
                                    <option value="{{ $service->id }}" selected>{{ $service->name }}</option>
                                    @else
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="status" class="form-control">
                                <option></option>
                                @foreach ($statuses as $status)
                                    @if($status == $filter['status'])
                                    <option value="{{ $status }}" selected>{{ $status }}</option>
                                    @else
                                    <option value="{{ $status }}">{{ $status }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Staff:</strong>
                            <select name="staff_id" class="form-control">
                                <option></option>
                                @foreach ($users as $staff)
                                @if($staff->getRoleNames() == '["Staff"]')
                                        @if($staff->id == $filter['staff'])
                                        <option value="{{ $staff->id }}" selected>{{ $staff->name }}</option>
                                        @else
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endif
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Customer:</strong>
                            <select name="customer_id" class="form-control">
                                <option></option>
                                @foreach ($users as $customer)
                                @if($customer->getRoleNames() == '["Customer"]')
                                        @if($customer->id == $filter['customer'])
                                        <option value="{{ $customer->id }}" selected>{{ $customer->name }}</option>
                                        @else
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endif
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Date Start:</strong>
                            <input type="date" name="date_start" value="{{$filter['date_start']}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Date End:</strong>
                            <input type="date" name="date_end" value="{{$filter['date_end']}}" class="form-control">
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