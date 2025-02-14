@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-left">
                    <h2>Quotes({{ $total_quote }})</h2>
                </div>
                <div class="float-end d-flex align-items-center">
                    @can('quote-edit')
                        <div class="input-group me-2">
                            <select name="bulk-status" class="form-control">
                                @foreach ($quote_statuses as $status)
                                    <option value="{{ $status }}" @if ($filter['status'] == $status) selected @endif>
                                        {{ $status }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button id="bulkStatusBtn" class="btn btn-primary" type="button"><i
                                        class="fa fa-save"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" id="bulkAssignStaffBtn" style="margin-top: -8px">
                            Assign Staff
                        </button>

                        <div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="staffModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staffModalLabel">Select Staff</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Select</th>
                                                        <th>Staff</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($staffs as $staff)
                                                        <tr>
                                                            <td>
                                                                <input class="form-check-input staff-checkbox"
                                                                    id="bulk-staff{{ $staff->id }}" type="checkbox"
                                                                    name="bulk-staff[]" value="{{ $staff->id }}"
                                                                    style="margin-top: -8px">
                                                            </td>
                                                            <td>{{ $staff->name }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <button id="assignStaffBtn" class="btn btn-primary" type="button"><i
                                                class=""></i>Assign Staff</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                <div class="float-end">
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <hr>
        <div class="row">
            @if (auth()->user()->hasRole('Admin'))
                <div class="col-md-12">
                    <h3>Filter</h3>
                    <hr>
                    <form action="{{ route('quotes.index') }}" method="GET" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>User:</strong>
                                    <select name="user_id" class="form-control">
                                        <option></option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                @if ($filter['user_id'] == $user->id) selected @endif>
                                                {{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Services:</strong>
                                    <select name="service_id" class="form-control">
                                        <option></option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}"
                                                @if ($filter['service_id'] == $service->id) selected @endif>
                                                {{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control">
                                        <option></option>
                                        @foreach ($quote_statuses as $status)
                                            <option value="{{ $status }}"
                                                @if ($filter['status'] == $status) selected @endif>
                                                {{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 offset-md-8">
                                <div class="d-flex flex-wrap justify-content-md-end">
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ url()->current() }}" class="btn btn-lg btn-secondary">Reset</a>
                                    </div>
                                    <div class="col-md-9 mb-3">
                                        <button type="submit" class="btn btn-lg btn-block btn-primary">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped">
                        <thead class="border-bottom">
                            <tr>
                                <td>
                                    <input type="checkbox" class="all-item-checkbox">
                                </td>
                                <th>Service</th>
                                <th>Send by</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($quotes))
                                @foreach ($quotes as $quote)
                                    @php
                                        $staffQuote = $quote->staffs->where('id', auth()->user()->id)->first();
                                    @endphp
                                    <tr class="border-bottom">
                                        <td>
                                            <input type="checkbox" class="item-checkbox" value="{{ $quote->id }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($quote->service->image)
                                                    <img src="{{ asset('service-images/' . $quote->service->image) }}"
                                                        alt="Service Image" class="rounded" width="auto" height="80">
                                                @endif
                                                <div class="ml-3">
                                                    <h6 class="mt-0 mb-1">{{ $quote->service_name }}</h6>
                                                    @if ($quote->sourcing_quantity)
                                                        <span class="text-muted">{{ $quote->sourcing_quantity }}
                                                            Quantity</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <strong>{{ $quote->user->name ?? 'Unknown' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <div>
                                                <strong>{{ auth()->user()->hasRole('Staff') ? $staffQuote->pivot->status : $quote->status }}</strong>
                                            </div>
                                            @if (auth()->user()->hasRole('Staff') && $quote->bid)
                                                @if ($quote->bid->staff_id == auth()->id())
                                                    <br>
                                                    <p><span class="badge badge-success">You have won the bid.</span></p>
                                                @else
                                                    <br>
                                                    <p><span class="badge badge-danger">Another staff member has won the
                                                            bid.</span></p>
                                                @endif
                                            @endif
                                            @if (auth()->user()->hasRole('Admin') && $quote->bid)
                                                <div class="mt-2">
                                                    <a href="{{ route('quote.bid', ['quote_id' => $quote->id, 'staff_id' => $quote->bid->staff_id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Selected Bid
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td>

                                            <form id="deleteForm{{ $quote->id }}"
                                                action="{{ route('quotes.destroy', $quote->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <a class="btn btn-outline-primary"
                                                    href="{{ route('quotes.show', $quote->id) }}">
                                                    View Detail
                                                </a>


                                                @if (auth()->user()->hasRole('Staff'))
                                                    @if ($staffQuote && $staffQuote->pivot->status == 'Pending')
                                                        <button type="button" class="btn btn-success accept-quote"
                                                            data-id="{{ $quote->id }}">Accept</button>
                                                        <button type="button" class="btn btn-danger reject-quote"
                                                            data-id="{{ $quote->id }}">Reject</button>
                                                    @endif
                                                    @if (is_null($quote->bid_id) || ($quote->bid && $quote->bid->staff_id == auth()->id()))
                                                        @if ($staffQuote->pivot->status == 'Accepted')
                                                            <a href="{{ route('quote.bid', ['quote_id' => $quote->id, 'staff_id' => auth()->id()]) }}"
                                                                class="btn btn-primary">
                                                                Bid
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if (auth()->user()->hasRole('Admin'))
                                                    <a href="{{ route('quote.bids', ['quote_id' => $quote->id]) }}"
                                                        class="btn btn-primary">
                                                        <i class="fas fa-eye"></i> View Bids
                                                    </a>
                                                @endif
                                                @can('quote-delete')
                                                    <button type="button" onclick="confirmDelete('{{ $quote->id }}')"
                                                        class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                                @endcan
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">There are no Quote.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                {!! $quotes->links() !!}
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.accept-quote').click(function() {
                let quoteId = $(this).data('id');
                if (confirm('Are you sure you want to accept this quote?')) {
                    updateQuoteStatus(quoteId, 'Accepted');
                }
            });

            $('.reject-quote').click(function() {
                let quoteId = $(this).data('id');
                if (confirm('Are you sure you want to reject this quote?')) {
                    updateQuoteStatus(quoteId, 'Rejected');
                }
            });

            function updateQuoteStatus(quoteId, status) {
                $.ajax({
                    url: '{{ route('quotes.updateStatus') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        id: quoteId,
                        status: status
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }
        });

        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }

        function getSelectedItems() {
            return $('.item-checkbox:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();
        }

        $(document).ready(function() {
            $('#bulkAssignStaffBtn').click(function() {
                const selectedItems = $('.item-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedItems.length > 0) {
                    // Manually trigger the modal if items are selected

                    $('#staffModal').modal('show');
                } else {
                    alert('Please select a Item to Assign Staff.');
                }
            });
        });

        $('#assignStaffBtn').click(function() {
            const selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            const selectedStaffs = $('input[name="bulk-staff[]"]:checked').map(function() {
                return $(this).val();
            }).get();
            if (selectedItems.length > 0 && selectedStaffs.length > 0) {
                if (confirm("Are you sure you want to assign the selected Staff to Quote?")) {
                    bulkAssignStaff(selectedItems, selectedStaffs);
                }
            } else {
                alert('Please select both Quote and Staff to assign.');
            }
        });

        function bulkAssignStaff(selectedItems, selectedStaffs) {
            $.ajax({
                url: '{{ route('quotes.bulkAssignStaff') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    selectedItems,
                    selectedStaffs
                }),
                success: function(data) {
                    alert(data.message);
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

        $(document).ready(function() {
            $('.all-item-checkbox').click(function() {
                var allCheckboxState = $(this).prop('checked');
                $('.item-checkbox').prop('checked', allCheckboxState);
            });

            $('#bulkStatusBtn').click(function() {
                const selectedItems = getSelectedItems();
                const statusValue = $('select[name="bulk-status"]').val();
                console.log(selectedItems, statusValue)
                if (statusValue && selectedItems.length > 0) {
                    if (confirm(`Are you sure you want to set ${statusValue} to the selected items?`)) {
                        editSelectedItems(selectedItems, statusValue);
                    }
                } else {
                    alert('Please select at least one item and choose a status to update.');
                }
            });

            function checkTableResponsive() {
                var viewportWidth = $(window).width();
                var $table = $('table');

                if (viewportWidth < 768) {
                    $table.addClass('table-responsive');
                } else {
                    $table.removeClass('table-responsive');
                }
            }

            checkTableResponsive();

            $(window).resize(function() {
                checkTableResponsive();
            });

            function editSelectedItems(selectedItems, status) {
                $.ajax({
                    url: '{{ route('quotes.bulkStatusEdit') }}',
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        selectedItems,
                        status
                    }),
                    success: function(data) {
                        alert(data.message);
                        window.location.reload();
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        alert('An error occurred while processing your request. Please try again.');
                    }
                });
            }
        });
    </script>
@endsection
