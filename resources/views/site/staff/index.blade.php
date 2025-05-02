@extends('site.layout.app')
@section('content')
    <style>
        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px);
            /* Match Bootstrap form-control height */
            padding: .375rem .75rem;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
            /* Bootstrap focus shadow */
        }

        .select2-container .select2-search__field {
            width: 100% !important;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
    </style>
    <div class="album py-5 bg-light">
        <div class="container">
            <h3 class="text-center mb-4">Our Members</h3>

            <!-- Filter Section -->
            <div class="card shadow-sm mb-4 p-4">
                <h4>Filter</h4>
                <hr>
                <form action="{{ route('staffProfile.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Sub Title / Designation:</strong></label>
                                <select name="sub_title" class="form-control select2" id="sub_title">
                                    <option value="">Select Designation</option>
                                    @foreach ($sub_titles as $sub_title)
                                        <option value="{{ $sub_title->id }}"
                                            {{ $sub_title->id == $filter['sub_title'] ? 'selected' : '' }}>
                                            {{ $sub_title->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Location:</strong></label>
                                <select name="location" class="form-control select2" id="location">
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location }}"
                                            {{ $location == $filter['location'] ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Zones:</strong></label>
                                <select name="zone_id" class="form-control select2" id="zone_id">
                                    <option value="">Select Zone</option>
                                    @foreach ($staffZones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ $zone->id == $filter['zone_id'] ? 'selected' : '' }}>
                                            {{ $zone->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Minimum Order Value:</strong></label>
                                <input type="number" class="form-control" name="min_order_value"
                                    value="{{ $filter['min_order_value'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Services:</strong></label>
                                <select name="service_id" class="form-control select2" id="service_id">
                                    <option value="">Select Service</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ $service->id == $filter['service_id'] ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Categories:</strong></label>
                                <select name="category_id" class="form-control select2" id="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == $filter['category_id'] ? 'selected' : '' }}>
                                            {{ $category->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <div class="d-flex justify-content-around">
                                <a href="{{ url()->current() }}" class="btn btn-secondary">Reset Filters</a>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Staff Members -->
            <div class="row">
                @if (count($staffs) > 0)
                    @foreach ($staffs as $staff)
                        <div class="col-md-3 mb-4">
                            @include('site.staff.card')
                        </div>
                    @endforeach
                @else
                    <div class="col-md-12">
                        <p class="text-center">There is no Staff.</p>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {!! $staffs->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Search...',
                allowClear: true,
                width: '100%',
                language: {
                    searching: function() {
                        return "Type to search...";
                    }
                }
            }).on('select2:open', function() {
                setTimeout(() => {
                    let searchBox = document.querySelector('.select2-search__field');
                    if (searchBox) {
                        searchBox.placeholder = "Type to search...";
                        searchBox.focus();
                    }
                }, 100);
            });
        });
    </script>
@endsection
