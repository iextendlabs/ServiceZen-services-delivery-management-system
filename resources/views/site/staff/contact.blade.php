@extends('site.layout.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-semibold text-slate-800">Contact {{ $user->name }}</h1>
        @if(Session::has('success'))
            <div class="mt-4 p-3 bg-emerald-50 text-emerald-700 rounded">{{ Session::get('success') }}</div>
        @endif

        <p class="text-sm text-slate-600 mt-2">Send a message and we'll forward it to the staff member.</p>

        <form action="{{ route('contact.staff.send', ['id' => $user->id]) }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Your name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Your email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Message</label>
                <textarea name="message" rows="6" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>{{ old('message') }}</textarea>
                @error('message') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('staffProfile.show', ['staffProfile' => $user->id]) }}" class="text-sm text-slate-600 hover:underline">Back to profile</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-md shadow">Send Message</button>
            </div>
        </form>
    </div>
</div>
@endsection
