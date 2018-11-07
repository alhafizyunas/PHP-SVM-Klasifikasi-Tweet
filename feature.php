<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit (3600);

try {
	$connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $ex){
	echo $ex->getMessage();
	exit();
}

?>
<div class="row-fluid">
	<!-- <div class="span6"> 
          <div class="widget-box">
            <div class="widget-content nopadding">
            
            </div>
          </div>
        </div>
</div> -->

<div class="span6"> 
<?php
$sql = "SELECT * 
		FROM twec
		Where keterangan='Trainning' 
		ORDER BY no";
$stmt = $connection->prepare($sql);
$stmt->setFetchMode(PDO::FETCH_OBJ); 
$stmt->execute();
$tweets = array();
$words = array();
$wordsIndex = 1;

while (($obj = $stmt->fetch()) == true) {
	$obj->words = explode(' ',$obj->prepro);
	foreach ($obj->words as $word) {
		
		$useWord = true;
		
		//Cek apakah telah ada di daftar kata
		if (in_array($word, $words)) {
			$useWord = false;
		}
		if (strlen($word) <= 0) {
			$useWord = false;
		}
		
		
		if ($useWord) {
			$words[$wordsIndex] = $word;
			$wordsIndex++;
		} else if (strlen($word) > 0) {
			
		}
	}
	
	$tweets[] = $obj;
}
$stmt->closeCursor();
?>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
  Edit Threshold
</button><br>


<?php

echo 'Tweet : '.count($tweets).'<br>';

foreach ($tweets as $k=>$v) {
	$v->features = array();
	foreach ($v->words as $v2) {
		$key = array_search($v2, $words);
		if ($key !== FALSE) {
			//Term Frequency
			if (!isset($v->features[$v2])) {
				$v->features[$v2] = 0;
			}
			$v->features[$v2]++;
		}
	}
	$tweets[$k] = $v;
}

$df = array();
foreach ($tweets as $k=>$v) {
	foreach ($v->words as $v2) {
		if (in_array($v2, $words)) {
			if (!isset($df[$v2])) {
				$df[$v2] = 0;
			}
			$df[$v2]++;
		}
	}
}

asort($df);		//Sort by value
echo 'Feature sebelum threshold : '.count($df).'<br>';

$idf = array();
foreach ($df as $k=>$v) {
	$idf[$k] = log(count($tweets) / $v);
}

//hitung tf.idf
$tfIdf = array();
foreach ($tweets as $k=>$v) {
	$tfIdf[$k] = array();
	foreach ($words as $k2=>$v2) {
		if (!isset($tfIdf[$k][$k2])) {
			$tfIdf[$k][$k2] = 0;
		}
		
		$tf = (isset($v->features[$v2])) ? $v->features[$v2] : 0;
		
		if (isset($idf[$v2])) {
			//Perlukah ditambah 1?
			$tfIdf[$k][$k2] = $tf * $idf[$v2]; //($idf[$v2] + 1);
		}
		
		//perbarui nilai fitur dengan tfidf
		//NOTE: perlukah hanya yang bernilai besar dari 0?
		if ($tfIdf[$k][$k2]) {
			$v->features[$v2] = $tfIdf[$k][$k2];
		}
	}
	$tweets[$k] = $v;
}

//Reduksi $df
$threshold = 26;
if(isset($_POST['threshold']) ){
		$threshold = $_POST['threshold'];
	}
foreach ($df as $k=>$v) {
	if ($v <= $threshold) {
		unset($df[$k]);
	}
}



echo 'Threshold : '.$threshold.'<br>';
echo 'Feature setelah threshold : '.count($df).'<br>';

$documents = array();
foreach ($tweets as $k=>$v) {
	$document = array($v->label);
	
	foreach ($v->features as $k2=>$v2) {
		if (isset($df[$k2])) {
			$key = array_search($k2, $words);
			if ($key !== FALSE) {
				$document[$key] = $v2;
			}	
		}
	}

}

try {
	//Simpan features.list untuk keperluan testing
	//Berisikan indeks kata, kata, dan nilai df-nya
	$featuresFile = fopen(__DIR__."/features.list", "w");
	arsort($df);
	foreach ($df as $word=>$dfValue) {
		$wordIndex = array_search($word, $words);
		// fwrite($featuresFile, $word.";".$dfValue."\n");
		fwrite($featuresFile, $wordIndex."\t".$word."\t".$dfValue."\n");
	}
	fclose($featuresFile);

	$trainFile = fopen(__DIR__."/train.data", "w");
			
	foreach ($documents as $v) {
		$rows = array();
		foreach ($v as $k2=>$v2) {
			
			if ($k2 == 0) {
				//Indeks 0 adalah kelas
				$rows[] = $v2;
			} else {
				//Indeks lainnya beserta nilainya
				$rows[] = $k2.":".round($v2,4);
			}
			
		}

		if (count($rows) > 0) {
			fwrite($trainFile, implode("\t", $rows)."\n");
		}
	}
	fclose($trainFile);

} catch (Exception $ex) {
	echo $ex->getMessage();
	exit();
}
?>
<!-- Modals 
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal body -->
      <div class="modal-body">
		<form action = "" method = "post">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h5 class="modal-title">Nilai Threshold</h5>
			<input type="text" name="threshold" placeholder="Nilai Threshold" class="span8"/><br />
			<input type="submit" name="submit" value="Ubah" class="btn btn-defult" /> 
		</form>
      </div>
    </div>
  </div>
</div>


</div>
</div>
<!-- </div> -->



<!-- Feature list -->



 
</div>

