@extends('site.layout.app')
@section('content')
    <div class="container mt-3">
        <h2 class="text-center">Categories</h2>
        <div id="categories">

            @foreach ($categories as $category)
                @if ($category->status == 1)
                    @if (count($category->childCategories) == 0)
                        @if (!$category->parentCategory)
                            <div class="row">
                                @include('site.categories.category_card', ['category' => $category])
                            </div>
                            <hr>
                        @endif
                    @else
                        <div class="row">
                            @include('site.categories.category_card', ['category' => $category]) <br>
                        </div>
                        <div class="row">
                            @foreach ($category->childCategories as $subcategory)
                                @if ($subcategory->status == 1)
                                    @include('site.categories.category_card', ['category' => $subcategory])
                                @endif
                            @endforeach
                        </div>
                        <hr>
                    @endif
                @endif
            @endforeach

        </div>
    </div>
@endsection
