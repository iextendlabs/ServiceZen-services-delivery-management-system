@extends('layouts.app') @section('content')
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
        <div class="col-md-8 col-sm-12 mb-2">
            <h2>Chat</h2>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="d-flex flex-wrap justify-content-md-end">
                @can('chat-create')
                <a class="btn btn-success mb-2" href="{{ route('chats.create') }}">New Chat</a>
                @endcan
                <button class="btn btn-primary mb-2 mx-2" id="showAllButton">Show All</button>
                <button class="btn btn-warning mb-2" id="unreadButton">Unread Chats</button>
                <div class="input-group mb-2">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                    <button class="btn btn-info" id="searchButton">Search</button>
                </div>
            </div>
        </div>
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
    <div class="row justify-content-center">
        <div class="col-md-7">
            <ul class="list-group">
                @foreach($users as $user) @if($user->chat)
                <a href="{{ route('chat.show', $user) }}" class="no-decoration custom-list-item">
                    <li class="list-group-item" data-user-name="{{ $user->name }}" data-user-email="{{ $user->email }}" data-status="{{ $user->chat->status }}" 
                        @if($user->chat->status == "1")
                            style="background-color:powderblue;"
                        @endif
                    >
                        <p class="user-link">{{ $user->name }}</p>

                        <span
                            class="float-right timestamp"
                            >{{ $user->chat->created_at->diffForHumans() }}</span
                        >
                        <p class="mb-0 chat-text">{{ $user->chat->text }}</p>
                    </li>
                </a>
                @endif @endforeach
            </ul>
        </div>
    </div>
</div>
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
    });
</script>
@endsection
