@extends('layouts.app')
@section('content')
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Staff Zone</h2>
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
    <form action="{{ route('staffZones.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Description:</strong>
                    <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Transport Charges:</strong>
                    <input type="number" name="transport_charges" value="{{ old('transport_charges') }}" class="form-control" placeholder="Transport Charges">
                </div>
            </div>
            <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection