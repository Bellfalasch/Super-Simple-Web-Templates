<?php

	function db2_searchInvoice($in) { cleanup($in);
		return db_MAIN("
			SELECT o.*, c.*
			FROM `orders` o
			LEFT OUTER JOIN `customers` c
			ON c.id = o.customer_id
			WHERE
			o.invoice_no <> '' AND
			(
				c.`firstname` LIKE {$in['name']}
			OR	c.`lastname` LIKE {$in['name']}
			OR	c.`street1` LIKE {$in['address']}
			OR	c.`street2` LIKE {$in['address']}
			OR	c.`mail` LIKE {$in['mail']}
			OR	LEFT(o.`dibs_date`,10) LIKE {$in['date']}
			OR	o.`invoice_no` LIKE {$in['invoice']}
			OR	o.`dibs_transid` LIKE {$in['transaction']}
			)
			ORDER BY o.`dibs_date` DESC
		;");
	}

	/**
	 * Hæmta precis alla fakturor (som betalats) och sammanstæll dem per månad før øverblick och revisorn.
	 * @return mysqli->query			fields: xxx
	 */
	function db_getInvoicesStatsAll() {
		return db_FIND("
			SELECT
				SUM(o.`sum`) AS totalt,
				SUM(o.`mva`) AS mvan,
				SUM(o.`sum`) - SUM(o.`mva`) AS sale,
				DATE_FORMAT(o.`dibs_date`,'%Y') AS ar,
				DATE_FORMAT(o.`dibs_date`,'%m') AS manad,
				COUNT(*) AS antal
			FROM `orders` o
			WHERE o.`invoice_no` IS NOT NULL
			GROUP BY
				DATE_FORMAT(o.`dibs_date`,'%Y-%m')
			ORDER BY
				YEAR(o.`dibs_date`) DESC,
				MONTH(o.`dibs_date`) ASC
		");
	}
	function db_getInvoicesStatsMonth($in) { cleanup($in);
		return db_FIND("
			SELECT
				SUM(o.`sum`) AS totalt,
				SUM(o.`mva`) AS mvan,
				SUM(o.`sum`) - SUM(o.`mva`) AS sale,
				DATE_FORMAT(o.`dibs_date`,'%Y') AS ar,
				DATE_FORMAT(o.`dibs_date`,'%m') AS manad,
				DATE_FORMAT(o.`dibs_date`,'%d') AS dag,
				COUNT(*) AS antal
			FROM `orders` o
			WHERE (o.`invoice_no` IS NOT NULL)
			AND DATE_FORMAT(o.`dibs_date`,'%Y') = {$in['year']}
			AND DATE_FORMAT(o.`dibs_date`,'%m') = {$in['month']}
			GROUP BY
				DATE_FORMAT(o.`dibs_date`,'%Y-%m-%d')
			ORDER BY
				DAY(o.`dibs_date`) ASC
		");
	}
	function db_getInvoicesStatsDay($in) { cleanup($in);
		return db_MAIN("
			SELECT o.`sum` AS totalt, o.`mva` AS mvan, o.`sum` - o.`mva` AS sale,
			o.`invoice_no`, o.`dibs_date`, o.`dibs_transid`, o.`id` AS cart_id
			FROM `orders` o
			WHERE (o.`invoice_no` IS NOT NULL)
			AND DATE_FORMAT(o.`dibs_date`,'%Y') = {$in['year']}
			AND DATE_FORMAT(o.`dibs_date`,'%m') = {$in['month']}
			AND DATE_FORMAT(o.`dibs_date`,'%d') = {$in['day']}
			ORDER BY o.`dibs_date` ASC
		");
	}
	function db_getInvoice($in) { cleanup($in);
		return db_MAIN("
			SELECT o.`sum`, o.`mva`, o.cart_id, o.`invoice_no`, o.`dibs_date`, o.`dibs_transid`, o.`id` AS order_id, c.*
			FROM `orders` o
			LEFT OUTER JOIN `customers` c
			ON c.id = o.customer_id
			WHERE o.`invoice_no` = {$in['invoice']}
		");
	}

	// Get every credit note for a selected invoice
	function db2_getInvoiceCredits($in) { cleanup($in);
		return db_MAIN("
			SELECT c.id, c.`date`, c.sum, c.mva, c.note, c.order_id, c.credit_no, u.mail
			FROM `order_credit` c
			LEFT OUTER JOIN users u
			ON u.id = c.user_id
			WHERE c.`order_id` = {$in['order']}
		");
	}
	function db2_createInvoiceCredits($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `order_credit`(`order_id`,`sum`,`mva`,`note`,`credit_no`,`user_id`)
			VALUES(
				{$in['order']},
				{$in['sum']},
				{$in['mva']},
				{$in['note']},
				{$in['credit_no']},
				{$in['user_id']}
			)
		");
	}
	// Each credit note needs a unique id-number that doesn't contain holes, find the current highest value.
	function db2_getHighestCreditNo() {
		return db_MAIN("
			SELECT MAX(`credit_no`) AS `creditno`
			FROM `order_credit`
		");
	}
	function db2_getInvoicesFromSpan($in) { cleanup($in);
		return db_MAIN("
			SELECT o.`sum`, o.`mva`, o.`invoice_no`, o.`dibs_date`, o.`dibs_transid`, o.`id`
			FROM `orders` o
			WHERE (o.`invoice_no` IS NOT NULL)
			AND o.`dibs_date` BETWEEN {$in['from']} AND {$in['to']}
			ORDER BY o.`dibs_date` ASC
		");
	}
	function db2_getCreditsFromSpan($in) { cleanup($in);
		return db_MAIN("
			SELECT c.id, c.`date`, c.sum, c.mva, c.note, c.order_id, c.credit_no, o.invoice_no
			FROM order_credit c
			LEFT OUTER JOIN `orders` o
			ON o.id = c.order_id
			WHERE (o.`invoice_no` IS NOT NULL)
			AND c.`date` BETWEEN {$in['from']} AND {$in['to']}
			ORDER BY c.`date` ASC
		");
	}

	////////////////// DISCOUNTS //////////////////////

	function db2_getDiscounts() {
		return db_MAIN("
			SELECT `id`, `title`, `code`, `start`, `stop`, `percentage`, `info`
			FROM `cart_codes`
			ORDER BY `id` DESC
		");
	}
	function db2_getDiscountsActive() {
		return db_MAIN("
			SELECT `id`, `title`, `code`, `start`, `stop`, `percentage`, `info`
			FROM `cart_codes`
			WHERE NOW() BETWEEN `start` AND `STOP`
			ORDER BY `id` DESC
		");
	}
	function db2_getDiscountsInactive() {
		return db_MAIN("
			SELECT `id`, `title`, `code`, `start`, `stop`, `percentage`, `info`
			FROM `cart_codes`
			WHERE NOW() NOT BETWEEN `start` AND `STOP`
			ORDER BY `id` DESC
		");
	}
	function db2_getDiscount($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `title`, `code`, `start`, `stop`, `percentage`, `info`
			FROM `cart_codes`
			WHERE id = {$in['id']}
		");
	}
	function db2_updateDiscount($in) { cleanup($in);
		return db_MAIN("
			UPDATE `cart_codes`
			SET
				`title` = {$in['title']},
				`code` = {$in['code']},
				`start` = {$in['start']},
				`stop` = {$in['stop']},
				`info` = {$in['info']}
			WHERE `id` = {$in['id']}
		");
	}
	function db2_createDiscount($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `cart_codes`
				(`title`,`code`,`start`,`stop`,`percentage`,`info`)
			VALUES(
				{$in['title']},
				{$in['code']},
				{$in['start']},
				{$in['stop']},
				{$in['percentage']},
				{$in['info']}
			)
		");
	}
	function db2_delDiscount($in) { cleanup($in);
		return db_MAIN("
			DELETE FROM `cart_codes`
			WHERE `id` = {$in['id']}
		");
	}




	////////////////// USERS //////////////////////

	function db_getUserLoginInfo($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `username`, `password`, `mail`, `level`
			FROM `users`
			WHERE `username` LIKE {$in['username']}
			OR `mail` LIKE {$in['username']}
			LIMIT 1
		;");
	}
	function db_getUsers() {
		return db_MAIN("
			SELECT `id`, `username`, `password`, `mail`, `level`
			FROM `users`
			ORDER BY `id` DESC
		");
	}
	function db_getUser($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `username`, `password`, `mail`, `level`
			FROM `users`
			WHERE id = {$in['id']}
		");
	}
	function db_setUpdateUser($in) { cleanup($in);
		return db_MAIN("
			UPDATE `users`
			SET
				`mail` = {$in['mail']},
				`password` = {$in['password']},
				`level` = {$in['level']}
			WHERE `id` = {$in['id']}
		");
	}
	function db_setUser($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `users`
				(`username`,`salt`,`mail`,`password`,`level`)
			VALUES(
				'',
				'',
				{$in['mail']},
				{$in['password']},
				{$in['level']}
			)
		");
	}
	function db_delUser($in) { cleanup($in);
		return db_MAIN("
			DELETE FROM `users`
			WHERE `id` = {$in['id']}
		");
	}

?>