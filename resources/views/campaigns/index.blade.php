@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Campaigns</h2>
        </div>
        <div class="col-md-6">
            @can('campaign-create')
            <a class="btn btn-success float-end" href="{{ route('campaigns.create') }}">Create</a>
            @endcan
            @can('campaign-delete')
            <a class="btn btn-danger float-end mr-2" href="#" onclick="confirmClear()">Clear All</a>
            <div id="clearConfirmation" class="alert alert-warning alert-dismissible fade show" role="alert" style="display:none;">
                Are you sure you want to clear all campaigns?
                <button type="button" class="btn btn-warning" onclick="clearAll()">Yes</button>
                <button type="button" class="btn btn-secondary" onclick="cancelClear()">No</button>
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
    <table class="table table-striped table-bordered">
        <thead>
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
