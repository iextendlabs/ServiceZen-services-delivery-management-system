@extends('site.layout.app')
<style>
    .fixed-left {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        font-size: 24px;
        padding: 10px 15px;
        z-index: 1050;
    }

    .fixed-right {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        font-size: 24px;
        padding: 10px 15px;
        z-index: 1050;
    }

    .close-button {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        padding: 5px 10px;
        z-index: 1050;
        background: transparent;
        border: none;
    }
</style>
@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light font-weight-bold">
                        Quote Details
                    </div>
                    <div class="card-body">
                        <!-- User Info -->
                        <h6><strong>Send by {{ $quote->user->name ?? 'N/A' }}</strong></h6>
                        <p><i class="fas fa-phone fa-sm"></i> {{ $quote->phone }}</p>
                        <p><i class="fab fa-whatsapp text-success"></i> {{ $quote->whatsapp }}</p>

                        @if ($quote->location)
                            <p>
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <a target="_blank" href="https://maps.google.com/?q={{ urlencode($quote->location) }}">
                                    {{ $quote->location }}
                                </a>
                            </p>
                        @endif

                        <hr>

                        <!-- Service Info -->
                        <h6><strong>Service Detail:</strong></h6>

                        <div class="media">
                            @if ($quote->service->image)
                                <img src="{{ asset('service-images/' . $quote->service->image) }}"
                                    alt="Service Image" class="mr-3 rounded" width="auto"
                                    height="80">
                            @endif
                            <div class="media-body">
                                <h6 class="mt-1 mb-1">{{ $quote->service_name }}</h6>
                                @if ($quote->sourcing_quantity)
                                    <span class="text-muted">{{ $quote->sourcing_quantity }}
                                        Quantity</span>
                                @endif
                            </div>
                        </div>

                        @if ($quote->serviceOption)
                            <ul class="pt-3">
                                @foreach ($quote->serviceOption as $option)
                                    <li>
                                        <p class="text-muted">
                                            {{ $option->option_name }} - @currency($option->option_price, true)
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <hr>

                        <!-- Status & Message -->
                        <p><strong>Status:</strong> <span class="badge badge-info">{{ $quote->status }}</span></p>
                        <p><strong>Message:</strong> {{ $quote->detail }}</p>

                        @if ($quote->images)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    @foreach ($quote->images as $key => $image)
                                        <img src="{{ asset('quote-images/' . $image->image) }}" alt="Inquiry Image"
                                            class="img-thumbnail gallery-image" data-toggle="modal"
                                            data-target="#imageModal" data-index="{{ $key }}"
                                            data-image="{{ asset('quote-images/' . $image->image) }}"
                                            style="width: 150px; height: 150px; object-fit: cover;">
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Full Screen Image Modal -->
                        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog"
                            aria-labelledby="imageModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content position-relative">
                                    <button id="prevImage" class="btn btn-dark fixed-left">❮</button>
                                    <button id="nextImage" class="btn btn-dark fixed-right">❯</button>

                                    <div class="modal-body text-center">
                                        <img id="modalImage" src="" class="img-fluid rounded shadow-lg">
                                    </div>
                                    <button type="button" class="close close-button" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let images = [];
            let currentIndex = 0;

            // Store images in an array
            $(".gallery-image").each(function() {
                images.push($(this).data("image"));
            });

            $(".gallery-image").click(function() {
                currentIndex = parseInt($(this).data("index"));
                updateModalImage();
            });

            $("#prevImage").click(function() {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateModalImage();
                }
            });

            $("#nextImage").click(function() {
                if (currentIndex < images.length - 1) {
                    currentIndex++;
                    updateModalImage();
                }
            });

            function updateModalImage() {
                $("#modalImage").attr("src", images[currentIndex]);

                // Disable buttons if at start or end
                $("#prevImage").prop("disabled", currentIndex === 0);
                $("#nextImage").prop("disabled", currentIndex === images.length - 1);
            }

            $('.show-map-btn').on('click', function() {
                var coordinates = $(this).data('coordinates');
                var [latitude, longitude] = coordinates.split(',').map(coord => parseFloat(coord.trim()));

                // Open a new window or redirect to a map URL with the coordinates
                var mapUrl = `https://maps.google.com/?q=${latitude},${longitude}`;
                window.open(mapUrl, '_blank');
            });
        });
    </script>
@endsection
