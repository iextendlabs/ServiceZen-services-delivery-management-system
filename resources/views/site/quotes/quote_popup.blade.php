<!-- Quote Request Modal -->
<div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-lg shadow-lg border-0">
            <div class="modal-body p-4">
                {{-- <h2 class="text-3xl font-bold text-purple-950 mb-6">Request a Quote</h2> --}}
                
                <div id="detailError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                    Your request contains contact details (phone number or email). Please remove them before submitting.
                </div>
                
                <form id="quoteForm" action="{{ route('siteQuotes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" id="service_id" name="service_id" value="{{ $service->id }}">
                    
                    <!-- If user is not authenticated, show name and email fields -->
                    @if (!auth()->check())
                        <div>
                            <label for="guest_name" class="block text-sm font-bold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> Name
                            </label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition" 
                                id="guest_name" name="guest_name" placeholder="Enter your name" required>
                        </div>

                        <div>
                            <label for="guest_email" class="block text-sm font-bold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> Email
                            </label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition" 
                                id="guest_email" name="guest_email" placeholder="Enter your email" required>
                        </div>
                    @else
                        <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id ?? '' }}">
                    @endif

                    <!-- Service Name -->
                    <div>
                        <label for="service_name" class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Service Name
                        </label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition" 
                            id="service_name" name="service_name" value="{{ $service->name }}" readonly required>
                    </div>

                    <!-- Service Option -->
                    @if (count($service->serviceOption) > 0)
                        <div>
                            <label for="service_option_id" class="block text-sm font-bold text-gray-700 mb-2">
                                Select Service Option
                            </label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition selectpicker" 
                                id="service_option_id" name="service_option_id[]" multiple data-live-search="true" data-actions-box="true">
                                @foreach ($service->serviceOption as $option)
                                    <option value="{{ $option->id }}">{{ $option->option_name }} (@currency($option->option_price, true))</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Detail -->
                    <div>
                        <label for="detail" class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Detail
                        </label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition h-40" 
                            id="detail" name="detail" rows="5" placeholder="Enter details" required></textarea>
                        <p class="text-xs text-red-600 mt-2">Please remove any contact details, it is against our policy.</p>
                    </div>

                    <!-- Mobile -->
                    <div>
                        <label for="number" class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Phone Number
                        </label>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input type="tel" id="number" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition"
                            value="{{ auth()->user()->customerProfile->number ?? '' }}" required>
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label for="whatsapp" class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="text-red-500">*</span> WhatsApp Number
                        </label>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input type="tel" id="whatsapp" name="whatsapp" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition"
                            value="{{ auth()->user()->customerProfile->whatsapp ?? '' }}" required>
                    </div>

                    <!-- Sourcing Quantity -->
                    <div>
                        <label for="sourcing_quantity" class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Sourcing Quantity
                        </label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition" 
                            id="sourcing_quantity" name="sourcing_quantity" placeholder="Enter quantity" required>
                    </div>

                    <!-- Affiliate Code -->
                    <div>
                        <label for="affiliate_code" class="block text-sm font-bold text-gray-700 mb-2">
                            Affiliate Code
                        </label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition" 
                            id="affiliate_code" name="affiliate_code" placeholder="Enter Affiliate Code">
                    </div>

                    <!-- Zone -->
                    <div>
                        <label for="zone" class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Zone
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition" 
                            id="zone" name="zone" required>
                            <option value="">Select Zone</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone }}" @if (isset($address) && isset($address['area']) && $address['area'] == $zone) selected @endif>{{ $zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Location
                        </label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-950 focus:border-transparent transition" 
                                id="location" name="location" placeholder="Enter your location" required>
                            <button type="button" class="px-4 py-2 bg-purple-950 text-white rounded-lg hover:bg-purple-900 transition font-semibold" 
                                id="getLocationBtn">
                                📍
                            </button>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    {{-- <div>
                        <label for="images" class="block text-sm font-bold text-gray-700 mb-2">
                            Upload Multiple Images
                        </label>
                        <div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-purple-950 hover:bg-purple-50 transition">
                            <i class="fa fa-cloud-upload-alt text-4xl text-gray-400 mb-2 block"></i>
                            <p class="text-gray-500 mb-3">Click to select images or drag & drop them here</p>
                            <input type="file" id="images" name="images[]" accept="image/*" multiple class="hidden">
                            <button type="button" class="px-4 py-2 bg-purple-950 text-white rounded-lg hover:bg-purple-900 transition font-semibold text-sm" 
                                id="selectImagesBtn">
                                Select Images
                            </button>
                        </div>
                        <div id="imagePreviewContainer" class="mt-4 flex flex-wrap gap-3"></div>
                    </div> --}}

                    <!-- Modal Footer -->
                    <div class="flex gap-3 justify-end pt-4 border-t border-gray-200">
                        <button type="button" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-semibold" 
                            data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="px-6 py-2 bg-purple-950 text-white rounded-lg hover:bg-purple-900 transition font-semibold">
                            Submit Quote
                        </button>
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
                $('#detailError').removeClass('hidden');
                $('#quoteModal').animate({
                    scrollTop: 0
                }, 500);
            } else {
                $('#detailError').addClass('hidden');

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
                            let errorHtml = '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6"><ul class="list-disc list-inside">';

                            // Loop through the errors object
                            $.each(errors, function(field, messages) {
                                $.each(messages, function(index, message) {
                                    errorHtml += '<li>' + message + '</li>';
                                });
                            });

                            errorHtml += '</ul></div>';

                            // Remove any existing error messages
                            $('#quoteModal .bg-red-50').remove();

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
            $(this).addClass('border-purple-950').addClass('bg-purple-50');
        });

        $("#drop-area").on("dragleave", function() {
            $(this).removeClass('border-purple-950').removeClass('bg-purple-50');
        });

        $("#drop-area").on("drop", function(event) {
            event.preventDefault();
            $(this).removeClass('border-purple-950').removeClass('bg-purple-50');
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

                    let imgWrapper = $("<div>")
                        .addClass("relative w-32 h-32 border border-gray-200 rounded-lg overflow-hidden inline-block");

                    let img = $("<img>")
                        .attr("src", e.target.result)
                        .addClass("w-full h-full object-cover");

                    let removeBtn = $("<button>")
                        .html("&times;")
                        .attr("type", "button")
                        .addClass("absolute top-1 right-1 rounded-full bg-red-500 text-white px-2 py-0 text-lg hover:bg-red-600 transition")
                        .click(function(evt) {
                            evt.preventDefault();
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
