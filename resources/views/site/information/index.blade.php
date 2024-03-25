@extends('site.layout.app')
@section('content')
<div class="album py-5 bg-light">
    <div class="container">
        <h1 class="text-center mb-4">Information Page</h1>
        <ul>
            @foreach($information as $page)
                <li><a href="{{ route('siteInformationPage.show', $page->id) }}">{{ $page->name }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
@endsection