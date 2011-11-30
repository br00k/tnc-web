/**
 * CORE Conference Manager
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.terena.org/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@terena.org so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 TERENA (http://www.terena.org)
 * @license    http://www.terena.org/license/new-bsd     New BSD License
 * @revision   $Id: conference.js 598 2011-09-15 20:55:32Z visser $
 */
$(function() {

	// add google maps style address completion
	if ($("#location_element").length>0) {

	var geocoder;
	var map;
	var marker;

	var geocoder;
	var map;
	var marker;

	var DEFAULT_LATITUDE = 41.659;
	var DEFAULT_LONGITUDE = -4.714;

	function initialize(){
		lat = $("#location_element input[name='lat']");
		lng = $("#location_element input[name='lng']");
		var latitude = lat.val();
		var longitude = lng.val();

		// use default location if nothing is saved
	    if (!latitude || !longitude) {
	        latitude = DEFAULT_LATITUDE;
	        longitude = DEFAULT_LONGITUDE;
	    }

		var latlng = new google.maps.LatLng(latitude, longitude);

		var options = {
		  zoom: 16,
		  center: latlng,
		  mapTypeId: google.maps.MapTypeId.SATELLITE
		};

		map = new google.maps.Map(document.getElementById("map_canvas"), options);

		geocoder = new google.maps.Geocoder();

		marker = new google.maps.Marker({
		  map: map,
		  draggable: true
		});
	}
	initialize();

	  $(function() {
	    $("#address").autocomplete({
	      //This bit uses the geocoder to fetch address values
	      source: function(request, response) {
	        geocoder.geocode( {'address': request.term }, function(results, status) {
	          response($.map(results, function(item) {
	            return {
	              label:  item.formatted_address,
	              value: item.formatted_address,
	              latitude: item.geometry.location.lat(),
	              longitude: item.geometry.location.lng()
	            }
	          }));
	        })
	      },
	      //This bit is executed upon selection of an address
	      select: function(event, ui) {
	        lat.val(ui.item.latitude);
	        lng.val(ui.item.longitude);
	        var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
	        marker.setPosition(location);
	        map.setCenter(location);
	      }
	    });
	  });

	// reverse geocoding
	google.maps.event.addListener(marker, 'drag', function() {
	  geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
	    if (status == google.maps.GeocoderStatus.OK) {
	      if (results[0]) {
	        $('#address').val(results[0].formatted_address);
	        $('#latitude').val(marker.getPosition().lat());
	        $('#longitude').val(marker.getPosition().lng());
	      }
	    }
	  });
	});

	} // end address completion
});
