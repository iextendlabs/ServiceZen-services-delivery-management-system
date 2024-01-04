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
        <div class="col-md-6">
            <h2>Chat</h2>
        </div>
        <div class="col-md-6">
            @can('chat-create')
            <a
                class="btn btn-success float-end"
                href="{{ route('chats.create') }}"
            >
                New Chat</a
            >
            @endcan
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
                    <li class="list-group-item" @if($user->
                        chat->status == "1")
                        style="background-color:powderblue;" @endif>
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
@endsection
