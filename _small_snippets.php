<?php
	// Since I'm new to PHP it's very handy to keep small small snippets of code for common tasks
	// like this until I have them forged into my memory (badly damaged one ;P).
?>


<!-- Show content X, but switch to content Y after a certain date. -->
	<?php if (date('Y-m-d') >= strtotime("2012-03-09")) { ?>
		<a href="/y">Y</a>
	<?php } else { ?>
		<a href="/x">X</a>
	<?php } ?>