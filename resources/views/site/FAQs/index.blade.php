@extends('site.layout.app')
@section('content')
<div class="album py-5 bg-light">
    <div class="container">
        <h1 id="faqs" class="text-center mb-4">Frequently Asked Questions</h1>
        @if(count($generalFAQ))
        <h2 class="text-center mt-5">General FAQs</h2>
        <div class="accordion" id="serviceAccordion">
            @foreach ($generalFAQ as $FAQ)
            <div class="card">
                <div class="card-header" id="heading{{ $FAQ->id }}">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{ $FAQ->id }}" aria-expanded="true" aria-controls="collapse{{ $FAQ->id }}">
                            {{ $FAQ->question }}
                        </button>
                    </h5>
                </div>
                <div id="collapse{{ $FAQ->id }}" class="collapse" aria-labelledby="heading{{ $FAQ->id }}" data-parent="#serviceAccordion">
                    <div class="card-body">
                        {{ $FAQ->answer }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if(count($categoriesFAQ))
        <h2 class="text-center">Category FAQs</h2>
        @foreach($categoriesFAQ as $category)
        <h4 class="mt-4">{{ $category->title }}</h4>
        <div class="accordion" id="categoryAccordion">
            @foreach ($category->FAQs as $FAQ)
            <div class="card">
                <div class="card-header" id="heading{{ $FAQ->id }}">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{ $FAQ->id }}" aria-expanded="true" aria-controls="collapse{{ $FAQ->id }}">
                            {{ $FAQ->question }}
                        </button>
                    </h5>
                </div>
                <div id="collapse{{ $FAQ->id }}" class="collapse" aria-labelledby="heading{{ $FAQ->id }}" data-parent="#categoryAccordion">
                    <div class="card-body">
                        {{ $FAQ->answer }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
        @endif

        @if(($servicesFAQ))
        <h2 class="text-center mt-5">Services FAQs</h2>
        @foreach($servicesFAQ as $service)
        <h4 class="mt-4">{{ $service->name }}</h4>
        <div class="accordion" id="serviceAccordion">
            @foreach ($service->FAQs as $FAQ)
            <div class="card">
                <div class="card-header" id="heading{{ $FAQ->id }}">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{ $FAQ->id }}" aria-expanded="true" aria-controls="collapse{{ $FAQ->id }}">
                            {{ $FAQ->question }}
                        </button>
                    </h5>
                </div>
                <div id="collapse{{ $FAQ->id }}" class="collapse" aria-labelledby="heading{{ $FAQ->id }}" data-parent="#serviceAccordion">
                    <div class="card-body">
                        {{ $FAQ->answer }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
        @endif
    </div>
</div>



@endsection