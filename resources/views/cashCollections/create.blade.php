@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="text-center">
                <h2>Submit Cash</h2>

                <h3>Total: {{$order->total_amount}}</h3>
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
    <form action="{{ route('cashCollection.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="staff_id" value="{{ $order->service_staff_id }}">
        <input type="hidden" name="staff_name" value="{{ $order->staff_name }}">
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Image:</strong>
                    <input type="file" name="image" id="image" class="form-control-file">
                    <br>
                    <img id="preview" height="130px">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    <textarea name="description" class="form-control" cols="10" rows="5"></textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Amount:</strong>
                    <input type="number" name="amount" class="form-control" value="{{$order->total_amount}}" placeholder="Amount">
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
@endsection