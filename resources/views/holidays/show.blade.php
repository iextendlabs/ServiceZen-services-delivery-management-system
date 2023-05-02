@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Holidays</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date:</strong>
                {{$holiday->date}}
                ({{ \Carbon\Carbon::parse($holiday->date)->format('l') }})
            </div>
        </div>
    </div>
@endsection