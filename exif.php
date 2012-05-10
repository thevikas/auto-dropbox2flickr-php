<?php

$dir = "/media/fa/pune 2010";
$fn= "IMG_4726.JPG";

$filename = "$dir/$fn";
#$jpeg = new PelJpeg($filename);
#$exif = $jpeg->getExif();

function getlonglat($filename)
{
	$long = array();
	$ar = exif_read_data($filename);
	foreach($ar as $key=>$v)
	{
		if($key == 'GPSLatitude' || $key == 'GPSLongitude')
		{
			$deg = s2($v[0]);
			$min = floatval(s2($v[1]) . "." . s2($v[2])); 
			$dd = $deg + $min/60;
			$long[$key] = $dd;
			continue;
		}
	}
	return $long;
}

function s2($v)
{
	$ss = explode('/',$v);
	print_r($ss);
	return $ss[0];
}

?>

