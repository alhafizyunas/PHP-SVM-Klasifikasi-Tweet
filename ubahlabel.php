<?php
if($_POST['v']=='label'){
echo $_POST['id']." -> ".$_POST['lbl'];
$connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "UPDATE unduh SET label = '$_POST[lbl]' WHERE no = '$_POST[id]'";
$stmt = $connection->prepare($sql);
$stmt->execute();
}

if($_POST['v']=='ket'){
echo $_POST['id']." -> ".$_POST['lbl'];
$connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "UPDATE unduh SET Keterangan = '$_POST[lbl]' WHERE no = '$_POST[id]'";
$stmt = $connection->prepare($sql);
$stmt->execute();
}
