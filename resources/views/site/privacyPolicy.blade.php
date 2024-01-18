@extends($app ? 'site.layout.app_layout' : 'site.layout.app')

@section('content')
    <div class="container">
        <section>
            <p>{!! $privacyPolicy !!}</p>
        </section>
    </div>
@endsection
