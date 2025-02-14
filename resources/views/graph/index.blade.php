@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointment Timeline</title>
  <!-- Include Google Charts library -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages':['timeline']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      // Create the data table.
      var dataTable = new google.visualization.DataTable();
      dataTable.addColumn({ type: 'string', id: 'Staff' });
      dataTable.addColumn({ type: 'string', id: 'Appointment' });
      dataTable.addColumn({ type: 'date', id: 'Start' });
      dataTable.addColumn({ type: 'date', id: 'End' });

      // Add your appointment data here
    var dataRows = [];

    @foreach($orders as $order)
    var timeSlot = '{{ $order['time_slot_value'] }}';
    var times = timeSlot.split(' -- ');
    var startTime = new Date('2023-12-01 ' + times[0]);
    var endTime = new Date('2023-12-01 ' + times[1]);
        dataRows.push([
            '{{ $order['staff_name'] }}',
            '{{ $order['customer_name'] }}',
            startTime,
            endTime
        ]);
    @endforeach

    dataTable.addRows(dataRows);

      // Set chart options
      var options = {
        timeline: {
          groupByRowLabel: true,
          showBarLabels: true
        }
      };

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.Timeline(document.getElementById('timeline_chart'));
      chart.draw(dataTable, options);
    }
  </script>

</head>
<body>
    <div id="container">
        <h4 style="text-align: center">ROTA</h4>
        <h5 style="text-align: center">Appointment Schedule <br> for <br> {{ $date }}</h5>
    </div>
    @if(!auth()->user()->hasRole("Staff"))

    <div id="container">
        <form method="get" action="{{ route('rota') }}">
            @csrf
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" value="{{ old('date',$date) }}" required>
            <button type="submit">Submit</button>
        </form>
    </div>
    @endif
  <!-- Create a div to hold the chart -->
  <div id="timeline_chart" style="height: 768px;"></div>
   <style>
    /* Custom CSS to make the timeline labels appear at the top */
    .google-visualization-timeline-axis-label {
      transform: rotate(45deg);
      transform-origin: 0 100%;
    }
  </style>
  <div id="container">
    @foreach($orders as $order)
        {{ $order['customer_id'] }}{{ $order['customer_name'] }}{{ $order['customer_email'] }}
    @endforeach
  </div>
</body>
</html>
@endsection
