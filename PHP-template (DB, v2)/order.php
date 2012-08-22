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
				// Save to database
				// Redirect to next step

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