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
			"Møre og Romsdal",
			"Nordland",
			"Nord-Trøndelag",
			"Oppland",
			"Oslo",
			"Rogaland",
			"Sogn og Fjordane",
			"Sør-Trøndelag",
			"Telemark",
			"Troms",
			"Vest-Agder",
			"Vestfold",
			"Østfold"
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