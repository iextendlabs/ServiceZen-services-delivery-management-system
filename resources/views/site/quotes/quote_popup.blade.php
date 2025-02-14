<!-- Quote Request Modal -->
<div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quoteModalLabel">Request a Quote</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('siteQuotes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="service_id" name="service_id" value="{{ $service->id }}">
                    <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id ?? '' }}">

                    <!-- Service Name -->
                    <div class="form-group">
                        <label for="service_name">Service Name</label>
                        <input required type="text" class="form-control" id="service_name" name="service_name"
                            value="{{ $service->name }}" readonly>
                    </div>

                    <!-- Service Option -->
                    @if (count($service->serviceOption) > 0)
                        <div class="form-group">
                            <label for="service_option_id">Select Service Option</label>
                            <select class="form-control" id="service_option_id" name="service_option_id">
                                <option value="">Select an Option</option>
                                @foreach ($service->serviceOption as $option)
                                    <option value="{{ $option->id }}">{{ $option->option_name }} (@currency($option->option_price, true))
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Detail -->
                    <div class="form-group">
                        <label for="detail">Detail</label>
                        <textarea required style="height: 150px" class="form-control" id="detail" name="detail" rows="3"
                            placeholder="Enter details"></textarea>
                    </div>

                    <!-- Mobile -->
                    <div class="form-group">
                        <label for="number">Phone Number</label>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input type="tel" id="number" name="phone" class="form-control">
                    </div>

                    <!-- WhatsApp -->
                    <div class="form-group">
                        <label for="whatsapp">WhatsApp Number</label>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input type="tel" id="whatsapp" name="whatsapp" class="form-control">
                    </div>

                    <!-- Sourcing Quantity -->
                    <div class="form-group">
                        <label for="sourcing_quantity">Sourcing Quantity</label>
                        <input type="number" class="form-control" id="sourcing_quantity" name="sourcing_quantity"
                            placeholder="Enter quantity" required>
                    </div>

                    <!-- Image Upload -->
                    <div class="form-group">
                        <label for="image" class="font-weight-bold">Upload Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image"
                                accept="image/*" onchange="previewImage(event)">
                            <label class="custom-file-label" for="image">Choose file...</label>
                        </div>
                        <div class="mt-3">
                            <img id="imagePreview" src="" class="img-fluid d-none border rounded"
                                style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Quote</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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

    function previewImage(event) {
        let reader = new FileReader();
        reader.onload = function() {
            let preview = document.getElementById('imagePreview');
            preview.src = reader.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
