<h3>Write a Review</h3>
<form action="{{ route('siteReviews.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(!Request::is('/'))

        @if(isset($service->id))
            <input type="hidden" name="service_id" value="{{ $service->id }}">
        @endif
        @if(isset($user->id))
            <input type="hidden" name="staff_id" value="{{ $user->id }}">
        @endif

        @if(isset($order->service_staff_id))
            <input type="hidden" name="staff_id" value="{{ $order->service_staff_id }}">
            <input type="hidden" name="order_id" value="{{ $order->id }}">
        @endif


    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Your Name:</strong>
                <input type="text" name="user_name" value="{{old('content')}}" class="form-control">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Review:</strong>
                <textarea class="form-control" style="height:150px" name="content" placeholder="Review">{{old('content')}}</textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong for="image">Upload Image</strong>
                <input type="file" name="image" id="image" class="form-control-file ">
                <br>
                <img id="preview" src="./review-images/" height="130px">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><label for="rating">Rating</label><br>
                @for($i = 1; $i <= 5; $i++) 
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                    <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
                </div>
                @endfor
            </div>
        </div>
        <div class="col-md-12 text-right">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>