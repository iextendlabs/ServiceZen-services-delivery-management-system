@extends('layouts.app')
@section('content')
<div class="container">
<div class="row">
    <div class="col-md-12 margin-tb">
        <h2>Add New Long Holiday</h2>
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
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Date Start:</strong>
                <input type="date" name="date_start" value="{{ old('date_start') }}" class="form-control" placeholder="Date Start" min="{{ date('Y-m-d') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Date End:</strong>
                <input type="date" name="date_end" value="{{ old('date_end') }}" class="form-control" placeholder="Date End" min="{{ date('Y-m-d') }}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Staff:</strong>
                <input type="text" id="staff-autocomplete" class="form-control" placeholder="Type staff name..." autocomplete="off" value="{{ old('staff_name') }}">
                <input type="hidden" name="staff_id" id="staff-id" value="{{ old('staff_id') }}">
                <div id="staff-suggestions" class="list-group" style="position: absolute; z-index: 1000;"></div>
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
</div> 
<script>
    $(document).ready(function() {
        $("#search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $("table tr").hide();

            $("table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var email = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (email.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });

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
                                var $li = $(
                                    '<li class="list-group-item list-group-item-action"></li>'
                                    ).text(user.text);
                                $li.on('click', function() {
                                    $input.val(user.text);
                                    $('#staff-id').val(user.id); // <-- Fix: set staff_id hidden field
                                    $suggestions.hide();
                                });
                                $suggestions.append($li);
                            });
                            $suggestions.show();
                        } else {
                            var $li = $('<li class="list-group-item text-muted"></li>').text(
                                'No data found');
                            $suggestions.append($li);
                            $suggestions.show();
                        }
                    },
                    error: function() {
                        $suggestions.empty();
                        var $li = $('<li class="list-group-item text-danger"></li>').text(
                            'Error fetching data');
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
    });
</script>
@endsection