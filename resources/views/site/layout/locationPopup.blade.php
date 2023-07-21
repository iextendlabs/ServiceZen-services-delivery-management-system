
<input type="hidden" name="session" id="session" @if(isset($address)) value="true" @else value="false" @endif>
<div class="modal fade" id="locationPopup">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Popup Header -->
      <div class="modal-header">
        <h5 class="modal-title">Set Location</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Popup Body -->
      <div class="modal-body">
        <div class="input-group mb-3">
          @csrf
          <input type="hidden" name="buildingName" id="popup_buildingName">
          <input type="hidden" name="flatVilla" id="popup_flatVilla">
          <input type="hidden" name="street" id="popup_street">
          <input type="hidden" name="area" id="popup_area">
          <input type="hidden" name="landmark" id="popup_landmark">
          <input type="hidden" name="city" id="popup_city">
          <input type="hidden" name="latitude" id="popup_latitude">
          <input type="hidden" name="longitude" id="popup_longitude">

          <div class="input-group-prepend" onclick="$('#popup_searchField').val('')">
            <span class="input-group-text">x</span>
          </div>
          <input type="text" class="form-control" id="popup_searchField" value="{{ session('address') ? session('address')['searchField'] : '' }}" name="searchField" placeholder="Search on map">
          <div class="input-group-append">
            <button class="btn btn-primary" id="setLocation" type="button">Search on map</button>
          </div>
        </div>
        OR 

        <label for="zoneSelect">Select Zone:</label>
        <select class="form-control" id="zoneSelect" onchange="window.location.href='/updateZone?zone='+this.value">
        <option value="">-- Select Zone -- </option>
          <!-- Loop through the $zones array to generate options -->
          @foreach ($zones as $zone)
            <option value="{{ $zone }}">{{ $zone }}</option>
          @endforeach
        </select>

        <div id="mapContainer" style="height: 400px; margin-top: 20px; display:none"></div>
      </div>

      <!-- Popup Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save Changes</button>
      </div>

    </div>
  </div>
</div>