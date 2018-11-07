<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit (3600);
$koneksi = mysqli_connect('localhost', 'root', 'root', 'tweet');
require_once 'vendor/autoload.php';
//require __DIR__ . '/vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
define('CONSUMER_KEY', 'Ts31GSYCT4NK481Fr0lG5USpO');
define('CONSUMER_SECRET', '47ws9SvekWLBqT2Ytw17DV2W8LSIHbamTz9slhTvIYtGG58p0x');
define('ACCESS_TOKEN', '262052819-6h2UlUPG54XBK26K7HixuFOlZDTlRQf4dI8L89Og');
define('ACCESS_TOKEN_SECRET', 'mRtvBegl7gvjyis9j6tnypQd8LOtnZlaJ5oz0jDfGT2gH');

function search($query)
{
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
return $connection->get('search/tweets', $query);
}
// $max_id = "";
// foreach (range(0,1) as $i) { // up to 20 result pages
// foreach (range(1, 20) as $i) { // up to 20 result pages
if(!empty($_POST)){
$query = array(
// "q" => "bukalapak until:2014-07-09",
"q" => "".$_POST['keyword']."",
"count" => $_POST['number'],
// "result_type" => "mixed",
// "max_id" => $max_id,
"lang" => "".$_POST['lang']."",
);
$results = search($query);
//echo json_encode($results->statuses);
//print_r(json_encode($results));



foreach ($results->statuses as $result) {
// echo " [" . $result->created_at . "] " . 
//$result->user->screen_name . ": " .
$result->text . "<br/><br/>";
$tweet = $result->text;
$tweet = preg_replace("/(https:\/\/)(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?/",'', $tweet);
$tweet = preg_replace('/ (\b (http|https|ftp|file) :\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i','',$tweet);
    //remove mention
$tweet = preg_replace ('/[@]+([A-Za-z0-9-_]+)/i','',$tweet);
    //remove hashtag
$tweet = preg_replace ('/[#]+([A-Za-z0-9-_]+)/i','',$tweet);

echo $tweet."<br>";
$que = mysqli_query($koneksi,"SELECT * FROM unduh3");
if($que){
$cek_exist = mysqli_fetch_array($que);
echo count($cek_exist)."<br>";
if(count($cek_exist)==0){
$q = mysqli_query($koneksi, "UPDATE unduh3 VALUES('','".$tweet."','','','')");
}else{
$q = mysqli_query($koneksi, "INSERT INTO unduh3 VALUES('','".$tweet."','','','')");
}
}
}
}else{
?>
<div class="span4">
<br /><br /><br />
<form action = "" method="POST">
    <input type="text" name="keyword" id="keyword" placeholder="Keyword" /><br />
    <input type="number" name="number" id="number" placeholder="Jumlah" /><br />
    <select name = "lang">
        <option value = "id">Bahasa Indonesia</option>
        <option value = "en">English</option>
    </select><br />
    <input type = "submit" name="submit" value="Unduh">
    <input type = "reset" name="reset" value="Reset">
    <br />
    <hr />
</form>
</div>
<div class="span8">
<h3>Hasil Crawling</h3>
<table class="table table-bordered table-stripped table-hover data-table">
    <tr>
        <th>Tweet</th>
    </tr>
<!-- <?php 
try {
    $connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $ex){
    echo $ex->getMessage();
    exit();
}
$sql = "SELECT * FROM unduh3";
$stmt = $connection->prepare($sql);
$stmt->setFetchMode(PDO::FETCH_OBJ); 
$stmt->execute();
if(count($stmt->fetch())!=0){
while (($obj = $stmt->fetch()) == true): ?>
   <tr>
       <td><?= $obj->tweet ?></td>
   </tr> 
<?php endwhile; }?>
</table>
</div>
<?php } ?> -->