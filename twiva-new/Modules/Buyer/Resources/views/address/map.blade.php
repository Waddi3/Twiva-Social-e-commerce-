@push('style')
    <style>

        #map-container {
            height: 800px; /* Set the desired height */
            max-width: none !important;
            width: 100% !important;
            position: relative;
            overflow: hidden;
        }
    </style>
@endpush

<p class="pt-9 text-lg font-semibold">Map</p>
@error('latitude')
<span class="text-error-800 text-sm">{{$message}}</span>
<br>
@enderror
@error('longitude')
<span class="text-error-800 text-sm">{{$message}}</span>
@enderror
<div>
    <div id="map-container">
        <div id="map" style="min-height: 800px"></div>
    </div>

    <input type="hidden" id="latitude" name="latitude" @if(isset($address)) value="{{ $address->latitude }}" @endif>
    <input type="hidden" id="longitude" name="longitude"
           @if(isset($address)) value="{{ $address->longitude }}" @endif>
</div>


@push('script')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('map.google_map_key') }}&libraries=places&callback=initMap"
        async defer>
    </script>

    <script>
        var latitude_longitude;

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, errorCallbackPosition);
            } else {
                console.error("navigator geolocation not found!");
            }
        }


        function initMap() {
            let geocoder = new google.maps.Geocoder();
            let defaultLat = `{{ $address->latitude ?? 'null' }}`;
            let defaultLng = `{{ $address->longitude ?? 'null' }}`;
            let def_zoom = 10;


            if ((defaultLat == 'null' || defaultLng == 'null') && latitude_longitude !== undefined) {
                defaultLat = latitude_longitude.latitude;
                defaultLng = latitude_longitude.longitude;
            } else if (defaultLat || defaultLng) {
                defaultLat = isNaN(+defaultLat) ? -1.2966100673251932 : +defaultLat;
                defaultLng = isNaN(+defaultLng) ? 36.82273099722985 : +defaultLng;
                def_zoom = isNaN(+defaultLat) ? 10 : 16;
            } else {
                defaultLat = -1.2966100673251932;
                defaultLng = 36.82273099722985;
            }

            document.getElementById('latitude').value = defaultLat;
            document.getElementById('longitude').value = defaultLng;


            const map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: defaultLat,
                    lng: defaultLng
                },
                zoom: def_zoom
            });


            const marker = new google.maps.Marker({
                position: {lat: defaultLat, lng: defaultLng},
                map: map
            });

            let city_name = $("#city_id option:selected").text();
            if (city_name) {
                geocoder.geocode({'address': city_name}, function (results, status) {
                    if (status === 'OK') {
                        map.setCenter(results[0].geometry.location);
                        marker.setPosition(results[0].geometry.location)
                    } else {
                        console.log('Geocode was not successful for the following reason: ' + status);
                    }
                });
            }

            map.addListener('click', (e) => {
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                marker.setPosition(e.latLng);
            });

            // Set the height and width of the map container
            document.getElementById('map').style.height = '800px';
            document.getElementById('map').style.width = '100%';
        }


        function showPosition(position) {
            latitude_longitude = {
                'latitude': position.coords.latitude,
                'longitude': position.coords.longitude
            };
            initMap();
        }

        const errorCallbackPosition = (error) => {
            if (error.code === error.PERMISSION_DENIED) {
                // The user denied permission, show an alert or a custom message
                console.error("For a better experience, turn on device location, which uses Google's location service.");
            } else if (error.code === error.POSITION_UNAVAILABLE) {
                // Location information is unavailable, handle accordingly
                console.error("Location information is not available.");
            } else if (error.code === error.TIMEOUT) {
                // The request to get user location timed out, handle accordingly
                console.error("Request for location information timed out.");
            } else {
                // Other error, handle accordingly
                console.error("An error occurred while fetching your location.");
            }
            console.log(error);
            // alert("For a better experience, turn on device location, which uses Google's location service.");
            // alert("To continue, turn on device location, which uses Google's location service");
        };

        getLocation();

        $('#city_id').on('change', function () {
            initMap();
        });
    </script>
@endpush
