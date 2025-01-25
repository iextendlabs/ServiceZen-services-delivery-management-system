<!-- Modal -->
<link href="{{ asset('css/checkout.css') }}?v={{ config('app.version') }}" rel="stylesheet">
<div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-labelledby="addToCartModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCartModalLabel">Book Now</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route($action) }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="option_id" value="{{ isset($option_id) ? $option_id : '' }}">
                    <input type="hidden" id="addToCartModalServices" name="service_id" value="{{ isset($serviceIds[0]) ? $serviceIds[0] : '' }}">
                    <input type="hidden" name="order_ids" value="{{ isset($order_ids) ? $order_ids : '' }}">
                    <div id="slots-container" class="col-md-12">
                        @include('site.checkOut.timeSlots')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-block btn-primary">Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('.close').click(function () {
        $('#addToCartModal').modal('hide'); 
    });
</script>
<script src="{{ asset('js/checkout.js') }}?v={{ config('app.version') }}"></script>
