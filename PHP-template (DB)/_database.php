<?php
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
?>