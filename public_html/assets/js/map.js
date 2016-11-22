(function(map) {
	'use strict';

	map.onDomReady = function() {
		map.init();
	}

	map.init = function() {
		GoogleMapsLoader.KEY = 'AIzaSyDq2BXy6EpcOpD_q7pkp44AF75Gbb3HDcc'
		map.render();
	}

	map.render = function() {
		if ($('#google-map').length > 0) {

			var el = document.getElementById('google-map');
			var centerLatLng = {lat: 6.995676, lng: 20.914284}
			var styles = map.styling();

			GoogleMapsLoader.load(function(google) {

				var call = $.ajax({
					type: 'GET',
					url: "/api/stations/get",
					dataType: 'json'
				})

				call.done(function(request) {
					// Create a map object and specify the DOM element for display.
					var map = new google.maps.Map(el, {
						center: centerLatLng,
						scrollwheel: true,
						styles: styles,
						zoom: 4
					});

					$.each(request, function(k,v) {
						var contentString = '<h4>' + v.title + '</h4>';
							contentString += 'Lat / Lng: ' + v.lat + ' / ' + v.lng + '<br>';
							contentString += 'Elevation: ' + v.elevation + '<br>';

						var infowindow = new google.maps.InfoWindow({
							content: contentString
						});

						var marker = new google.maps.Marker({
							map: map,
							position: {lat: v.lat, lng: v.lng},
							animation: google.maps.Animation.DROP
						});

						marker.addListener('click', function() {
							if (infoWindowVisible()) {
								infowindow.close();
								infoWindowVisible(false);
							} else {
								infowindow.open(map, marker);
								infoWindowVisible(true);
							}
						});
					});

					/* Toggle marker click */
					var infoWindowVisible = (function () {
					    var currentlyVisible = false;
					    return function (visible) {
							if (visible !== undefined) {
								currentlyVisible = visible;
							}
							return currentlyVisible;
						};
					}());

				});
			});
		}
	}

	map.styling = function() {
		var styleArray = [
			{
				featureType: "all",
				stylers: [
					{ saturation: -80 }
				]
			},{
				featureType: "road.arterial",
				elementType: "geometry",
				stylers: [
					{ hue: "#00ffee" },
					{ saturation: 50 }
				]
			},{
				featureType: "poi.business",
				elementType: "labels",
				stylers: [
					{ visibility: "off" }
				]
			}
		];

		return styleArray;
	}
})(window.map = window.map || {});
$(document).ready(map.onDomReady);
