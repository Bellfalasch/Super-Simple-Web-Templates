<?php
// Collection of different usable functions from basically all over the place.


/*
 * @version 1.01
 * @author Ketil Stadskleiv <ks@akkreditering.net>
 * http://norskwebforum.no/viewtopic.php?p=243716
 * @param mixed $value
 * @return string
*/
function quote_smart ($value){
	if ( get_magic_quotes_gpc () && !is_null ($value) ) {
		$value = stripslashes ($value);
	}
	if ( is_numeric ($value) && strpos ($value,',') !== false ){
		$value = str_replace (',', '.', $value);
	}
	if ( is_null($value) ){
		$value = 'NULL';
	}
	elseif ( !is_numeric ($value) ) {
		$value = "'" . mysql_real_escape_string ($value) . "'";
	}
	return $value;
}


function Gen_Password ($MinLen = 7, $MaxLen = 10) {
	if ($MinLen < 7) {
		$MinLen = 7;
	}
 
	$NumChars = mt_rand ($MinLen, $MaxLen);
	$Password = '';
	$Pool = "23456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ_-";
	$PoolEnd = strlen ($Pool);
 
	for ($Run = 0; $Run <= $NumChars; ++$Run) {
		$Password .= substr ($Pool, mt_rand (0, $PoolEnd), 1);
	}
 
	return $Password;
}


function Gen_Salt ($MinLen = 6, $MaxLen = 10) {
	if ($MinLen < 6) {
		$MinLen = 6;
	}
 
	$NumChars = mt_rand ($MinLen, $MaxLen);
	$Salt = '';
	$Pool = array_merge (range (chr (32), chr (126)), range (chr (128), chr (254)));
	$PoolEnd = count ($Pool);
 
	for ($Run = 0; $Run < $NumChars; $Run++) {
		$Salt .= $Pool[mt_rand (0, $PoolEnd)];
	}
 
	return $Salt;
}


function ConvertSize ($size) {
	$i = 0;
	$iec = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	while ( ($size / 1024) > 1 ) {
		$size = $size / 1024;
		$i++;
	}
	return substr ($size, 0, strpos ($size, '.') + 2) . $iec[$i];
}


function Strip_filename ($filename) {
	$temp    = $filename;
	$temp    = strtolower($temp);
	
	$find    = array ('Ã¦' ,  'Ã¸', 'Ã¥' , 'Ã†' , 'Ã˜' , 'Ã…' , '  ', ' ');
	$replace = array ('ae', 'oe', 'aa', 'ae', 'oe', 'aa', ' ' , '_');
	$temp    = str_replace ($find, $replace, $temp);
		
	for ( $i = 0; $i < strlen ($temp); $i++ ) {
		if ( preg_match ('([0-9]|[a-z]|_)', $temp[$i]) ) {
			$result = $result . $temp[$i];
		}
	}
	
	$find    = array ('__');
	$replace = array ('_',);
	$result    = str_replace ($find, $replace, $result);
	
	return $result;
}


function Ago ($timestamp) {
	$difference = time () - $timestamp;
	$periods = array ('second(s)', 'minute(s)', 'hour(s)', 'day(s)', 'week(s)', 'month(s)', 'year(s)', 'decade(s)');
	$lengths = array ('60','60','24','7','4.35','12','10');
	
	for($j = 0; $difference >= $lengths[$j]; $j++)
	$difference /= $lengths[$j];
	$difference = round ($difference);
	
	if($difference != 1) $periods[$j].= '';
	$tekst = $difference . $periods[$j] . ' siden';
	
	return $tekst;
}


function getExtension ($str) {
	$i = strrpos ($str, '.');
	if ( !$i ) { return ''; }
	$l = strlen ($str) - $i;
	$ext = substr ($str, $i + 1, $l);
	return strtolower ($ext);
}


function createPic ($img_name, $filename, $new_w, $new_h) {
	 
	$ext = getExtension ($img_name);
	
	if ( !strcmp ('jpg', $ext) || !strcmp ('jpeg',$ext) ) {
		$src_img = imagecreatefromjpeg ($img_name);
	}
	if( !strcmp ('png', $ext) ) {
		$src_img = imagecreatefrompng ($img_name);
		imagealphablending ($src_img, false);
		imagesavealpha ($src_img, true);
	}
	if( !strcmp ('gif', $ext) ) {
		$src_img = imagecreatefromgif ($img_name);
		imagealphablending ($src_img, false);
		imagesavealpha ($src_img, true);
	}
	
	
	$old_x = imagesx ($src_img);
	$old_y = imagesy ($src_img);
		
	$ratio1 = $old_x / $new_w;
	$ratio2 = $old_y / $new_h;
	
	if ( $ratio1 > $ratio2 )	{
		$thumb_w = $new_w;
		$thumb_h = $old_y / $ratio1;
	} else {
		$thumb_h = $new_h;
		$thumb_w = $old_x / $ratio2;
	}
	
	$dst_img = imagecreatetruecolor ($thumb_w,$thumb_h);
	
	imagecopyresampled ($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y); 
	
	if ( !strcmp ('png', $ext) ) {
		imagepng ($dst_img, $filename, 0);
	}
	elseif ( !strcmp ('gif', $ext) ) {
		imagegif ($dst_img, $filename);
	} else {
		imagejpeg ($dst_img, $filename, 100);
	}
	
	imagedestroy ($dst_img); 
	imagedestroy ($src_img); 
}


function setTransparency($new_image,$image_source) {
		   
	$transparencyIndex = imagecolortransparent($image_source);
	$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
	
	if ($transparencyIndex >= 0) {
		$transparencyColor    = imagecolorsforindex($image_source, $transparencyIndex);   
	}
			   
	$transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
	imagefill($new_image, 0, 0, $transparencyIndex);
	imagecolortransparent($new_image, $transparencyIndex);
		   
} 


function cropPic ($cropped_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	
	$ext = getExtension ($image);
	
	$newImageWidth = ceil ($width * $scale);
	$newImageHeight = ceil ($height * $scale);
		
	if ( !strcmp ('gif', $ext) ) {
		$source = imagecreatefromgif ($image);
	}
	elseif( !strcmp ('png', $ext) ) {
		$source = imagecreatefrompng ($image);
	} else {
		# !strcmp ('jpg', $ext) || !strcmp ('jpeg',$ext)
		$source = imagecreatefromjpeg ($image);
	}
	
	$newImage = imagecreatetruecolor ($newImageWidth, $newImageHeight);
	
	setTransparency ($newImage, $source); 
		
	imagecopyresampled ($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
	
	if ( !strcmp ('png', $ext) ) {
		imagepng ($newImage, $cropped_image_name, 0);
	}
	elseif ( !strcmp ('gif', $ext) ) {
		imagegif ($newImage, $cropped_image_name);
	} else {
		imagejpeg ($newImage, $cropped_image_name, 100);
	}

	return $cropped_image_name;
}


function getHeight ($image) {
	$sizes = getimagesize ($image);
	$height = $sizes[1];
	return $height;
}


function getWidth ($image) {
	$sizes = getimagesize ($image);
	$width = $sizes[0];
	return $width;
}


function CutText ($str, $limit, $cont = false) {
    if ( $cont == true ) {
		return strlen ($str) > $limit ? substr ($str, 0, $limit - 3) . '...' : $str;
	} else {
		return substr ($str, 0, $limit);
	}
}


function Delete_tree ($dirname) {
    if ( !file_exists ($dirname) ) {
        return false;
    }
 
    if ( is_file ($dirname) || is_link ($dirname) ) {
        return unlink ($dirname);
    }
 
    $dir = dir ($dirname);
    while ( false !== $entry = $dir->read() ) {
        if ( $entry == '.' || $entry == '..' ) {
            continue;
        }
        Delete_tree ($dirname . DIRECTORY_SEPARATOR . $entry);
    }
 
    $dir->close();
    return rmdir ($dirname);
}


function Days_between_dates ($from, $to) {
	$fromDate = date ('Y-m-d', $from);
	$toDate = date ('Y-m-d', $to);

	$dateMonthYearArr = array ();
	$fromDateTS = strtotime ($fromDate);
	$toDateTS = strtotime ($toDate);
	
	for ($currentDateTS = $fromDateTS; $currentDateTS <= $toDateTS; $currentDateTS += (60 * 60 * 24)) {
		$dateMonthYearArr[] = $currentDateTS;
	}
	
	return $dateMonthYearArr;
}


/*
 * Validation functions
 * @author Hellkeepa
 * http://norskwebforum.no/pastebin/7609
*/

function Val_href ($String, $Length = '') {
	if ( $Length = "*" && $String == '' ) {
		return '';
	}
 
	if ( substr ($String, 0, 7) == "mailto:" ) {
		if ( Val_EMail (substr ($String, 7)) ) {
			return $String;
		}
		return false;
	}

	$RegExp = '#^(?:(?:http|https|dchub|ftp)://)?((?:[\\w\\pL-]+\\.)+[a-z\\pL]{2,5})((?:/[\\w\\%-]*)+(?:\\.\\w{1,6})*(\\?(?:[\\w-]+=[\\w-]+)?(?:\\&[\\w-]+=[\\w-]+)*\\&?)?)?\\z#ui';
	
	if ( preg_match ($RegExp, $String) ) {
		return $String;
	}
	return false;
}


function Val_email ($str) {
	$RegExp = "/^[a-zA-Z][\\w\\pL\\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\\w\\pL\\.-]*[a-zA-Z0-9]\\.[a-zA-Z][a-zA-Z\\.]*[a-zA-Z]\\z/u";  

	if (preg_match ($RegExp, $str)) {
		return $str;
	}
	return false;
}


function Val_ip ($str) {
	if (long2ip (ip2long ($str)) != $str) {
		return false;
	}
	return $str;
}


function Val_num ($String, $MaxLength = '+') {
	if ( $MaxLength == "*" && $String == '' ) {
		return '';
	}
 
	if ( is_int ($MaxLength) ) {
		$MaxLength = "{1,$MaxLength}";
	} elseif ( $MaxLength != "*" ) {
		$MaxLength = '+';
	}
 
	if ( preg_match ('/^\\d'.$MaxLength.'\\z/', $String) ) {
		return $String;
	}
	return false;
}


function Val_name ($String, $Extra = '', $MaxLength = '+') {
	if ($MaxLength == "*" && $String == '') {
		return '';
	}
 
	if (is_int ($MaxLength)) {
		$MaxLength = "{1,$MaxLength}";
	} elseif ($MaxLength != "*") {
		$MaxLength = '+';
	}
 
	$OKChars = addcslashes ($Extra, '_[]').'\\w\\pL \\.\\-';
 
	if (preg_match ('/^\\w['.$OKChars.']'.$MaxLength.'\\z/u', $String)) {
		return $String;
	}
	return false;
}

?>