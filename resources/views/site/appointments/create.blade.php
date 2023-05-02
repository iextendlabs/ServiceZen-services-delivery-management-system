@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Book Your Service</h2>
    </div>
</div>
<div class="album bg-light">
  <div class="container">
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
    <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="service_id" value="{{ $service_id }}">
        <input type="hidden" name="customer_id" value="{{ $customer_id }}">
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Date:</strong>
                    <input type="date" name="date" id="date" min="{{ date('Y-m-d'); }}" value="{{ date('Y-m-d'); }}" class="form-control" placeholder="Date">
                </div>
            </div>
            <div class="col-md-12">
                <strong >Time Slots</strong>
                <div id="time-slots-container">
                    @if(count($timeSlots))
                    @foreach($timeSlots as $timeSlot)
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="time_slot" value="{{ $timeSlot->time_start }} -- {{ $timeSlot->time_end }}">{{ $timeSlot->time_start }} -- {{ $timeSlot->time_end }}
                        </label>
                    </div>
                    @endforeach
                    @else
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were Holiday on your selected date.
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Time:</strong>
                    <input type="text" name="selected_time" class="form-control" disabled placeholder="Select the Time Slots">
                    <input type="hidden" name="time" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Address:</strong>
                    <textarea name="address" class="form-control" cols="10" rows="5"></textarea>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" name="checkout" class="btn btn-success">Checkout</button>
                <button type="submit" name="continue" class="btn btn-primary">Continue Booking</button>
            </div>
        </div>
    </form>
  </div>
</div>
<script>
    var time_slot = $('input[name="time_slot"]');

    var selected_time = $('input[name="selected_time"]');
    var time = $('input[name="time"]');

    $('#time-slots-container').on('change','input[name="time_slot"]',function() {
        if ($(this).is(':checked')) {
            selected_time.val($(this).val());
            time.val($(this).val());
        }
    });
</script>
<script>
// JavaScript Code
$('#date').change(function() {
    var selectedDate = $(this).val();

    // Make AJAX call to retrieve time slots for selected date
    $.ajax({
        url: '/slots',
        method: 'GET',
        data: {
            date: selectedDate
        },
        success: function(response) {
            var timeSlots = response;
            if (typeof timeSlots === 'string') {

                var timeSlotsContainer = $('#time-slots-container');
                timeSlotsContainer.empty();
                $('input[name="selected_time"]').val('');
                $('input[name="time"]').val('');

                var html = '<div class="alert alert-danger"><strong>Whoops!</strong> There were Holiday on your selected date.</div>';
                timeSlotsContainer.append(html);
            } else {

                var timeSlotsContainer = $('#time-slots-container');
                timeSlotsContainer.empty();
                $('input[name="selected_time"]').val('');
                $('input[name="time"]').val('');

                timeSlots.forEach(function(timeSlot) {
                    var html = '<div class="form-check"><label class="form-check-label">';
                    html += '<input type="radio" class="form-check-input" name="time_slot" value="'+timeSlot.time_start + ' -- ' + timeSlot.time_end+'">'+timeSlot.time_start + ' -- ' + timeSlot.time_end+'</label>'
                    html +='</div>'
                    timeSlotsContainer.append(html);
                });
            }
        },
        error: function() {
            alert('Error retrieving time slots.');
        }
    });
});
</script>

@endsection