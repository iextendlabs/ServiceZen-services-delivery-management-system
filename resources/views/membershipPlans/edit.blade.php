@extends('layouts.app') @section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Edit Affiliate Membership Plan</h2>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br /><br />
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('membershipPlans.update', $membership_plan->id) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="url" value="{{ url()->previous() }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Plan Name:</strong>
                        <input type="text" name="plan_name" class="form-control" value="{{ $membership_plan->plan_name }}"
                            placeholder="Plan Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Membership Fee:</strong>
                        <input type="number" name="membership_fee" class="form-control" value="{{ $membership_plan->membership_fee }}"
                            placeholder="membership_fee">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Expiry Date:</strong>
                        <input type="date" name="expiry_date" class="form-control" value="{{ $membership_plan->expiry_date }}"
                            placeholder="Expiry Date">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option></option>
                            <option value="1" @if ($membership_plan->status == '1') selected @endif>Enable</option>
                            <option value="0" @if ($membership_plan->status == '0') selected @endif>Disable</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
