<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Appointment Print</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<style>
    .table td, .table th {
        vertical-align: middle;
        text-align: center;
    }
    .badge {
        padding: 0em 1em;
        font-size: 85%;
        line-height: 2;
    }
</style>
</head>
<body>
    <div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Appointments</h2>
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
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th>No</th>
                    <th>Service</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>date</th>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Staff</th>
                    <th>Group</th>
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
                    <td>{{ $appointment->customer->name }}</td>
                    <td>{{ $appointment->staff->name }}</td>
                    <td>{{ $appointment->time_slot->group->name }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="10" class="text-center" >There is no appointment.</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
        window.onload = function() {
            window.print();
        };
    </script>
</html>
