@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Information Details</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 border-right">
                            <h6 class="text-muted">Name</h6>
                            <p class="lead mb-3">{{ $information->name }}</p>

                            <h6 class="text-muted">Position</h6>
                            <p><span class="badge text-info">{{ $information->position }}</span></p>
                        </div>

                        <div class="col-md-8">
                            <h6 class="text-muted">Description</h6>
                            <div class="bg-light p-3 rounded" style="min-height:150px;">
                                {!! $information->description !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-muted small d-flex justify-content-between">
                    <div>Published: {{ optional($information->created_at)->format('M d, Y') ?? '-' }}</div>
                    <div>Updated: {{ optional($information->updated_at)->diffForHumans() ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection