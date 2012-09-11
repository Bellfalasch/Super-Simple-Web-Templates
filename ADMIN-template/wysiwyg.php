<?php

	ob_start();

// Database setup (MySQL)
// ****************************************************************************
	
	// Set constants after environment
	if ($_SERVER['SERVER_NAME'] == 'localhost' && 1 == 2) // Always go remote (ugly hack)
	{	// LOCAL
		DEFINE('DB2_USER', 'default');
		DEFINE('DB2_PASS', 'r7eRUCZf4hqmXQGn');
		DEFINE('DB2_HOST', 'localhost');
		DEFINE('DB2_NAME', 'nxtcms');
	} else {
		// LIVE (change to your settings)
		DEFINE('DB2_USER', 'byggeriet');
		DEFINE('DB2_PASS', 'XEivchfi');
		DEFINE('DB2_HOST', 'mysql-5.dataguard.no');
		DEFINE('DB2_NAME', 'byggeriet');
	}
	
	// Set up database class
	global $mysqli;
	$mysqli = new mysqli( DB2_HOST, DB2_USER, DB2_PASS, DB2_NAME );
	$mysqli->set_charset('utf8');

// ****************************************************************************
	
	
	include("login/include/session.php");
	
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Min butikk</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" />
	<link rel="stylesheet" href="css/style.css?v=<?= rand(); ?>" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<?php
// db-functions
// ****************************************************************************

		function db_getButikData($butik) {
			$q = "SELECT 
				  c.`butiktext`, c.`fritext`, c.`openings`, c.`adresse`, c.`kart_lng`, c.`kart_lat`, c.`kart_zoom`, c.`url`, c.`title`, c.`county`,
				  c.`butiksbilde`, i.`link_url` AS url1, i.`image_src` AS src1, ii.`link_url` AS url2, ii.`image_src` AS src2
				  FROM `nxtcms_content` c
				  LEFT OUTER JOIN  `nxtcms_images` i ON i.`type` = 2
				  AND i.`store_id` = $butik
				  LEFT OUTER JOIN  `nxtcms_images` ii ON ii.`type` = 3
				  AND ii.`store_id` = $butik
				  WHERE c.`id` = $butik";
			return db_MAIN( $q );
		}
		function db_getButikIdForNonAdmin($adminid) {
			$q = "SELECT `id`
				  FROM `nxtcms_content`
				  WHERE `user_loginid` = '$adminid'";
			return db_MAIN( $q );
		}
		function db_getDefaultAds($type) {
			$q = "SELECT `id`, `title`, `type`, `link_url`, `image_src`
				  FROM `nxtcms_images`
				  WHERE `type` = $type
				  AND `store_id` = 0
				  ORDER BY `id` DESC";
			return db_MAIN( $q );
		}
		function db_getMyAds($butik,$type) {
			$q = "SELECT `id`, `link_url`, `image_src`
				  FROM `nxtcms_images`
				  WHERE `type` = $type
				  AND `store_id` = $butik";
			return db_MAIN( $q );
		}
		// db-manipulation
		function db_setMyAd_upd($id,$url,$src) {
			$q = "UPDATE `nxtcms_images` SET
				  `link_url` = $url,
				  `image_src` = $src
				  WHERE `id` = $id
				;";
			return db_MAIN( $q );
		}
		function db_setMyAd_ins($butik,$url,$src,$type) {
			$q = "INSERT INTO `nxtcms_images`
				  (`link_url`,`image_src`,`store_id`,`type`)
				  VALUES($url, $src, $butik, $type)";
			return db_MAIN( $q );
		}


// ****************************************************************************

		$user_id = $session->username;
		$butik_id = 0; // Denna variabel vill vi komma åt inne på Upload med ...
		
		// Ær du admin hæmta edit-id från GET
		if ($session->isAdmin())
		{
			if (isset($_GET['id']))
				$butik_id = trim( $_GET['id'] );
		
		// Annars utgå från ditt admin-id
		} else {
		
			$butik_id = -1; // Vi kan senare førutsætta att ær butikid -1 så visa INTE butikslista, men visa om 0
			
			$result = db_getButikIdForNonAdmin($user_id);
			if ($result->num_rows > 0)
			{
				while ( $row = $result->fetch_object() )
				{
					$butik_id = $row->id;
				}
			}
		}

		function addSlashesOrNull($text) {
			$text = trim($text);
			if ($text == '')
				return 'null';
			else
				return "'" . $text . "'";
		}


		// Om man postat något så spara
		if ( isset($_REQUEST['spara']) ) {

			// Spara all postad data i variabler
			$strBeskrivning 	= trim( $_REQUEST['input_butik'] );
			$strFritext 		= trim( $_REQUEST['input_fritext'] );
			$strAapningstider 	= trim( $_REQUEST['input_aapningstider'] );
			$strAdress		 	= trim( $_REQUEST['input_adress'] );
			$strKartLng	 		= trim( $_REQUEST['kart_lng'] );
			$strKartLat	 		= trim( $_REQUEST['kart_lat'] );
			$strKartZoom	 	= trim( $_REQUEST['kart_zoom'] );
			$strButiksBilde	 	= trim( $_REQUEST['butiksbilde'] );
			$strUrl	 			= trim( $_REQUEST['input_url'] );
			$strTitle	 		= trim( $_REQUEST['input_title'] );
			$strCounty	 		= trim( $_REQUEST['countySelect'] );

			$strButiksbilde  = trim($_REQUEST['butiksbild']);
			$strAnnonslinke1 = trim($_REQUEST['annonslink2']);
			$strAnnonsBilde1 = trim($_REQUEST['annonsbilde2']);
			$strAnnonslinke2 = trim($_REQUEST['annonslink3']);
			$strAnnonsBilde2 = trim($_REQUEST['annonsbilde3']);
			
			// Skickar man inte med någon bild så nollstæll æven eventuellt inskriven url
			if ($strAnnonsBilde1 == '') $strAnnonslinke1 = '';
			if ($strAnnonsBilde2 == '') $strAnnonslinke2 = '';

//			if ($strCounty == '')
//				$strCounty = 'null';
//			else
//				$strCounty = "'" . $strCounty . "'";

			// Hæmta upp vald butik och spara data dit.
			$result = db_getButikData($butik_id);
			if ($result->num_rows > 0)
			{
				$q = "UPDATE `nxtcms_content` SET
					  `butiksbilde` = " . addSlashesOrNull($strButiksbilde) . ",
					  `butiktext` = '$strBeskrivning',
					  `fritext` = " . addSlashesOrNull($strFritext) . ",
					  `openings` = '$strAapningstider',
					  `adresse` = '$strAdress',
					  `kart_lng` = '$strKartLng',
					  `kart_lat` = '$strKartLat',
					  `kart_zoom` = $strKartZoom,
					  `county` = " . addSlashesOrNull($strCounty) . "
					  WHERE `id` = $butik_id
					;";
				db_MAIN( $q );
				$errorMsg = '<div class="alert alert-success"><h4>Data sparad</h4></div>';
			}


			/***************************************************************
			 * Hantera sparning av annonser (se om data finns: uppdatera, eller skapa ny)
			 */
			$result = db_getMyAds($butik_id, 2);
			$intAnnonse1 = -1;
			if ($result->num_rows > 0)
			{
				while ( $row = $result->fetch_object() )
				{
					$intAnnonse1 = $row->id;
				}
			}

			if ($intAnnonse1 > 0)
			{
				db_setMyAd_upd( $intAnnonse1, addSlashesOrNull($strAnnonslinke1), addSlashesOrNull($strAnnonsBilde1) );
				//$errorMsg .= '<div class="alert alert-success"><h4>Data 1 sparad</h4>SQL-UPDATE</div>';

			} else {
				db_setMyAd_ins( $butik_id, addSlashesOrNull($strAnnonslinke1), addSlashesOrNull($strAnnonsBilde1), 2 );
				//$errorMsg .= '<div class="alert alert-success"><h4>Data 1 sparad</h4>SQL-INSERT</div>';
			}

			// Next ad:
			$result = db_getMyAds($butik_id, 3);
			$intAnnonse2 = -1;
			if ($result->num_rows > 0)
			{
				$row = $result->fetch_object();
				$intAnnonse2 = $row->id;
			}

			if ($intAnnonse2 > 0) {
				db_setMyAd_upd( $intAnnonse2, addSlashesOrNull($strAnnonslinke2), addSlashesOrNull($strAnnonsBilde2) );
				//$errorMsg .= '<div class="alert alert-success"><h4>Data 2 sparad</h4>SQL-UPDATE</div>';

			} else {
				db_setMyAd_ins( $butik_id, addSlashesOrNull($strAnnonslinke2), addSlashesOrNull($strAnnonsBilde2), 3 );
				//$errorMsg .= '<div class="alert alert-success"><h4>Data 2 sparad</h4>SQL-INSERT</div>';
			}

		} else {

			// Skapa alla variabler som fyller formulæret med data, børja med att kolla databasen.
			$result = db_getButikData($butik_id);
			if ($result->num_rows > 0)
			{
				//$errorMsg = '<div class="alert alert-success"><h4>Tab 1</h4><p>This tab is for editing the main-data of this store (no ads).</p></div>';
				while ( $row = $result->fetch_object() )
				{
					$strBeskrivning 	= $row->butiktext;
					$strFritext 		= $row->fritext;
					$strAapningstider 	= $row->openings;
					$strAdress		 	= $row->adresse;
					$strKartLng	 		= $row->kart_lng;
					$strKartLat	 		= $row->kart_lat;
					$strKartZoom	 	= $row->kart_zoom;
					$strUrl	 			= $row->url;
					$strTitle	 		= $row->title;
					$strCounty	 		= $row->county;

					$strButiksbilde  = $row->butiksbilde;
					$strAnnonslinke1 = $row->url1;
					$strAnnonsBilde1 = $row->src1;
					$strAnnonslinke2 = $row->url2;
					$strAnnonsBilde2 = $row->src2;
				}
			} else {
				//$errorMsg = '<div class="alert alert-error"><h4>Warning</h4><p>There is no store with this id at the moment. If you are an admin you should connect this store to Wordpress.</p></div>';
				$strBeskrivning 	= '';
				$strFritext 		= '';
				$strAapningstider 	= '';
				$strAdress		 	= '';
				$strKartLng	 		= '';
				$strKartLat	 		= '';
				$strKartZoom	 	= '';
				$strButiksBilde	 	= '';
				$strUrl	 			= '';
				$strTitle			= '';
				$strCounty			= '';

				$strButiksbilde  = '';
				$strAnnonslinke1 = '';
				$strAnnonsBilde1 = '';
				$strAnnonslinke2 = '';
				$strAnnonsBilde2 = '';
			}
		}
	
	?>
    <?php
    	if ($strKartLng != '' && $strKartZoom != 0) {
    		$lng  = $strKartLng;
			$lat  = $strKartLat;
			$zoom = $strKartZoom;
		} else {
			$lat  = '59.917139432621006';
			$lng  = '10.727569958038316';
			$zoom = 11;
		}
    ?>
<?php if ($butik_id != 0) { ?>
	<script type="text/javascript">
		function initialize() {
			
			var myLatLng = new google.maps.LatLng(<?= $lat ?>, <?= $lng ?>);

			var myOptions = {
				zoom: <?= $zoom ?>,
				center: myLatLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			
			var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

			var image = new google.maps.MarkerImage(
			  '/wp-content/themes/byggeriet/images/icon_byggeriet.png',
			  new google.maps.Size(18,21),
			  new google.maps.Point(0,0),
			  new google.maps.Point(9,21)
			);

			var shadow = new google.maps.MarkerImage(
			  '/wp-content/themes/byggeriet/images/icon_byggeriet_shadow.png',
			  new google.maps.Size(32,21),
			  new google.maps.Point(0,0),
			  new google.maps.Point(9,21)
			);

			var shape = {
			  coord: [17,0,17,1,17,2,17,3,17,4,17,5,17,6,17,7,17,8,17,9,17,10,17,11,17,12,17,13,17,14,17,15,17,16,10,17,10,18,9,19,9,20,7,20,7,19,6,18,6,17,0,16,0,15,0,14,0,13,0,12,0,11,0,10,0,9,0,8,0,7,0,6,0,5,0,4,0,3,0,2,0,1,0,0,17,0],
			  type: 'poly'
			};
			
			var marker = new google.maps.Marker({
				position: myLatLng,
				map: map,
				draggable: true,
				icon: image,
				shadow: shadow,
				shape: shape
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

			google.maps.event.addListener(map, 'zoom_changed', function() {
				document.getElementById("zoom").value = map.getZoom();
			});
		}
		

		function loadScript() {
		  var script = document.createElement("script");
		  script.type = "text/javascript";
		  script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyAN_BvtCll_62uobQKpm4Zjitxes0x0Mwg&sensor=false&callback=initialize";
		  document.body.appendChild(script);
		}
		window.onload = loadScript;

		function onPreview() {
			myForm = document.getElementById("theForm");
			myForm.action = 'preview.php';
			myForm.target = '_blank';
			myForm.submit();
			return false;
		}

		function onSubmit() {
			myForm = document.getElementById("theForm");
			myForm.action = '';
			myForm.target = '';
			//myForm.submit();
		}

		$(document).ready(function() {
			
			$('#input_annonsbild3').change(function() {
				//alert( $(this).attr("title") + ' - ' + $(this).val() );
				//alert( $(this).find('option:selected').attr("title") + ' - ' + $(this).find('option:selected').val() );
				$the_title = $(this).find('option:selected').attr("title");

				//alert($the_title);

				if ($the_title != undefined)
					$('#input_annonslinke3').val( $(this).find('option:selected').attr("title") );
			});

			$('#input_annonsbild2').change(function() {
				$the_title = $(this).find('option:selected').attr("title");

				if ($the_title != undefined)
					$('#input_annonslinke2').val( $(this).find('option:selected').attr("title") );
			});

		});
    </script>
    <script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
		tinyMCE.init({
			// General options
			mode : "textareas",
			theme : "advanced",
			plugins : "spellchecker,iespell,inlinepopups,paste,nonbreaking",

			// Theme options
//			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
//			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
//			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,justifyfull,|,undo,redo",
			theme_advanced_buttons2 : "outdent,indent,link,unlink,|,cut,copy,paste,pastetext,pasteword,|,cleanup",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
        	editor_selector : "mceEditor",
        	editor_deselector : "mceNoEditor",
			width: "100%",
			height: "300"
		});
	</script>
<?php } ?>
</head>
<body>

	<?php include("_header.php") ?>

<div id="container">
	
	<?php if ($errorMsg != '') { ?>
		<?= $errorMsg ?>
	<?php } ?>
	

<?php if ($butik_id != 0) { ?>
	<form method="post" action="" id="theForm">

	  <div class="row">
		<div class="span7">
			
			<label for="input_butik">Butikkbeskrivelse</label>
			<textarea id="input_butik" name="input_butik" class="mceEditor"><?= $strBeskrivning ?></textarea>
			<br /><br />

			
			<label for="input_fritext">Fritekst:</label>
			<textarea id="input_fritext" name="input_fritext" class="mceEditor"><?= $strFritext ?></textarea>
			<br /><br />
			
		</div>

		<div class="span4 offset1">
		
			<label for="input_aapningstider">Åpningstider:</label>
			<textarea id="input_aapningstider" name="input_aapningstider" class="span4 mceNoEditor" rows="3"><?= $strAapningstider ?></textarea>
			<br /><br />
			
			<label for="input_adress">Her finner du oss:</label>
			<textarea id="input_adress" name="input_adress" class="span4 mceNoEditor" rows="5"><?= $strAdress ?></textarea>
			<br /><br />

			<input type="hidden" name="input_title" value="<?= $strTitle ?>" />
			<input type="hidden" name="input_url" value="<?= $strUrl ?>" />

			<label for="countySelect">Velg fylke:</label>
			<select id="countySelect" name="countySelect" class="span4">
				<?php
					$arrCounty = array(
						"Akershus",
						"Aust-Agder",
						"Buskerud",
						"Finnmark",
						"Hedmark",
						"Hordaland",
						"Møre og Romsdal",
						"Nordland",
						"Nord-Trøndelag",
						"Oppland",
						"Oslo",
						"Rogaland",
						"Sogn og Fjordane",
						"Sør-Trøndelag",
						"Telemark",
						"Troms",
						"Vest-Agder",
						"Vestfold",
						"Østfold"
					);
					
					// Loopa ut allt i arrayen, vælj det man satt i databasen
					$somethingChecked = false;
					//echo '<option value="0">- ' . $strCounty . ' -</option>';
					foreach ($arrCounty as $theCounty)
					{
						if ( strtolower($strCounty) === strtolower($theCounty) ) {
							$countyChecked = ' selected="selected"';
							$somethingChecked = true;
						} else {
							$countyChecked = '';
						}
						echo '<option value="' . $theCounty . '"' . $countyChecked . '>' . $theCounty . '</option>';
					}
					
					// Ær inget valt i databasen skriv ut "inget valt"-valet på olika sætt
					if ($somethingChecked) {
						echo '<option value="0">- Inget fylke -</option>';
					} else {
						echo '<option value="0" selected="selected">- Inget fylke valt -</option>';
					}
				?>
			</select>
			<br /><br />
			
			<label>Kart:</label>
			<div id="map_canvas"></div><br />
			
			<input type="hidden" name="kart_lng" id="lng" value="<?= $strKartLng ?>" />
			<input type="hidden" name="kart_lat" id="lat" value="<?= $strKartLat ?>" />
			<input type="hidden" name="kart_zoom" id="zoom" value="<?= $strKartZoom ?>" />
		
		</div>
	  </div>


	  <div class="form-horizontal">
		<fieldset>
			<legend>Butikkbilde</legend>
			<br />

			<div class="span7">
		
				<div class="control-group">
					<label for="butiksbild" class="control-label">Bilde:</label>
					<div class="controls">
						<select id="butiksbild" name="butiksbild" class="input-xlarge">
						<?php
							// * Hæmta alla filer i mappen
							$dir = "uploads/kunde_$butik_id/v1/";
							$files = scandir($dir);
							$somethingChecked = FALSE;
							$strSelected = '';

							foreach($files as $key => $value)
							{
								if ($value != '.' && $value != '..')
								{
									if ( $strButiksbilde === $dir . '' . $value ) {
										$strSelected = ' selected="selected"';
										$somethingChecked = true;
									} else
										$strSelected = '';
										
									echo '<option value="' . $dir . $value . '"' . $strSelected . '>' . $value . '</option>';
								}
							}

							// Ær inget valt i databasen skriv ut "inget valt"-valet på olika sætt
							if ($somethingChecked) {
								$strSelected = '';
							} else {
								if ($strButiksbilde != '') {
									echo '<option value="' . $strButiksbilde . '" selected="selected">Fra Wordpress: ' . basename($strButiksbilde) . '</option>';
									echo '<option value="">- Ikke bruk noen butikkbilde -</option>';
								} else
									echo '<option value="" selected="selected">- Ikke bruk noen butikkbilde -</option>';
							}
							//echo '<option value=""' . $strSelected .'>- Ikke bruk noen butiksbilde -</option>';
						?>
						</select>
						<?php if ($strButiksbilde != '') { ?>
							<!--<img src="<?= $strButiksbilde ?>" alt="" /><br />-->
						<?php } ?>
					</div>
				</div>

			</div>

			<div class="span3 offset1">

				<a href="crop2.php?id=<?= $butik_id ?>&amp;v=1" target="_blank" class="btn btn-primary">Last opp <strong>butikkbilde</strong></a>
				<p><small>(<strong>280</strong>px maksbredde, <strong>200</strong>px makshøyde)</small></p>

			</div>
			
		</fieldset>
		<br /><br /><br />


		
		<fieldset>
			<legend>Stor annonse</legend>
			<br />

			<div class="span7">

				<div class="control-group">
					<label for="input_annonsbild2" class="control-label">Bilde:</label>
					<div class="controls">
						<!--
						<?= $strAnnonsBilde1 ?>
						<?= $strAnnonslinke1 ?>
						-->
						<select id="input_annonsbild2" name="annonsbilde2" class="input-xlarge">
							<optgroup label="Mine annonser">
						<?php
							// * Hæmta alla filer i mappen
							$dir = "uploads/kunde_$butik_id/v2/";
							$files = scandir($dir);
							$strSelected = "";
							$somethingChecked = false;

							foreach($files as $key => $value)
							{
								if ($value != '.' && $value != '..')
								{
									if ( $strAnnonsBilde1 === $dir . '' . $value ) {
										$strSelected = ' selected="selected"';
										$somethingChecked = true;
									} else
										$strSelected = '';
										
									echo '<option value="' . $dir . $value . '"' . $strSelected . '>' . $value . '</option>';
								}
							}
							echo "<option disabled='disabled'></option>";
							echo "</optgroup>";


							// function db_getDefaultAds($type)
							// `id`, `title`, `type`, `link_url`, `image_src`
							$result = db_getDefaultAds(2);
							if ($result->num_rows > 0)
							{
								echo '<optgroup label="Default Byggeriet annonser">';
								while ( $row = $result->fetch_object() )
								{
									// Klick går direkt till ny url med id som sparar kalaset
									if ( $strAnnonsBilde1 === $row->image_src ) {
										$strSelected = ' selected="selected"';
										$somethingChecked = true;
									} else
										$strSelected = '';

									echo "<option value='" . $row->image_src . "' title='" . $row->link_url . "'$strSelected>" . $row->title . "</option>";
								}
								echo "</optgroup>";
							}
							
							// Ær inget valt i databasen skriv ut "inget valt"-valet på olika sætt
							if ($somethingChecked) {
								$strSelected = '';
							} else {
								$strSelected = ' selected="selected"';
							}
							echo "<option disabled='disabled'></option>";
							echo '<option value=""' . $strSelected .'>- Ikke bruk denne annonsen -</option>';

						?>
						</select>
						<?php if ($strAnnonsBilde1 != '') { ?>
							<!--<img src="<?= $strAnnonsBilde1 ?>" alt="" /><br />-->
						<?php } ?>
					</div>
				</div>
		
				<div class="control-group">
					<label for="input_annonslinke2" class="control-label">Link:</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">http://</span><input type="text" id="input_annonslinke2" name="annonslink2" value="<?= $strAnnonslinke1 ?>" class="input-xlarge" />
						</div>
					</div>
				</div>

			</div>

			<div class="span3 offset1">

				<a href="crop2.php?id=<?= $butik_id ?>&amp;v=2" target="_blank" class="btn btn-primary">Last opp <strong>stor</strong> annonse</a>
				<p><small>(<strong>640</strong>px maksbredde, fri høyde)</small></p>

			</div>

		</fieldset>

		<?php
		/*
			// function db_getDefaultAds($type)
			// `id`, `title`, `type`, `link_url`, `image_src`
			$result = db_getDefaultAds(2);
			if ($result->num_rows > 0)
			{
				echo "<strong>Default ads:</strong> ";
				while ( $row = $result->fetch_object() )
				{
					// Klick går direkt till ny url med id som sparar kalaset
					echo "<a href='#' class='btn btn-mini' title='" . $row->link_url . " - " . $row->image_src . "'>" . $row->title . "</a> ";
				}
			}
		*/
		?>
		<br /><br /><br />


		
		<fieldset>
			<legend>Liten annonse</legend>
			<br />
		
			<div class="span7">

				<div class="control-group">
					<label for="input_annonsbild3" class="control-label">Bilde:</label>
					<div class="controls">
						<select id="input_annonsbild3" name="annonsbilde3" class="input-xlarge" title="top">
							<optgroup label="Mine annonser" title="first">
						<?php
							// * Hæmta alla filer i mappen
							$dir = "uploads/kunde_$butik_id/v3/";
							$files = scandir($dir);
							$strSelected = "";
							$somethingChecked = false;

							foreach($files as $key => $value)
							{
								if ($value != '.' && $value != '..')
								{
									if ( $strAnnonsBilde2 === $dir . '' . $value ) {
										$strSelected = ' selected="selected"';
										$somethingChecked = true;
									} else
										$strSelected = '';
										
									//echo "<option>$strAnnonsBilde2 --- $dir/$value";
									echo '<option value="' . $dir . $value . '"' . $strSelected . '>' . $value . '</option>';
								}
							}
							echo "<option disabled='disabled'></option>";
							echo "</optgroup>";


							// function db_getDefaultAds($type)
							// `id`, `title`, `type`, `link_url`, `image_src`
							$result = db_getDefaultAds(3);
							if ($result->num_rows > 0)
							{
								echo '<optgroup id="defaults3" label="Default Byggeriet annonser" title="last">';
								while ( $row = $result->fetch_object() )
								{
									// Klick går direkt till ny url med id som sparar kalaset
									// TODO: rullistan hanterar inte bilder som inte ligger i ens egna mapp så æven echo på aktuellt db-innehåll
									if ( $strAnnonsBilde2 === $row->image_src ) {
										$strSelected = ' selected="selected"';
										$somethingChecked = true;
									} else
										$strSelected = '';

									echo "<option value='" . $row->image_src . "' title=\"" . $row->link_url . "\"$strSelected>" . $row->title . "</option>";
								}
								echo "</optgroup>";
							}
							
							// Ær inget valt i databasen skriv ut "inget valt"-valet på olika sætt
							if ($somethingChecked) {
								$strSelected = '';
							} else {
								$strSelected = ' selected="selected"';
							}
							echo "<option disabled='disabled'></option>";
							echo '<option value=""' . $strSelected .'>- Ikke bruk denne annonsen -</option>';

						?>
						</select>
						<?php if ($strAnnonsBilde2 != '') { ?>
							<!--<img src="<?= $strAnnonsBilde2 ?>" alt="" /><br />-->
						<?php } ?>

					</div>
				</div>

				<div class="control-group">
					<label for="input_annonslinke3" class="control-label">Link:</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">http://</span><input type="text" id="input_annonslinke3" name="annonslink3" value="<?= $strAnnonslinke2 ?>" class="input-xlarge" />
						</div>
					</div>
				</div>

			</div>

			<div class="span3 offset1">

				<a href="crop2.php?id=<?= $butik_id ?>&amp;v=3" target="_blank" class="btn btn-primary">Last opp <strong>liten</strong> annonse</a>
				<p><small>(<strong>280</strong>px maksbredde, fri høyde)</small></p>

			</div>

		</fieldset>
	  </div>


		<div id="placeholder_save">
			<div class="form-actions">

				<input type="submit" name="spara" value="Lagre" class="btn btn-primary" onclick="onSubmit();" />

				<a class="btn" href="#" onclick="onPreview(); return false;">
					<i class="icon-eye-open"></i>
					Forhåndsvisning
				</a>

			</div>
		</div>

	</form>

<?php } elseif ($butik_id === 0) { ?>

	<?php
		$result = db_MAIN("SELECT `id`, `title` FROM `nxtcms_content` ORDER BY `title` ASC;");
		if ($result->num_rows > 0)
		{
			while ( $row = $result->fetch_object() )
			{
				echo '<a href="?id=' . $row->id . '">' . $row->title . '</a><br />';
			}
		}
	?>

<?php } ?>
</div>
	
</body>
</html><?php
// Database main function (does all the talking to the database class and handling of errors)
// This can be updated so that it don't let empty results through, just uncomment all comments =)
// ****************************************************************************	

	function db_MAIN($sql)
	{
		global $mysqli;
		$result = $mysqli->query( $sql );
		if ( $result )
		{
//			if ($result->num_rows > 0) {
				//echo "<strong>( Rows: " . $result->num_rows . " - Fields: " . $result->field_count . " )</strong><br />";
				return $result;
//			} else {
				//echo 'There are no results to display.';
//				return null;
//			}
		} else {
			printf("<div class='error'>There has been an error from MySQL: %s<br /><br />%s</div>", $mysqli->error, nl2br($sql));
			//return null;
			exit; // halta all kod, kritiskt fel
		}
	}



// Close database
// ****************************************************************************	

	$mysqli->close();



// END FILE
// ****************************************************************************
?>