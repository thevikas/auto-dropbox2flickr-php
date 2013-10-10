doq
<?php
#202.56.215.54, 202.54.215.55
/* Last updated with phpFlickr 1.3.2
 *
 * This example file shows you how to call the 100 most recent public
 * photos.  It parses through them and prints out a link to each of them
 * along with the owner's name.
 *
 * Most of the processing time in this file comes from the 100 calls to
 * flickr.people.getInfo.  Enabling caching will help a whole lot with
 * this as there are many people who post multiple photos at once.
 *
 * Obviously, you'll want to replace the "<api key>" with one provided
 * by Flickr: http://www.flickr.com/services/api/key.gne
 */

require_once("phpFlickr.php");

define('MIN_SPACE_NEEDED_MB',100);


$settings = parse_ini_file("settings.ini");
$f = new phpFlickr($settings['apikey'],$settings['secret'],true);
$token = $settings['user-token'];

$is_public = $settings['access-public'];
$is_friend = $settings['access-friend'];
$is_family = $settings['access-family'];

echo "Destination Directory :" . $settings['destination-directory'];
$space = disk_free_space($settings['destination-directory']);
echo " (" . round($space/(1024*1024)) . " MB free space) \n";

check_space($settings['destination-directory']);

function check_space($dest_folder)
{
    $space = disk_free_space($dest_folder);
    $left_space_MB = round($space/(1024*1024));
    if($left_space_MB < MIN_SPACE_NEEDED_MB)
    {
        echo "Too little ($left_space_MB MB) space left in destination. Won't proceed\n";
        die;
    }
}

function doquery($sql)
{
    global $myi;
    $r = $myi->query($sql);
    if(!$r)
    {
        echo $myi->error . " in " . $sql;
        die;
    }
    return $r;
}

#for - first time account authentication
#$r = $f->auth2("write");
#echo $r . "\n";
#die;

#the frob that was granted access
#$frob = "72157623321360871-6875d6cb3e8d800e-543758";
#$r = $f->auth_getToken($frob);
#print_r($r);
#die;

$_SESSION['phpFlickr_auth_token'] = $token;

$myi = mysqli_connect($settings['mysql_host'],$settings['mysql_user'],$settings['mysql_pass'],$settings['mysql_db']);
if(!$myi)
    die('myi connect error');


echo "starting...";

#$dir = "/media/cdrive/Photos/triund3";
#$dir = "/media/cdrive/Photos/china_island";
#$dir = "/media/cdrive/Photos/nainital";
#$dir = "/media/cdrive/Photos/kareri";
#$dir = "/media/cdrive/Photos/Manali-Beas-Source";
#2011-nov
# "/home/vikas/Pictures/from-Arc-1"

#2012-may
$dir = $settings['pictures-directory'];

$ignored=0;
if(true)
{
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
    $sorted_files = array();
    /* This is the correct way to loop over the directory. */
    /*while (false !== ($file = readdir($handle)))
    {
        $sorted_files[] = $file;
    }*/
    $test2 = `/usr/bin/find '$dir'`;
    $sorted_files = explode("\n",$test2);
    echo "found . " . count($sorted_files);
    
    echo "found files ($dir):" . count($sorted_files) . "\n";
    //print_r($sorted_files);
    sort($sorted_files);

    foreach($sorted_files as $file)
    {
        //search the file in db
        $ff = explode('.',$file);
        $r = doquery("select * from uploads where title='" . $ff[0] . "'");
        $rs = $r->fetch_object();
        if($rs)
        {
            //print_r($rs);
            echo $ff[0] . " already uploaded ($ignored ignored).\n";
            $ignored++;
            continue;
        }
        if(!strstr(strtoupper($file),"JPG"))
            continue;
        if(strstr(strtoupper($file),"JPG.UPLOADED") !== FALSE)
            continue;
        if(strstr($file,"original"))
        {
            echo "$file ignored...\n";
            continue;
        }
        
        //if found, ignore it
        if(!file_exists($file))
            $filepath = "$dir/$file";
        else
            $filepath = $file;
        
        if(is_dir($filepath)) 
            continue;
        echo "uploading $filepath...";
        $ll = getlonglat($filepath);



        $st = time();
        
        if(file_exists($filepath . ".uploaded"))
        {
            echo ",file already uploaded\n";
            continue;
        }
        //sync_upload ($photo, $title = null, $description = null, $tags = null, $is_public = null, $is_friend = null, $is_family = null) {
        $rt = $f->sync_upload ($filepath,null,               null,         null, $is_public,        $is_friend,        $is_family);

        if(!$rt)
        {
            echo "Failed.\n";
            continue;
        }

        $et = time();
        $spent = $et - $st;
        if(file_exists($filepath . ".uploaded"))
        {
            echo ",file already uploaded\n";
            continue;
        }
        else if('move' == $settings['after_copy'])
        {
            rename($filepath,$settings['destination-directory'] . "/$file");
            echo ",file moved";
        }
        else if('resize' == $settings['after_copy'])
        {
            echo ",resizing from " . filesize($filepath);
            resizeimage($filepath);
            file_put_contents($filepath . ".uploaded","uploaded on " . date('Y-m-d H:i:s'));
            clearstatcache();
            
            echo " to " . filesize($filepath) . " ";
        }

        check_space($settings['destination-directory']);

        if($rt>0)
        {
            if(isset($ll['GPSLatitude']))
            {
                $f->photos_geo_setLocation($rt,$ll['GPSLatitude'],$ll['GPSLongitude']);
                echo "geotagged (" . $ll['GPSLatitude'] . "," . $ll['GPSLongitude'] . ")...";
            }
        }
        echo " upload took $spent seconds\n";
    }

}


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
    return $ss[0];
}

function resizeimage($filename)
{
    //
    $image = new Imagick( $filename );
    if($image)
    {
        $height=$image->getImageHeight();
        $width=$image->getImageWidth();
    
        if ($height > $width)
            $image->scaleImage( 600 , 800 ,  true );
        else
            $image->scaleImage( 800 , 600 , true );
    
        $image->writeImage( $filename );
        $image->destroy();
    }
}

?>
