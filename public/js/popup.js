var showMapError = false;
function mapReady() {
    $(document).ready(function () {
        var session = $('input[name="session"]').val();
        if (session == "false") {
            setTimeout(function () {
                $("#locationPopup").modal("show");
            }, 1000);
        }
        $("#change-address").click(function () {
            $("#locationPopup").modal("show");
        });
        initAutocomplete();
        if($('.location-search-wrapper').length){
            initMap();
        }
    });
}

$(document).ready(function () {
    $(".modal-footer .btn-primary").click(function () {
        $.ajax({
            url: "/saveLocation", // Replace with your server endpoint URL
            method: "POST", // Use the appropriate HTTP method
            data: $("#locationPopup input"),
            success: function (response) {
                location.reload(true);
            },
            error: function (xhr, status, error) {
                console.log("Save Changes error:", error);
            },
        });
    });
});

var map;
var marker;
var autocomplete;

$(document).ready(function () {
    var mapContainer = document.getElementById("mapContainer");

    mapContainer.style.display = "block";
    showMapError = false;
    showMap();
});

document.getElementById("setLocation").addEventListener("click", function () {
    var mapContainer = document.getElementById("mapContainer");

    mapContainer.style.display = "block";
    showMapError = true;
    showMap();
});

function showMap() {
    var searchValue = document.getElementById("popup_searchField").value;

    if (searchValue) {
        var geocoder = new google.maps.Geocoder();
        map = new google.maps.Map(document.getElementById("mapContainer"), {
            center: {
                lat: 0,
                lng: 0,
            },
            zoom: 15,
        });

        geocoder.geocode(
            {
                address: searchValue,
            },
            function (results, status) {
                if (status === "OK") {
                    map.setCenter(results[0].geometry.location);

                    if (marker) {
                        marker.setMap(null);
                    }

                    marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                    });

                    map.addListener("click", function (event) {
                        placeMarker(event.latLng);
                    });
                    fillAddressFields(results[0]);
                } else {
                    alert(
                        "Geocode was not successful for the following reason: " +
                            status
                    );
                }
            }
        );
    } else {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    map = new google.maps.Map(
                        document.getElementById("mapContainer"),
                        {
                            center: {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            },
                            zoom: 15,
                        }
                    );

                    if (marker) {
                        marker.setMap(null);
                    }

                    marker = new google.maps.Marker({
                        position: {
                            lat: latitude,
                            lng: longitude,
                        },
                        map: map,
                    });

                    map.addListener("click", function (event) {
                        placeMarker(event.latLng);
                    });

                    fillAddressFieldsFromMarker();
                },
                function () {
                    map = new google.maps.Map(
                        document.getElementById("mapContainer"),
                        {
                            center: {
                                lat: 23.4241,
                                lng: 53.8478,
                            },
                            zoom: 7,
                        }
                    );

                    if (marker) {
                        marker.setMap(null);
                    }

                    map.addListener("click", function (event) {
                        placeMarker(event.latLng);
                    });

                    fillAddressFieldsFromMarker();
                }
            );
        } else {
            handleLocationError(false);
        }
    }
}

function placeMarker(location) {
    if (marker) {
        marker.setMap(null);
    }

    marker = new google.maps.Marker({
        position: location,
        map: map,
        draggable: true,
    });

    marker.addListener("dragend", function () {
        fillAddressFieldsFromMarker();
    });

    fillAddressFieldsFromMarker();
}

function fillAddressFields(place) {
    const popup_buildingNameField = document.getElementById("popup_buildingName");
    const popup_landmarkField = document.getElementById("popup_landmark");
    const popup_areaField = document.getElementById("popup_area");
    const popup_flatVillaField = document.getElementById("popup_flatVilla");
    const popup_streetField = document.getElementById("popup_street");
    const popup_cityField = document.getElementById("popup_city");
    const popup_latitudeField = document.getElementById("popup_latitude");
    const popup_longitudeField = document.getElementById("popup_longitude");
    const popup_searchField = document.getElementById("popup_searchField");

    popup_buildingNameField.value = "";
    popup_landmarkField.value = "";
    popup_areaField.value = "";
    popup_flatVillaField.value = "";
    popup_streetField.value = "";
    popup_latitudeField.value = "";
    popup_longitudeField.value = "";
    popup_cityField.value = "";
    
    const addressComponents = place.address_components;

    for (let i = 0; i < addressComponents.length; i++) {
        const component = addressComponents[i];
        const types = component.types;

        if (types.includes("premise")) {
            popup_buildingNameField.value = component.long_name;
        } else if (types.includes("point_of_interest")) {
            popup_landmarkField.value = component.long_name;
        } else if (
            types.includes("neighborhood") ||
            types.includes("sublocality")
        ) {
            popup_areaField.value = component.long_name;
        } else if (types.includes("popup_street_number")) {
            popup_flatVillaField.value = component.long_name;
        } else if (types.includes("route")) {
            popup_streetField.value = component.long_name;
        } else if (types.includes("locality")) {
            popup_cityField.value = component.long_name;
        }
        popup_latitudeField.value = place.geometry.location.lat();
        popup_longitude.value = place.geometry.location.lng();
    }

    const address = place["formatted_address"];

    popup_searchField.value = address;
    if(!popup_areaField.value){
    popup_searchField.value = "";
    if (showMapError)
        alert('Address is not accurate. On map and select address.')
    }
    if (typeof fillFormAddressFields === 'function') {
        fillFormAddressFields(place);
    }
}

function reverseGeocode(latitude, longitude) {
    var geocoder = new google.maps.Geocoder();
    var latLng = new google.maps.LatLng(latitude, longitude);

    geocoder.geocode(
        {
            latLng: latLng,
        },
        function (results, status) {
            if (status === "OK") {
                if (results[0]) {
                    fillAddressFields(results[0]);
                } else {
            if (showMapError)
                    alert("No address found for the current location");
                }
            } else {
            if (showMapError)
                alert("Geocoder failed due to: " + status);
            }
        }
    );
}

function handleLocationError(browserHasGeolocation) {
    alert(
        browserHasGeolocation
            ? "Error: The Geolocation service failed."
            : "Error: Your browser doesn't support geolocation."
    );
}

function fillAddressFieldsFromMarker() {
    if (marker) {
        var markerPosition = marker.getPosition();
        reverseGeocode(markerPosition.lat(), markerPosition.lng());
    }
}
// Handle the place selection event
var autocomplete;
function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById("popup_searchField")
    );
    autocomplete.addListener("place_changed", function () {
        var place = autocomplete.getPlace();

        if (!place.geometry) {
            if (showMapError)
            alert("No details available for input: " + place.name);
            return;
        }

        if (marker) {
            marker.setMap(null);
        }

        map.setCenter(place.geometry.location);
        placeMarker(place.geometry.location);
    });
}
