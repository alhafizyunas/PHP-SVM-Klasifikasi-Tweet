<?php
$array_kamus=array();
$_SESSION['kamus']=true;
$data=file_get_contents('kamus-ind.txt');
$baris=explode("\n",$data);

foreach($baris as $val)
{
	$kata=explode("(",$val);
	$isi=trim($kata[0]);
	//array_push($array_kamus,trim($kata[0]));
	$array_kamus[$isi]=$isi;
}
?>