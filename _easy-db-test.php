<meta charset="UTF-8" />
<?php
	//phpinfo();

	// Database variables
	DEFINE('DB_USER', 'default');
	DEFINE('DB_PASS', 'r7eRUCZf4hqmXQGn');
	DEFINE('DB_HOST', 'localhost');
	DEFINE('DB_NAME', 'test');

	$dbc = @mysqli_connect( DB_HOST, DB_USER, DB_PASS, DB_NAME ) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );
	// echo("<h2>BEKLAGER! FEIL HOS VÅR LEVERANDØR!!! IKKE TILGANG TIL DATABASE:</H2><br /><br />:\n " . mysql_error() . "\n");

	mysqli_set_charset( $dbc, 'utf8' );

	$q = "SELECT Fornavn FROM tblMailingliste WHERE id = 128";
	$r = @mysqli_query( $dbc, $q );

	if ( $r ) {

		echo "gott!<br />";

		while ( $row = mysqli_fetch_array( $r, MYSQLI_ASSOC ) ) {
			echo $row['Fornavn'] . "<br />";
		}

	}

?>

	<script language="JavaScript">
		<!--
		function isEmail(str) {
			var supported = 0;
			if (window.RegExp) {
				var tempStr = "a";
				var tempReg = new RegExp(tempStr);
				if (tempReg.test(tempStr)) supported = 1;
			}
			if (!supported) {
				return (str.indexOf(".") > 2) && (str.indexOf("@") > 0);
			}
			var r1 = new RegExp("(@.*@)|(\\.\\.)|(@\\.)|(^\\.)");
			var r2 = new RegExp("^.+\\@(\\[?)[a-zA-Z0-9\\-\\.]+\\.([a-zA-Z]{2,4}|[0-9]{1,4})(\\]?)$");
			return (!r1.test(str) && r2.test(str));
		}

		function valider_form() {

			var Fornavn	  = document.getElementById("Fornavn");
			var Etternavn = document.getElementById("Etternavn");
			var Epost	  = document.getElementById("Epost");
			var error	  = '';

			if (! (Fornavn.value && Etternavn.value)) {
				error += ' - Du har ikke skrevet ditt for- og/eller etternavn\n';
			}

			if (Epost.value) {
				if(!isEmail(Epost.value) == 1) {
					error += ' - Feil i epost-adressen\n';
				}
			} else {
				error += ' - Angi din epost-adresse\n';
			}

			if (error == '') {
				document.registrer.submit();
			}
			else {
				alert('Vennligst korriger følgende:\n' + error);
				return false;
			}
		}
		//-->
	</script>

	<style>
		fieldset {
			border: 1px solid black;
			border-radius: 5px;
		}
		legend {
			color: gray;
		}
		label {
			display: block;
			width: 100px;
			float: left;
		}
	</style>


	<fieldset>
		<legend>Team Olympia</legend>

		<form name="registrer" method="post" action="" onsubmit="return valider_form();">

			<img src="http://olympiasport.no/bilder/teamolympialogo.gif" align="left">
			Her kan du melde deg inn i Team Olympia! Det er kun fordeler med å være medlem i "Team Olympia"! Du vil
			være den første som mottar informasjon om spesielle tilbud, aktuelle kurs, nyttige tips og annen aktuell
			informasjon fra butikken.<br /><br /><br /><br />

			<label for="Fornavn">Fornavn:</label>
			<input type="tekst" name="Fornavn" id="Fornavn" size="20" maxlength="255" /><br />
			
			<label for="Etternavn">Etternavn:</label>
			<input type="tekst" name="Etternavn" id="Etternavn" size="20" maxlength="255" /><br />
			
			<label for="Epost">E-post:</label>
			<input type="tekst" name="Epost" id="Epost" size="20" maxlength="100" /><br />
			
			<label for="Mobil">Mobil:</label>
			<input type="tekst" name="Mobil" id="Mobil" size="20" maxlength="100" /><br />
			
			<input type="submit" name="registrer" value="Registrer">

		</form>

	</fieldset>


<?php
	if ( isset($_POST['Fornavn']) && $_POST['Fornavn'] != "" ) {

		// Fetch all form data and secure it
		$Fornavn = mysqli_real_escape_string( $dbc, trim( $_POST['Fornavn'] ) );
		$Etternavn = mysqli_real_escape_string( $dbc, trim( $_POST['Etternavn'] ) );
		$Epost = mysqli_real_escape_string( $dbc, strtolower( trim( $_POST['Epost'] ) ) );
		$Telefon = isset( $_POST['Telefon'] ) ? mysqli_real_escape_string( $dbc, trim( $_POST['Telefon'] ) ) : "";
		
		$q = "SELECT Fornavn, Etternavn FROM tblMailingliste WHERE Epost = '" . $Epost . "' LIMIT 1";
		$r = @mysqli_query( $dbc, $q );

		//if ( $r ) { // false om db-fel, annars true
		$num = mysqli_num_rows($r); // rækna rader
		
		if ( $num > 0 ) {

			//echo "Personen fanns redan!<br />";
			while ( $row = mysqli_fetch_array( $r, MYSQLI_ASSOC ) ) {
				//echo "FEIL! " . $row['Fornavn'] . " " . $row['Etternavn'] . " finns allrede registrert.<br />";
				echo "FEIL! Adressen " . $Epost . " finns allrede registrert.<br />";
			}

		} else {
		
			$q = "INSERT INTO tblMailingliste(Fornavn, Etternavn, Epost, Telefon) VALUES('$Fornavn', '$Etternavn', '$Epost' , '$Telefon')";
			$r = @mysqli_query( $dbc, $q );
			if ( $r ) {
				echo "CONGRATS! Du ær inlagd med e-postadressen " . $Epost . "<br />";
				mysqli_free_result($r); // Empty rs
			} else {
				echo "Beklager, det oppstod en feil! Ta kontakt med oss via email hvis du er i tvil!<br />";
			}
		}
		
	} else {
	
		$Fornavn = "x";
		$Etternavn = "x";
		$Epost = "x";
		$Telefon = "x";
		
	}
?>

	<fieldset>
		<legend>Følgende data er registrert:</legend>
		
		<label>Fornavn:</label>		<?php echo $Fornavn; ?><br />
		<label>Etternavn:</label>	<?php echo $Etternavn; ?><br />
		<label>Epost:</label>		<?php echo $Epost; ?><br />
		<label>Telefon:</label>		<?php echo $Telefon; ?><br /><br />
		
		Takk skal du ha <?php echo $Fornavn; ?>&nbsp;<?php echo $Etternavn; ?>!<br /><br />

		<a href="index.php">&lt;&lt; Tilbake</a>
		
	</fieldset>

<?

	mysqli_close($dbc);

	/*
		*** Formulær
		*** Validera formulær
		*** Kolla om e-post redan finns (hantera)
		*** Skjut in data
		* Send mail till registranten
		* Send mail till OS
		* Tack-skærm
		* Unsub-funktion (skicka in e-post, update db)
	*/

?>