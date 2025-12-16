@extends('site.layout.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-slate-800">Services by {{ $user->name }}</h1>
            <a href="{{ route('staffProfile.show', ['staffProfile' => $user->id]) }}" class="text-sm text-slate-600 hover:underline">Back to profile</a>
        </div>

        @if(count($services) > 0)
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    @include('site.services.card')
                @endforeach
            </div>
        @else
            <p class="mt-6 text-slate-600">No services found for this staff member.</p>
        @endif
    </div>
</div>
@endsection
