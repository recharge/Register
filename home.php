<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>KidzArt | Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->

    <link href="/kidzart/css/bootstrap.css" rel="stylesheet">
    <link href="/kidzart/css/kidzart.css" rel="stylesheet" >
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <script src="/kidzart/js/cufon-yui.js" type="text/javascript"></script>
	<script src="/kidzart/js/Spumoni_400.font.js" type="text/javascript"></script>
	<script src="/kidzart/js/cufon_replace.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script>
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
          <a class="brand" href="#">KidzArt</a>
          <div class="nav-collapse collapse">

            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                <ul class="dropdown-menu">

                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>

                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
            <form class="navbar-form pull-right">
              <input class="span2" type="text" placeholder="Email">
              <input class="span2" type="password" placeholder="Password">
              <button type="submit" class="btn">Sign in</button>
            </form>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>Welcome!</h1>
        <p>To the KidzArt Online Registration System & Information Portal!</p>
        
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span6">
		    <form class="formcontainer sign-in form-horizontal">
			    <div class="control-group">
				    <label class="control-label" for="inputEmail">Email</label>
				    <div class="controls">
				    	<div class="input-prepend">
				    		<span class="add-on"><i class="icon-envelope"></i></span><input type="text" id="inputEmail" placeholder="Email">
				    	</div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputPassword">Password</label>
				    <div class="controls">
				    	<div class="input-prepend">
				    		<span class="add-on"><i class="icon-lock"></i></span><input type="password" id="inputPassword" placeholder="Password">
				    	</div>
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					    <button type="submit" class="btn">Sign In</button>
				    </div>
			    </div>
		    </form>
        </div>
        <div class="span6">
		    <form class="formcontainer sign-up form-horizontal">
			    <div class="control-group">
				    <label class="control-label" for="inputEmail">Name</label>
				    <div class="controls">
				    	<input type="text" id="inputEmail" placeholder="Name">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputPassword">Email</label>
				    <div class="controls">
				    	<input type="password" id="inputPassword" placeholder="Email">
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					    <button type="submit" class="btn">Sign Up</button>
				    </div>
			    </div>
		    </form>
        </div>

      </div>

      <hr>

      <footer>
        <p>&copy; 2012 KidzArt</p>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript"> Cufon.now(); </script>
    <script src="/kidzart/js/jquery.js"></script>

  </body>
</html>
