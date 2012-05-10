<?php
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
define('str_date_mysql',"Y-m-d H:i:s");
$f = new phpFlickr("5266d2fbe2b7d9fab0538ef263708089","1f4f0551d1e89fb2",true);

$_SESSION['phpFlickr_auth_token'] = $token = "72157623445896698-35fa4b35989d4582";


$myi = mysqli_connect('localhost','root','tj18','flickr');
if(!$myi)
    die('myi connect error');

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

$page_num = 1;

do
    {
        echo "\nsearching... $page_num";

        $results = $f->photos_search(
            array(
                'min_upload_date' => strtotime('2010-01-01')
                ,'user_id' => '69052655@N00'
                ,'extras' => 'date_upload'
                ,'page' => $page_num
                ));

        foreach($results['photo'] as $p)
        {
            $dt = date(str_date_mysql,$p['dateupload']);
            $title = $p['title'];
            doquery("insert into uploads(dateuploaded,title) values('{$dt}','{$title}')");
        }
        $page_num++;
    }
while(count($results['photo'])>0);

//$results = $f->photos_getRecent(NULL,100,1);
//min_upload_date

//print_r($results);

echo "last photo: " . date(str_date_mysql,$results['photo'][99]['dateupload']) . "\n";

?>
