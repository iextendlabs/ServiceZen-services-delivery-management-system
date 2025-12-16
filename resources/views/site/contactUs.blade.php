@extends($app ? 'site.layout.app_layout' : 'site.layout.app')

@section('content')
    <div class="container">
        <section>
            <div class="py-5">
                <div class="text-4xl font-bold text-purple-900 text-center mb-4">
                    <h1>Contact Us</h1> 
                </div>
                <p>{!! $contactUs !!}</p>
            </div>
        </section>
    </div>
@endsection
