@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Order Chat</h2>
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
                        <div class="chat-message {{ $chat->user_id === auth()->user()->id ? 'bot-message' : 'user-message' }}">
                            {{ $chat->text }} <br>
                            @foreach($chat->user->getRoleNames() as $v)
                            <span class="chat-role">{{ $v }}</span>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="chat-message no-message">
                        No Chat <br>
                    </div>
                    @endif
                    <form action="{{ route('orders.chatUpdate',$id) }}" method="POST" class="mt-5">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <strong>Text:</strong>
                                    <textarea name="text" class="form-control" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <strong>Notify to:</strong>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="staff" name="staff" checked>
                                        <label class="form-check-label" for="staff">Staff</label>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="driver" name="driver" checked>
                                        <label class="form-check-label" for="driver">Driver</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 text-right no-print">
                                @can('order-edit')
                                <button type="submit" class="btn btn-primary">Update</button>
                                @endcan
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