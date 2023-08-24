@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Affiliate</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $affiliate->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {{ $affiliate->email }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Code:</strong>
                {{ $affiliate->affiliate->code }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Commission:</strong>
                {{ $affiliate->affiliate->commission }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Fix Salary:</strong>
                {{ $affiliate->affiliate->fix_salary }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Roles:</strong>
                @if(!empty($affiliate->getRoleNames()))
                    @foreach($affiliate->getRoleNames() as $v)
                        <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection