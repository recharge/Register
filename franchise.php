<?php
include 'config/config.php'; 
session_start();

$uid = $_SESSION['FID'];
$eid = $_SESSION['EID'];
if ($uid) {
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ? AND active = 1");
	$ps->execute(array($uid));
	$user = $ps->fetch(PDO::FETCH_ASSOC);
	
	if ($eid != "") {
		$ps = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
		$ps->execute(array($eid));
		$employee = $ps->fetch(PDO::FETCH_ASSOC);
	}
}

// remove the directory path we don't want 
$request  = str_replace("/franchise/", "", $_SERVER['REQUEST_URI']); 
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

if (!$user) {
	$_SESSION['FID'] = $uid = "";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>KidzArt | Register | Franchise</title>
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
          <?php if ($user['branding'] == 2) { ?>
				<a class="brand" href="/franchise">Art Innovators</a>
          	<?php } else { ?>
          		<a class="brand" href="/franchise">KidzArt</a>
          	<?php } ?>
          <div class="nav-collapse collapse">
            <?php include('t/franchise/nav.php'); ?>
            <?php include('t/franchise/topright.php'); ?>
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
	  
	  <?php if ($user['id'] != "" && $user['live'] == 0) { ?>
	  <div class="alert alert-info">
	  	<button type="button" class="close" data-dismiss="alert">×</button>
   		Your store has not yet been enabled. Please contact KidzArt Corporate to enable your store.
      </div>
	  <?php } ?>
      
      <?php 
      	if ($uid == "") {
      		if ($resource == "signup") {
				include('t/franchise/signup.php');
			} else {
				include('t/franchise/frontpage.php');
			}
      	} else {
      		if ($resource == "signup") {
    				include('t/franchise/signup.php');
    			} else if ($resource == "class") {
    				include('t/franchise/class.php');
    			} else if ($resource == "meetingplace") {
    				include('t/franchise/meetingplace.php');
    			} else if ($resource == "students") {
    				include('t/franchise/students.php');
    			} else if ($resource == "profile") {
    				include('t/franchise/profile.php');
    			} else if ($resource == "coupons") {
    				include('t/franchise/coupons.php');
    			} else if ($resource == "employees") {
    				include('t/franchise/employees.php');
    			} else if ($resource == "transactions") {
    				include('t/franchise/billing.php');
    			} else if ($resource == "venues") {
					  include('t/franchise/venues.php');
  				} else if ($resource == "customfields") {
            include('t/franchise/customfields.php');
          } else if ($resource == "bizcenter") {
  					include('t/franchise/bizcenter.php');
  				} else if ($resource == "curriccenter") {
  					include('t/franchise/curriccenter.php');
  				} else if ($resource == "email") {
  					include('t/franchise/email.php');
  				} else if ($resource == "users") {
            include('t/franchise/users.php');
          } else {
      				include('t/franchise/app.php'); 
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
