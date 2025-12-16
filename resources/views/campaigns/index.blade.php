@extends('layouts.app')
@section('content')
@section('page_title')
    <h3 class="text-bold">Campaigns</h3>
@endsection
<div class="container">
    <div class="row">
        <div class="col-md-6 mb-3">
            <h2>Campaigns</h2>
        </div>
        <div class="col-md-6 mt-3">
            @can('campaign-create')
            <a class="btn text-primary fs-5 font-weight-bold float-end" href="{{ route('campaigns.create') }}">Create</a>
            @endcan
            @can('campaign-delete')
            <a class="btn text-danger float-end fs-5 font-weight-bold mr-2" href="#" onclick="confirmClear()">Clear All</a>
            <div id="clearConfirmation" class="alert alert-warning alert-dismissible fade show" role="alert" style="display:none;">
                Are you sure you want to clear all campaigns?
                <button type="button" class="btn text-warning ml-2" onclick="clearAll()">Yes</button>
                <button type="button" class="btn text-secondary" onclick="cancelClear()">No</button>
            </div>
            @endcan
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <span>{{ $message }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <span>{{ $message }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <table class="table table-hover table-bordered">
        <thead class="table-white bg-primary text-white">
            <tr>
                <th>Sr#</th>
                <th class="text-left">Title</th>
                <th class="text-left">Body</th>
            </tr>
        </thead>
        <tbody>
            @if(count($campaigns))
            @foreach ($campaigns as $campaign)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $campaign->title }}</td>
                <td class="text-left">{{ $campaign->body }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="3" class="text-center">There is no campaign.</td>
            </tr>
            @endif
        </tbody>
    </table>
    {!! $campaigns->links() !!}
</div>
<script>
    function confirmClear() {
        $('#clearConfirmation').fadeIn();
    }

    function clearAll() {
        window.location.href = "{{ route('campaigns.clear') }}";
    }

    function cancelClear() {
        $('#clearConfirmation').fadeOut();
    }
</script>


@endsection
