@extends('layouts.app')
@section('content')
    @php
        $category_row = 0;
    @endphp
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <h3 class="mb-0">Edit Google AdSense</h3>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the following issues:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('settings.adsUpdate', $setting->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <label><span class="text-danger">*</span> Key:</label>
                        <input type="text" name="key" value="{{ $setting->key }}" class="form-control" disabled>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                @foreach (['home', 'category', 'service'] as $page)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#{{ $page }}"
                            role="tab">
                            {{ ucfirst($page) }} Page
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content border rounded-bottom p-3 bg-light">
                @foreach (['home', 'category', 'service'] as $page)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $page }}"
                        role="tabpanel">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <strong>{{ ucfirst($page) }} Page Ads</strong>
                            </div>
                            <div class="card-body">
                                @php
                                    if ($page === 'home') {
                                        $positions = ['head', 'top', 'center', 'bottom'];
                                    } elseif ($page === 'category') {
                                        $positions = ['head', 'top', 'bottom'];
                                    } else {
                                        $positions = ['head', 'top', 'right', 'bottom'];
                                    }
                                @endphp

                                @foreach ($positions as $position)
                                    <div class="mb-3">
                                        <label>{{ ucfirst($position) }} Code</label>
                                        <textarea class="form-control" name="codes[{{ $page }}][{{ $position }}]"
                                            rows="{{ $position == 'head' ? '3' : '6' }}">{{ $ads[$page][$position] ?? '' }}</textarea>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-4">Update</button>
            </div>
        </form>
    </div>
@endsection
