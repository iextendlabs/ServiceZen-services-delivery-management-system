@extends('site.layout.app')
@section('content')
<div class="container">
    <div id="categories">
        @foreach ($categories as $category)
        <h3>Main Category</h3>
        <div class="row">
            @include('site.categories.category_card', ['category' => $category])
        </div>
        @if ($category->childCategories->count() > 0)
        <h3>Sub Category</h3>
        <div class="row">
            @include('site.categories.child_categories', ['children' => $category->childCategories])
        </div>
        @endif
        @endforeach
    </div>
</div>
@endsection