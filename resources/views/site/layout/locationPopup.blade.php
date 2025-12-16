<input type="hidden" name="session" id="session" @if(isset($address)) value="true" @else value="false" @endif>
<!-- Tailwind modal styling (static). Dynamic functionality and IDs preserved for existing JS. -->
<div id="locationPopup" class="fixed inset-0  backdrop-blur-sm  z-50 mt-6 backdrop-blur-sm flex items-center justify-center" style="display:none; margin-top:6%; margin-left:40%;">

  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 relative">
    <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-purple-500 transition text-xl" onclick="if (typeof $ !== 'undefined' && $('#locationPopup').modal) { $('#locationPopup').modal('hide'); } else { $('#locationPopup').hide(); }">&times;</button>

    <h2 class="text-2xl font-semibold text-purple-600 mb-4 text-center">Set Location</h2>
    <div class="mb-4">
      <label for="zoneSelect" class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
      <select id="zoneSelect" name="zone" class="w-full border border-purple-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 focus:outline-none">
        <option value="">-- Select Zone --</option>
        @foreach ($zones as $zone)
        <option value="{{ $zone }}">{{ $zone }}</option>
        @endforeach
      </select>
    </div>

    <div class="flex items-center my-4">
      <div class="flex-grow border-t border-purple-200"></div>
      <span class="mx-2 text-gray-500 text-sm uppercase">or</span>
      <div class="flex-grow border-t border-purple-200"></div>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">Add Address</label>
      <div class="flex items-center gap-2">
        <button type="button" class="text-gray-400 hover:text-gray-600 px-2 py-1 rounded focus:outline-none" onclick="document.getElementById('popup_searchField').value=''">&#10005;</button>
        <input type="text" id="popup_searchField" name="searchField" value="{{ session('address') ? (session('address')['searchField'] ? session('address')['searchField'] : session('address')['area']) : '' }}" placeholder="Search" class="flex-1 rounded-lg border border-purple-200 px-3 py-2 focus:ring-2 focus:ring-purple-400 focus:outline-none" />
        <button class="bg-purple-500 text-white px-4 py-2 rounded-lg font-semibold transition" id="setLocation" type="button">Search</button>
      </div>
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
    </div>

    <div class="flex items-center my-4">
      <div class="flex-grow border-t border-purple-200"></div>
      <span class="mx-2 text-gray-500 text-sm uppercase">or</span>
      <div class="flex-grow border-t border-purple-200"></div>
    </div>

    <div class="mb-6">
      <div id="mapContainer" class="bg-purple-50 border border-purple-200 rounded-lg h-48 flex items-center justify-center text-purple-400" style="margin-top:10px; display:none">🗺️ Click Map to Select Location</div>
    </div>

    <div class="pt-2">
      <button type="button" id="saveLocation" class="w-full bg-gradient-to-r from-purple-500 to-indigo-500 text-white py-3 rounded-xl font-semibold hover:opacity-90 transition">Save Location</button>
    </div>
  </div>
</div>