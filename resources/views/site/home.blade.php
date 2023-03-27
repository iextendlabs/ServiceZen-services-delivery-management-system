@extends('site.layout.app')
@section('content')
<style>
  .card-img-top{
    height: 250px;
  }
</style>
<section class="jumbotron text-center">
  <div class="container">
    <h1 class="jumbotron-heading">Best In the Town Saloon Services</h1>
      <p class="lead text-muted">Get Your Desired Saloon Beauty service at Your Door, easy to schedule and just few clicks away.</p>
  </div>
</section>
<div class="album py-5 bg-light">
  <div class="container">
    <div class="row">
      @foreach ($services as $service)
      <div class="col-md-4">
        <div class="card mb-4 box-shadow">
          <p class="card-text"><b>{{ $service->name }}</b></p>
          <img class="card-img-top" src="./service-images/{{ $service->image }}" alt="Card image cap">
          <div class="card-body">
            <p class="card-text">{{ substr($service->description,0,80)}}....</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary">Book</button>
              </div>
              <small class="text-muted"><b>${{ $service->price }}</b></small>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection