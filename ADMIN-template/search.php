<?php
	/* Set up template variables */
	$pagetitle = 'Admin/Invoice search';
	ob_start();
?>
<?php require('_header.php'); ?>


	<div class="page-header">
		<h1>
			Search
			<small>Find your invoice</small>
		</h1>
	</div>

	<form class="form-horizontal" action="" method="get">

		<div class="row">
			<div class="span12">
				<p>
					Fill in as much, or as little, information that you know. If some character to search for is unknowned 
					you can use the underscore (_) to match any character. For instance, searching for a invoice number but you
					have lost the last digit, can be done with "1001_".
				</p>
				<p>
					Or you could use the more powerful wildcard to replace any number of characters (even none). 
					This for instance - "%and%" - would find "Anders", "Sandy", and "Kristiansand".
				</p>
				<p>
					Everything you fill in will be searched for, the fields doesn't exclude each other
					(it's "OR" logic-based, so more fields filled in generates more results)!
				</p>
				<p>&nbsp;</p>

	<?php

		// Initiate all variables (formGet works even when no post is done, this makes the variables as empty strings, ready for use later anyway).
		$formInvoice = qsGet('invoice');
		$formTransaction = qsGet('transaction');
		$formDate = qsGet('date');
		$formMail = qsGet('mail');
		$formAddress = qsGet('address');
		$formName = qsGet('name');

		
		// A search was done, handle the data sent in
		if (qsGet('search') === 'search')
		{
			if ($formMail != '') {
				if (!isValidLengthString($formMail,5,255)) {
					pushError('Teksten i "E-mail" er for lang. Legg inn tekst med maks 255 tegn.');
				}
			}

			if (!isValidLengthString($formInvoice,0,10)) {
				pushError('Invoice-number too long');
			}
			if (!isValidLengthString($formTransaction,0,15)) {
				pushError('Transaction-number too long');
			}
			if (!isValidLengthString($formDate,0,10)) {
				pushError('Invalid length of date');
			}
			if (!isValidLengthString($formAddress,0,60)) {
				pushError('Address is too long');
			}
			if (!isValidLengthString($formName,0,60)) {
				pushError('Name is too long');
			}

			if (empty($_SESSION['ERRORS'])) {
				
				/*
				$formName
				if (instr(',', $formAddress) > 0)
				{
					$addressArr = explode(',', $formAddress);
				}
				*/

				$result = db2_searchInvoice( array(
							'invoice' => $formInvoice,
							'transaction' => $formTransaction,
							'date' => $formDate,
							'mail' => $formMail,
							'address' => $formAddress,
							'name' => $formName
						) );

				if (!is_null($result)) {
					//echo "Sparat!";
					echo '
						<table>
							<caption>Table of results for your search</caption>
							<thead>
								<tr>
									<th>Invoice_no</th>
									<th>Date</th>
									<th>Buyer</th>
									<th>Transaction</th>
									<th>Value</th>
								</tr>
							</thead>
							<tbody>
					';

					while ( $row = $result->fetch_object() )
					{
						echo "
								<tr>
									<td>
										<a href='invoice.php?id=" . $row->invoice_no . "'>" . $row->invoice_no . "</a>
									</td>
									<td>
										$row->dibs_date
									</td>
									<td>
										$row->firstname $row->lastname ($row->street1, $row->postal_code $row->city)
									</td>
									<td>
										$row->dibs_transid
									</td>
									<td>
										$row->sum NOK
									</td>
								</tr>
						";
					}

					echo '
							</tbody>
						</table>';

				} else {
					echo "<p>Inget resultat!</p>";
				}

			}

		}

	?>


	<?php
		// Output errors, if there are any (the function handles this) and send me a e-mail of this
		outputErrors($_SESSION['ERRORS']);
	?>

			</div>
		</div>

		<div class="row">
			<div class="span6">
				
				<div class="control-group">
					<?php
						$thisLabel = "Invoice-number";
						$thisId = "Invoice";
						$thisVar = $formInvoice;
						$thisDesc = "This is printed on every invoice in the top right part: 'Fakturanummer'. This is Smartlappers own internal invoice-number, the one used for accounting.";
						$thisLength = 10;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Dibs transaction ID";
						$thisId = "Transaction";
						$thisVar = $formTransaction;
						$thisDesc = "Also printed in the top right corner of every invoice is 'Transaksjonsnummer'. This is the number Dibs use to uniquely identify invoices.";
						$thisLength = 15;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Invoice date";
						$thisId = "Date";
						$thisVar = $formDate;
						$thisDesc = "(YYYY-MM-DD) The actual date printed on the invoice (might differ from time of purchase if done late at night, or from different timezone), printed as 'Fakturadato' in the top right corner.";
						$thisLength = 10;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
					</div>
				</div>

			</div>
			<div class="span6">

				<div class="control-group">
					<?php
						$thisLabel = "Buyer E-mail";
						$thisId = "Mail";
						$thisVar = $formMail;
						$thisDesc = "The buyers e-mail address.";
						$thisLength = 255;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Street address";
						$thisId = "Address";
						$thisVar = $formAddress;
						//$thisDesc = "Streetname for the buyer. If you at the same time also want to limit street to a specific city/town, please use the comma sign as a seperator. If this second parameter is used and it is numeric we will search for a zip code instead of a town name.";
						$thisDesc = "Name of the street the buyer ordered to.";
						$thisLength = 60;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Buyer Name";
						$thisId = "Name";
						$thisVar = $formName;
						//$thisDesc = "This will search both in the fields for firstname and lastname in the database, seperate each name with a space.";
						$thisDesc = "This will search both in the fields for firstname and lastname in the database as a full string. So a search for 'Bobby Westberg' will not return anything, but a search for 'Anders%' would find both firstname 'Anders' and lastname 'Andersson'.";
						$thisLength = 60;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

			</div>
		</div>

	</form>


<?php require('_footer.php'); ?>