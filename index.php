<?php
include 'config/config.php';
session_start();

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);

$uid = $_SESSION['UID'];
if ($uid) {
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$ps = $pdo->prepare("SELECT * FROM users WHERE id = ? AND active = 1");
	$ps->execute(array($uid));
	$user = $ps->fetch(PDO::FETCH_ASSOC); 

  $ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
  $ps->execute(array($user['home_franchise']));
  $franchise = $ps->fetch(PDO::FETCH_ASSOC);
}

include 'bin/functions.php';
include 'bin/actions.php';

if (!$user) {
	$_SESSION['UID'] = $uid = "";
}

if ($user && ($user['emergency_contact_name'] == "" || $user['emergency_contact_phone'] == "")) {
	setError(3, "Please add an emergency contact under your profile.");
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>KidzArt | Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->

    <link href="/css/bootstrap.css" rel="stylesheet">
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script>
	<script src="/js/bootstrap.js"></script>
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
			<?php
			$ps = $pdo->prepare("SELECT branding FROM franchises WHERE id = ?");
			$ps->execute(array($user['home_franchise']));
			$branding = $ps->fetchColumn();
			
			if ($branding == 2) {
			?>
          <a class="brand" href="/">Art Innovators</a>
          	<?php } else { ?>
          <a class="brand" href="/">KidzArt</a>
          	<?php } ?>
          <div class="nav-collapse">
            <?php include('t/user/nav.php'); ?>
            <?php include('t/user/topright.php'); ?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
	  <?php if ($_SESSION['error']['message'] != "") { ?>
	  <div class="alert <?php echo $_SESSION['error']['type'] ?>">
	  	<button type="button" class="close" data-dismiss="alert">×</button>
   		<?php echo $_SESSION['error']['message'] ?>
      </div>
      <?php $_SESSION['error']['type'] = ""; $_SESSION['error']['message'] = ""; ?>
	  <?php } ?>
      
      <?php 
      
      	if ($uid == "") {
      		include('t/user/frontpage.php'); 
      	} else {
      		if ($_GET['error'] == 404) {
	      		// remove the directory path we don't want 
            $request = substr($_SERVER['REQUEST_URI'], 1);
    				$request  = str_replace("", "", $request); 
    				// split the path by '/'  
    				$params     = @split("/", $request);
    				
    				// filter out query string
    				$qstring = $_SERVER['QUERY_STRING'];
    				$params = str_replace("?".$qstring, "", $params);
    				
    				// gets the requested resource
    				$resource = strtolower($params[0]); //echo $resource; print_r($_REQUEST);
    				
    				$_GET['page'] = $resource;
      		}
      		if ($_GET['page'] == "profile") {
	      		include('t/user/profile.php'); 
      		} else if ($_GET['page'] == "children") {
	      		include('t/user/children.php'); 
      		} else if ($_GET['page'] == "class") {
	      		include('t/user/class.php'); 
      		} else if ($_GET['page'] == "cart") {
	      		include('t/user/cart.php'); 
      		} else if ($_GET['page'] == "billing") {
	      		include('t/user/billing.php'); 
      		} else {
	      		include('t/user/app.php'); 
      		}
      	}
      ?>

      <hr>

      <footer>
        <?php
        $ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
        $ps->execute(array($user['home_franchise']));
        $franchise = $ps->fetch(PDO::FETCH_ASSOC); 
        ?>
        <?php if ($franchise) { ?>
        <p class="center muted">Contact us! | <?php echo $franchise['name'] ?> | <?php echo formatphone($franchise['phone']) ?> | <?php echo $franchise['email'] ?></p>
        <?php } ?>
        <p>&copy; 2013 KidzArt</p>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript"> Cufon.now(); </script>

  </body>
</html>
