<!-- Quote Request Modal -->
<style>
    .form-text.text-muted {
        color: #dc3545 !important;
        /* Red color for emphasis */
        font-size: 0.9em;
        margin-top: 5px;
    }
</style>
<div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quoteModalLabel">Request a Quote</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="detailError" class="alert alert-danger d-none" role="alert">
                    Your request contains contact details (phone number or email). Please remove them before submitting.
                </div>
                <form id="quoteForm" action="{{ route('siteQuotes.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="service_id" name="service_id" value="{{ $service->id }}">
                    <!-- If user is not authenticated, show name and email fields -->
                    @if (!auth()->check())
                        <div class="form-group">
                            <span style="color: red;">*</span><label for="guest_name">Name</label>
                            <input type="text" class="form-control" id="guest_name" name="guest_name"
                                placeholder="Enter your name" required>
                        </div>

                        <div class="form-group">
                            <span style="color: red;">*</span><label for="guest_email">Email</label>
                            <input type="email" class="form-control" id="guest_email" name="guest_email"
                                placeholder="Enter your email" required>
                        </div>
                    @else
                        <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id ?? '' }}">
                    @endif

                    <!-- Service Name -->
                    <div class="form-group">
                        <span style="color: red;">*</span><label for="service_name">Service Name</label>
                        <input required type="text" class="form-control" id="service_name" name="service_name"
                            value="{{ $service->name }}" readonly>
                    </div>

                    <!-- Service Option -->
                    @if (count($service->serviceOption) > 0)
                        <div class="form-group">
                            <label for="service_option_id">Select Service Option</label>
                            <select class="form-control selectpicker" id="service_option_id" name="service_option_id[]"
                                multiple data-live-search="true" data-actions-box="true">
                                @foreach ($service->serviceOption as $option)
                                    <option value="{{ $option->id }}">{{ $option->option_name }} (@currency($option->option_price, true))
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Detail -->
                    <div class="form-group">
                        <span style="color: red;">*</span><label for="detail">Detail</label>
                        <textarea style="height: 150px" class="form-control" id="detail" name="detail" rows="3"
                            placeholder="Enter details" required></textarea>
                        <small class="form-text text-muted">Please remove any contact details, it is against our
                            policy.</small>
                    </div>

                    <!-- Mobile -->
                    <div class="form-group">
                        <span style="color: red;">*</span><label for="number">Phone Number</label>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input type="tel" id="number" name="phone" class="form-control"
                            value="{{ auth()->user()->customerProfile->number ?? '' }}" required>
                    </div>

                    <!-- WhatsApp -->
                    <div class="form-group">
                        <span style="color: red;">*</span><label for="whatsapp">WhatsApp Number</label>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input type="tel" id="whatsapp" name="whatsapp" class="form-control"
                            value="{{ auth()->user()->customerProfile->whatsapp ?? '' }}" required>
                    </div>

                    <!-- Sourcing Quantity -->
                    <div class="form-group">
                        <span style="color: red;">*</span><label for="sourcing_quantity">Sourcing Quantity</label>
                        <input type="number" class="form-control" id="sourcing_quantity" name="sourcing_quantity"
                            placeholder="Enter quantity" required>
                    </div>

                    <div class="form-group">
                        <label for="affiliate_code">Affiliate Code</label>
                        <input type="text" class="form-control" id="affiliate_code" name="affiliate_code"
                            placeholder="Enter Affiliate Code">
                    </div>

                    <div class="form-group">
                        <span style="color: red;">*</span><label for="location">Zone</label>
                        <select class="form-control" id="zone" name="zone" required>
                            <option value="">Select Zone</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone }}" @if (isset($address) && isset($address['area']) && $address['area'] == $zone) selected @endif>{{ $zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <span style="color: red;">*</span><label for="location">Location</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="location" name="location"
                                placeholder="Enter your location" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="getLocationBtn">
                                    📍 Use Current Location
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="form-group">
                        <label for="images" class="font-weight-bold">Upload Multiple Images</label>
                        <div id="drop-area" class="border p-3 rounded text-center"
                            style="border: 2px dashed #ccc; cursor: pointer;">
                            <i class="fa fa-cloud-upload-alt fa-2x text-muted"></i>
                            <p class="text-muted">Click to select images or drag & drop them here</p>
                            <input type="file" id="images" name="images[]" accept="image/*" multiple
                                class="d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="selectImagesBtn">Select
                                Images</button>
                        </div>
                        <div id="imagePreviewContainer" class="mt-3 d-flex flex-wrap"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Quote</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#quoteForm').on('submit', function(e) {
            let detailValue = $('#detail').val().trim();
            const phoneRegex = /\b(\+?\d{1,3}[-.\s]?)?\(?\d{2,4}\)?[-.\s]?\d{3,4}[-.\s]?\d{4,9}\b/g;
            const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/g;

            if (phoneRegex.test(detailValue) || emailRegex.test(detailValue)) {
                e.preventDefault();
                $('#detailError').removeClass('d-none');
                $('#quoteModal').animate({
                    scrollTop: 0
                }, 500);
            } else {
                $('#detailError').addClass('d-none');

                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#quoteModal').modal('hide');
                            alert(response.message);
                        } else {
                            let errors = response.errors;
                            let errorHtml = '<div class="alert alert-danger"><ul>';

                            // Loop through the errors object
                            $.each(errors, function(field, messages) {
                                $.each(messages, function(index, message) {
                                    errorHtml += '<li>' + message + '</li>';
                                });
                            });

                            errorHtml += '</ul></div>';

                            // Remove any existing error messages
                            $('#quoteModal .alert-danger').remove();

                            // Prepend the new error messages to the modal body
                            $('#quoteModal .modal-body').prepend(errorHtml);
                            $('#quoteModal').animate({
                                scrollTop: 0
                            }, 500);
                        }
                    },
                    error: function(xhr) {
                        // Handle server errors
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });
    });
    $("#getLocationBtn").click(function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    let latitude = position.coords.latitude;
                    let longitude = position.coords.longitude;

                    // Fetch address from OpenStreetMap API
                    $.getJSON(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`,
                        function(data) {
                            if (data.display_name) {
                                $("#location").val(data.display_name);
                            } else {
                                $("#location").val(`${latitude}, ${longitude}`);
                            }
                        }
                    ).fail(function() {
                        $("#location").val(`${latitude}, ${longitude}`);
                    });
                },
                function(error) {
                    if (error.code === error.PERMISSION_DENIED) {
                        alert("You denied location access. Please allow it in browser settings.");
                    } else {
                        alert("Error fetching location: " + error.message);
                    }
                }
            );
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    });

    $(document).ready(function() {
        function initializeIntlTelInput(inputField, countryCodeField) {
            if (!$(inputField).length || !$(countryCodeField).length) return; // Ensure elements exist

            // Avoid reinitialization
            if ($(inputField).data("iti-initialized")) return;

            const iti = window.intlTelInput($(inputField)[0], {
                showSelectedDialCode: true,
                initialCountry: "ae",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/utils.js?1707906286003",
            });

            $(inputField).data("iti-initialized", true);

            // Set initial country code
            $(inputField).on("countrychange", function() {
                $(countryCodeField).val("+" + iti.getSelectedCountryData().dialCode);
            });

            // Set initial value on first load
            setTimeout(function() {
                $(countryCodeField).val("+" + iti.getSelectedCountryData().dialCode);
            }, 500);
        }

        // Initialize phone fields
        initializeIntlTelInput("#number", "#number_country_code");
        initializeIntlTelInput("#whatsapp", "#whatsapp_country_code");

        // Ensure reinitialization on modal open
        $("#quoteModal").on("shown.bs.modal", function() {
            initializeIntlTelInput("#number", "#number_country_code");
            initializeIntlTelInput("#whatsapp", "#whatsapp_country_code");
        });
    });

    $('.custom-file-input').on('change', function(event) {
        let fileName = $(this).val().split("\\").pop();
        $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
    });

    $(document).ready(function() {

        $('.selectpicker').selectpicker();

        $("#selectImagesBtn, #drop-area").on("click", function(event) {
            if (event.target !== this) return; // Prevent triggering itself
            $("#images").get(0).click();
        });

        $("#images").off("change").on("change", function(event) {
            previewImages(event.target.files);
        });

        // Drag & Drop Feature
        $("#drop-area").on("dragover", function(event) {
            event.preventDefault();
            $(this).css("border-color", "#007bff");
        });

        $("#drop-area").on("dragleave", function() {
            $(this).css("border-color", "#ccc");
        });

        $("#drop-area").on("drop", function(event) {
            event.preventDefault();
            $(this).css("border-color", "#ccc");
            let files = event.originalEvent.dataTransfer.files;
            previewImages(files);
        });

        function previewImages(files) {
            let previewContainer = $("#imagePreviewContainer");

            $.each(files, function(index, file) {
                // Check if the image already exists in the preview
                let existingImages = previewContainer.find("img").map(function() {
                    return $(this).attr("src");
                }).get();

                let reader = new FileReader();
                reader.onload = function(e) {
                    if (existingImages.includes(e.target.result)) return; // Skip duplicate images

                    let imgWrapper = $("<div>").addClass("position-relative m-2").css({
                        width: "120px",
                        height: "120px",
                        border: "1px solid #ddd",
                        borderRadius: "8px",
                        overflow: "hidden",
                        display: "inline-block",
                        position: "relative"
                    });

                    let img = $("<img>").attr("src", e.target.result).addClass("img-thumbnail")
                        .css({
                            width: "100%",
                            height: "100%",
                            objectFit: "cover"
                        });

                    let removeBtn = $("<button>")
                        .html("&times;")
                        .addClass("btn btn-sm btn-danger position-absolute")
                        .css({
                            top: "5px",
                            right: "5px",
                            borderRadius: "50%",
                            padding: "2px 6px"
                        })
                        .click(function() {
                            imgWrapper.remove();
                        });

                    imgWrapper.append(img).append(removeBtn);
                    previewContainer.append(imgWrapper);
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
