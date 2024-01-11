@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Companies</h2>
        </div>
        <div class="col-md-6">
            @can('company-create')
            <a class="btn btn-success float-end" href="{{ route('companies.create') }}">Create</a>
            @endcan
            @can('company-delete')
            <a class="btn btn-danger float-end mr-2" href="#" onclick="confirmClear()">Clear All</a>
            <div id="clearConfirmation" class="alert alert-warning alert-dismissible fade show" role="alert" style="display:none;">
                Are you sure you want to clear all companies?
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
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Sr#</th>
                <th class="text-left">Title</th>
                <th class="text-left">Body</th>
            </tr>
        </thead>
        <tbody>
            @if(count($companies))
            @foreach ($companies as $company)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $company->title }}</td>
                <td class="text-left">{{ $company->body }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="3" class="text-center">There is no company.</td>
            </tr>
            @endif
        </tbody>
    </table>
    {!! $companies->links() !!}
</div>
<script>
    function confirmClear() {
        $('#clearConfirmation').fadeIn();
    }

    function clearAll() {
        window.location.href = "{{ route('companies.clear') }}";
    }

    function cancelClear() {
        $('#clearConfirmation').fadeOut();
    }
</script>


@endsection
