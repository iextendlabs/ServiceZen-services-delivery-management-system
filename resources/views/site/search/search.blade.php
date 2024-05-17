@extends('site.layout.app')
@section('content')
    
    <div class="container">
        <div class="row justify-content-center mt-2">
                @if (Session::has('success'))
                    <span class="alert alert-success text-center w-75" role="alert" >
                        <strong>{{ Session::get('success') }}</strong>
                    </span>
                @endif
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col-md-4 service-box ">
                <div class="card mb-4 box-shadow">
                    <a href="/serviceDetail/{{ $service->id }}">
                        <p class="card-text service-box-title text-center"><b>{{ $service->name }}</b></p>
                        <img class="card-img-top" src="./service-images/{{ $service->image }}" alt="Card image cap">
                    </a>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted service-box-price">
                                @if (isset($service->discount))
                                    <s>
                                @endif
                                @currency($service->price)
                                @if (isset($service->discount))
                                    </s>
                                @endif
                                @if (isset($service->discount))
                                    <b class="discount"> @currency($service->discount)</b>
                                @endif
                            </small>

                            <small class="text-muted service-box-time"><i class="fa fa-clock"> </i>
                                {{ $service->duration }}</small>
                        </div>

                        {{-- <a href="/addToCart/{{ $service->id }}"><button type="button" class="btn btn-block btn-primary"> Book Now</button></a> --}}
                        <button onclick="openBookingPopup('{{ $service->id }}')" type="button"
                            class="btn btn-block btn-primary"> Book Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
