@extends('site.layout.app')
@section('content')
<style>
    .card-img-top {
        height: 300px !important;
        width: 300px;
    }

    #categories a{
        text-decoration: none;
        color: inherit;
        transition: color 0.2s;
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
        </div>
        <hr>
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