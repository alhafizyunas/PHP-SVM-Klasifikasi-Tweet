<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit (3600);

if (!extension_loaded('svm')) {
	echo 'PHP extension for SVM is not loaded';
	exit();
}

function debug() {
	foreach (func_get_args() as $arg) {
		echo '<pre>'.print_r($arg, true).'</pre>';
	}
}

function debux() {
	foreach (func_get_args() as $arg) {
		echo '<pre>'.print_r($arg, true).'</pre>';
	}
	exit();
}

try {
	$connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $ex){
	echo $ex->getMessage();
	exit();
}

?>

            <!-- <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>NO</th>
                  <th>MANUAL</th>
                  <th>KOMPUTER</th>
                </tr>
              </thead>
              <tbody> -->

<?php
$sql = "SELECT * 
		FROM unduh4
		WHERE keterangan='Testing'
		ORDER BY no LIMIT 5";

// if(!empty($_POST['label']) AND $_POST['label']!=''){
// 	$label = $_POST['label'];
// 	$sql = "SELECT * 
// 		FROM unduh
// 		WHERE keterangan='Testing'
// 		AND label = $label
// 		ORDER BY no";
// }
$stmt = $connection->prepare($sql);
$stmt->setFetchMode(PDO::FETCH_OBJ); 
$stmt->execute();
$tweets = array();
while (($obj = $stmt->fetch()) == true) {
	$obj->words = explode(' ',$obj->prepro);
	foreach ($obj->words as $k=>$word) {
		
		$useWord = true;
		if (strlen($word) <= 3) {
			$useWord = false;
		}
		if (!$useWord) {
			unset($obj->words[$k]);
		}
	}
	
	$tweets[] = $obj;
}
$stmt->closeCursor();
	
	debug ($tweets);
//Baca features.list
//Berisikan indeks kata, kata, dan nilai df-nya
//Dibuatkan variabel $words dan $df sebagaimana training
$words = array();
$df = array();
$featuresFileLines = preg_split('/\r\n|\n|\r/', trim(file_get_contents(__DIR__."/features.list")));
foreach ($featuresFileLines as $line) {
	$parts = explode("\t", $line);
	$words[$parts[0]] = $parts[1];
	$df[$parts[1]] = $parts[2];
}
asort($df);		//Sort by value

$idf = array();
foreach ($df as $k=>$v) {
	$idf[$k] = log(count($tweets) / $v);
}

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
			
			$tfIdf[$k][$k2] = $tf * $idf[$v2]; 
		}
		
		//perbarui nilai fitur dengan tfidf
		//NOTE: perlukah hanya yang bernilai besar dari 0?
		if ($tfIdf[$k][$k2]) {
			$v->features[$v2] = $tfIdf[$k][$k2];
		}
	}
	$tweets[$k] = $v;
}

debug ($v);
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
	//Apabila tweet tidak mengandung fitur, maka tidak dihitung
	if (count($document) > 1) {
		$documents[$k] = $document;
	}
}

debug($document);
 
try {
	$model = new SVMModel();
	// $model->load(__DIR__.'/model3.svm');
	// //$sama = 0;
	foreach ($documents as $k=>$v) {
		$result = $model->getlabels($v2);
		$style = '';
		// if ($tweets[$k]->label == $result) {
		// 	$sama++;

		// }
		
		echo '<td>'.$tweets[$k]->no.'</td>';
		// echo '<td>'.$tweets[$k]->label.'</td>';
		echo '<td>'.$result.'</td>';
		echo '</tr>';
		

	}
	
	// echo "<br>";
	// echo "<h4>Akurasi <b>".$sama."</b> dari <b>".count($documents).'</b> atau <b>'.(round($sama/count($documents), 2) * 100).'%</b></h4>';
} catch (Exception $ex) {
	echo $ex->getMessage();
	exit();
}
?>
 </tbody>
</table>        
