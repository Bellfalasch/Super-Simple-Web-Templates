<?php
	/* Set up template variables */
	$PAGE_title = 'Takk for bestilling';
	$PAGE_desc = '';
?>
<?php require('_header.php'); ?>


	<div class="row">
		<div class="span12">
	
		<?php

			// Hæmta från databasen baserat på session-id senaste uppslaget med vervningar

		?>

			<div class="page-header">
				<h1>
					Snart får du Morgenbladet i posten!
				</h1>
			</div>

			<p>
				<strong>Vi sender deg Morgenbladet gratis i 3 uker. Vi har notert oss følgende informasjon:</strong>
			</p>

			<p>
				<strong>Fornavn:</strong> xxx<br />
				<strong>Etternavn:</strong> xxx<br />
				<strong>E-post:</strong> xxx<br />
				<strong>c/o:</strong> xxx<br />
				<strong>Poststed:</strong> xxx<br />
				<strong>Gate/vei:</strong> xxx<br />
				<strong>Husnummer:</strong> xxx<br />
				<strong>Oppgang:</strong> xxx<br />
				<strong>Etasje:</strong> xxx<br />
				<strong>Leilighet:</strong> xxx<br />
				<strong>Mobil:</strong> xxx<br />
				<strong>Telefon:</strong> xxx<br />
				<strong>E-post:</strong> xxx
			</p>

			<p>
				<a href="<?= $SYS_folder ?>/friends.php">Verv dine venner her</a>
			</p>

		</div>
	</div>


<?php require('_footer.php'); ?>