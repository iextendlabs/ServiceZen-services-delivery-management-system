@extends('layouts.app') 
@section('content')
<style>
    .custom-list-item {
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .user-link {
        color: #3498db;
        font-weight: bold;
        text-decoration: none;
    }

    .timestamp {
        color: #666;
        font-size: 12px;
    }

    .chat-text {
        margin-top: 5px;
    }
    
    .no-decoration {
        text-decoration: none;
    }
    .custom-list-item:hover{
        text-decoration: none;
        background-color:#c8d1d2 !important;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-12 mb-2">
            <h2 class="me-5 justify-content-md-start d-inline">Chat</h2>
            <div class="float-end">
                <div class="dropdown ml-5">
                    <button class="btn btn-light mb-2  dropleft" type="button" id="actionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
                        @can('chat-create')
                            <li>
                                <a class="dropdown-item text-mute mb-2" href="{{ route('chats.create') }}"><i class="fas fa-comment-dots me-2"></i>New Chat</a>
                            </li>
                        @endcan
                        <li>
                            <button class="dropdown-item text-mute mb-2" id="showAllButton" type="button"><i class="fas fa-comments me-2"></i> Show All</button>
                        </li>
                        <li>
                            <button class="dropdown-item text-mute mb-2" id="unreadButton" type="button"><i class="fas fa-envelope-open-text me-2"></i> Unread Chats</button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="input-group justify-content-md-start">
                <input type="text" id="searchInput" class="form-control w-25" placeholder="Search...">
                <button class="btn btn-info" id="searchButton">Search</button>
            </div>            
    
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button
                    type="button"
                    class="btn-close float-end"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
            @endif
            <hr />
            {{-- scrollable user list --}}
            <div class="scrollable-user-list overflow-auto" style="max-height: 500px;">
            <ul class="list-group">
                @foreach($users as $user)
                    @if($user->chat)
                        <li
                            class="list-group-item custom-list-item"
                            data-user-name="{{ $user->name }}"
                            data-user-email="{{ $user->email }}"
                            data-status="{{ $user->chat->status }}"
                            data-show-url="{{ route('chat.show', $user) }}"
                            @if($user->chat->status == "1")
                                style="background-color:powderblue;"
                            @endif
                        >
                            <p class="user-link">{{ $user->name }}</p>
                            <span class="float-right timestamp">{{ $user->chat->created_at->diffForHumans() }}</span>
                            <p class="mb-0 chat-text">{{ $user->chat->text }}</p>
                        </li>
                    @endif
                @endforeach
            </ul>
            </div>
            
        </div>
        <div class="col-md-8 col-sm-12">
           <div id="chatDetail"> Select a chat to view messages </div>
        </div>    
    </div>
</div>
@section('scripts')
<script>
    $(document).ready(function () {
        // Function to filter chats based on search criteria
        $("#searchButton").on("click", function () {
            var searchText = $("#searchInput").val().toLowerCase();

            $(".list-group-item").each(function () {
                var userName = $(this).attr("data-user-name").toLowerCase();
                var userEmail = $(this).attr("data-user-email").toLowerCase();

                if (userName.includes(searchText) || userEmail.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Function to filter unread chats
        $("#unreadButton").on("click", function () {
            $(".list-group-item").each(function () {
                var status = $(this).attr("data-status");

                if (status === "1") {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $("#searchInput").val("");

        });

        $("#showAllButton").on("click", function () {
            $(".list-group-item").show();
            $("#searchInput").val("");

        });

        // Auto-open the first visible chat on page load
        var $first = $('.custom-list-item:visible').first();
        if ($first.length) {
            // small delay to ensure layout settled
            setTimeout(function(){ $first.trigger('click'); }, 50);
        }
    });


        // Track last loaded chat URL for reload after posting
        var lastChatUrl = null;

        // Spinner markup shown while loading chat
        var spinnerHtml = '<div class="d-flex justify-content-center align-items-center" style="min-height:200px"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>';

        // Handle chat click to load content via AJAX into the right pane
        $(document).on('click', '.custom-list-item', function (e) {
            e.preventDefault();
            var link = $(this).data('show-url') || $(this).find('a').attr('href');

            // Highlight selected chat
            $('.custom-list-item').css('background-color', '');
            $(this).css('background-color', '#c8d1d2');

            // Remember last loaded chat URL
            lastChatUrl = link;

            // Fetch chat via AJAX
            if (!link) { return; }
            // show spinner while loading
            $('#chatDetail').html(spinnerHtml);
            $.get(link, function (data) {
                $('#chatDetail').html(data);
                var $scroll = $('#chatDetail').find('.scroll-div');
                if ($scroll.length) { $scroll.scrollTop($scroll[0].scrollHeight); }
            }).fail(function () {
                $('#chatDetail').html('<div class="alert alert-danger">Could not load chat. Please try again.</div>');
            });
        });

        // Delegate AJAX form submit from dynamically loaded partial
        $(document).on('submit', '#chatForm', function (e) {
            e.preventDefault();
            var $form = $(this);
            var action = $form.attr('action');
            var formData = $form.serialize();

            // Show simple loading indicator on button
            var $btn = $form.find('button[type="submit"]');
            var oldText = $btn.html();
            $btn.prop('disabled', true).html('Sending...');

            $.post(action, formData)
                .done(function (resp) {
                    // Determine user show url: try to get from form hidden input ids[1]
                    var userId = $form.find('input[name="ids[1]"]').val();
                    if (userId) {
                        var reloadUrl = lastChatUrl || $('.custom-list-item[data-show-url]').filter(function () {
                            return $(this).data('show-url').indexOf('/' + userId) !== -1;
                        }).data('show-url');
                        if (reloadUrl) {
                            $.get(reloadUrl, function (html) {
                                $('#chatDetail').html(html);
                                var $scroll = $('#chatDetail').find('.scroll-div');
                                if ($scroll.length) { $scroll.scrollTop($scroll[0].scrollHeight); }
                            });
                        }
                    }
                })
                .fail(function () {
                    alert('Failed to send message.');
                })
                .always(function () {
                    $btn.prop('disabled', false).html(oldText);
                });
        });
</script>
@endsection
@endsection
