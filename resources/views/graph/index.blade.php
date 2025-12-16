@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointment Timeline</title>

<!-- Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['timeline']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn({ type: 'string', id: 'Staff' });
    dataTable.addColumn({ type: 'string', id: 'Appointment' });
    dataTable.addColumn({ type: 'date', id: 'Start' });
    dataTable.addColumn({ type: 'date', id: 'End' });

    var dataRows = [];
    @foreach($orders as $order)
        var timeSlot = '{{ $order['time_slot_value'] }}';
        var times = timeSlot.split(' -- ');
        var appointmentDate = '{{ $date }}';
        var startTime = new Date(appointmentDate + ' ' + times[0]);
        var endTime = new Date(appointmentDate + ' ' + times[1]);
        dataRows.push([
            '{{ $order['staff_name'] }}',
            '{{ $order['customer_name'] }} ({{ $order['time_slot_value'] }})',
            startTime,
            endTime
        ]);
    @endforeach

    dataTable.addRows(dataRows);

    var options = {
        timeline: {
            groupByRowLabel: true,
            showBarLabels: true,
            rowLabelStyle: { fontName: 'Poppins', fontSize: 16, color: '#fff' },
            barLabelStyle: { fontName: 'Poppins', fontSize: 13, color: '#222' }
        },
        backgroundColor: 'transparent',
        colors: ['#F472B6', '#C084FC', '#60A5FA', '#34D399', '#FBBF24'],
    };

    var chart = new google.visualization.Timeline(document.getElementById('timeline_chart'));
    chart.draw(dataTable, options);
}
</script>

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(rgba(15, 15, 40, 0.6), rgba(15, 15, 40, 0.6)),
        url('https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=870') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
}
src=""
/* Main wrapper */
.modern-dashboard-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    padding: 40px 20px;
}

/* Glassmorphic card */
.main-card-container {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    max-width: 1200px;
    width: 100%;
    padding: 40px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Header */
.header-card {
    text-align: center;
    margin-bottom: 30px;
}
.title-rota {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(90deg, #F472B6, #C084FC, #60A5FA);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 2px;
    margin-bottom: 10px;
}
.title-schedule {
    font-size: 1.3rem;
    color: #f0f0f0;
    font-weight: 400;
}

/* Form */
.form-card {
    margin-bottom: 30px;
    display: flex;
    justify-content: center;
}
.date-form {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: center;
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    padding: 10px 20px;
    box-shadow: inset 0 0 10px rgba(255,255,255,0.1);
}
.date-form label {
    color: #f9fafb;
    font-weight: 600;
}
.date-form input[type="date"] {
    background: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
    padding: 10px 15px;
    border-radius: 10px;
    outline: none;
}
.date-form input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
}
.date-form button {
    background: linear-gradient(90deg, #C084FC, #F472B6);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.date-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(192, 132, 252, 0.4);
}

/* Chart area */
.chart-wrapper {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    padding: 25px;
    min-height: 400px;
    box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.2);
}

/* Mobile */
@media (max-width: 768px) {
    .main-card-container {
        padding: 25px;
    }
    .title-rota {
        font-size: 2rem;
    }
    .title-schedule {
        font-size: 1.1rem;
    }
    .date-form {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
</head>

<body>
<div class="modern-dashboard-wrapper">
    <div class="main-card-container">

        <div class="header-card">
            <h4 class="title-rota">ROTA</h4>
            <h5 class="title-schedule">Appointment Schedule<br>for<br><strong>{{ $date }}</strong></h5>
        </div>

        @if(!auth()->user()->hasRole("Staff"))
        <div class="form-card">
            <form method="get" action="{{ route('rota') }}" class="date-form">
                @csrf
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="{{ old('date',$date) }}" required>
                <button type="submit" >View Schedule</button>
            </form>
        </div>
        @endif

        <div class="chart-wrapper">
            <div id="timeline_chart" style="width: 100%; height: 70vh;"></div>
        </div>

        <div class="order-info-footer" style="display:none;">
            @foreach($orders as $order)
            {{ $order['customer_name'] }}
            @endforeach
        </div>

    </div>
</div>
</body>
</html>
@endsection
