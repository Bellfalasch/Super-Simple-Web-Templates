<?php
/**
 * Very rough code for easy handling of SQL and databases on a local + remote site
 * 
 * This is always included as a global.php file in every project. The SQL can be in another file
 * if you'd like. I then just write loads of SQL-functions that the front-end developers easily
 * can call and use. Se the four examples in code.
 * Main db-function db_MAIN can easily be manipulated so that it automatically outputs zero rows
 * errors.

 * TODO:
 * Rensa upp exemplena och ersætt SQL-koden med ordentlig kod istællet (som skriver ut data, inte bygger SQL:er, inte sista iaf)
 * Skriv om så den kan hantera INS och SEL på olika sætt, och smidigare
 * Reducera mængden kod en programmerare måste upprepa før att få jobbet gjort
 * Rensa struntkod, kommentarer, etc, som inte behøvs
 * Prøva med bind_param på INS
 * Skriv om som en klass?
 * http://bathroom.baclayon.com/php/php-mysqli.php (massa bra, se æven affected_rows)
 */

// Database setup (MySQL)
// ****************************************************************************	
	
	// Set constants for db-access after environment
	if ($_SERVER['SERVER_NAME'] == 'localhost')
	{	// LOCAL
		DEFINE('DB_USER', 'default');			// Username for database
		DEFINE('DB_PASS', 'r7eRUCZf4hqmXQGn');	// Password for database
		DEFINE('DB_HOST', 'localhost');			// Server for database
		DEFINE('DB_NAME', 'test');				// Select database on server
	} else {
		// LIVE (change to your settings)
		DEFINE('DB_USER', 'xxx');
		DEFINE('DB_PASS', 'xxx');
		DEFINE('DB_HOST', 'xxx');
		DEFINE('DB_NAME', 'xxx');
	}
	
	// Set up database class
	global $mysqli;
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	if (mysqli_connect_errno()) { die("<p>Can't connect to this database or server =/</p>"); }
	$mysqli->set_charset('utf8');




// Prepared SQL-functions extracting data from db (our examples further down can be used)
// ****************************************************************************		
	
	/**
	 * Example 1: Short comment of function with info on param and return-list for developer
	 * so they don't need to worry about the SQL at all. This one with good comments.
	 * 
	 * @param undergruppe(string) 		undergruppeID you wanna find
	 * returns: g1, artnr, artikkel, beskrivelse, prefert
	 */
	function db_getItemsFromSubcategory($undergruppe) {
		$q = "SELECT `g1`, `artnr`, `artikkel`, `beskrivelse`, `prefert`
			  FROM `ingredients`
			  WHERE `ug1` = '$undergruppe'
			  ORDER BY `artikkel` ASC";
		return db_MAIN( $q );
	}
	

	
// Actual usages (change content in here, just for example purposes
// ****************************************************************************		

	// Example 1: See function declared above
	$result = db_getItemsFromSubcategory('x100upz');


	// Example 2: Build simple SQL INSERT statement and then run it through "db_MAIN"
	$q = "INSERT INTO `groups` (`g1`, `navn`)
		  SELECT DISTINCT(`g1`) AS id, `gruppe` AS gruppenavn
		  FROM `tmp`
		  WHERE `gruppe` <> '' AND `gruppe` <> 'gruppe'
		  ORDER BY `gruppe` ASC";
	db_MAIN( $q );
	
	
	// Example 3: Perform a SELECT and then loop through that data and build an INSERT-statement that is later pushed to database
	$result = db_MAIN("SHOW COLUMNS FROM `tmp`
		  WHERE `field` <> 'g1' AND `field` <> 'ug1' AND
			`field` <> 'gruppe' AND `field` <> 'undergruppe' AND
			`field` <> 'artnr' AND `field` <> 'artikkel' AND
			`field` <> 'beskrivelse' AND `field` <> 'status' AND
			`field` <> 'enhet' AND `field` <> 'ansvar' AND
			`field` <> 'listepris' AND `field` <> 'prisingsenhet' AND
			`field` <> 'prisliste' AND `field` <> 'prefert' AND
			`field` <> 'id'");
	if ( isset( $result ) )
	{
		$q = "INSERT INTO `idun_properties`(`navn`) VALUES";
		while ( $row = $result->fetch_object() )
		{
			$q .= "('" . $row->Field . "'), ";
		}
		$q = substr( trim($q), 0, -1) . ";"; // Strip trailing , and add a ;
		db_MAIN($q);
		
	} else {
		echo "<p>ERROR: No data found!</p>";
	}
	
	
	// Example 4: Drop and then create table a new, then select data from table X and loop trough
	// 			  it while for each row also looping through table Y building a big INSERT-statement.
	db_MAIN("DROP TABLE IF EXISTS `prop-ingr`;");
	db_MAIN( "CREATE TABLE IF NOT EXISTS `prop-ingr`
			(`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			 `artikel_id` INT NOT NULL,
			 `property_id` INT NOT NULL,
			 `value` VARCHAR(20)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;" );
	$result = db_MAIN("SELECT `id`, `navn` FROM `idun_properties` ORDER BY `id` ASC");
	$q = "";
	if ( isset( $result ) )
	{
		while ( $row = $result->fetch_object() )
		{
			$result2 = db_MAIN("SELECT `id`, `artnr`, `" . $row->navn . "` AS `value` FROM `idun_tmp` WHERE `" . $row->navn . "` <> '' AND `id` > 1 AND LCASE(`prisliste`) <> 'nei' AND `prisliste` <> '' ORDER BY `id` ASC");
			if ( isset( $result2 ) )
			{
				$q = "INSERT INTO `idun_prop-ingr`(`artikel_id`,`property_id`,`value`) VALUES";
				while ( $row2 = $result2->fetch_object() )
				{
					$q .= "(" . $row2->id . "," . $row->id . ",'" . strtolower($row2->value) . "'), ";
				}
				$q = substr( trim($q), 0, -1) . ";";
				if ( substr( $q, -6) != "VALUE;" )
				{
					db_MAIN($q);
				}
			}
		}
		
	} else {
		echo "<p>ERROR: Inga kolumner funna</p>";
	}
	
	
	
	
// Database main function (does all the talking to the database class and handling of errors)
// This can be updated so that it don't let empty results through, just uncomment all comments =)
// ****************************************************************************	

	function db_MAIN($sql)
	{
		global $mysqli;
		$result = $mysqli->query( $sql );
		if ( $result )
		{

			// TODO:
			// Should fit it to work with this (maybe split this func up):
			// The mysqli_insert_id() function returns the ID generated by a query on a table with a column having the AUTO_INCREMENT attribute. If the last query wasn't an INSERT or UPDATE statement or if the modified table does not have a column with the AUTO_INCREMENT attribute, this function will return zero.
			// $mysqli->query($query);
			// printf ("New Record has id %d.\n", $mysqli->insert_id);

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