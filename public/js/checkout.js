$(document).on("change", "#date,#zone,#area,.checkout_service_id,.checkBooking_service_id,.order_service_id", function () {
    if($('.checkout_service_id:checked').val()){
        var checkedServiceIds = [$('.checkout_service_id:checked').val()];
    }

    if ($('#addToCartModalServices').val()) {
        var addToCartModalServices = [$('#addToCartModalServices').val()];
    } else if($('.checkBooking_service_id:checked').val()){
        var addToCartModalServices = [$('.checkBooking_service_id:checked').val()];
    }

    $.ajax({
        url: '/slots',
        method: 'GET',
        cache: false,
        data: {
            date: $('#date').val(),
            area: $('select[name="zone"]').val(),
            order_id: $('input[name="order_id"]').length ? $('input[name="order_id"]').val() : '',
            service_ids: addToCartModalServices ?? checkedServiceIds,
            zoneShow : addToCartModalServices ? 0 : 1
        },
        beforeSend: function () {
            $('#loading').show(); // Show the loading element
        },
        success: function (response) {
            var timeSlots = response;
            var timeSlotsContainer = $('#slots-container');
            timeSlotsContainer.empty();
            timeSlotsContainer.html(response);
            if (typeof updateTotal === 'function')
                updateTotal()
            $('[name=service_staff_id]:checked').length === 0 && $('[name=service_staff_id]').first().attr('checked', true).trigger('change');

        },
        error: function () {
            alert('Error retrieving time slots.');
        },
        complete: function () {
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

$(document).on('change', 'input[name="service_staff_id"]', function () {
    var slotName = $(this).attr('data-slot');
    var staffName = $(this).attr('data-staff');
    $('#selected-staff').html(staffName);
    if (typeof updateTotal === 'function')
        updateTotal()
});