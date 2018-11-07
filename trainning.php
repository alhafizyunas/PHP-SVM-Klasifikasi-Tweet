
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

$sql = "SELECT * 
		FROM twec 
		WHERE keterangan='Trainning' 
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
		
		if ($useWord) {
			$words[$wordsIndex] = $word;
			$wordsIndex++;
		} else if (strlen($word) > 0 ) {
		}
	}
	
	$tweets[] = $obj;
}
$stmt->closeCursor();

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
	$idf[$k] = log10(count($tweets) / $v);
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

try {
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
	
	$svm = new SVM();
	$options = $svm->getOptions();
	// debux($options);
	// debux($df);
	/*
	http://php.net/manual/en/class.svm.php
	Array
	(
		[101] => 0		//OPT_TYPE			C_SVC 
		[102] => 2		//OPT_KERNEL_TYPE 	KERNEL_RBF
		[103] => 3		//OPT_DEGREE 
		[205] => 0		//OPT_COEF_ZERO 	Algorithm parameter for poly and sigmoid kernels
		[302] => 0
		[301] => 1
		[201] => 0		//OPT_GAMMA			Algorithm parameter for Poly, RBF and Sigmoid kernel types
		[202] => 0.5	//OPT_NU 			The option key for the nu parameter, only used in the NU_ SVM types
		[207] => 100	//OPT_P				Memory cache size, in MB
		[206] => 1		//OPT_C 			The option for the cost parameter that controls tradeoff between errors and generality - effectively the penalty for misclassifying training examples.
		[203] => 0.001	//OPT_EPS 			The option key for the Epsilon parameter, used in epsilon regression
		[204] => 0.1	//OPT_P 			Training parameter used by Episilon SVR regression
	)
	*/
	$opt_c = 0.3;
	$opt_g = 0.5;
	
	if(!empty($_POST['opt_c']) AND !empty($_POST['opt_g'])){
		$opt_c = $_POST['opt_c'];
		$opt_g = $_POST['opt_g'];
	}
	$options = array(Svm::OPT_C => $opt_c, Svm::OPT_GAMMA => $opt_g);
	try{
		$svm->setOptions($options);
	}catch(SVMException $e){
		echo 'Exception';
	}
	
	($svm->getOptions());
	
	
	?>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
  Ubah Parameter
</button><br>
<?php
	echo "Data Yang Mengandung Fitur : ".count($documents)."<br>";
	echo "Nilai C : $opt_c<br>";
	echo "Nilai Gamma : $opt_g<br>";
	echo "k=10";
	$akurasi = array();
	$jml_dt = count($documents);
	$jml_sg = ceil($jml_dt/10);

	?>
<br>
	<table class="table table-bordered table-hover table-stripped">
		<tr>
			<th>Fold</th>
			<th>Jumlah Benar</th>
			<th>Jumlah Segmen</th>
			<th>Akurasi</th>
		</tr>
	<?php
	for ($k=1; $k<=10; $k++) {
	echo "<tr>";
		echo '<td>'.$k.'</td>';
		$doc = $documents;
		
		if ($k>1) {
			
			$top= (((10-$k)*($jml_sg-1))+1);
			$bottom = ((((10-$k)*($jml_sg-1))+1)+($jml_sg));
			$size = $bottom - $top;
			$test = array_slice($doc, $size, ($jml_sg), true);
			array_splice($doc, $top, ($jml_sg));
			$model = $svm->train($doc);
			$model->save(__DIR__."/model".$k.".svm");
			try {
				$models = new SVMModel();
				$models->load(__DIR__.'/model'.$k.'.svm');
				$sama = 0;
				foreach ($test as $key=>$val) {
					$result = $models->predict($val);
					
					if ($test[$key][0] == $result) {
						$sama++;
					}
				}
				
				echo "<td>".$sama."</td><td>".count($test).'</td><td>'.$akurasi[$k]=(round($sama/count($test), 2) * 100).'%</td>';
			} catch (Exception $ex) {
				echo $ex->getMessage();
				exit();
			}
			
			
		} else {
			
			$top = (((10-$k)*($jml_sg-1))+1);
			$test = array_slice($doc, $top, ($jml_sg), true);
			array_slice($doc, $top, ($jml_sg));
			$model = $svm->train($doc);
			$model->save(__DIR__."/model".$k.".svm");
			try {
				$models = new SVMModel();
				$models->load(__DIR__.'/model'.$k.'.svm');
				$sama = 0;
				foreach ($test as $key=>$val) {
					$result = $models->predict($val);
					
					if ($test[$key][0] == $result) {
						$sama++;
					}
				}
				
				echo "<td>".$sama."</td><td>".count($test).'</td><td>'.$akurasi[$k]=(round($sama/count($test), 2) * 100).'%</td>';
			} catch (Exception $ex) {
				echo $ex->getMessage();
				exit();
			}
		}
		echo '<tr>';
	}
	echo "</table>";
	
		$model = $svm->train($documents);

	$model->save(__DIR__."/model.svm");
	echo 'Rataan Akurasi : '. array_sum($akurasi)/10 .'%<br>';
} catch (Exception $ex) {
	echo $ex->getMessage();
	exit();
}
?>
<!-- Modals -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal body -->
      <div class="modal-body">
		<form action = "" method = "post">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h5 class="modal-title">Ubah Parameter</h5>
			<input type="text" name="opt_c" placeholder="Nilai C" class="span8"/><br />
			<input type="text" name="opt_g" placeholder="Nilai Gamma" class="span8"/><br />
			<input type="submit" name="submit" value="Ubah" class="btn btn-defult" /> 
		</form>
      </div>
    </div>
  </div>
</div>
</div>
