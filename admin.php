<?php
include 'config/config.php';
session_start();

$uid = $_SESSION['AID'];
if ($uid) {
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$ps = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
	$ps->execute(array($uid));
	$user = $ps->fetch(PDO::FETCH_ASSOC);
}

// remove the directory path we don't want 
$request  = str_replace("/admin/", "", $_SERVER['REQUEST_URI']); 
// split the path by '/'  
$params     = @split("/", $request);

// filter out query string
$qstring = $_SERVER['QUERY_STRING'];
$params = str_replace("?".$qstring, "", $params);

// gets the requested resource
$resource = strtolower($params[0]); //echo $resource; print_r($_REQUEST);

$id = $params[1];

include 'bin/functions.php';
include 'bin/actions.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>KidzArt | Register | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->

    <link href="/css/bootstrap.css" rel="stylesheet">
    <link type="text/css" href="/css/redmond/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <link href="/css/kidzart.css" rel="stylesheet" >
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <script src="/js/cufon-yui.js" type="text/javascript"></script>
	<script src="/js/Spumoni_400.font.js" type="text/javascript"></script>
	<script src="/js/cufon_replace.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
	<script src="/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="/js/bootstrap.js"></script>
    <script src="/js/highcharts.js"></script>
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">

        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/admin/">KidzArt</a>
          <div class="nav-collapse collapse">
            <?php include('t/admin/nav.php'); ?>
            <?php include('t/admin/topright.php'); ?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
	  
      
      <?php 
      	if ($uid == "") {
      		include('t/admin/frontpage.php');
      	} else {
      		if ($resource == "customers") {
				include('t/admin/customers.php');
			} else if ($resource == "franchises") {
				include('t/admin/franchises.php');
			} else if ($resource == "administrators") {
				include('t/admin/administrators.php');
			} else if ($resource == "bizcenter") {
				include('t/admin/bizcenter.php');
			} else if ($resource == "curriccenter") {
				include('t/admin/curriccenter.php');
			} else if ($resource == "email") {
        include('t/admin/email.php');
      } else {
				include('t/admin/app.php'); 
			}
      	}
      ?>

      <hr>

      <footer>
        <p>&copy; 2013 KidzArt</p>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript"> Cufon.now(); </script>

  </body>
</html>
