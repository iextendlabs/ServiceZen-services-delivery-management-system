@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Service Staff Categories and Services</h2>
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
    <form action="{{ route('serviceStaff.categories-and-services.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="category-services" role="tabpanel" aria-labelledby="category-services-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group scroll-div">
                        <span style="color: red;">*</span><strong>Category:</strong>
                        <input type="text" name="search-category" id="search-category" class="form-control" placeholder="Search Category">
                        <table class="table table-striped table-bordered category-table">
                            <thead>
                                <tr>
                                    <th> <input type="checkbox" class="category-checkbox" name="category" value="all"> </th>
                                    <th>Title</th>
                                </tr>
                            </thead>
                            <tbody id="category-table-body">
                                @foreach ($categories as $category)
                                <tr>
                                    <td>

                                        <input type="checkbox" class="category-checkbox" name="category_ids[]" value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', $serviceStaff->categories()->pluck('category_id')->toArray() ?? [])) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $category->title }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group scroll-div">
                        <span style="color: red;">*</span><strong>Services:</strong>
                        <input type="text" name="search-services" id="search-services" class="form-control" placeholder="Search Services">
                        <table class="table table-striped table-bordered services-table">
                            <thead>
                                <tr>
                                    <th> <input type="checkbox" class="service-checkbox" name="service" value="all"> </th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody id="service-table-body">
                                @foreach ($services as $service)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="service-checkbox" name="service_ids[]" value="{{ $service->id }}" data-category="{{ $service->category_id }}" {{ in_array($service->id, old('service_ids', $serviceStaff->services()->pluck('service_id')->toArray() ?? [])) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $service->name }}</td>

                                    <td>{{ isset($service->discount) ? 
                                    $service->discount : $service->price }}</td>
                                    <td>{{ $service->duration ?? ""}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
$(document).ready(function() {
    function sortCheckedToTop(tableId, checkboxClass) {
        const $table = $(tableId);
        const $rows = $table.find('tr');
        
        $rows.sort(function(a, b) {
            const aChecked = $(a).find(checkboxClass).is(':checked');
            const bChecked = $(b).find(checkboxClass).is(':checked');
            
            if (aChecked && !bChecked) return -1;
            if (!aChecked && bChecked) return 1;
            return 0;
        });
        
        $table.append($rows);
    }
    sortCheckedToTop('#category-table-body', '.category-checkbox');
    sortCheckedToTop('#service-table-body', '.service-checkbox');
});
</script>
@endsection