@extends('site.layout.app')
@section('content')
<div class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center mb-6">
            <h1 id="faqs" class="text-3xl font-extrabold text-slate-900 mb-2">Frequently Asked Questions</h1>
            <p class="text-slate-500 mb-4">Helpful answers and quick tips to get the most out of our services.</p>
            <div class="mx-auto max-w-xl">
                <input id="faqSearch" type="search" placeholder="Search questions..." aria-label="Search FAQs" class="w-full px-4 py-2 rounded-full border border-slate-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-400 transition" />
            </div>
        </div>

        {{-- Tailwind grid --}}
        @if(count($generalFAQ))
            <h2 class="text-xl font-semibold text-slate-800 mb-4">General FAQs</h2>
            <div class="grid gap-5 grid-cols-1 sm:grid-cols-2" id="generalFaqs">
                @foreach ($generalFAQ as $FAQ)
                <div class="faq-card bg-white text-slate-900 rounded-2xl p-5 relative shadow-lg border border-slate-100 transform transition hover:-translate-y-1" data-title="{{ strtolower($FAQ->question) }}">
                    <span class="absolute right-4 top-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-pink-500 to-violet-600 text-white">General</span>
                    <div class="text-sm text-slate-400 mb-2">Asked by site</div>
                    <button class="faq-question w-full flex items-center justify-between gap-3 text-left text-base font-semibold focus:outline-none" aria-expanded="false" aria-controls="faq-{{ $FAQ->id }}">
                        <span>{{ $FAQ->question }}</span>
                        <svg class="chev w-5 h-5 text-slate-600 transition-transform" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div class="h-1 my-3 rounded-full bg-gradient-to-r from-pink-200 to-violet-200"></div>
                    <div id="faq-{{ $FAQ->id }}" class="faq-answer overflow-hidden max-h-0 transition-all text-white" role="region">
                        {!! $FAQ->answer !!}
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        {{-- Category FAQs --}}
        @php
            $hasCategoryFAQs = false;
            foreach($categoriesFAQ as $category) {
                if(count($category->FAQs) > 0) { $hasCategoryFAQs = true; break; }
            }
        @endphp
        @if($hasCategoryFAQs)
            <h2 class="text-xl font-semibold text-slate-800 mt-8 mb-4">Category FAQs</h2>
            @foreach($categoriesFAQ as $category)
                @if(count($category->FAQs) > 0)
                <h4 class="text-lg font-semibold text-slate-700 mt-4 mb-2">{{ $category->title }}</h4>
                <div class="grid gap-5 grid-cols-1 sm:grid-cols-2">
                    @foreach ($category->FAQs as $FAQ)
                    <div class="faq-card bg-white text-white-900 rounded-2xl p-5 relative shadow-lg border border-slate-100 transform transition hover:-translate-y-1" data-title="{{ strtolower($FAQ->question) }}">
                        <span class="absolute right-4 top-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-pink-500 to-violet-600 text-white">{{ strlen($category->title) > 12 ? substr($category->title,0,12).'...' : $category->title }}</span>
                        <div class="text-sm text-slate-400 mb-2">In {{ $category->title }}</div>
                        <button class="faq-question w-full flex items-center justify-between gap-3 text-left text-base font-semibold focus:outline-none" aria-expanded="false" aria-controls="faq-{{ $FAQ->id }}">
                            <span>{{ $FAQ->question }}</span>
                            <svg class="chev w-5 h-5 text-slate-600 transition-transform" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="h-1 my-3 rounded-full bg-gradient-to-r from-pink-200 to-violet-200"></div>
                        <div id="faq-{{ $FAQ->id }}" class="faq-answer overflow-hidden max-h-0 transition-all text-white" role="region">
                            {!! $FAQ->answer !!}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            @endforeach
        @endif

        {{-- Service FAQs --}}
        @php
            $hasServiceFAQs = false;
            foreach($servicesFAQ as $service) {
                if(count($service->FAQs) > 0) { $hasServiceFAQs = true; break; }
            }
        @endphp
        @if($hasServiceFAQs)
            <h2 class="text-xl font-semibold text-slate-800 mt-8 mb-4">Services FAQs</h2>
            @foreach($servicesFAQ as $service)
                @if(count($service->FAQs) > 0)
                <h4 class="text-lg font-semibold text-slate-700 mt-4 mb-2">{{ $service->name }}</h4>
                <div class="grid gap-5 grid-cols-1 sm:grid-cols-2">
                    @foreach ($service->FAQs as $FAQ)
                    <div class="faq-card bg-white text-slate-900 rounded-2xl p-5 relative shadow-lg border border-slate-100 transform transition hover:-translate-y-1" data-title="{{ strtolower($FAQ->question) }}">
                        <span class="absolute right-4 top-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-pink-500 to-violet-600 text-white">Service</span>
                        <div class="text-sm text-slate-400 mb-2">{{ $service->name }}</div>
                        <button class="faq-question w-full flex items-center justify-between gap-3 text-left text-base font-semibold focus:outline-none" aria-expanded="false" aria-controls="faq-{{ $FAQ->id }}">
                            <span>{{ $FAQ->question }}</span>
                            <svg class="chev w-5 h-5 text-slate-600 transition-transform" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="h-1 my-3 rounded-full bg-gradient-to-r from-pink-200 to-violet-200"></div>
                        <div id="faq-{{ $FAQ->id }}" class="faq-answer overflow-hidden max-h-0 transition-all text-white" role="region">
                            {!! $FAQ->answer !!}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            @endforeach
        @endif

    </div>
</div>

<script>
    (function(){
        // Toggle FAQ answers and apply Tailwind classes for open state
        document.querySelectorAll('.faq-question').forEach(function(btn){
            btn.addEventListener('click', function(){
                var card = btn.closest('.faq-card');
                var answer = card.querySelector('.faq-answer');
                var chev = btn.querySelector('.chev');
                var expanded = btn.getAttribute('aria-expanded') === 'true';

                // Close other answers in same grid (single-open behavior)
                var container = card.parentElement;
                container.querySelectorAll('.faq-answer').forEach(function(openEl){
                    var otherCard = openEl.closest('.faq-card');
                    if(otherCard !== card){
                        openEl.style.maxHeight = null;
                        otherCard.classList.remove('faq-open');
                        var otherBtn = otherCard.querySelector('.faq-question');
                        if(otherBtn) otherBtn.setAttribute('aria-expanded','false');
                        var otherChev = otherCard.querySelector('.chev'); if(otherChev) otherChev.classList.remove('rotate-180');
                    }
                });

                if(!expanded){
                    // open
                    answer.classList.add('open');
                    answer.style.maxHeight = answer.scrollHeight + 24 + 'px';
                    btn.setAttribute('aria-expanded','true');
                    if(chev) chev.classList.add('rotate-180');
                    // add Tailwind-like open classes: gradient bg + white text
                    card.classList.add('faq-open');
                    card.classList.remove('bg-white','text-slate-900');
                    card.classList.add('bg-gradient-to-r','from-pink-500','to-violet-600','text-white');
                } else {
                    // close
                    answer.style.maxHeight = null;
                    answer.classList.remove('open');
                    btn.setAttribute('aria-expanded','false');
                    if(chev) chev.classList.remove('rotate-180');
                    card.classList.remove('faq-open');
                    // restore original classes
                    card.classList.remove('bg-gradient-to-r','from-pink-500','to-violet-600','text-white');
                    card.classList.add('bg-white','text-slate-900');
                }
            });
        });

        // Simple client-side search
        var search = document.getElementById('faqSearch');
        if(search){
            search.addEventListener('input', function(e){
                var q = e.target.value.trim().toLowerCase();
                document.querySelectorAll('.faq-card').forEach(function(card){
                    var title = card.getAttribute('data-title') || '';
                    if(!q || title.indexOf(q) !== -1){
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    })();
</script>

@endsection