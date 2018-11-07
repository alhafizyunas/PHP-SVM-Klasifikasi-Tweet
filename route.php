<?php
function route($r){

	switch ($r) {
		case 'crawling':
			include 'crawling.php';
			break;
		case 'preprocessing':
			include 'preprocess.php';
			break;
		case 'feature':
			include 'feature.php';
			$_SESSION['bc'] = 'Feature';
			break;
		case 'training':
			include 'trainning.php';
			$_SESSION['bc'] = 'Training';
			break;
		case 'testing':
			include 'testing.php';
			break;
		default:
			echo "<h1>Twitter E-Commerce</h1>";
			break;
	}
}
?>