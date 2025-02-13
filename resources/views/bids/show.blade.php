@extends('layouts.app')

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
                    <p><strong>Amount:</strong> $<span id="bid-amount">{{ $bid->bid_amount }}</span></p>
                    <p><strong>Comment:</strong> <span id="bid-comment">{{ $bid->comment ?? 'No comment' }}</span></p>
                    <button class="btn btn-warning mt-2" id="update-bid-btn">Update Bid</button>

                    <div id="update-bid-form" class="mt-3" style="display: none;">
                        <input type="number" id="new_bid_amount" class="form-control mb-2" placeholder="Enter new amount">
                        <button class="btn btn-primary" id="submit-update-bid">Save</button>
                    </div>
                </div>

                <!-- Chat Section -->
                <div class="card chat-container">
                    <div class="card-header bg-success text-white text-center">Bid Chat</div>
                    <div class="card-body chat-box" id="chat-box">
                        <ul id="messages-list" class="list-unstyled"></ul>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        <input type="text" id="chat-message" class="form-control chat-input"
                            placeholder="Type a message...">
                        <button class="btn btn-success ms-2" id="send-message"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            @else
                <div class="card p-3">
                    <form action="{{ route('quote.bid.store', ['quote_id' => $quote->id, 'staff_id' => $staff_id]) }}"
                        method="POST">
                        @csrf
                        <input type="number" name="bid_amount" class="form-control mb-2" placeholder="Enter bid amount"
                            required>
                        <textarea name="comment" class="form-control mb-2" placeholder="Leave a comment (optional)"></textarea>
                        <button type="submit" class="btn btn-success w-100">Submit Bid</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

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
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let bidId = {{ $bid->id ?? 'null' }};
            let userId = {{ auth()->id() }};
            let chatBox = $("#chat-box");
            let messagesList = $("#messages-list");
            let messageInput = $("#chat-message");
            let sendButton = $("#send-message");

            function fetchMessages() {
                $.get(`/bid-chat/${bidId}/messages`, function(messages) {
                    messagesList.empty();
                    messages.forEach(msg => {
                        let isSender = msg.sender_id === userId;
                        let messageClass = isSender ? "chat-sender text-end" :
                            "chat-receiver text-start";
                        messagesList.append(
                            `<li class="chat-message ${messageClass}">
                        <strong>${msg.sender.name}:</strong> ${msg.message}
                    </li>`
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

            setInterval(fetchMessages, 3000);
            fetchMessages();

            $("#update-bid-btn").click(() => $("#update-bid-form").toggle());

            $("#submit-update-bid").click(function() {
                let newBidAmount = $("#new_bid_amount").val().trim();
                if (!newBidAmount) return alert("Enter a valid bid amount.");

                $.post(`/bid/${bidId}/update`, {
                    bid_amount: newBidAmount,
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    if (response.success) {
                        $("#bid-amount").text(response.new_bid_amount);
                        $("#new_bid_amount").val("");
                        $("#update-bid-form").hide();
                        fetchMessages();
                    }
                });
            });
        });
    </script>
@endsection
