<?php
	// Since I'm new to PHP it's very handy to keep small small snippets of code for common tasks
	// like this until I have them forged into my memory (badly damaged one ;P).
?>


<!-- Show content X, but switch to content Y after a certain date. -->
	<?php if (date('Y-m-d') >= '2012-03-09') { ?>
		<a href="#yyyy">Y</a>
	<?php } else { ?>
		<a href="#xxxx">X</a>
	<?php } ?>
	
<?php
	// If text is empty return it as "null", otherwise return it with appended and prepended ' (for SQL)
	function addSlashesOrNull($text)
	{
		$text = trim($text);
		if ($text == '')
			return 'null';
		else
			return "'" . $text . "'";
	}
?>