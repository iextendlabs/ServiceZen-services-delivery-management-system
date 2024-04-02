@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2> Show Complaint</h2>
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
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
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Title:</strong>
                    {{ $complaint->title }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    {{ $complaint->description }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Status:</strong>
                    {{ $complaint->status }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>User:</strong>
                    {{ $complaint->user->name }}
                </div>
            </div>
            @if ($complaint->order_id)
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Order:</strong>
                        <a href="{{ route('order.show', $complaint->order_id) }}">{{ $complaint->order_id }}</a>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="chat-container">
                @if (count($complaint->chats) > 0)
                    <div class="scroll-div">
                        @foreach ($complaint->chats as $chat)
                            <div class="chat-message {{ $chat->user_id == auth()->user()->id ? 'bot-message' : 'user-message' }}">
                                {{ $chat->text }} <br>
                                <span class="chat-role">{{ $chat->created_at }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="chat-message no-message">
                        No Chat <br>
                    </div>
                @endif
                <form action="{{ route('complaints.addComplaintChat') }}" method="POST" class="mt-5">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <strong>Text:</strong>
                                <textarea name="text" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="col-md-7 text-right no-print">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
