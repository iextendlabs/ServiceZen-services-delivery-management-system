@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row align-items-center mb-4">
        <div class="col-md-12 margin-tb">
            <h2>Service Staff Documents</h2>
        </div>
        <div class="col-md-12 mt-2 mt-md-0 pt-2">
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.general', $serviceStaff->id) }}">
                    General
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.categories-commission', $serviceStaff->id) }}">
                    Categories Commission
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.assign-drivers', $serviceStaff->id) }}">
                    Assign Drivers
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.time-slots', $serviceStaff->id) }}">
                    Time Slots
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.zones', $serviceStaff->id) }}">
                    Zones
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.gallery', $serviceStaff->id) }}">
                    Gallery
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.categories-and-services', $serviceStaff->id) }}">
                     Categories & Services
                </a>
                @if($socialLinks)
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.social-links', $serviceStaff->id) }}">
                     Social Links
                </a>
                @endif
            </div>
        </div>
    </div> 
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('serviceStaff.documents.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="document" role="tabpanel" aria-labelledby="document-tab">
            <div class="row">
                @foreach($documents as $field => $label)
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>{{ $label }}:</strong>
                            <input type="file" name="{{ $field }}" class="form-control document-upload" data-field="{{ $field }}">
                            @if($serviceStaff->document && $serviceStaff->document->$field)
                            <p>Current File: <a href="{{ asset('staff-document/' .$serviceStaff->document->$field) }}" target="_blank">{{ $serviceStaff->document->$field }}</a></p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-block btn-primary">Update</button>
        </div>
    </div>
</form>
</div>
@endsection