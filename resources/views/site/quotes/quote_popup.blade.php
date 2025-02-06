<!-- Quote Request Modal -->
<div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quoteModalLabel">Request a Quote</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('siteQuotes.store')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="service_id" name="service_id" value="{{ $service->id }}">
                    <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id ?? "" }}">

                    <div class="form-group">
                        <label for="service_name">Service Name</label>
                        <input required type="text" class="form-control" id="service_name" name="service_name" value="{{ $service->name }}" readonly>
                    </div>
                    @if (count($service->serviceOption) > 0)
                    <div class="form-group">
                        <label for="service_option_id">Select Service Option</label>
                        <select class="form-control" id="service_option_id" name="service_option_id">
                            <option value="">Select an Option</option>
                            @foreach ($service->serviceOption as $option)
                                <option value="{{ $option->id }}">{{ $option->option_name }}(@currency( $option->option_price, true))</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="detail">Detail</label>
                        <textarea required style="height: 150px" class="form-control" id="detail" name="detail" rows="3" cols="5" placeholder="Enter details"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Quote</button>
                </div>
            </form>
        </div>
    </div>
</div>
