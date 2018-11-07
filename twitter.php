<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit (3600);

$koneksi = mysqli_connect('localhost', 'root', 'root', 'tweet');
require_once 'vendor/autoload.php';
// require __DIR__ . '/vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

define('CONSUMER_KEY', '51q7c4mVBSIqajDhLKzpYFuJT');
define('CONSUMER_SECRET', 'FgbX7E7vu91LSULYrI9pdXaMWARbQFg5sTUeC2guJJoe2gnWJC');
define('ACCESS_TOKEN', '262052819-mX1QaRE1ESPZVO03sWkCp4Avf7Fl4Xm3rQD5nT9u');
define('ACCESS_TOKEN_SECRET', 'yw6q9iiRRjFiDFt42AVgszvMpNuffT7gB7BgfYvhiqghU');

function search($query)
{
  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
  return $connection->get('search/tweets', $query);
}

// $max_id = "";
// foreach (range(0,1) as $i) { // up to 20 result pages
// foreach (range(1, 20) as $i) { // up to 20 result pages

  $query = array(
  // "q" => "bukalapak until:2014-07-09",
    "q" => "bukalapak",
    "count" => 2,
    // "result_type" => "mixed",
    // "max_id" => $max_id,
    "lang" => "id"
  );

 $results = search($query);
  //echo json_encode($results->statuses);

  //print_r(json_encode($results));


  foreach ($results->statuses as $result) {
 $result->text . "<br/><br/>";
  $tweet = $result->text;
   $tweet = preg_replace("/(https:\/\/)(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?/",'', $tweet);
  echo $tweet."<br>";
   $que = mysqli_query($koneksi,"SELECT * FROM unduh2 WHERE tweet");
   if($que){
  $cek_exist = mysqli_fetch_array($que);
  echo count($cek_exist)."<br>";
  if(count($cek_exist)==0){
	$q = mysqli_query($koneksi, "INSERT INTO unduh2 VALUES('','','".$tweet."','', '','','')");
  }
  }

  }


?>
