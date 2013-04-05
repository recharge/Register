<!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>Welcome!</h1>
        <p>To the KidzArt Online Registration System & Information Portal!</p>
        
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span6">
		    <form class="formcontainer sign-in form-horizontal" action="" method="POST">
			    <input type="hidden" name="action" value="doLogin" />
			    <div class="control-group">
				    <label class="control-label" for="inputEmail">Email</label>
				    <div class="controls">
				    	<div class="input-prepend">
				    		<span class="add-on"><i class="icon-envelope"></i></span><input type="text" id="inputEmail" name="ka_email" placeholder="Email">
				    	</div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputPassword">Password</label>
				    <div class="controls">
				    	<div class="input-prepend">
				    		<span class="add-on"><i class="icon-lock"></i></span><input type="password" id="inputPassword" name="ka_password" placeholder="Password">
				    	</div>
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					    <button type="submit" class="btn">Sign In</button>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputPassword"></label>
				    <div class="controls">
				    	<a href="#forgotpw" data-toggle="modal">Forgot Password?</a>
				    </div>
			    </div>
		    </form>
        </div>
        <div class="span6">
		    <form class="formcontainer sign-up form-horizontal" action="" method="post">
			  <input type="hidden" name="action" value="doRegister" />
			  	<div class="control-group">
				    <label class="control-label" for="inputEmail">Name</label>
				    <div class="controls">
				    	<div class="input-prepend">
				    		<span class="add-on"><i class="icon-user"></i></span>
				    		<input type="text" id="inputEmail" placeholder="Name" name="ka_name">
				    	</div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputEmail">Email</label>
				    <div class="controls">
				    	<div class="input-prepend">
				    		<span class="add-on"><i class="icon-envelope"></i></span>
				    		<input type="text" id="inputEmail" placeholder="Email" name="ka_email">
				    	</div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputPassword">Password</label>
				    <div class="controls">
				    	<div class="input-prepend">
				    		<span class="add-on"><i class="icon-lock"></i></span>
				    		<input type="password" id="inputPassword" placeholder="Password" name="ka_password">
				    	</div>
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
      
    <div class="modal hide fade" id="forgotpw">
    <form action="/" method="post" class="form-inline" style="margin:0;">
    	<input type="hidden" name="action" value="doForgotPasswordUser" >
	    <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		    <h3>Forgot Password</h3>
	    </div>
	    <div class="modal-body">
            <p>
            	Enter your email address below and we'll send you a new password:
            </p>
            <p>
            	<input type="text" name="email" placeholder="Email Address"> <button class="btn btn-primary">Submit</button>
            </p>
	    </div>
	    <div class="modal-footer">
		    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
	    </div>
    </form>
	</div>