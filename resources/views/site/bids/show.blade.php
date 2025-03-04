@extends('site.layout.app')
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
        background: transparent;
        border: none;
    }
</style>
@section('content')
    <div class="container d-flex justify-content-center">
        <div class="bid-container w-75">
            <h2 class="mb-4 text-center">Bid on Quote #{{ $quote->id }}</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card p-3 mb-4">
                <h5>Your Current Bid</h5>
                <p><strong>Amount:</strong>AED<span id="bid-amount">{{ $bid->bid_amount }}</span></p>
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


            </div>

            <!-- Full Screen Image Modal -->
            <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content position-relative">
                        <button id="prevImage" class="btn btn-dark fixed-left">❮</button>
                        <button id="nextImage" class="btn btn-dark fixed-right">❯</button>

                        <div class="modal-body text-center">
                            <img id="modalImage" src="" class="img-fluid rounded shadow-lg">
                        </div>
                        <button type="button" class="close close-button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Chat Section -->
            <div class="card chat-container">
                <div class="card-header bg-success text-white text-center">Bid Chat</div>
                <div class="card-body chat-box" id="chat-box">
                    <ul id="messages-list" class="list-unstyled mb-0"></ul>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <label for="file-upload" class="btn btn-secondary m-2">
                        <i class="fas fa-paperclip"></i>
                    </label>
                    <input type="file" id="file-upload" class="d-none">
                    <input type="text" id="chat-message" class="form-control chat-input" placeholder="Type a message...">
                    <button class="btn btn-success ml-2" id="send-message"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            let images = [];
            let currentIndex = 0;

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

                $("#prevImage").prop("disabled", currentIndex === 0);
                $("#nextImage").prop("disabled", currentIndex === images.length - 1);
            }
        });
        $(document).ready(function() {
            let bidId = {{ $bid->id ?? 'null' }};
            let userId = {{ auth()->id() }};
            let chatBox = $("#chat-box");
            let messagesList = $("#messages-list");
            let messageInput = $("#chat-message");
            let sendButton = $("#send-message");
            let fileInput = $("#file-upload");

            function fetchMessages() {
                $.get(`/bid-chat/${bidId}/messages`, function(messages) {
                    messagesList.empty();
                    messages.forEach(msg => {
                        let isSender = msg.sender_id == userId;
                        let messageClass = isSender ? "chat-sender" : "chat-receiver";
                        let messageContent = msg.file == 1 ?
                            `<strong>${msg.sender.name}:</strong><a href="/quote-images/bid-chat-files/${msg.message}" target="_blank">📎 View File</a>` :
                            `<strong>${msg.sender.name}:</strong> ${msg.message}`;

                        messagesList.append(
                            `<li class="chat-message ${messageClass}">${messageContent}</li>`
                        );
                    });
                    chatBox.scrollTop(chatBox[0].scrollHeight);
                });
            }

            sendButton.click(function() {
                let message = messageInput.val().trim();
                if (!message) return;

                $.post(`/bid-chat/${bidId}/send`, {
                    message,
                    _token: "{{ csrf_token() }}"
                }, function() {
                    messageInput.val("");
                    fetchMessages();
                });
            });

            fileInput.change(function() {
                let fileData = fileInput.prop("files")[0];
                if (!fileData) return;

                let formData = new FormData();
                formData.append("file", fileData);
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: `/bid-chat/${bidId}/send`,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        fetchMessages();
                    },
                    error: function(error) {
                        alert("File upload failed!");
                    }
                });
            });

            setInterval(fetchMessages, 3000);
            fetchMessages();
        });
    </script>
@endsection
