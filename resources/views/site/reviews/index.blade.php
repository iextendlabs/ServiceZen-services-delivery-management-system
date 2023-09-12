@extends('site.layout.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Reviews</h2>
        </div>
    </div>
    <div class="row">
        @if($reviews)
        @foreach($reviews as $review)
        <div class="col-md-8 offset-md-2">
            <div class="card m-2">
                <div class="card-header">
                    <p class="card-title float-left"><strong>{{$review->user_name}}</strong> {{$review->created_at}}</p>
                    <div class="star-rating float-right">
                        @for($i = 1; $i <= 5; $i++) @if($i <=$review->rating)
                            <span class="text-warning">&#9733;</span>
                            @else
                            <span class="text-muted">&#9734;</span>
                            @endif
                            @endfor
                    </div>
                </div>
                <div class="card-body">

                    <p class="card-text">{{$review->content}}</p>
                    @if(isset($review->images))
                    @foreach($review->images as $image)
                    <img src="/review-images/{{ $image->image }}" height="auto" width="30%" alt="">
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @endif
        <div class="col-md-8">
            {!! $reviews->links() !!}

        </div>

    </div>


</div>
@endsection