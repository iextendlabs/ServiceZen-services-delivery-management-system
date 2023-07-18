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
    });
}

$(document).ready(function () {
    $(".modal-footer .btn-primary").click(function () {
        $.ajax({
            url: "/saveLocation", // Replace with your server endpoint URL
            method: "POST", // Use the appropriate HTTP method
            data: $("#locationPopup input"),
            success: function (response) {
                $("#locationPopup").modal("hide");
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

    showMap();
});

document.getElementById("setLocation").addEventListener("click", function () {
    var mapContainer = document.getElementById("mapContainer");

    mapContainer.style.display = "block";

    showMap();
});

function showMap() {
    var searchValue = document.getElementById("searchField").value;

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
    const buildingNameField = document.getElementById("buildingName");
    const landmarkField = document.getElementById("landmark");
    const areaField = document.getElementById("area");
    const flatVillaField = document.getElementById("flatVilla");
    const streetField = document.getElementById("street");
    const cityField = document.getElementById("city");
    const latitudeField = document.getElementById("latitude");
    const longitudeField = document.getElementById("longitude");
    const searchField = document.getElementById("searchField");

    buildingNameField.value = "";
    landmarkField.value = "";
    areaField.value = "";
    flatVillaField.value = "";
    streetField.value = "";
    latitudeField.value = "";
    longitudeField.value = "";
    cityField.value = "";
    
    const addressComponents = place.address_components;

    for (let i = 0; i < addressComponents.length; i++) {
        const component = addressComponents[i];
        const types = component.types;

        if (types.includes("premise")) {
            buildingNameField.value = component.long_name;
        } else if (types.includes("point_of_interest")) {
            landmarkField.value = component.long_name;
        } else if (
            types.includes("neighborhood") ||
            types.includes("sublocality")
        ) {
            areaField.value = component.long_name;
        } else if (types.includes("street_number")) {
            flatVillaField.value = component.long_name;
        } else if (types.includes("route")) {
            streetField.value = component.long_name;
        } else if (types.includes("locality")) {
            cityField.value = component.long_name;
        }
        latitudeField.value = place.geometry.location.lat();
        longitude.value = place.geometry.location.lng();
    }

    const address = place["formatted_address"];

    searchField.value = address;
    if(!areaField.value){
    searchField.value = "";
        alert('Address is not accurate. On map and select address.')
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
                    alert("No address found for the current location");
                }
            } else {
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
        document.getElementById("searchField")
    );
    autocomplete.addListener("place_changed", function () {
        var place = autocomplete.getPlace();

        if (!place.geometry) {
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
