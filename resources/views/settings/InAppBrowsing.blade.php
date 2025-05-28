@extends('layouts.app')
<style>
    #section_zone {
        border: 1px solid #ced4da;
        border-radius: .25rem;
        width: 335px !important;
    }
</style>
@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <h3 class="mb-0">Edit In App Browsing Links</h3>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the following issues:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('settings.appBrowsingUpdate', $setting->id) }}" method="POST" enctype="multipart/form-data"
            id="app-browsing-form">
            @csrf
            @method('PUT')

            <div id="sections-container">
                @php
                    $sections = old(
                        'sections',
                        isset($sections) && count($sections) > 0
                            ? $sections
                            : [
                                [
                                    'name' => '',
                                    'status' => 1,
                                    'sort' => 0,
                                    'entries' => [
                                        [
                                            'image' => '',
                                            'destinationUrl' => '',
                                            'zone' => [],
                                        ],
                                    ],
                                ],
                            ],
                    );
                @endphp

                @foreach ($sections as $sectionIndex => $section)
                    <div class="card mb-4 section-item">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mt-2">
                                        <input type="text"
                                            class="form-control section-name @error("sections.$sectionIndex.name") is-invalid @enderror"
                                            name="sections[{{ $sectionIndex }}][name]"
                                            value="{{ old("sections.$sectionIndex.name", $section['name']) }}"
                                            placeholder="Section name (e.g., Business, Social)" required>
                                        @error("sections.$sectionIndex.name")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-2">
                                        <select
                                            class="form-control section-status @error("sections.$sectionIndex.status") is-invalid @enderror"
                                            name="sections[{{ $sectionIndex }}][status]" required>
                                            <option value="1"
                                                {{ old("sections.$sectionIndex.status", $section['status']) == 1 ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="0"
                                                {{ old("sections.$sectionIndex.status", $section['status']) == 0 ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                        @error("sections.$sectionIndex.status")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mt-2">
                                        <input type="text"
                                            class="form-control section-sort @error("sections.$sectionIndex.sort") is-invalid @enderror"
                                            name="sections[{{ $sectionIndex }}][sort]"
                                            value="{{ old("sections.$sectionIndex.sort", $section['sort'] ?? 0) }}"
                                            placeholder="Section Sort Order">
                                        @error("sections.$sectionIndex.sort")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-section mt-2"
                                        {{ count($sections) == 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i> Remove Section
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered entries-table">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Image</th>
                                        <th style="width: 30%">Destination URL</th>
                                        <th style="width: 30%">Zones</th>
                                        <th style="width: 10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="entries-container">
                                    @foreach ($section['entries'] as $entryIndex => $entry)
                                        @php
                                            $imagePath = null;
                                            if (isset($entry['existing_image'])) {
                                                $imagePath = $entry['existing_image'];
                                            } elseif (
                                                isset($entry['image']) &&
                                                file_exists(public_path('app-browsing-icon/' . $entry['image']))
                                            ) {
                                                $imagePath = $entry['image'];
                                            }
                                        @endphp
                                        <tr class="entry">
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div>
                                                        @if ($imagePath)
                                                            <input type="hidden"
                                                                name="sections[{{ $sectionIndex }}][entries][{{ $entryIndex }}][existing_image]"
                                                                value="{{ $imagePath }}">
                                                        @endif
                                                        <input type="file"
                                                            class="form-control image-upload @error("sections.$sectionIndex.entries.$entryIndex.image") is-invalid @enderror"
                                                            name="sections[{{ $sectionIndex }}][entries][{{ $entryIndex }}][image]"
                                                            accept="image/*" style="width: 220px;">
                                                        <small class="form-text text-muted">300x300px</small>
                                                        @error("sections.$sectionIndex.entries.$entryIndex.image")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="image-preview"
                                                        style="{{ $imagePath ? '' : 'display:none;' }}">
                                                        @if ($imagePath)
                                                            <img src="{{ asset('app-browsing-icon/' . $imagePath) }}"
                                                                alt="Image preview" style="max-height: 80px; width: auto;"
                                                                class="img-thumbnail">
                                                        @else
                                                            <img src="#" alt="Image preview"
                                                                style="max-height: 80px; width: auto;"
                                                                class="img-thumbnail">
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="url"
                                                    class="form-control @error("sections.$sectionIndex.entries.$entryIndex.destination_url") is-invalid @enderror"
                                                    name="sections[{{ $sectionIndex }}][entries][{{ $entryIndex }}][destination_url]"
                                                    placeholder="https://example.com"
                                                    value="{{ old("sections.$sectionIndex.entries.$entryIndex.destination_url", $entry['destinationUrl'] ?? '') }}">
                                                @error("sections.$sectionIndex.entries.$entryIndex.destination_url")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <div class="form-group" id="section_zone">
                                                    <select
                                                        class="form-control entry-zone selectpicker @error("sections.$sectionIndex.entries.$entryIndex.zone") is-invalid @enderror"
                                                        name="sections[{{ $sectionIndex }}][entries][{{ $entryIndex }}][zone][]"
                                                        multiple data-live-search="true" data-actions-box="true">
                                                        @foreach ($zones as $zone)
                                                            <option value="{{ $zone }}"
                                                                {{ in_array($zone, old("sections.$sectionIndex.entries.$entryIndex.zone", $entry['zone'] ?? [])) ? 'selected' : '' }}>
                                                                {{ $zone }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error("sections.$sectionIndex.entries.$entryIndex.zone")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-entry">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @error("sections.$sectionIndex.entries")
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-sm btn-success add-entry">
                                    <i class="fas fa-plus"></i> Add Entry
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between mt-4">
                <div>
                    <button type="button" id="add-section" class="btn btn-info">
                        <i class="fas fa-plus"></i> Add New Section
                    </button>
                </div>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save"></i> Update All
                </button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            let sectionIndex = {{ count($sections) }};
            let entryIndices = {!! json_encode(array_fill(0, count($sections), count($sections[0]['entries'] ?? 1))) !!};

            $('.entry-zone').selectpicker();

            // Add new section
            $('#add-section').on('click', function() {
                const html = `
                <div class="card mb-4 section-item">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mt-2">
                                    <input type="text" class="form-control section-name" name="sections[${sectionIndex}][name]" 
                                        placeholder="Section name (e.g., Business, Social)" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mt-2">
                                    <select class="form-control section-status" name="sections[${sectionIndex}][status]" required>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mt-2">
                                    <input type="text" class="form-control section-sort" name="sections[${sectionIndex}][sort]" 
                                        placeholder="Section Sort Order" value="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger ml-3 remove-section">
                                    <i class="fas fa-trash"></i> Remove Section
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered entries-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%">Image</th>
                                    <th style="width: 40%">Destination URL</th>
                                    <th style="width: 20%">Zones</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody class="entries-container">
                                <tr class="entry">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div>
                                                <input type="file"
                                                       class="form-control image-upload"
                                                       name="sections[${sectionIndex}][entries][0][image]"
                                                       accept="image/*"
                                                       style="width: 220px;">
                                                <small class="form-text text-muted">300x300px</small>
                                            </div>
                                            <div class="image-preview" style="display:none;">
                                                <img src="#"
                                                     alt="Image preview"
                                                     style="max-height: 80px; width: auto;"
                                                     class="img-thumbnail">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="url" class="form-control" 
                                               name="sections[${sectionIndex}][entries][0][destination_url]" 
                                               placeholder="https://example.com">
                                    </td>
                                    <td>
                                        <div class="form-group" id="section_zone">
                                            <select class="form-control entry-zone selectpicker" 
                                                    name="sections[${sectionIndex}][entries][0][zone][]" 
                                                    multiple
                                                    data-live-search="true" data-actions-box="true">
                                                @foreach ($zones as $zone)
                                                    <option value="{{ $zone }}">{{ $zone }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger remove-entry">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-sm btn-success add-entry">
                                <i class="fas fa-plus"></i> Add Entry
                            </button>
                        </div>
                    </div>
                </div>`;

                $('#sections-container').append(html);

                // Initialize selectpicker for the new entry's zone
                $('#sections-container .section-item:last .entry-zone.selectpicker').selectpicker({
                    width: '100%',
                    size: 'auto'
                });

                entryIndices[sectionIndex] = 1;
                sectionIndex++;

                // Enable all remove section buttons if there's more than one section
                if ($('.section-item').length > 1) {
                    $('.remove-section').prop('disabled', false);
                }
            });

            // Add entry to a specific section
            $(document).on('click', '.add-entry', function() {
                const sectionItem = $(this).closest('.section-item');
                const sectionIndex = $('.section-item').index(sectionItem);
                const entriesContainer = sectionItem.find('.entries-container');

                let maxIndex = -1;
                entriesContainer.find('.entry').each(function() {
                    const nameAttr = $(this).find('[name*="[entries]"]').first().attr('name');
                    const matches = nameAttr.match(/\[entries\]\[(\d+)\]/);
                    if (matches && matches[1]) {
                        const currentIndex = parseInt(matches[1]);
                        if (currentIndex > maxIndex) {
                            maxIndex = currentIndex;
                        }
                    }
                });
                const entryIndex = maxIndex + 1;

                const html = `
                <tr class="entry">
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <input type="file"
                                       class="form-control image-upload"
                                       name="sections[${sectionIndex}][entries][${entryIndex}][image]"
                                       accept="image/*"
                                       style="width: 220px;">
                                <small class="form-text text-muted">300x300px</small>
                            </div>
                            <div class="image-preview" style="display:none;">
                                <img src="#"
                                     alt="Image preview"
                                     style="max-height: 80px; width: auto;"
                                     class="img-thumbnail">
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="url" class="form-control" 
                               name="sections[${sectionIndex}][entries][${entryIndex}][destination_url]" 
                               placeholder="https://example.com">
                    </td>
                    <td>
                        <div class="form-group" id="section_zone">
                            <select class="form-control entry-zone selectpicker" 
                                    name="sections[${sectionIndex}][entries][${entryIndex}][zone][]" 
                                    multiple
                                    data-live-search="true" data-actions-box="true">
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone }}">{{ $zone }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-entry">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;

                entriesContainer.append(html);

                // Initialize selectpicker for the new entry's zone
                entriesContainer.find('.entry:last .entry-zone.selectpicker').selectpicker({
                    width: '100%',
                    size: 'auto'
                });
            });

            // Remove section
            $(document).on('click', '.remove-section', function() {
                if ($('.section-item').length > 1) {
                    const sectionIndex = $(this).closest('.section-item').index();
                    entryIndices.splice(sectionIndex, 1); // Remove the entry index for this section

                    $(this).closest('.section-item').remove();

                    // Reindex sections
                    $('.section-item').each(function(index) {
                        $(this).find('.section-name').attr('name', `sections[${index}][name]`);
                        $(this).find('.section-status').attr('name', `sections[${index}][status]`);
                        $(this).find('.section-sort').attr('name', `sections[${index}][sort]`);

                        // Reindex entries
                        $(this).find('.entry').each(function(entryIndex) {
                            $(this).find('.image-upload').attr('name',
                                `sections[${index}][entries][${entryIndex}][image]`);
                            $(this).find('input[type="url"]').attr('name',
                                `sections[${index}][entries][${entryIndex}][destination_url]`
                            );
                            $(this).find('input[name$="[existing_image]"]').attr('name',
                                `sections[${index}][entries][${entryIndex}][existing_image]`
                            );
                            $(this).find('.entry-zone').attr('name',
                                `sections[${index}][entries][${entryIndex}][zone][]`
                            );
                        });
                    });

                    // Disable remove button if only one section left
                    if ($('.section-item').length === 1) {
                        $('.remove-section').prop('disabled', true);
                    }
                } else {
                    alert('At least one section is required.');
                }
            });

            // Remove entry
            $(document).on('click', '.remove-entry', function() {
                const entriesContainer = $(this).closest('.entries-container');
                const sectionItem = $(this).closest('.section-item');
                const sectionName = sectionItem.find('.section-name').val();

                if (entriesContainer.find('.entry').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert('At least one entry is required when section has a name.');
                }
            });

            // Preview image
            $(document).on('change', '.image-upload', function() {
                const input = this;
                const previewWrapper = $(this).closest('td').find('.image-preview');
                const previewImg = previewWrapper.find('img');
                const existingImageInput = $(this).siblings(
                    'input[type="hidden"][name$="[existing_image]"]');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.attr('src', e.target.result);
                        previewWrapper.show();

                        // If there was an existing image, keep the hidden field
                        if (existingImageInput.length) {
                            existingImageInput.val('');
                        }
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    // If no file selected but we have an existing image, keep it
                    if (existingImageInput.length && existingImageInput.val()) {
                        previewWrapper.show();
                    } else {
                        previewWrapper.hide();
                    }
                }
            });

            $('.image-preview').each(function() {
                const img = $(this).find('img');
                if (img.attr('src') && img.attr('src') !== '#') {
                    $(this).show();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
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
        });
    </script>
@endsection
