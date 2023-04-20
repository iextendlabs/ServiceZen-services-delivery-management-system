@extends('site.layout.app')
<base href="/public">
<style>
    .box-shadow {
        background: none !important;
    }
</style>
@section('content')
<section class="jumbotron text-center">
  <div class="container">
      <h1 class="jumbotron-heading">Best In the Town Saloon Services</h1>
      <p class="lead text-muted">Get Your Desired Saloon Beauty service at Your Door, easy to schedule and just few clicks away.</p>
  </div>
</section>
<div class="album py-5 bg-light">
  <div class="container">
    <div class="row">
        <div class="col-8">
          <img class="card-img-top" src="./service-images/{{ $service->image }}" alt="Card image cap">
        </div>
        <div class="col-4 box-shadow">
          <h4 class="card-text"><b>{{ $service->name }}</b></h4>
          <div class="card-body">
            <p class="card-text">{!! $service->description !!}</p>
            <p class="card-text"><b>Duration:</b>{{ $service->duration }}</p>
            <p class="card-text"><b>Price:</b>${{ $service->price }}</p>
            <div class="btn-group">
                <a href="/booking/{{ $service->id }}"><button type="button" class="btn btn-sm btn-outline-secondary"> Book</button></a>
            </div>
          </div>
        </div>
    </div><hr>
    @if(count($service->package))
    <h2>Package</h2><br>
    <div class="row">
      @foreach($service->package as $package)
      <div class="col-md-4">
        <div class="card mb-4 box-shadow">
          <p class="card-text"><b>{{ $package->service->name }}</b></p>
          <img class="card-img-top" src="./service-images/{{ $package->service->image }}" alt="Card image cap">
          <div class="card-body">
            <p class="card-text">{{ $package->service->short_description }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted"><b>Duration:{{ $package->service->duration }}</b></small>
              <small class="text-muted"><b>Price:${{ $package->service->price }}</b></small>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
</div>
@endsection