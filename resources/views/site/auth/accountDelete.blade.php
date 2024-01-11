@extends('site.layout.app')
<style>
    .error-alert {
    width: 100%;
    padding: 9px 57px;
    font-size: 80%;
    color: #dc3545;
    }
    .success-alert {
    width: 100%;
    padding: 9px 57px;
    font-size: 80%;
    color: #199700;
    }
</style>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-4">
                <div class="card-header">Delete Account</div>
                
                @if(Session::has('error'))
                    <span class="alert alert-danger" role="alert">
                        <strong>{{ Session::get('error') }}</strong>
                    </span>
                @endif
                @if(Session::has('success'))
                <span class="alert alert-success" role="alert">
                        <strong>{{ Session::get('success') }}</strong>
                    </span>
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
                <div class="card-body">
                    <form method="POST" action="{{ route('deleteAccountMail') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ auth()->user()->email }}" placeholder="Enter Email to Delete Account" required autocomplete="email" autofocus>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
