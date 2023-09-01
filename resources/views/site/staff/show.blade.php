@extends('site.layout.app')
@section('content')
<style>
    .card-img-top {
        height: 300px !important;
        width: 300px;
    }

</style>
<div class="album py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>{{ $user->name }}</h2>
                <hr>
                <!-- Add more contact details if needed -->
            </div>
            <div class="col-md-12 text-center">
                <img src="./staff-images/{{ $user->staff->image }}" alt="{{ $user->name }}" class="img-fluid rounded-circle mb-3 card-img-top">
            </div>
            @if($socialLinks)
            <div class="col-md-12 text-center">
                <h3>Social Links</h3>

                <!-- Facebook -->
                <a style="color: #3b5998;" href="{{$user->staff->facebook}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-facebook-f fa-lg"></i>
                </a>

                <!-- Twitter -->
                <a style="color: #fffc00;" href="{{$user->staff->snapchat}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-snapchat fa-lg"></i>
                </a>

                <!-- Youtube -->
                <a style="color: #ed302f;" href="{{$user->staff->youtube}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-youtube fa-lg"></i>
                </a>

                <!-- Instagram -->
                <a style="color: #ac2bac;" href="{{$user->staff->instagram}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>

                <!-- Tiktok -->
                <a style="color: #1e3050;" href="{{$user->staff->tiktok}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
            @endif
            @if($user->staff->youtube_video)
            <div class="col-md-12 text-center mt-3 mb-3">
                <iframe width="592" height="333" src="https://www.youtube.com/embed/{{ $user->staff->youtube_video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
            @endif
            @if($user->staff->images)
            <div class="col-md-12 mt-2 mb-3">
                <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach(array_chunk(explode(',', $user->staff->images), 3) as $key => $chunk)
                        <li data-target="#imageCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach(array_chunk(explode(',', $user->staff->images), 3) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $image)
                                <div class="col-md-4">
                                    <img src="/staff-images/{{ $image }}" class="d-block w-100 card-img-top">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

            </div>
            @endif
        </div>
        <h3 class="text-center">My Services</h3>
        <div class="row" id="categories">
            @foreach($categories as $category)
            @if($category->id == 10 || $category->id == 11)
            @continue
            @endif
            @if(count($category->childCategories) == 0)
            @if(!$category->parentCategory)
            <div class="col-md-4 service-box">
                <div class="card mb-4 box-shadow">
                    <a href="\?id={{$category->id}}">
                        <p class="card-text service-box-title text-center"><b>{{ $category->title }}</b></p>
                        <div class="col-md-12 text-center">
                            <div class="d-flex justify-content-center align-items-center" style="min-height: 230px;">
                                <img class="card-img-top img-fluid" src="./service-category-images/{{ $category->image }}" alt="Card image cap">
                            </div>
                        </div>
                    </a>

                </div>
            </div>
            @endif
            @else
            <div class="col-md-4 service-box">
                <div class="card mb-4 box-shadow">
                    <a href="\?id={{$category->id}}">
                        <p class="card-text service-box-title text-center"><b>{{ $category->title }}</b></p>
                        <div class="col-md-12 text-center">
                            <div class="d-flex justify-content-center align-items-center" style="min-height: 230px;">
                                <img class="card-img-top img-fluid" src="./service-category-images/{{ $category->image }}" alt="Card image cap">
                            </div>
                        </div>
                    </a>

                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endsection