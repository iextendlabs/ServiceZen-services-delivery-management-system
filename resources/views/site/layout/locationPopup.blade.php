<input type="hidden" name="session" id="session" @if(isset($address)) value="true" @else value="false" @endif>
<div class="modal fade" id="locationPopup">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Popup Header -->
      <div class="modal-header">
        <h5 class="modal-title">Set Location</h5>
        <button type="button" class="close" data-dismiss="modal" onclick="$('#locationPopup').modal('hide')">&times;</button>

      </div>

      <!-- Popup Body -->
      <div class="modal-body">
        <select class="form-control" id="zoneSelect">
            <option value="">-- Select Zone -- </option>
            @foreach ($zones as $zone)
            <option value="{{ $zone }}">{{ $zone }}</option>
            @endforeach
        </select>
        <p>OR Add Address </p>

        <div class="input-group mb-3">
          @csrf
          <input type="hidden" name="buildingName" id="popup_buildingName">
          <input type="hidden" name="flatVilla" id="popup_flatVilla">
          <input type="hidden" name="street" id="popup_street">
          <input type="hidden" name="district" id="popup_district">
          <input type="hidden" name="area" id="popup_area">
          <input type="hidden" name="landmark" id="popup_landmark">
          <input type="hidden" name="city" id="popup_city">
          <input type="hidden" name="latitude" id="popup_latitude">
          <input type="hidden" name="longitude" id="popup_longitude">

          <div class="input-group-prepend" onclick="$('#popup_searchField').val('')">
            <span class="input-group-text">x</span>
          </div>
          <input type="text" class="form-control" id="popup_searchField" value="{{ session('address') ? (session('address')['searchField'] ? session('address')['searchField'] : session('address')['area']) : '' }}" name="searchField" placeholder="Search">
          <div class="input-group-append">
            <button class="btn btn-primary" id="setLocation" type="button">Search</button>
          </div>
        </div>
        <p>OR Click Map </p>
        <div id="mapContainer" style="height: 400px; margin-top: 20px; display:none"></div>
      </div>

      <!-- Popup Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-block btn-primary">Save Location</button>
      </div>

    </div>
  </div>
</div>