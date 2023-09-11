@extends('site.layout.app')
@section('content')
<div class="album py-5 bg-light">
    <div class="container">
        <h3 class="text-center">Our Team</h3>
        <div class="row" id="categories">
            @foreach($staffs as $staff)
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="col-md-12 text-center">
                        <div class="d-flex justify-content-center align-items-center" style="min-height: 230px;">
                            <img src="./staff-images/{{ $staff->staff->image }}" class="card-img-top img-fluid rounded-circle" alt="{{ $staff->name }}">
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $staff->name }}</h5>
                        <a href="{{ route('staffProfile.show',$staff->id) }}" class="btn btn-block btn-primary">View</a>
                        @for($i = 1; $i <= 5; $i++) @if($i <=$staff->averageRating()) <span class="text-warning">&#9733;</span>
                            @else
                            <span class="text-muted">&#9734;</span>
                            @endif
                            @endfor
                            ({{ count($staff->reviews)}} Reviews)
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>
@endsection