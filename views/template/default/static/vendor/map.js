$(function() {

	// GOOGLE MAP
	$("#map-block").height($("#map-wrapper").height());	// Set Map Height
	function initialize($) {
		var mapOptions = {	
			zoom: 8,
			center: new google.maps.LatLng(17.421306, 78.457553),
			disableDefaultUI: true
		};
		var map = new google.maps.Map(document.getElementById('map-block'), mapOptions);
	}
	google.maps.event.addDomListener(window, 'load', initialize);

});	