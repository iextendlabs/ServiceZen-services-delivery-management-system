@extends('layouts.app')
@section('content')
    <div class="container" style="max-width: 700px">
        <h3 class="h3 text-center border-bottom pb-3">Holiday Calender</h3>
        <div id='full_calendar_events'></div>
    </div>
    
    <script>
        $(document).ready(function () {
            var SITEURL = "{{ url('/') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var calendar = $('#full_calendar_events').fullCalendar({
                editable: false,
                editable: false,
                events: SITEURL + "/holidays",
                displayEventTime: true,
                selectable: true,
                selectHelper: true,
                select: function (date) {
                        var date = $.fullCalendar.formatDate(date, "Y-MM-DD");
                        $.ajax({
                            url: SITEURL + "/holidays/crud-ajax",
                            data: {
                                date: date,
                                type: 'create'
                            },
                            type: "POST",
                            success: function (data) {
                                displayMessage("Holiday created.");
                                calendar.fullCalendar('renderEvent', {
                                    id: data.id,
                                    date: date
                                }, true);
                                calendar.fullCalendar('unselect');
                            }
                        });
                },
                eventClick: function (event) {
                    var eventDelete = confirm("Are you sure to Delete?");
                    if (eventDelete) {
                        $.ajax({
                            type: "POST",
                            url: SITEURL + '/holidays/crud-ajax',
                            data: {
                                id: event.id,
                                type: 'delete'
                            },
                            success: function (response) {
                                calendar.fullCalendar('removeEvents', event.id);
                                displayMessage("Holiday removed");
                            }
                        });
                    }
                }
            });
        });
        function displayMessage(message) {
            toastr.success(message, 'Event');            
        }
    </script>
@endsection
