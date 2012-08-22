<?php
	/* Set up template variables */
	$PAGE_title = 'Bestill';
	$PAGE_desc = '';
?>
<?php require('_header.php'); ?>


	<div class="row">
		<div class="span12">
	
		<?php

			$strFornavn = formGet("fornavn");
			$strEtternavn = formGet("etternavn");
			$strPostnr = formGet("postnr");

			// If data posted
			if ( formGet("submit") != '' ) {

				// Validate data
				// Show possible errors inline
				pushError('Produkt eksisterer ikke i databasen.');

				// Save to database
				if (!empty($_SESSION['ERRORS'])) {
				
					outputErrors($_SESSION['ERRORS']);

				} else {
				
					// Save to database
					// Example:
					/*
						$putincart = db_setPutProductInCart( array(
										'smartlapper_id' => $strFornavn,
										'smartlapper_id' => $strEtternavn
									 ) );

						// Denna ger inget inserted_id eftersom tabellen inte har någon autoincrement, den ger -1 eller 0 før fail resp. success
						if ($putincart >= 0) {
							pushDebug("<p>Created data in `carts_has_smartlapper` (join-table, no id).</p>");
						} else {
							pushError_tran("db_setPutProductInCart() couldn't create a new Product in the shopping Cart for this session.");
						}
					*/

					// Redirect to next step
					if (empty($_SESSION['ERRORS_TRAN']) && empty($_SESSION['ERRORS'])) {
						$redirectme = true;
					}

				}

			}

		?>

			<div class="page-header">
				<h1>
					Få MORGENBLADET gratis i 3 uker!
				</h1>
			</div>

			<p>
				Kjære XXX<br />
				YYY vil gjerne at du skal motta Morgenbladet gratis i tre uker.<br />
				For å sikre levering av Morgenbladet, ber vi om at du fyller i feltene under:
			</p>
			
			<form method="post" action="">

				<div class="fieldgroup">
					<label for="tbxFornavn">Fornavn:</label>
					<input type="text" id="tbxFornavn" name="fornavn" value="<?= $strFornavn ?>" />
				</div>

				<div class="fieldgroup">
					<label for="tbxEtternavn">Etternavn:</label>
					<input type="text" id="tbxEtternavn" name="etternavn" value="<?= $strEtternavn ?>" />
				</div>

				<div class="fieldgroup">
					<label for="tbxPostnr">Postnr:</label>
					<input type="text" id="tbxPostnr" name="postnr" value="<?= $strPostnr ?>" />
				</div>

				<!-- SHOW+USE ONLY WHEN ACTIVATED IN ADMIN -->
				<p>
					<em>Dine personalia hentes automatiskt når du oppgir navn og postnummer.</em>
				</p>

				c/o:<br />
				Poststed:<br />
				Gate/vei:<br />
				Husnummer:<br />
				Oppgang:<br />
				Etasje:<br />
				Leilighet:<br />
				Mobil:<br />
				Telefon:<br />
				E-post:<br />
				
				[]	Morgenbladet forbeholder seg retten til å kontakte deg etter endt gratisperiode.<br />
					Dette tilbudet gjelder nye abonnenter i Norge som ikke har vært abonnent de siste 12 mnd.

				<input type="submit" name="submit" value="Send inn &raquo;" />

			</form>

		</div>
	</div>


<?php require('_footer.php'); ?>