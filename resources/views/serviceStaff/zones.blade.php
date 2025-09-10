@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Service Staff Zones</h2>
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
    <form action="{{ route('serviceStaff.zones.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="zone" role="tabpanel" aria-labelledby="zone-tab">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Zones</h5>
                            <div class="form-group">
                                <input type="text" id="zoneSearch" class="form-control" placeholder="Search zones...">
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <div class="mb-2">
                                <button type="button" id="addZoneBtn" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Add New Zone
                                </button>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="50px">
                                            <input type="checkbox" id="selectAllZones">
                                        </th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th width="50px">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="zoneTable">
                                    @foreach($staffZones as $zone)
                                    <tr class="zone-row">
                                        <td>
                                            <input type="checkbox" name="zones[]" class="zone-checkbox" 
                                                value="{{ $zone->id }}" {{ in_array($zone->id, old('zones', $serviceStaff->staffZones->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                        </td>
                                        <td class="zone-name">{{ $zone->name }}</td>
                                        <td>{{ $zone->description ?? 'N/A' }}</td>
                                        <td></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
    $(document).ready(function() {
        // Add new zone row
        $('#addZoneBtn').click(function() {
            const newZoneCount = $('.new-zone').length;
            const newRow = $(`
                <tr class="zone-row new-zone">
                    <td>
                        <input type="checkbox" class="zone-checkbox" disabled>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="text" name="new_zones[${newZoneCount}][name]" required class="form-control form-control-sm new-zone-name" placeholder="Zone name">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="text" name="new_zones[${newZoneCount}][description]" required class="form-control form-control-sm new-zone-desc" placeholder="Description">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-zone-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            
            $('#zoneTable').prepend(newRow);
            
            // Add event listener to remove button
            newRow.find('.remove-zone-btn').click(function() {
                newRow.remove();
                // Re-index remaining zones if needed
                $('.new-zone').each(function(index) {
                    $(this).find('[name^="new_zones"]').each(function() {
                        const name = $(this).attr('name').replace(/\[\d+\]/, `[${index}]`);
                        $(this).attr('name', name);
                    });
                });
            });
        });

        // Search functionality
        $('#zoneSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.zone-row').each(function() {
                const $row = $(this);
                const name = $row.find('.zone-name').text().toLowerCase() || 
                            $row.find('.new-zone-name').val().toLowerCase();
                const desc = $row.find('td:eq(2)').text().toLowerCase() || 
                            $row.find('.new-zone-desc').val().toLowerCase();
                
                $row.toggle(name.includes(searchTerm) || desc.includes(searchTerm));
            });
        });

        // Select all functionality
        $('#selectAllZones').change(function() {
            const isChecked = $(this).prop('checked');
            $('.zone-checkbox:not(:disabled)').prop('checked', isChecked);
        });
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

    sortCheckedToTop('#zoneTable', '.zone-checkbox');
});
</script>
@endsection