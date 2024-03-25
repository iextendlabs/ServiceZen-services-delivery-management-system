@extends('site.layout.app')

@section('content')
<div class="album py-5 bg-light">
    <div class="container">
        <section>
            <h1 class="text-center mb-4">{{ $information->name }}</h1>
            <div>{!! $information->description !!}</div>
        </section>
    </div>
</div>
@endsection