<?php
// Database setup (MySQL)
// ****************************************************************************	
	
	// Set constants for db-access after environment
	if ($_SERVER['SERVER_NAME'] == 'localhost')
	{	// LOCAL
		DEFINE('DB_USER', 'root');				// Username for database
		DEFINE('DB_PASS', '');					// Password for database
		DEFINE('DB_HOST', 'localhost');			// Server for database
		DEFINE('DB_NAME', 'test');				// Select database on server
	} else {
		// LIVE (change to your settings)
		DEFINE('DB_USER', 'xxx');
		DEFINE('DB_PASS', 'xxx');
		DEFINE('DB_HOST', 'localhost');
		DEFINE('DB_NAME', 'database');
	}
	
	// Set up database class
	global $mysqli;
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	if (mysqli_connect_errno()) { die("<p>" . mysqli_connect_errno() . " - Can't connect to this database or server =/</p>"); }
	$mysqli->set_charset('utf8');




// Prepared SQL-functions extracting data from db.
// ****************************************************************************		

	/* EVERY FUNCTION HERE IS JUST AN EXAMPLE OF SYNTAX, SO DELETE EVERYTHING YOU DO NOT NEED! */
	/* SOME FUNCTIONS INCLUDE DOCUMENTATION IN FULL OR SMALL FORM (OR NONE), YOUR CHOICE =) */

	
	/**
	 * Get every existing product from the database, and all the information.
	 * @return mysqli->query			fields: `id`, `title`, `price`, `mva`, `amount_ST`, `amount_KL`, `amount_SK`, `information`
	 */
	function db_getProducts($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `title`, `price`, `mva`, `amount_ST`, `amount_KL`, `amount_SK`, `information`
			FROM `products`
			WHERE `active` = 1
			AND LEFT(`artno`,2) = {$in['artcode']}
			ORDER BY `price` ASC
		");
	}

	/**
	 * Get every existing product from the database, and all the information.
	 * @return mysqli->query			fields: `id`, `title`, `image_src`, `symbol_cats_id`
	 */
	function db_getSymbolsFromCat($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `title`, `image_src`, `symbol_cats_id`
			FROM `symbols`
			WHERE `symbol_cats_id` = {$in['catid']}
			AND `active` = 1
			ORDER BY 
				CASE WHEN `id` = 165 THEN 2 ELSE 1 END
			ASC, `title` ASC
		");
	}

	/**
	 * Connect one created nametag with the current sessions cart, thus putting a nametag you've made into your cart (for later buying).
	 * @param int carts_id 				The current users cart
	 * @param int smartlapper_id		The unique nametag the current user just now is trying to put in it's shoppingcart
	 * @param int product 				To find what price to store in the cart (for future tax references) we need the product id
	 * @return int mysql->insert_id
	 */
	function db_setPutProductInCart($in) { cleanup($in);
		return db_INSERT("
			INSERT INTO `cart_contains`
				(`cart_id`, `smartlapper_id`, `amount`)
			VALUES(
				{$in['cart_id']},
				{$in['smartlapper_id']},
				1
			)
		");
	}

	/**
	 * Save new address data for customer.
	 */
	function db_setNewAddress($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `customers`
			(`firstname`, `lastname`, `street1`, `street2`, `postal_code`, `city`, `telephone`, `mail`, `newsletter`, `updated`)
			VALUES(
				{$in['firstname']},
				{$in['lastname']},
				{$in['street1']},
				{$in['street2']},
				{$in['postal_code']},
				{$in['city']},
				{$in['telephone']},
				{$in['mail']},
				{$in['newsletter']},
				NOW()
			)
		");
	}

	/**
	 * Update address data for existing customer (current session).
	 */
	function db_setUpdateAddress($in) { cleanup($in);
		return db_MAIN("
			UPDATE `customers`
			SET
				`firstname` = {$in['firstname']},
				`lastname` = {$in['lastname']},
				`street1` = {$in['street1']},
				`street2` = {$in['street2']},
				`postal_code` = {$in['postal_code']},
				`city` = {$in['city']},
				`telephone` = {$in['telephone']},
				`mail` = {$in['mail']},
				`newsletter` = {$in['newsletter']},
				`updated` = NOW()
			WHERE `id` = {$in['id']}
		");
	}

	// Get the number of products, and the total price, from current shopping cart.
	function db_getMiniCartOverview($in) { cleanup($in);
		return db_MAIN("
			SELECT
				SUM(sc.`price`) AS `total`,
				SUM(sc.`mva`) AS `taxes`,
				COUNT(*) AS `products`
			FROM `smartlapp_contains` sc
			LEFT OUTER JOIN `cart_contains` cc
			ON cc.`smartlapper_id` = sc.`smartlapper_id`
			WHERE cc.`cart_id` = {$in['cart']}
			AND cc.`amount` > 0
			AND sc.`price` IS NOT NULL
		;");
	}
	function db_getPrintBatchInvoiceInfo($in) { cleanup($in);
		return db_MAIN("
			SELECT o.*,
				c.firstname, c.lastname, c.street1, c.street2, c.postal_code, c.city
			FROM `orders` o
			LEFT OUTER JOIN `batch_contains` bc ON bc.`orders_id` = o.`id`
			LEFT OUTER JOIN `customers` c ON c.`id` = o.`customer_id`
			WHERE bc.`batches_id` = {$in['batch']}
		;");
	}
	function db_getPrintBatchInvoiceProducts($in) { cleanup($in);
		return db_MAIN("
			SELECT p.id AS pid, p.artno, p.title, sc.price AS sum, sc.mva,
			s.line1, ss.image_src,
			cc.cart_id, s.id AS sid, s.symbol_id,
			p.amount_KL, p.amount_ST, p.amount_SK
			FROM smartlapp_contains sc
			LEFT OUTER JOIN products p ON sc.products_id = p.id
			LEFT OUTER JOIN smartlapper s ON sc.smartlapper_id = s.id
			LEFT OUTER JOIN cart_contains cc ON cc.smartlapper_id = s.id
			LEFT OUTER JOIN symbols ss ON ss.id = s.symbol_id
			WHERE cc.cart_id = {$in['cartid']}
		");
	}
	// Temp-SQL, den hæmtar alla ordrar som borde printas och prøvar att laga PDF før dem.
	function db_getEveryInvoice() {
		return db_MAIN("
			SELECT o.cart_id
			FROM `orders` o
			WHERE `invoice_no` IS NOT NULL
			ORDER BY id DESC
		;");
	}

	function db_setDropSmartlappFromCart($in) { cleanup($in);
		return db_MAIN("
			DELETE FROM `smartlapp_contains`
			WHERE `smartlapper_id` = {$in['smartlapp']}
			AND `products_id` = {$in['product']}
			AND `smartlapper_id` = (SELECT `smartlapper_id` FROM `cart_contains` WHERE `cart_id` = {$in['cart']} AND `smartlapper_id` = {$in['smartlapp']})
			LIMIT 1
		;");
	}
	function db_setPrintPutInBatch($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `batch_contains`(`batches_id`, `orders_id`)
			VALUES({$in['batch']}, {$in['order']})
		;");
	}

	function db_setOrderTotalPrice($in) { cleanup($in);
		return db_MAIN("
			UPDATE `orders`
			SET `sum` = {$in['sum']}, `mva` = {$in['mva']}
			WHERE `cart_id` = {$in['cart']}
		;");
	}

	// Anvænds inte længre efter att cart_contains slutat logga price och mva =)
	function db_setNewPricesOnCart($in) { cleanup($in);
		return db_MAIN("
			UPDATE `cart_contains`
			SET
				`price` = (SELECT SUM(`price`) FROM `smartlapp_contains` WHERE `smartlapper_id` = {$in['smartlapp']}),
				`mva` = (0.25 * (SELECT SUM(`price`) FROM `smartlapp_contains` WHERE `smartlapper_id` = {$in['smartlapp']}) )
			WHERE `cart_id` = {$in['cart']}
			AND `smartlapper_id` = {$in['smartlapp']}
		;");
	}




// Database main functions (does all the talking to the database class and handling of errors)
// ****************************************************************************	

	function db_FIND($sql)
	{
		global $mysqli;
		$result = $mysqli->query( $sql );
		if ( $result )
		{
			if ($result->num_rows > 0) {
				return $result;
			} else {
				return null;
			}
		} else {
			db_printError($mysqli->error, $mysqli->errno, $sql);
			return null;
		}
	}

	// Run SQL that doesn't return a dataset, and return inserted id
	function db_INSERT($sql)
	{
		global $mysqli;
		$result = $mysqli->query($sql);
		if ( $result )
			return $mysqli->insert_id;
		else {
			db_printError($mysqli->error, $mysqli->errno, $sql);
			return -1;
		}
	}

	function db_EXEC($sql)
	{
		global $mysqli;
		$result = $mysqli->query($sql);
		if ( $result )
			return $mysqli->affected_rows;
		else {
			db_printError($mysqli->error, $mysqli->errno, $sql);
			return -1;
		}
	}

	// Enklare db-brygga som væljer SQL-funktion baserat på typ av SQL man skickar in (førutsætter att man ej blandar typer).
	// Anledningen till de olika versionerna ær att de returnerar olika saker och datatyper.
	function db_MAIN($sql)
	{
		switch(substr(trim($sql),0,6))
		{
			case "SELECT":
				return db_FIND($sql);
				break;

			case "UPDATE":
				return db_EXEC($sql);
				break;

			case "DELETE":
				return db_EXEC($sql);
				break;

			case "INSERT": 
				return db_INSERT($sql);
				break;

			// Skulle mot førmodan någon annan SQL-typ skickas in så chansa på att det ær någon form av SQL som returnerar ett dataset (som SELECT).
			default:
				return db_FIND($sql);
				break;
		}
	}

	// Enkel felhantering
	function db_printError($error_msg, $error_no, $sql)
	{
		echo "
		<div class='errors'>
			<p>
				There has been an error from MySQL: (", $error_no, ") ", $error_msg, ".
			</p>
			<code>", nl2br($sql), "</code>
		</div>";
	}



// Helper functions for SQL
// ****************************************************************************	

	// Aktivera transaction-hantering
	function db_doBeginTran()
	{
		global $mysqli;
		$mysqli->autocommit(false);

		$_SESSION['ERRORS_TRAN'] = array();
	}

	// Gør commit eller rollback, och stæng av transaction-hantering
	function db_doEndTran()
	{
		global $mysqli;

		if (!empty($_SESSION['ERRORS_TRAN'])) {
			$mysqli->rollback();
		}
		$mysqli->commit();
		
		// Reset autocommit to true (only the SQL's just before needed transaction support)
		$mysqli->autocommit(true);

		$_SESSION['ERRORS_TRAN'] = null;
	}

	// db_EXEC-funktioner skickar in en array som "tvættas" med quote_smart i denna funktion
	function cleanup(&$in)
	{
		foreach($in as $key => $value) {
			$in[$key] = quote_smart($value);
		}
	}

	// Smart stræng/int-hantering før SQL:er
	// http://norskwebforum.no/viewtopic.php?p=243716
	function quote_smart($value)
	{
		global $mysqli;
		
		if ( get_magic_quotes_gpc() && !is_null($value) ) {
			$value = stripslashes($value);
		}
		if ( is_numeric($value) && strpos($value,',') !== false ) {
			$value = str_replace(',', '.', $value);
		}
		if ( is_null($value) ) {
			$value = 'NULL';
		}
		elseif ( !is_numeric($value) ) {
			$value = "'" . $mysqli->real_escape_string($value) . "'";
		}
		return $value;
	}
?>