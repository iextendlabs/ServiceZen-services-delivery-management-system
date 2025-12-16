<!-- Modal -->
<link href="{{ asset('css/checkout.css') }}?v={{ config('app.version') }}" rel="stylesheet">
<div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-labelledby="addToCartModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-lg shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h2 class="text-2xl font-bold text-purple-950">Book Now</h2>
                <button type="button" class="text-gray-400 hover:text-gray-600 text-2xl" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route($action) }}" method="POST">
                <div class="modal-body p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="option_id" value="{{ isset($option_id) ? $option_id : '' }}">
                    <input type="hidden" id="addToCartModalServices" name="service_id" value="{{ isset($serviceIds[0]) ? $serviceIds[0] : '' }}">
                    <input type="hidden" name="order_ids" value="{{ isset($order_ids) ? $order_ids : '' }}">
                    <div id="slots-container">
                        @include('site.checkOut.timeSlots')
                    </div>
                </div>
                <div class="modal-footer border-t border-gray-200 gap-3 pt-6 pb-6 px-8">
                    <button type="button" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-semibold" 
                        data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-6 py-2 bg-purple-950 text-white rounded-lg hover:bg-purple-900 transition font-semibold">
                        Book
                    </button>
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
