<?php
	/* Set up template variables */
	$pagetitle = 'Admin/Start';
	ob_start();
?>
<?php require('_header.php'); ?>
<?php
	ob_clean();
	header('Location: ' . $SYS_folder . '/_admin/login.php');
?>


	<div class="page-header">
		<h1>
			Smartlapper admin
			<small>For administrating</small>
		</h1>
	</div>
<!--
	<div class="row">
		<div class="span12">
			<ul>
				<li>Login in (sjukt enkel, men kunna portas lætt till framtida system - addon:able)</li>
				<li>Lægg upp video (rubrik, beskrivning, kategori, bo/upplev, bilde, vimeo-id)</li>
				<li>Lasta upp videobild som skalar till alla format</li>
				<li>Knyt lænkar till video (helst smidig ajax)</li>
				<li>Skapa kategorier</li>
				<li>Enkel logout</li>
				<li>Databasstruktur</li>
				<li>Superadmin kan lasta upp filmer, brukaren kan bara redigera dem (nivåsystem)</li>
				<li>Fas 2: saltat passord, glømt løsenord, etc</li>
				<li>Enkel egen-admin (ændra epostadress, username, passord)</li>
				<li>Login på e-postadress (ikke bruk username)?</li>
			</ul>
		</div>
	</div>
-->

<?php require('_footer.php'); ?>