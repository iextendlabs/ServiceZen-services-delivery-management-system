@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row align-items-center mb-4">
        <div class="col-md-12 margin-tb pb-2">
            <h2>Service Staff Social Links</h2>
        </div>
        <div class="col-md-12 mt-2 mt-md-0">
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
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.documents', $serviceStaff->id) }}">
                     Documents
                </a>
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
    <form action="{{ route('serviceStaff.social-links.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Instagram:</strong>
                        <input type="text" name="instagram" class="form-control" placeholder="Instagram" value="{{ old('instagram',$serviceStaff->staff->instagram ?? "") }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Snapchat:</strong>
                        <input type="text" name="snapchat" class="form-control" placeholder="Snapchat" value="{{ old('snapchat',$serviceStaff->staff->snapchat ?? "") }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Facebook:</strong>
                        <input type="text" name="facebook" class="form-control" placeholder="Facebook" value="{{ old('facebook', $serviceStaff->staff->facebook ?? "") }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Youtube:</strong>
                        <input type="text" name="youtube" class="form-control" placeholder="Youtube" value="{{ old('youtube',$serviceStaff->staff->youtube ?? "") }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Tiktok:</strong>
                        <input type="text" name="tiktok" class="form-control" placeholder="Tiktok" value="{{ old('tiktok',$serviceStaff->staff->tiktok ?? "") }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-block btn-primary">Update</button>
        </div>
    </div>
</form>
</div>
@endsection