var map;
var marker;
var circle;
var geocoder;
function load() {
    geocoder = new google.maps.Geocoder();

    if ($("#field-lat").val() != '' && $("#field-lng").val() != '')
        var latlng = new google.maps.LatLng($("#field-lat").val(), $("#field-lng").val());
    else
        var latlng = new google.maps.LatLng(29.996563, 31.217719);
    var myOptions = {
        zoom: 15,
        center: latlng,
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("retailer-map"), myOptions);
    var searchBox = new google.maps.places.SearchBox(document.getElementById('pac-input'));
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(document.getElementById('pac-input'));

    google.maps.event.addListener(searchBox, 'places_changed', function() {
     searchBox.set('map', null);
     var places = searchBox.getPlaces();
     var bounds = new google.maps.LatLngBounds();
     var i, place;
     for (i = 0; place = places[i]; i++) {
       (function(place) {
          if (marker) {
              marker.setMap(null);
          }
          document.getElementById("field-lat").value = place.geometry.location.lat();
          document.getElementById("field-lng").value = place.geometry.location.lng();
          console.log("Coordinates found / Latitude - " + place.geometry.location.lat() + " & longitude - " + place.geometry.location.lng());
         marker = new google.maps.Marker({

           position: place.geometry.location,
            draggable: true
         });
         marker.bindTo('map', searchBox, 'map');
         google.maps.event.addListener(marker, 'map_changed', function() {
           if (!this.getMap()) {
             this.unbindAll();
           }
         });
         bounds.extend(place.geometry.location);

       }(place));
     }
     map.fitBounds(bounds);
     searchBox.set('map', map);
     map.setZoom(Math.min(map.getZoom(),15));
   });

    addMarker(map.getCenter());

    google.maps.event.addListener(map, "click", function(event) {
        addMarker(event.latLng);
    });

}

function addMarker(location) {
    if (marker) {
        marker.setMap(null);
    }
    document.getElementById("field-lat").value = location.lat();
    document.getElementById("field-lng").value = location.lng();
    console.log("Coordinates found / Latitude - " + location.lat() + " & longitude - " + location.lng());
    marker = new google.maps.Marker({
        position: location,
        draggable: true
    });
    marker.setMap(map);
    google.maps.event.addListener(marker, "dragend", function(event) {
        newlatlng = event.latLng;
        map.setCenter(newlatlng);
        document.getElementById("field-lat").value = newlatlng.lat();
        document.getElementById("field-lng").value = newlatlng.lng();
    });
}

window.onload = function() {
    load();
    $('#field-zip').blur(function() {
        codeAddress();
    });
    $('#field-zip').css('text-transform', 'uppercase');
}
