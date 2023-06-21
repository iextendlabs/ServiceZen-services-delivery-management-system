@extends('site.layout.app')
<style>
    .custom-control-label {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border-radius: 0.25rem;
        transition: background-color 0.3s ease;
    }

    .custom-control-label:hover {
        background-color: #f8f9fa;
    }

    .custom-control-input:checked~.custom-control-label {
        background-color: #007bff;
        color: #fff;
    }


    .custom-control-label img {
        margin-right: 0.5rem;
        width: 30px;
        height: 30px;
        object-fit: cover;
        border-radius: 50%;
        overflow: hidden;
    }
    .custom-control {
    padding: 0px !important;
    }

    .custom-radio .custom-control-label::before {
        display: none;
    }

    .scroll-div {
        height: 140px;
        overflow: hidden;
        overflow-y: scroll;
    }
</style>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>
                <div class="card-body">
                    <form method="POST" action="/customer-post-registration">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Partner</label>

                            <div class="col-md-6 scroll-div">
                                @if(count($partners))
                                @foreach($partners as $partner)
                                <div class="form-group">
                                    <div class="custom-control custom-radio">
                                        <input style="display: none;" type="radio" id="{{$partner->name}}" name="partner_id" class="custom-control-input" value="{{$partner->id}}">
                                        <label class="custom-control-label" for="{{$partner->name}}">
                                            <img src="/partner-images/{{$partner->image}}" alt="Image" height="130px">
                                            {{$partner->name}}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
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