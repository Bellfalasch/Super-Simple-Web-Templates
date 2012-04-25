<?php
	/**
	 * Trying out the Google Maps API v3
	 * http://code.google.com/apis/maps/documentation/javascript/tutorial.html
	 * 
	 */
?><!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100%; }
      body {
      	height: 100%;
      	margin: 0;
      	padding: 0;
      }
      #map_canvas {
      		width:  600px;
      		height: 500px;
      		border: 3px solid black;
      		float:  left;
      }
    </style>

    <script type="text/javascript">
		function initialize() {
			
			var myLatLng = new google.maps.LatLng(-34.397, 150.644);

			var myOptions = {
				zoom: 4,
				center: myLatLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

			/*
			var image = 'images/beachflag.png';
			var myLatLng = new google.maps.LatLng(-33.890542, 151.274856);
			var beachMarker = new google.maps.Marker({
				position: myLatLng,
				map: map,
				icon: image
			});
			*/
			//var myLatLng = new google.maps.LatLng(-33.890542, 151.274856);

			var marker = new google.maps.Marker({
			    position: myLatLng,
			    map: map,
			    title:"Hello World!"
			});
		}

		function loadScript() {
		  var script = document.createElement("script");
		  script.type = "text/javascript";
		  script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyAN_BvtCll_62uobQKpm4Zjitxes0x0Mwg&sensor=false&callback=initialize";
		  document.body.appendChild(script);
		}

		window.onload = loadScript;
    </script>

  </head>
  <body>
    
    <div id="map_canvas"></div>

  </body>
</html>