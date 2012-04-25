<label for="countySelect">Velg fylke:</label>
<select id="countySelect" name="countySelect">
	<option value="0">Inget fylke valt</option>
	<?php
		$arrCounty = array(
			"Akershus",
			"Aust-Agder",
			"Buskerud",
			"Finnmark",
			"Hedmark",
			"Hordaland",
			"M�re og Romsdal",
			"Nordland",
			"Nord-Tr�ndelag",
			"Oppland",
			"Oslo",
			"Rogaland",
			"Sogn og Fjordane",
			"S�r-Tr�ndelag",
			"Telemark",
			"Troms",
			"Vest-Agder",
			"Vestfold",
			"�stfold"
		);
		
		foreach ($arrCounty as $theCounty)
		{
			if ( $strCounty === $theCounty ) {
				$countyChecked = ' checked="checked"';
			} else {
				$countyChecked = '';
			}
			echo '<option value="' . $theCounty . '"' . $countyChecked . '>' . $theCounty . '</option>';
		}
	?>
</select>