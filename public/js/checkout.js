$(document).on("change","#date,#zone",function() {
    
    // Make AJAX call to retrieve time slots for selected date
    $.ajax({
        url: '/slots',
        method: 'GET',
        data: {
            date: $('#date').val(),
            area: $('select[name="zone"]').val(),
            order_id: $('input[name="order_id"]').length ? $('input[name="order_id"]').val() : '',
        },
        beforeSend: function() {
            $('#loading').show(); // Show the loading element
        },
        success: function(response) {
            var timeSlots = response;
            var timeSlotsContainer = $('#slots-container');
            timeSlotsContainer.empty();
            timeSlotsContainer.html(response);
        },
        error: function() {
            alert('Error retrieving time slots.');
        },
        complete: function() {
            $('#loading').hide(); // Hide the loading element after success or error
        }
    });
});

function convertTo12Hour(time) {
    var parts = time.split(':');
    var hours = parseInt(parts[0]);
    var minutes = parseInt(parts[1]);

    var suffix = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12; // Convert 0 to 12

    var formattedTime = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0') + ' ' + suffix;

    return formattedTime;
}

$(document).on('change', 'input[name="service_staff_id"]', function() {
    var slotName = $(this).attr('data-slot');
    var staffName = $(this).attr('data-staff');

    $('#selected-time-slot').html(slotName);
    $('#selected-staff').html(staffName);
});