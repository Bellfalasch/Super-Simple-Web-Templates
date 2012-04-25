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

    <?php
		$indata = '<iframe width="280" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.no/maps/ms?ie=UTF8&hl=no&msa=0&msid=110450116719553756323.000493bf9d0fbe256a4a1&ll=68.079257,15.613493&spn=0.001402,0.003004&z=17&output=embed"></iframe><br /><small>Vis <a href="http://maps.google.no/maps/ms?ie=UTF8&hl=no&msa=0&msid=110450116719553756323.000493bf9d0fbe256a4a1&ll=68.079257,15.613493&spn=0.001402,0.003004&z=17&source=embed" style="color:#0000FF;text-align:left">Byggeriet Byggmester Knut Høivaag  </a> i et større kart</small>';

		// Øppna indata och finn i den førsta 'll=', ta ut datan och splitta på ',' så har vi sparad lng och lat! =D
		// "ll=68.079257,15.613493&"

		$foundat = strpos( $indata, 'll=' ) + 3; // +3 to skip over the QS of 'll='
		$endsat  = strpos( $indata, '&', $foundat + 1 );
		$latlng  = substr( $indata, $foundat, $endsat-$foundat );

		$pieces = explode(",", $latlng);
		$lat 	= $pieces[0];
		$lng 	= $pieces[1];
    ?>

    <script type="text/javascript">
		function initialize() {
			
			var myLatLng = new google.maps.LatLng(<?= $lat ?>, <?= $lng ?>);

			var myOptions = {
				zoom: 4,
				center: myLatLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

			var marker = new google.maps.Marker({
			    position: myLatLng,
			    map: map,
			    title: 'Uluru (Ayers Rock)',
			    draggable: true
			});

			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			});

			// Add dragging event listeners.
			google.maps.event.addListener(marker, 'dragstart', function() {
				//updateMarkerAddress('Dragging...');
			});

			// Add dragging END event listeners.
			google.maps.event.addListener(marker, 'dragend', function() {
				//updateMarkerAddress('Dragging...');
				document.getElementById("lng").value = marker.getPosition().lng();
				document.getElementById("lat").value = marker.getPosition().lat();
			});

			//moveBus( map, marker );
		}

		//function moveBus( map, marker ) {
		//    marker.setPosition( new google.maps.LatLng( 0, 0 ) );
		//    map.panTo( new google.maps.LatLng( 0, 0 ) );
		//};

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
    
    <div id="map_canvas"></div><br />
    Lng: <input type="text" id="lng" value="<?= $lng ?>" /><br />
    Lat: <input type="text" id="lat" value="<?= $lat ?>" />

  </body>
</html>