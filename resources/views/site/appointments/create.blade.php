@extends('site.layout.app')
<style>
    .list-group-item {
        font-family: 'Helvetica', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        background-color: #fff;
        border-color: #333;
    }

    .list-group-item:hover {
        background-color: #333;
        color: #fff;
        border-color: #333;
    }

    .list-group-item.active {
        background-color: #b4b3b3 !important;
        color: #fff !important;
        border-color: #b4b3b3 !important;
    }

    .badge-available {
        background-color: #28a745;
    }

    .badge-unavailable {
        background-color: #dc3545;
    }

    .badge {
        color: white
    }

    #map {
        height: 100%;
        width: 100%;
    }

    .popup img {}

    .popup {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .popup .close {
        color: black;
        font-size: 35px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 50px;
        opacity: 1;
        /* cursor: pointer; */
    }

    .location-search-wrapper {
        max-width: 558px;
        width: 100%;
        padding: 16px;
        background-color: #fff;
        margin: 0 auto;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .25);
        border-radius: 12px;
    }

    .location-container {
        position: relative;
    }

    .location-search {
        position: relative;
        height: 48px;
        min-width: 418px;
        border: 1px solid rgba(0, 0, 0, .12);
        border-radius: 24px;
        display: flex;
        align-items: center;
        padding: 8px;
        background-color: #fff;
    }

    .location-search .location-search-left {
        margin: 0 10px;
        display: flex;
        align-items: center;
    }

    .location-search .location-search-input-wrapper {
        width: 100%;
        height: 28px;
    }

    .location-search .location-search-input-wrapper .location-search-input {
        border: none;
        width: 100%;
        line-height: 24px;
        font-size: 1rem;
        color: rgba(0, 0, 0, .87);
        outline: none;
        background-color: transparent;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .location-search .location-search-right.en {
        margin-left: 8px;
        display: flex;
        align-items: center;
    }

    .location-search .location-search-right .location-search-clear.en {
        margin-right: 6px;
        transition: .3s ease-out;
        padding: 6px;
        cursor: pointer;
    }

    .location-search .location-search-right .locate-me {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #d9f6ff;
        border-radius: 16px;
        min-width: 32px;
        height: 32px;
        padding: 0 8px 2px;
        cursor: pointer;
    }

    .location-search .location-search-right .locate-me span {
        display: block;
        font-size: 14px;
        line-height: 18px;
        color: #00c3ff;
        width: 105px;
        margin-left: 2px;
    }

    .location-search .location-search-right .locate-me .locate-me-icon {
        width: 18px;
        height: 18px;
    }
</style>

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
        <div class="location-search-wrapper">
            <div class="location-container welcome-section">
                <div id="navbar-location-button" class="location-search lg">
                    <div class="location-search-left">
                        <svg width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0 6.2468C0 2.80246 2.69144 0 5.99974 0C9.30812 0 12 2.80246 12 6.2468C12 9.56227 6.55647 17.3084 6.32455 17.6364L6.10836 17.9429C6.0828 17.9789 6.04277 18 5.99974 18C5.95736 18 5.91707 17.9789 5.89177 17.9429L5.67545 17.6364C5.44367 17.3084 0 9.56227 0 6.2468ZM8.149 6.2468C8.149 5.01276 7.18511 4.00921 5.99974 4.00921C4.81502 4.00921 3.85047 5.01276 3.85047 6.2468C3.85047 7.48021 4.81506 8.4844 5.99974 8.4844C7.18507 8.4844 8.149 7.48021 8.149 6.2468Z" fill="black" fill-opacity="0.87"></path>
                        </svg>
                    </div>
                    <div class="location-search-input-wrapper">
                        <input id="searchInput" type="text" name="location-input" placeholder="Search for area, street name, landmark..." autocomplete="off" value="" class="location-search-input">
                    </div>
                    <div class="location-search-right en">
                        <div class="location-search-clear en" style="display:none;">
                            <svg width="15" height="15" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.3266 8.99984L16.2251 3.10128C16.5916 2.73512 16.5916 2.14101 16.2251 1.77485C15.8587 1.40838 15.2652 1.40838 14.8987 1.77485L9.00016 7.67342L3.10128 1.77485C2.73481 1.40838 2.14132 1.40838 1.77485 1.77485C1.40838 2.14101 1.40838 2.73512 1.77485 3.10128L7.67373 8.99984L1.77485 14.8984C1.40838 15.2646 1.40838 15.8587 1.77485 16.2248C1.95809 16.4078 2.19823 16.4994 2.43807 16.4994C2.6779 16.4994 2.91804 16.4078 3.10128 16.2245L9.00016 10.326L14.8987 16.2245C15.082 16.4078 15.3221 16.4994 15.5619 16.4994C15.8018 16.4994 16.0419 16.4078 16.2251 16.2245C16.5916 15.8584 16.5916 15.2643 16.2251 14.8981L10.3266 8.99984Z" fill="black" fill-opacity="0.87"></path>
                            </svg>
                        </div>
                        <div class="locate-me" id="manualLocationButton">
                            <span>Set my location</span>
                            <div class="locate-me-icon">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.375 9C12.375 10.8633 10.8633 12.375 9 12.375C7.10508 12.375 5.625 10.8633 5.625 9C5.625 7.10508 7.10508 5.625 9 5.625C10.8633 5.625 12.375 7.10508 12.375 9ZM9 7.3125C8.06836 7.3125 7.3125 8.06836 7.3125 9C7.3125 9.93164 8.06836 10.6875 9 10.6875C9.93164 10.6875 10.6875 9.93164 10.6875 9C10.6875 8.06836 9.93164 7.3125 9 7.3125ZM9 0C9.46758 0 9.84375 0.37793 9.84375 0.84375V2.30238C12.8953 2.68313 15.3176 5.10469 15.6973 8.15625H17.1562C17.6238 8.15625 18 8.53242 18 9C18 9.46758 17.6238 9.84375 17.1562 9.84375H15.6973C15.3176 12.8953 12.8953 15.3176 9.84375 15.6973V17.1562C9.84375 17.6238 9.46758 18 9 18C8.53242 18 8.15625 17.6238 8.15625 17.1562V15.6973C5.10469 15.3176 2.68313 12.8953 2.30238 9.84375H0.84375C0.37793 9.84375 0 9.46758 0 9C0 8.53242 0.37793 8.15625 0.84375 8.15625H2.30238C2.68313 5.10469 5.10469 2.68313 8.15625 2.30238V0.84375C8.15625 0.37793 8.53242 0 9 0ZM3.9375 9C3.9375 11.7949 6.20508 14.0625 9 14.0625C11.7949 14.0625 14.0625 11.7949 14.0625 9C14.0625 6.20508 11.7949 3.9375 9 3.9375C6.20508 3.9375 3.9375 6.20508 3.9375 9Z" fill="#00C3FF"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="popup">
            <div id="map"></div>
            <span class="close">&times;</span>
        </div>
        <form action="{{ route('booking.store') }}" method="POST">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service_id }}">
            <input type="hidden" name="customer_id" value="{{ $customer_id }}">
            <div class="row">
                <div class="col-md-12 text-center">
                    <br>
                    <h3><strong>Address</strong></h3>
                    <hr>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Building Name:</strong>
                        <input type="text" name="buildingName" id="buildingName" class="form-control" placeholder="Building Name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Area:</strong>
                        <input type="text" name="area" id="area" class="form-control" placeholder="Area">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Flat / Villa:</strong>
                        <input type="text" name="flatVilla" id="flatVilla" class="form-control" placeholder="Flat / Villa">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Street:</strong>
                        <input type="text" name="street" id="street" class="form-control" placeholder="Street">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>City:</strong>
                        <input type="text" name="city" id="city" class="form-control" placeholder="City">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Date:</strong>
                        <input type="date" name="date" id="date" min="{{ date('Y-m-d'); }}" value="{{ date('Y-m-d'); }}" class="form-control" placeholder="Date">
                    </div>
                </div>
                <div class="col-md-8">
                    <strong>Time Slots</strong>
                    <div class="list-group" id="time-slots-container">
                        @if(count($timeSlots))
                        @foreach($timeSlots as $timeSlot)
                        <label>
                            <div class="list-group-item d-flex justify-content-between align-items-center time-slot">
                                <input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="{{ $timeSlot->group_id }}" value="{{ $timeSlot->id }}" @if($timeSlot->active == "Unavailable") disabled @endif>
                                <p>{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }} </p>
                                @if($timeSlot->active == "Unavailable")
                                <span class="badge badge-unavailable">Unavailable</span>
                                @else
                                <span class="badge badge-available">Available</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                        @else
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were Holiday on your selected date.
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-3"><br>
                    <strong>Available Staff</strong>
                    <div class="list-group" id="staff-container">
                        <div class="alert alert-danger">
                            <strong>Please!</strong> First select time slot.
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Time:</strong>
                        <input type="text" name="selected_time" class="form-control" disabled placeholder="Select the Time Slots">
                        <input type="hidden" name="time_slot_id" class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Address:</strong>
                        <textarea name="address" class="form-control" cols="10" rows="5"></textarea>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" name="checkout" class="btn btn-success">Proceed Order</button>
                    <button type="submit" name="continue" class="btn btn-primary">Add More</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#manualLocationButton").click(function() {
            $(".popup").fadeIn();
        });

        $(".close").click(function() {
            $(".popup").fadeOut();
        });
    });
</script>
<script>
    function initMap() {
        const map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: 23.4241,
                lng: 53.8478
            },
            zoom: 7
        });

        const geocoder = new google.maps.Geocoder();

        let marker;

        document.getElementById('searchInput').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchLocation();
            }
        });

        document.getElementById('searchInput').addEventListener('blur', function() {
            searchLocation();
        });

        document.getElementById('manualLocationButton').addEventListener('click', function() {
            const manualLocation = map.getCenter();

            if (marker) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                map: map,
                position: manualLocation
            });

            geocoder.geocode({
                location: manualLocation
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        updateAddressFields(results[0]);
                    }
                } else {
                    alert('Geocoder failed due to: ' + status);
                }
            });
        });

        map.addListener('click', function(event) {
            if (marker) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                position: event.latLng,
                map: map
            });

            geocoder.geocode({
                location: event.latLng
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        updateAddressFields(results[0]);
                        $(".popup").fadeOut();
                    }
                } else {
                    alert('Geocoder failed due to: ' + status);
                }
            });
        });

        function searchLocation() {
            const searchInput = document.getElementById('searchInput').value;

            geocoder.geocode({
                address: searchInput
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        map.setCenter(results[0].geometry.location);
                        map.setZoom(15);

                        if (marker) {
                            marker.setMap(null);
                        }

                        marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location
                        });

                        updateAddressFields(results[0]);
                    }
                } else {
                    alert('Geocoder failed due to: ' + status);
                }
            });
        }

        function updateAddressFields(place) {
            const buildingNameField = document.getElementById('buildingName');
            const landmarkField = document.getElementById('landmark');
            const areaField = document.getElementById('area');
            const flatVillaField = document.getElementById('flatVilla');
            const streetField = document.getElementById('street');
            const cityField = document.getElementById('city');

            const addressComponents = place.address_components;
            for (let i = 0; i < addressComponents.length; i++) {
                const component = addressComponents[i];
                const types = component.types;

                if (types.includes('premise')) {
                    buildingNameField.value = component.long_name;
                } else if (types.includes('point_of_interest')) {
                    landmarkField.value = component.long_name;
                } else if (types.includes('neighborhood') || types.includes('sublocality')) {
                    areaField.value = component.long_name;
                } else if (types.includes('street_number')) {
                    flatVillaField.value = component.long_name;
                } else if (types.includes('route')) {
                    streetField.value = component.long_name;
                } else if (types.includes('locality')) {
                    cityField.value = component.long_name;
                }
            }
        }
    }
</script>

<script>
    var time_slot = $('input[name="time_slot"]');

    var selected_time = $('input[name="selected_time"]');
    var time = $('input[name="time_slot_id"]');

    $('#time-slots-container').on('change', 'input[name="time_slot"]', function() {

        if ($(this).is(':checked')) {
            $(".time-slot").removeClass("active");
            $(this).parents().addClass('active');
            selected_time.val($(this).parent().find('p').text());
            time.val($(this).val());
        }
    });

    $('#staff-container').on('change', 'input[name="service_staff_id"]', function() {
        if ($(this).is(':checked')) {
            $(".staff").removeClass("active");
            $(this).parents().addClass('active');
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
                    $('input[name="time_slot_id"]').val('');

                    var html = '<div class="alert alert-danger"><strong>Whoops!</strong> There were Holiday on your selected date.</div>';
                    timeSlotsContainer.append(html);
                } else {

                    var timeSlotsContainer = $('#time-slots-container');
                    timeSlotsContainer.empty();
                    $('input[name="selected_time"]').val('');
                    $('input[name="time_slot_id"]').val('');

                    timeSlots.forEach(function(timeSlot) {
                        var html = '<label><div class="list-group-item d-flex justify-content-between align-items-center time-slot">';
                        if (timeSlot.active == "Unavailable") {
                            html += '<input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="' + timeSlot.group_id + '" value="' + timeSlot.id + '" disabled><p>' + convertTo12Hour(timeSlot.time_start) + ' -- ' + convertTo12Hour(timeSlot.time_end) + '</p> <span class="badge badge-unavailable">Unavailable</span>'
                        } else {
                            html += '<input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="' + timeSlot.group_id + '" value="' + timeSlot.id + '"><p>' + convertTo12Hour(timeSlot.time_start) + ' -- ' + convertTo12Hour(timeSlot.time_end) + '</p> <span class="badge badge-available">Available</span> '
                        }
                        html += '</div></label>'
                        timeSlotsContainer.append(html);
                    });
                }
            },
            error: function() {
                alert('Error retrieving time slots.');
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
</script>
<script>
    // JavaScript Code
    $('#time-slots-container').on('change', 'input[name="time_slot"]', function() {
        var group = $(this).attr('data-group');

        if (group) {
            // Make AJAX call to retrieve time slots for selected date
            $.ajax({
                url: '/staff-group',
                method: 'GET',
                data: {
                    group: group
                },
                success: function(response) {
                    var staffs = response;

                    var staffContainer = $('#staff-container');
                    staffContainer.empty();

                    staffs.forEach(function(staff) {
                        var html = '<label><div class="list-group-item d-flex justify-content-between align-items-center staff"><input style="display: none;" type="radio" class="form-check-input" name="service_staff_id" value="' + staff[0].id + '">';
                        html += '<img src="/staff-images/' + staff[0].image + '" height="100px" alt="Staff Image" class="rounded-circle">'
                        html += '<span>' + staff[0].name + '</span>'
                        html += '</div></label>'
                        staffContainer.append(html);
                    });
                },
                error: function() {
                    alert('Error retrieving staffs.');
                }
            });
        } else {
            var timeSlotsContainer = $('#staff-container');
            timeSlotsContainer.empty();

            var html = '<div class="alert alert-danger"><strong>Whoops!</strong> There is no staff on your select time slot.</div>';
            timeSlotsContainer.append(html);
        }

    });
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJ0A4bxdhZ4FWomyO-tSEa4Qn0KY1jpT8&callback=initMap"></script>
@endsection