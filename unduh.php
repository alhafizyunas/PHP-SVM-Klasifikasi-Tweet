<?php
// function convert_to_normal_text($text) {

//     $normal_characters = "a-zA-Z0-9\s`~!@#$%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]";
//     $normal_text = preg_replace("/[^$normal_characters]/", '', $text);

//     return $normal_text;
// }
// function convertword($text){
// 		$kamus = file_get_contents('file-normalisasi.txt');
// 		$perkata = explode("\n", $kamus);
// 		foreach ($perkata as $value) {
// 			$k  = explode(":", $value);		
// 			$katanotnormal = trim($k[0]);
// 			$katanormal = trim($k[1]);
// 			$kamusnormalisasi[$katanormal] = $katanotnormal;
// 		}	
// 		// foreach($text as $k => $v){
// 			$kata = explode(' ',$text);
// 			$tmpNewWords = '';
// 			foreach($kata as $key => $value){
// 				$kataasal = $value;
// 				if (in_array($value, $kamusnormalisasi)) {
// 					$kata[$key] = array_search($value, $kamusnormalisasi);
// 				} 	
// 			}
// 			$cteksbaru= implode(" ",$kata);
			
		
// 		return $cteksbaru;
// 	}
// function removeStopWords($text){
// 	$txt = explode(' ', $text);
// 	$temp = array();
// 	$stopwordDict = explode("\n", file_get_contents('kamus-stopword.txt'));
// 	foreach($txt as $t){
// 		if(!in_array($t, $stopwordDict) AND strlen($t)>2){
// 			$temp[] = $t;
// 		}
// 	}
// 	$teks = trim(implode(' ',$temp));
// 	unset($temp);
// 	return $teks;
// }
function preprocess($text) {
		//casefolding
	// $text = strtolower($text);
	// $text = preg_replace ('/[.,:;!?(){}]/i',' ',$text);
	$text = preg_replace('/ (\b (http|https|ftp|file) :\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i','',$text);
	//remove mention
	$text = preg_replace ('/[@]+([A-Za-z0-9-_]+)/i','',$text);
	//remove hashtag
	$text = preg_replace ('/[#]+([A-Za-z0-9-_]+)/i','',$text);
	
	// $text = preg_replace ('/[0-9]/i',' ',$text);
	
	// 	//convertword
	// $text = convertword($text);
	// 	//stopword removal
	// $text = removeStopWords($text);
	// 	//tokenizing
	// $word = preg_split('/[ ]/',$text);
	
	// //stemming
	// $stem = array ();
	// foreach ($word as $v) {
	// 	if ($v != '') {
	// 		$stem[] = ECS($v);
	// 	}
	// }
	// $text = implode (' ', $stem);
	
	return $text;
	
}?>
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>Tweet</th>
                  <th>Preprocess</th>
                 <!--  <th>Label</th>
                  <th>Keterangan</th> -->
                </tr>
              </thead>
              <tbody>
<?php
	try {
		$connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		// $sql = "SELECT *
		// 		FROM tb_katadasar";
		// $stmt = $connection->prepare($sql);
		// $stmt->setFetchMode(PDO::FETCH_OBJ); 
		// $stmt->execute();
		// $basicwords = array();
		// while (($obj = $stmt->fetch ()) == true) {
		// 	$basicwords [$obj->katadasar] = $obj->katadasar;
		// }
		// $stmt->closeCursor();
			
		$sql = "SELECT * FROM unduh4 LIMIT 10";
		
		$stmt = $connection->prepare($sql);
		$stmt->setFetchMode(PDO::FETCH_OBJ); 
		$stmt->execute();
		while (($obj = $stmt->fetch()) == true) {
			echo '<tr>';
			// echo '<td>'.$obj->akun.'</td>';
			echo '<td>'.$obj->tweet.'</td>';
			echo '<td>'.preprocess($obj->tweet).'</td>';
			
			echo '</tr>';
				
			$sql = "UPDATE unduh4 set prepro = :preprocess WHERE no = '".$obj->no."'";
			$stmt2 = $connection->prepare($sql);
			$stmt2->execute(array(':preprocess'=>preprocess($obj->tweet)));
			}
			$stmt->closeCursor();
		} catch(PDOException $ex){
			echo $ex->getMessage();
			exit();
		}
?>		
			