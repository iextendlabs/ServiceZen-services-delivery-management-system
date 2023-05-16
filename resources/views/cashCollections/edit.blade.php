@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Edit Cash Collection</h2>
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
    <form action="{{ route('cashCollection.update',$cash_collection->id) }}" method="POST">
        @csrf
        @method('PUT')
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Status:</strong>
                    <select name="status" class="form-control">
                        @if($cash_collection->status == "Not Approved")
                            <option value="Not Approved" selected>Not Approved</option>
                            <option value="Approved">Approved</option>
                        @elseif($cash_collection->status == "Approved")
                            <option value="Not Approved">Not Approved</option>
                            <option value="Approved" selected>Approved</option>
                        @else
                            <option></option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-12 text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection