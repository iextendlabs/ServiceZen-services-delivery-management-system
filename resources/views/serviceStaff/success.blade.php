@extends('layouts.app')
@section('content')
<div class="container">
    <div class="alert alert-success">
        <span>Service Staff Created Successfully.</span>
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-success" href="{{ route('serviceStaff.categories-commission', $user_id) }}" target="_blank">Categories Commission</a>
            <a class="btn btn-success" href="{{ route('serviceStaff.time-slots', $user_id) }}" target="_blank">Time Slots</a>
            <a class="btn btn-success" href="{{ route('serviceStaff.assign-drivers', $user_id) }}" target="_blank">Assign Drivers</a>
            <a class="btn btn-success" href="{{ route('serviceStaff.zones', $user_id) }}" target="_blank">Zones</a>
             @if($socialLinks)
             <a class="btn btn-success" href="{{ route('serviceStaff.social-links', $user_id) }}" target="_blank">Social Links</a>
            @endif
            <a class="btn btn-success" href="{{ route('serviceStaff.gallery', $user_id) }}" target="_blank">Gallery</a>
            <a class="btn btn-success" href="{{ route('serviceStaff.categories-and-services', $user_id) }}" target="_blank">Categories & Services</a>
            <a class="btn btn-success" href="{{ route('serviceStaff.documents', $user_id) }}" target="_blank">Documents</a>
        </div>
    </div>
</div>
@endsection