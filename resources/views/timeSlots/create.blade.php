@extends('layouts.app')
@section('content')
<div class="container py-4">
    @section('page_title')
        <h3 class="">Add New Time Slot</h3>
    @endsection
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Add New Time Slot</h4>
        </div>

        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger rounded-3">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('timeSlots.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold">
                            <span class="text-danger">*</span> Name
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-lg" placeholder="Name" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" class="form-select form-select-lg">
                            <option value="General" {{ old('type') == 'General' ? 'selected' : ''}}>General</option>
                            <option value="Specific" {{ old('type') == 'Specific' ? 'selected' : ''}}>Specific</option>
                            <option value="Partner" {{ old('type') == 'Partner' ? 'selected' : ''}}>Partner</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold"><span class="text-danger">*</span> Time Start</label>
                        <input type="time" name="time_start" value="{{ old('time_start') }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold"><span class="text-danger">*</span> Time End</label>
                        <input type="time" name="time_end" value="{{ old('time_end') }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold"><span class="text-danger">*</span> No. of Seats</label>
                        <input type="number" name="seat" value="{{ old('seat',1) }}" class="form-control" min="1" required>
                    </div>

                    <div class="col-12 col-md-4" id="dateRow" style="display: none;">
                        <label class="form-label fw-semibold"><span class="text-danger">*</span> Date</label>
                        <input type="date" name="date" value="{{ old('date') }}" class="form-control">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold"><span class="text-danger">*</span> Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ old('status') == '1' ? 'selected' : ''}}>Enable</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : ''}}>Disable</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                        <a href="{{ route('timeSlots.index') }}" class="btn btn-outline-secondary rounded-3 btn-md">Cancel</a>
                        <button type="submit" class="btn btn-outline-primary rounded-3 btn-md">Save Time Slot</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.querySelector('select[name="type"]');
        const dateRow = document.getElementById('dateRow');

        function toggleDate() {
            if (!typeSelect) return;
            if (typeSelect.value === 'Specific') {
                dateRow.style.display = 'block';
            } else {
                dateRow.style.display = 'none';
            }
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', toggleDate);
            toggleDate();
        }

        // Basic client-side bootstrap-style validation (optional)
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endsection