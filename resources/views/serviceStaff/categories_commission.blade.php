@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Service Staff Commission and Categories base Commission</h2>
            </div>
        </div>
        <div class="col-md-12 mt-2 mt-md-0 py-4">
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <a class="btn btn-outline-primary btn-md px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.general', $serviceStaff->id) }}">
                    General
                </a>
                @if($socialLinks)
                <a class="btn btn-outline-primary btn-md px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.social-links', $serviceStaff->id) }}">
                    Social Links
                </a>
                @endif
                <a class="btn btn-outline-primary btn-md px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.time-slots', $serviceStaff->id) }}">
                    Time Slots
                </a>
                <a class="btn btn-outline-primary btn-md px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.assign-drivers', $serviceStaff->id) }}">
                    Assign Drivers
                </a>
                <a class="btn btn-outline-primary btn-md px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.zones', $serviceStaff->id) }}">
                    Zones
                </a>
                <a class="btn btn-outline-primary btn-md px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.gallery', $serviceStaff->id) }}">
                    Gallery
                </a>
                <a class="btn btn-outline-primary btn-md px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.categories-and-services', $serviceStaff->id) }}">
                     Categories & Services
                </a>
                <a class="btn btn-outline-primary btn-md px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.documents', $serviceStaff->id) }}">
                     Documents
                </a>
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
    <form action="{{ route('serviceStaff.categories-commission.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="categories-commission" role="tabpanel" aria-labelledby="categories-commission-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Commission:</strong>
                        <input type="number" name="commission" value="{{ old('commission',$serviceStaff->staff->commission ?? "") }}" class="form-control" placeholder="Commission In %">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Categories base commission:</strong>
                        <table id="categoryTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Category Commission</th>
                                    <th>Services</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($serviceStaff->affiliateCategories)
                                @foreach ($serviceStaff->affiliateCategories as $index => $staffCategory)
                                <tr>
                                    <td>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select name='categories[{{ $index }}][category_id]' class="form-control category-select" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        @if($staffCategory->category_id == $category->id) selected @endif>
                                                        {{ $category->title }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-md-12">
                                            <div class="form-group d-flex">
                                                <input type="number" name="categories[{{ $index }}][category_commission]"
                                                    value="{{ $staffCategory->commission }}" class="form-control category-commission"
                                                    placeholder="Commission" required min="1">
                                                <select name="categories[{{ $index }}][commission_type]" class="form-control commission-type">
                                                    <option value="percentage" {{ $staffCategory->commission_type == 'percentage' ? 'selected' : '' }}>%</option>
                                                    <option value="fixed" {{ $staffCategory->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success add-service" data-category-row="{{ $index }}">
                                            <i class="fa fa-plus-circle"></i> Add Service
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove-category">
                                            <i class="fa fa-minus-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr id="service-container-{{ $index }}">
                                    <td colspan="4">
                                        <div class="service-wrapper d-flex flex-wrap">
                                            @if($staffCategory->services)
                                            @foreach ($staffCategory->services as $serviceIndex => $service)
                                            <div class="service-box col-md-6 border-bottom mb-3 py-3">
                                                <div class="form-group">
                                                    <select name="categories[{{ $index }}][services][{{ $serviceIndex }}][service_id]"
                                                        class="form-control service-select select2" required>
                                                        <option value="">Select Service</option>
                                                        @foreach ($services as $serviceOption)
                                                        <option value="{{ $serviceOption->id }}"
                                                            @if($service->service_id == $serviceOption->id) selected @endif>
                                                            {{ $serviceOption->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group d-flex">
                                                    <input type="number" name="categories[{{ $index }}][services][{{ $serviceIndex }}][service_commission]"
                                                        value="{{ $service->commission }}" class="form-control service-commission" required min="1">
                                                    <select name="categories[{{ $index }}][services][{{ $serviceIndex }}][commission_type]" class="form-control commission-type">
                                                        <option value="percentage" {{ $service->commission_type == 'percentage' ? 'selected' : '' }}>%</option>
                                                        <option value="fixed" {{ $service->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-danger remove-service"><i class="fa fa-minus-circle"></i></button>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button id="addCategoryBtn" onclick="addCategoryRow();" type="button" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-block btn-primary">Update</button>
        </div>
    </div>
</form>
</div>
<script>
    $("#search-services").keyup(function() {
        let value = $(this).val().toLowerCase();

        $(".services-table tr").hide();

        $(".services-table tr").each(function() {
            let $row = $(this);

            let name = $row.find("td:nth-child(2)").text().toLowerCase();

            if (name.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });
    $("#search-category").keyup(function() {
        let value = $(this).val().toLowerCase();

        $(".category-table tr").hide();

        $(".category-table tr").each(function() {
            let $row = $(this);

            let title = $row.find("td:nth-child(2)").text().toLowerCase();

            if (title.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });

    $('.category-checkbox').click(function() {
        var categoryId = $(this).val();

        if (categoryId === 'all') {
            var allCheckboxState = $(this).prop('checked');
            $('.category-checkbox').prop('checked', allCheckboxState);
        }
    });

    $('.service-checkbox').click(function() {
        var serviceId = $(this).val();

        if (serviceId === 'all') {
            var allCheckboxState = $(this).prop('checked');
            $('.service-checkbox').prop('checked', allCheckboxState);
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('.service-select').select2();

        $('.category-select').each(function () {
            var categoryId = $(this).val();
            var categoryRow = $(this).closest('tr').next('tr').attr('id');
            var serviceWrapper = $(`#${categoryRow} .service-wrapper`);

            if (categoryId) {
                serviceWrapper.find('.service-select').each(function () {
                    var selectedServiceId = $(this).val();
                    var dropdown = $(this);
                    
                    fetchServices(categoryId, dropdown, selectedServiceId);
                });
            }
        });
    });
    var category_row = {{ $serviceStaff->affiliateCategories->count() ?? 0 }};
    

    function addCategoryRow() {
        var newRow = `
            <tr>
                <td>
                    <div class="col-md-12">
                        <div class="form-group">
                            <select name='categories[${category_row}][category_id]' class="form-control category-select" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="col-md-12">
                        <div class="form-group d-flex">
                            <input type="number" name="categories[${category_row}][category_commission]" class="form-control category-commission" placeholder="Commission" required min="1">
                            <select name="categories[${category_row}][commission_type]" class="form-control commission-type">
                                <option value="percentage">%</option>
                                <option value="fixed">Fixed</option>
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-success add-service" data-category-row="${category_row}">
                        <i class="fa fa-plus-circle"></i> Add Service
                    </button>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-category">
                        <i class="fa fa-minus-circle"></i>
                    </button>
                </td>
            </tr>
            <tr id="service-container-${category_row}">
                <td colspan="4">
                    <div class="service-wrapper d-flex flex-wrap"></div>
                </td>
            </tr>
        `;

        $('#categoryTable tbody').append(newRow);
        category_row++;
    }

    $(document).on('click', '.remove-category', function () {
        $(this).closest('tr').next('tr').remove();
        $(this).closest('tr').remove();
    });

    $(document).on('click', '.add-service', function () {
        var categoryRow = $(this).data('category-row');
        var serviceWrapper = $(`#service-container-${categoryRow} .service-wrapper`);
        var serviceIndex = serviceWrapper.find('.service-box').length;
        var categoryId = $(`select[name="categories[${categoryRow}][category_id]"]`).val();

        var newServiceRow = `
            <div class="service-box col-md-6 border-bottom mb-3 py-3">
                <div class="form-group">
                    <select name="categories[${categoryRow}][services][${serviceIndex}][service_id]" class="form-control service-select" required>
                        <option value="">Select Service</option>
                    </select>
                </div>
                <div class="form-group d-flex">
                    <input type="number" name="categories[${categoryRow}][services][${serviceIndex}][service_commission]" class="form-control service-commission" placeholder="Service Commission" required min="1">
                    <select name="categories[${categoryRow}][services][${serviceIndex}][commission_type]" class="form-control commission-type">
                        <option value="percentage">%</option>
                        <option value="fixed">Fixed</option>
                    </select>
                </div>
                <button type="button" class="btn btn-danger remove-service"><i class="fa fa-minus-circle"></i></button>
            </div>
        `;

        serviceWrapper.append(newServiceRow);

        serviceWrapper.find('.service-select').last().select2();

        if (categoryId) {
            fetchServices(categoryId, serviceWrapper.find('.service-select').last());
        }
    });

    $(document).on('click', '.remove-service', function () {
        $(this).closest('.service-box').remove();
    });

    $(document).on('change', '.category-select', function () {
        var categoryId = $(this).val();
        var categoryRow = $(this).closest('tr').next('tr').attr('id');
        var serviceWrapper = $(`#${categoryRow} .service-wrapper`);

        if (categoryId) {
            serviceWrapper.find('.service-select').each(function () {
                fetchServices(categoryId, $(this));
            });
        } else {
            serviceWrapper.find('.service-select').html('<option value="">Select Service</option>').select2();
        }
    });

    function fetchServices(categoryId, dropdown, selectedServiceId = null) {
        $.ajax({
            url: "{{ route('getServicesByCategory') }}",
            type: "GET",
            data: { category_id: categoryId },
            success: function (data) {
                dropdown.html('<option value="">Select Service</option>');
                $.each(data, function (index, service) {
                    var selected = (selectedServiceId && selectedServiceId == service.id) ? "selected" : "";
                    dropdown.append(`<option value="${service.id}" ${selected}>${service.name}</option>`);
                });
                dropdown.select2();
            }
        });
    }
</script>
@endsection