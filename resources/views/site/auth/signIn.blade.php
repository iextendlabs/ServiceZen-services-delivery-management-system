@extends('site.layout.app')
@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Welcome Back</h2>
                <p class="text-sm text-gray-500 mt-1">Sign in to continue to your account</p>
            </div>

            @if(Session::has('error'))
                <div class="rounded-md bg-red-50 p-3 mb-4 text-sm text-red-700">{{ Session::get('error') }}</div>
            @endif
            @if(Session::has('success'))
                <div class="rounded-md bg-green-50 p-3 mb-4 text-sm text-green-700">{{ Session::get('success') }}</div>
            @endif

            <form method="POST" action="{{ route('customer.post-login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus class="w-full rounded-md border-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded-md border-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="remember" id="remember" class="rounded border-gray-200 text-indigo-600 shadow-sm" {{ old('remember') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <div>
                    <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Login</button>
                </div>

                <div class="text-center text-sm text-gray-600">
                    <span>Don't have an account?</span>
                    <a href="{{ route('customer.registration') }}" class="text-indigo-600 hover:underline">Register</a>
                </div>

                <div class="pt-2 border-t mt-2">
                    <div class="text-center text-sm text-gray-600 mb-2">Or register as</div>
                    <div class="flex justify-center gap-3">
                        <a href="{{ route('customer.registration') }}?type=Affiliate" class="text-sm text-indigo-600 hover:underline">Affiliate</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('customer.registration') }}?type=Freelancer" class="text-sm text-indigo-600 hover:underline">Freelancer</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
