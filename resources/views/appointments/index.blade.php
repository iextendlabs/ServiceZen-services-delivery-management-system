@extends('layouts.main')

@section('content')
    <div class="container">
        <h2>Appointments</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Staff</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Customer Phone</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->service->name }}</td>
                        <td>{{ $appointment->staff->name }}</td>
                        <td>{{ date('F j, Y', strtotime($appointment->start_time)) }}</td>
                        <td>{{ date('g:i A', strtotime($appointment->start_time)) }} - {{ date('g:i A', strtotime($appointment->end_time)) }}</td>
                        <td>{{ $appointment->customer_name }}</td>
                        <td>{{ $appointment->customer_email }}</td>
                        <td>{{ $appointment->customer_phone }}</td>
                        <td>{{ $appointment->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
