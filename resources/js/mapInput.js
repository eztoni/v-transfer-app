function initialize() {

    $('form').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    const locationInputs = document.getElementsByClassName("map-input");

    const autocompletes = [];
    const geocoder = new google.maps.Geocoder;
    for (let i = 0; i < locationInputs.length; i++) {

        const input = locationInputs[i];
        const fieldKey = input.id.replace("-input", "");

        const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

        const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -33.8688;
        const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 151.2195;

        const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
            center: {lat: latitude, lng: longitude},
            zoom: 13
        });
        const marker = new google.maps.Marker({
            map: map,
            position: {lat: latitude, lng: longitude},
        });

        marker.setVisible(isEdit);

        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.key = fieldKey;
        autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
        registerLivewireEvents(autocomplete, fieldKey);

    }


    for (let i = 0; i < autocompletes.length; i++) {
        const input = autocompletes[i].input;
        const autocomplete = autocompletes[i].autocomplete;
        const map = autocompletes[i].map;
        const marker = autocompletes[i].marker;

        google.maps.event.addListener(autocomplete, 'place_changed', (data) => {
            marker.setVisible(false);

            const place = autocomplete.getPlace();
            if(!place && !data){
                return
            }

            if (place && !data) {
                geocoder.geocode({'placeId': place.place_id}, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        const lat = results[0].geometry.location.lat();
                        const lng = results[0].geometry.location.lng();
                        setLocationCoordinates(autocomplete.key, lat, lng);
                    }
                });
                myLatLong = place.geometry.location;

            } else {
                const lat = data.lat;
                const lng = data.lng;
                setLocationCoordinates(autocomplete.key, data.lat, data.lng);
                myLatLong = new google.maps.LatLng(data.lat, data.lng);
            }

            map.setCenter(myLatLong);
            map.setZoom(15);

            marker.setPosition(myLatLong);
            marker.setVisible(true);
        });
    }
}

function setLocationCoordinates(key, lat, lng) {

    const addressField = document.getElementById(key + "-" + "input");

    const addressFieldValue = document.getElementById(key + "-" + "address");
    addressFieldValue.value = addressField.value
    addressFieldValue.dispatchEvent(new Event('input'));

    const latitudeField = document.getElementById(key + "-" + "latitude");
    const longitudeField = document.getElementById(key + "-" + "longitude");
    latitudeField.value = lat;
    latitudeField.dispatchEvent(new Event('input'));
    longitudeField.value = lng;
    longitudeField.dispatchEvent(new Event('input'));

}

function registerLivewireEvents(autocomplete, fieldKey) {
    $(document).ready( ()=>{
        window.livewire.on('updateMap', data => {
            autocomplete.set('place',null)

            latitude = data.lat
            longitude = data.lng
            google.maps.event.trigger(autocomplete, 'place_changed', {
                lat: parseFloat(latitude),
                lng: parseFloat(longitude)
            });

        });
    })

}
