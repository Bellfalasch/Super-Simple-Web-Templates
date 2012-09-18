<!-- footer -->
	</article>

	<aside>
		<h1>Aside</h1>
		<p>Aside</p>
	</aside>

	<footer>
		Footer
	</footer>

	<?php
		if (DEV_ENV) {
			printDebugger();
		}
	?>
	
	<!-- !!!ANALYTICS!!! -->
</body>
</html>
<?php
// Close database
// ****************************************************************************	

	$mysqli->close();


// END FILE
// ****************************************************************************
?>