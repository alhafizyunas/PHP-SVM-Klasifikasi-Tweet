<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit (3600);
$koneksi = mysqli_connect('localhost', 'root', 'root', 'tweet');
require_once 'vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;
define('CONSUMER_KEY', 'MotZi779YdKTOdymrxGUoKzte');
define('CONSUMER_SECRET', '5mli5kr6dvsGx9dUELhKglu0ZUvSdbgon2MMV8MmQXgP37NBCf');
define('ACCESS_TOKEN', '262052819-UfYZR6M8olJ9zrYjlPbv1Ldn90RsdRixE53BrzjO');
define('ACCESS_TOKEN_SECRET', 'y46ldVz7LztRXC23qGkemoyov0t9XAADkpkg47h46uw5Z');

function search($query)
{
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
return $connection->get('search/tweets', $query);
}

//query pengunduhan (katakunci,jumlah,bahasa)
if(!empty($_POST)){
$query = array(
"q" => "".$_POST['keyword']."",
"count" => $_POST['number'],
"lang" => "".$_POST['lang']."",
);
$results = search($query);


//penyimpanan text tweet
foreach ($results->statuses as $result) {
$result->text . "<br/><br/>";
$tweet = $result->text;
echo $tweet."<br>";
$que = mysqli_query($koneksi,"SELECT * FROM unduh WHERE tweet");
if($que){
$cek_exist = mysqli_fetch_array($que);
echo count($cek_exist)."<br>";
if(count($cek_exist)==1){
$q = mysqli_query($koneksi, "UPDATE unduh VALUES('','".$tweet."','','','')");
}else{
$q = mysqli_query($koneksi, "INSERT INTO unduh VALUES('','".$tweet."','','','')");
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
<?php 
try {
    $connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $ex){
    echo $ex->getMessage();
    exit();
}
$sql = "SELECT * FROM unduh LIMIT 10";
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
<?php } ?>
<!-- <div>
<p align='right'><input type = "reset" name="hapus" value="Hapus Data"></p></div>
<?php
 try {
    $connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $ex){
    echo $ex->getMessage();
    exit();
}
$sql = " DELETE FROM `unduh2`";
if(isset($_POST['hapus']) ){
        $sql= $_POST['hapus'];
        }
$stmt = $connection->prepare($sql);
$stmt->setFetchMode(PDO::FETCH_OBJ); 
$stmt->execute();
?> -->
