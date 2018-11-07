<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit (3600);
session_start();
include_once 'route.php';
include_once 'ecs.php';
try {
  $connection = new PDO("mysql:host=localhost;dbname=tweet","root","root");
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $ex){
  echo $ex->getMessage();
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>TWEC</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="css/bootstrap.min.css" />
<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="css/fullcalendar.css" />
<link rel="stylesheet" href="css/maruti-style.css" />
<link rel="stylesheet" href="css/maruti-media.css" class="skin-color" />
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="./">TWEC</a></h1>
</div>
<!--close-Header-part--> 

<!--top-Header-messaages-->
<div class="btn-group rightzero"> <a class="top_message tip-left" title="Manage Files"><i class="icon-file"></i></a> <a class="top_message tip-bottom" title="Manage Users"><i class="icon-user"></i></a> <a class="top_message tip-bottom" title="Manage Comments"><i class="icon-comment"></i><span class="label label-important">5</span></a> <a class="top_message tip-bottom" title="Manage Orders"><i class="icon-shopping-cart"></i></a> </div>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> 
      <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
      <a href="#"><?= (!empty($_SESSION['bc'])) ? $_SESSION['bc'] : '' ?></a>
    </div>
  </div>
  <div class="container-fluid"><margin-top: -75px;>
    <div class="row-fluid">
          </div>
   	<div class="quick-actions_homepage">
    <ul class="quick-actions">
          <li> <a href="./index.php"> <i class="icon-home"></i> My Dashboard </a> </li>
          <li> <a href="./index.php?r=crawling"> <i class="icon-download"></i> Crawling</a> </li>
          <li> <a href="./index.php?r=preprocessing"> <i class="icon-cabinet"></i> Preprocessing </a> </li>
          <li> <a href="./index.php?r=feature"> <i class="icon-piechart"></i> Ekstraksi Fitur </a> </li>
          <li> <a href="./index.php?r=training"> <i class="icon-book"></i> Training </a> </li>
          <li> <a href="./index.php?r=testing"> <i class="icon-pdf"></i> Testing </a> </li>
        </ul>
   </div>
   
    <div class="row-fluid">
      <div class="widget-box">
        
        <div class="widget-content no-padding">
          <div class="row-fluid">
            <?php 
            if(isset($_GET['r'])) {
              route($_GET['r']);
            }else{
            ?>
            <!-- GAMBAR DEPAN -->
            <img src = "images/gallery/teras.jpg">
            <?php
            }
            ?>
          </div>
        </div>
        <div class="widget-footer"></div>
      </div>
    </div>
    <hr>
  </div>
</div>
</div>
</div>
<div class="row-fluid">
  <div id="footer" class="span12"> <?= date('Y'); ?> &copy; Al Hafiz Yunas </div>
</div>
<script src="js/excanvas.min.js"></script> 
<script src="js/jquery.min.js"></script> 
<script src="js/jquery.ui.custom.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<!-- <script src="js/jquery.flot.min.js"></script>  -->
<!-- <script src="js/jquery.flot.resize.min.js"></script>  -->
<script src="js/jquery.peity.min.js"></script> 
<script src="js/fullcalendar.min.js"></script> 
<script src="js/maruti.js"></script> 
<!-- <script src="js/maruti.dashboard.js"></script>  -->
<!-- <script src="js/maruti.chat.js"></script>  -->
<script src="js/jquery.dataTables.min.js"></script> 
<script src="js/maruti.tables.js"></script>
 

<script type="text/javascript">
  // This function is called from the pop-up menus to transfer to
  // a different page. Ignore if the value returned is a null string:
  function goPage (newURL) {

      // if url is empty, skip the menu dividers and reset the menu selection to default
      if (newURL != "") {
      
          // if url is "-", it is this page -- reset the menu:
          if (newURL == "-" ) {
              resetMenu();            
          } 
          // else, send page to designated URL            
          else {  
            document.location.href = newURL;
          }
      }
  }

// resets the menu selection upon entry to this page:
function resetMenu() {
   document.gomenu.selector.selectedIndex = 2;
}
</script>
<script>
    function ubahLabel(lbl, id, v){
      $.ajax({
        url: 'ubahlabel.php',
        data: {id : id, lbl : lbl, v : v},
        type: 'post',
        dataType: 'json',
        success: function(data){
          alert("sukses");
        },
      });
    }
</script>
</body>
</html>
