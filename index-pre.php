<?php
set_time_limit(600);
require_once "ecs.php";

function convert_to_normal_text($text) {

    $normal_characters = "a-zA-Z0-9\s`~!@#$%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]";
    $normal_text = preg_replace("/[^$normal_characters]/", '', $text);

    return $normal_text;
}
function termNormalization($text){
		$kamus = file_get_contents('file-normalisasi.txt');
		$perkata = explode("\n", $kamus);
		foreach ($perkata as $value) {
			$k  = explode(":", $value);		
			$katanotnormal = trim($k[0]);
			$katanormal = trim($k[1]);
			$kamusnormalisasi[$katanormal] = $katanotnormal;
		}	
		// foreach($text as $k => $v){
			$kata = explode(' ',$text);
			$tmpNewWords = '';
			foreach($kata as $key => $value){
				$kataasal = $value;
				if (in_array($value, $kamusnormalisasi)) {
					$kata[$key] = array_search($value, $kamusnormalisasi);
				} 	
			}
			$cteksbaru= implode(" ",$kata);
			// $this->delimitedMsg[$k]=$cteksbaru;
		// }
		
		return $cteksbaru;
	}
function removeStopWords($text){
	$txt = explode(' ', $text);
	$temp = array();
	$stopwordDict = explode("\n", file_get_contents('kamus-stopword.txt'));
	foreach($txt as $t){
		if(!in_array($t, $stopwordDict) AND strlen($t)>2){
			$temp[] = $t;
		}
	}
	$teks = trim(implode(' ',$temp));
	unset($temp);
	return $teks;
}
function preprocess($text) {
	$text = strtolower($text);
	$text = convert_to_normal_text($text);
	//remove punctuation(karakter simbol)
	//remove URL
	$text = preg_replace('/ (\b (http|https|ftp|file) :\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i','',$text);
	//remove mention
	$text = preg_replace ('/[@]+([A-Za-z0-9-_]+)/i','',$text);
	//remove hashtag
	$text = preg_replace ('/[#]+([A-Za-z0-9-_]+)/i','',$text);
	//clean number
	//clean one character
	//remove RT
	//remove negation word
	//remove emoticon
	
	//tokenizing
	$text = preg_replace ('/[0-9]/i',' ',$text);
	
	//normalisasibahasa
	//convertword
	//convertnumber
	$text = termNormalization($text);
	//stopword removal
	$text = removeStopWords($text);
	$word = preg_split('/[ .,:;!?(){}]/',$text);
	
	//untuk normalisasi
	/*foreach ($word as $k=>$v){
		$word[$k] = preg_replace("/(.)\\1+/", "$1", $word[$k]);
	}
	return implode (' ',$word);*/
	
	//stemming
	$stem = array ();
	foreach ($word as $v) {
		if ($v != '') {
			$stem[] = ECS($v);
		}
	}
	$text = implode (' ', $stem);
	
	return $text;
	
}
?>
<html lang="en">
<head>
<title>Maruti Admin</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="css/bootstrap.min.css" />
<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="css/uniform.css" />
<link rel="stylesheet" href="css/select2.css" />
<link rel="stylesheet" href="css/maruti-style.css" />
<link rel="stylesheet" href="css/maruti-media.css" class="skin-color" />
</head>
<body>
<div id="sidebar"> <a href="#" class="visible-phone"><i class="icon icon-th-list"></i> Tables</a><ul>
    <li class="active"><a href="index.html"><i class="icon icon-home"></i> <span>Dashboard</span></a> </li>
    <li> <a href="charts.html"><i class="icon icon-signal"></i> <span>Charts &amp; graphs</span></a> </li>
    <li> <a href="widgets.html"><i class="icon icon-inbox"></i> <span>Widgets</span></a> </li>
    <li><a href="tables.html"><i class="icon icon-th"></i> <span>Tables</span></a></li>
    <li><a href="grid.html"><i class="icon icon-fullscreen"></i> <span>Full width</span></a></li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Forms</span> <span class="label label-important">3</span></a>
      <ul>
        <li><a href="form-common.html">Basic Form</a></li>
        <li><a href="form-validation.html">Form with Validation</a></li>
        <li><a href="form-wizard.html">Form with Wizard</a></li>
      </ul>
    </li>
    <li><a href="buttons.html"><i class="icon icon-tint"></i> <span>Buttons &amp; icons</span></a></li>
    <li><a href="interface.html"><i class="icon icon-pencil"></i> <span>Eelements</span></a></li>
    <li class="submenu"> <a href="#"><i class="icon icon-file"></i> <span>Addons</span> <span class="label label-important">4</span></a>
      <ul>
        <li><a href="index2.html">Dashboard2</a></li>
        <li><a href="gallery.html">Gallery</a></li>
        <li><a href="calendar.html">Calendar</a></li>
        <li><a href="chat.html">Chat option</a></li>
      </ul>
    </li>
  </ul>
</div>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="#" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#" class="current">Tables</a> </div>
	<div class="container-fluid">
   	
    <h1>Tables</h1>
  </div>
  <div class="container-fluid">
  <div class="quick-actions_homepage">
    <ul class="quick-actions">
          <li> <a href="#"> <i class="icon-dashboard"></i> My Dashboard </a> </li>
          <li> <a href="#"> <i class="icon-shopping-bag"></i> Shopping Cart</a> </li>
          <li> <a href="#"> <i class="icon-web"></i> Web Marketing </a> </li>
          <li> <a href="#"> <i class="icon-people"></i> Manage Users </a> </li>
          <li> <a href="#"> <i class="icon-calendar"></i> Manage Events </a> </li>
        </ul>
   </div>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
             <span class="icon"><i class="icon-th"></i></span> 
            <h5>Data table</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  
                  <th>Tweet</th>
                  <th>Preprocess</th>
 
                </tr>
              </thead>
<?php
	try {
		$connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		$sql = "SELECT *
				FROM tb_katadasar";
		$stmt = $connection->prepare($sql);
		$stmt->setFetchMode(PDO::FETCH_OBJ); 
		$stmt->execute();
		$basicwords = array();
		while (($obj = $stmt->fetch ()) == true) {
			$basicwords [$obj->katadasar] = $obj->katadasar;
		}
		$stmt->closeCursor();
			
		$sql = "SELECT * FROM unduh3 LIMIT 10";
		$stmt = $connection->prepare($sql);
		$stmt->setFetchMode(PDO::FETCH_OBJ); 
		$stmt->execute();
		echo '<tbody>';
		while (($obj = $stmt->fetch()) == true) {
			echo '<tr>';
			// echo '<td>'.$obj->akun.'</td>';
			echo '<td>'.$obj->tweet.'</td>';
			echo '<td>'.preprocess($obj->tweet).'</td>';
			echo '</tr>';
				
			$sql = "UPDATE unduh3 set prepro = :preprocess WHERE no = '".$obj->no."'";
			$stmt2 = $connection->prepare($sql);
			$stmt2->execute(array(':preprocess'=>preprocess($obj->tweet)));
			}
			echo '</tbody>';
			$stmt->closeCursor();
		} catch(PDOException $ex){
			echo $ex->getMessage();
			exit();
		}
?>
            </table>
          </div>
        </div>
		
</div>
    </div>
  </div>
</div>
<div class="row-fluid">
  <div id="footer" class="span12"> 2012 &copy; Marutii Admin. Brought to you by <a href="http://themedesigner.in">Themedesigner.in</a> </div>
</div>
<script src="js/jquery.min.js"></script> 
<script src="js/jquery.ui.custom.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<script src="js/jquery.uniform.js"></script> 
<script src="js/select2.min.js"></script> 
<script src="js/jquery.dataTables.min.js"></script> 
<script src="js/maruti.js"></script> 
<script src="js/maruti.tables.js"></script>
</body>
</html>
