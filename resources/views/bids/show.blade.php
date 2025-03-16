@extends('layouts.app')

<style>
    .bid-container {
        max-width: 75%;
    }

    .chat-container {
        border-radius: 10px;
        overflow: hidden;
    }

    .chat-box {
        height: 400px;
        overflow-y: auto;
        background: #f8f9fa;
        padding: 15px;
        display: flex;
        flex-direction: column;
    }

    .chat-message {
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 10px;
        word-wrap: break-word;
        font-size: 14px;
        max-width: 75%;
        /* Ensures message width adapts */
        display: inline-block;
        clear: both;
    }

    .chat-sender {
        background-color: #dcf8c6;
        align-self: flex-end;
        float: right;
        margin-left: auto;
        text-align: right;
    }

    .chat-receiver {
        background-color: #ffffff;
        align-self: flex-start;
        border: 1px solid #ddd;
        float: left;
        margin-right: auto;
        text-align: left;
    }

    .chat-input {
        flex: 1;
        border-radius: 20px;
        padding: 10px;
        border: 1px solid #ddd;
    }

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
    }
</style>
@section('content')
    <div class="container d-flex justify-content-center">
        <div class="bid-container w-75">
            <h2 class="mb-4 text-center">Bid on Quote #{{ $quote->id }}</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($bid)
                <div class="card p-3 mb-4">
                    <h5>Your Current Bid</h5>
                    <p><strong>Amount:</strong> AED<span id="bid-amount">{{ $bid->bid_amount }}</span></p>
                    <p><strong>Comment:</strong> <span id="bid-comment">{{ $bid->comment ?? 'No comment' }}</span></p>
                    

                    @if ($bid->images)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                @foreach ($bid->images as $key => $image)
                                    <img src="{{ asset('quote-images/bid-images/' . $image->image) }}" alt="Inquiry Image"
                                        class="img-thumbnail gallery-image" data-toggle="modal" data-target="#imageModal"
                                        data-index="{{ $key }}"
                                        data-image="{{ asset('quote-images/bid-images/' . $image->image) }}"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Full Screen Image Modal -->
                    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content position-relative">
                                <button id="prevImage" class="btn btn-dark position-absolute fixed-left">❮</button>
                                <button id="nextImage" class="btn btn-dark position-absolute fixed-right">❯</button>

                                <div class="modal-body text-center">
                                    <img id="modalImage" src="" class="img-fluid rounded shadow-lg">
                                </div>
                                <button type="button" class="close-button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>

                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="card p-3">
                    <form action="{{ route('quote.bid.store', ['quote_id' => $quote->id, 'staff_id' => $staff_id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="number" name="bid_amount" class="form-control mb-2" placeholder="Enter bid amount"
                            required>
                        <textarea name="comment" class="form-control mb-2" placeholder="Leave a comment (optional)"></textarea>
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
                        <button type="submit" class="btn btn-success w-100">Submit Bid</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        });
        
    </script>
@endsection
