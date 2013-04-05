
	<div class="row">
		<div class="span4 offset4 well">
			<legend>Administration Sign In</legend>
          	
			<?php if ($_SESSION['error']['message'] != "") { ?>
			  <div class="alert <?php echo $_SESSION['error']['type'] ?>">
			  	<button type="button" class="close" data-dismiss="alert">×</button>
		   		<?php echo $_SESSION['error']['message'] ?>
		      </div>
		      <?php $_SESSION['error']['type'] = ""; $_SESSION['error']['message'] = ""; ?>
			<?php } ?>
          	
			<form method="POST" action="" accept-charset="UTF-8">
				<input type="hidden" name="action" value="doLoginAdmin">
				<input type="text" id="username" class="span4" name="ka_email" placeholder="Email">
				<input type="password" id="password" class="span4" name="ka_password" placeholder="Password">
				<button type="submit" name="submit" class="btn btn-info btn-block">Sign in</button>
			</form>    
		</div>
	</div>