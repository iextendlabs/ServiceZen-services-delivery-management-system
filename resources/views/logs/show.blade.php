@extends('layouts.app')
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Log Viewer: {{ $currentFile }}.log</h1>

            <form action="{{ route('logs.clear', ['file' => $currentFile]) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to clear this log file?')">
                    Empty Log
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $logContent }}</pre>
            </div>
        </div>
    </div>
@endsection