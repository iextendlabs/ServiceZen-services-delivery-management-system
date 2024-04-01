@extends('layouts.app') @section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Edit Complaint</h2>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br /><br />
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('complaints.update', $complaint->id) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="url" value="{{ url()->previous() }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Title:</strong>
                        <input type="text" name="title" value="{{ $complaint->title }}" class="form-control"
                            placeholder="Title" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Description:</strong>
                        <textarea name="description" cols="30" rows="10" style="height:150px" class="form-control">{{ $complaint->description }}</textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option value="Open" @if ($complaint->status === 'Open') selected @endif>Open</option>
                            <option value="Close" @if ($complaint->status === 'Close') selected @endif>Close</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>User:</strong>
                        <select name="user_id" class="form-control">
                            <option></option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @if ($complaint->user_id == $user->id) selected @endif>
                                    {{ $user->name }} | {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
