@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Chat with {{ $user->name }}</h2>
        </div>
    </div>
    <div class="container">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="row">
                <div class="chat-container">
                    @if(count($chats))
                    <div class="scroll-div">
                        @foreach($chats as $chat)
                        <div class="chat-message {{ $chat->admin_id ? 'bot-message' : 'user-message' }}">
                            {{ $chat->text }} <br>
                            @if($chat->admin_id)
                            @foreach($chat->admin->getRoleNames() as $v)
                            <span class="chat-role">{{ $v }}</span>
                            @endforeach
                            @else
                            @foreach($chat->user->getRoleNames() as $v)
                            <span class="chat-role">{{ $v }}</span>
                            @endforeach
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="chat-message no-message">
                        No Chat <br>
                    </div>
                    @endif
                    <form action="{{ route('chats.store') }}" method="POST" class="mt-5">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <strong>Text:</strong>
                                    <textarea name="text" class="form-control" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="ids[1]" value="{{ $user->id }}">
                            <input type="hidden" name="url" value="1">
                            
                            <div class="col-md-12 text-right no-print">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>
@endsection