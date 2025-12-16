@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="mb-0">Add New Long Holiday</h2>
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
<form action="{{ route('longHolidays.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card mt-3">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-12 col-md-6 mb-3">
                    <label class="font-weight-bold"><span class="text-danger">*</span> Date Start</label>
                    <input type="date" name="date_start" value="{{ old('date_start') }}" class="form-control" placeholder="Date Start" min="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group col-12 col-md-6 mb-3">
                    <label class="font-weight-bold"><span class="text-danger">*</span> Date End</label>
                    <input type="date" name="date_end" value="{{ old('date_end') }}" class="form-control" placeholder="Date End" min="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group col-12 mb-3">
                    <label class="font-weight-bold"><span class="text-danger">*</span> Staff</label>
                    <input type="text" id="staff-autocomplete" class="form-control" placeholder="Type staff name..." autocomplete="off" value="{{ old('staff_name') }}">
                    <input type="hidden" name="staff_id" id="staff-id" value="{{ old('staff_id') }}">
                    <ul id="staff-suggestions" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></ul>
                </div>

                <div class="col-12 text-center mt-2">
                    <button type="submit" class="btn btn-md btn-primary shadow-sm float-end font-weight-bold">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
<script>
    (function($){
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function setupUserAutocomplete(inputSelector, suggestionsSelector, role) {
            var $input = $(inputSelector);
            var $suggestions = $(suggestionsSelector);
            $input.on('input', debounce(function() {
                var query = $(this).val();
                if (query.length < 2) {
                    $suggestions.hide();
                    return;
                }
                $.ajax({
                    url: '{{ route('autocomplete.name') }}',
                    data: {
                        q: query,
                        role: role
                    },
                    success: function(data) {
                        $suggestions.empty();
                        if (Array.isArray(data) && data.length) {
                            data.forEach(function(user) {
                                var $li = $('<li class="list-group-item list-group-item-action"></li>').text(user.text);
                                $li.on('click', function() {
                                    $input.val(user.text);
                                    $('#staff-id').val(user.id);
                                    $suggestions.hide();
                                });
                                $suggestions.append($li);
                            });
                            $suggestions.show();
                        } else {
                            var $li = $('<li class="list-group-item text-muted"></li>').text('No data found');
                            $suggestions.append($li);
                            $suggestions.show();
                        }
                    },
                    error: function() {
                        $suggestions.empty();
                        var $li = $('<li class="list-group-item text-danger"></li>').text('Error fetching data');
                        $suggestions.append($li);
                        $suggestions.show();
                    }
                });
            }, 300));
            $input.on('blur', function() {
                setTimeout(function() {
                    $suggestions.hide();
                }, 200);
            });
        }

        setupUserAutocomplete('#staff-autocomplete', '#staff-suggestions', 'Staff');
    })(jQuery);
</script>
@endsection