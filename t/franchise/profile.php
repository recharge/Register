<div class="row">
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
    
	<div class="span10">

	<h2>Franchise Profile</h2>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/profile" method="POST">
				<input type="hidden" name="action" value="doUpdateFranchiseProfile" />

				<ul id="myTab" class="nav nav-tabs">
					<li class="active"><a href="#personal" data-toggle="tab">Personal Info</a></li>
					<li class=""><a href="#franchise" data-toggle="tab">Franchise Setup</a></li>
					<li class=""><a href="#email" data-toggle="tab">Emails</a></li>
				</ul>

				<div id="myTabContent" class="tab-content">
					<div class="tab-pane fade active in" id="personal">
			            
			            <?php $fieldName = "contact"; ?>
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Main Contact</label>
			              <div class="controls">
			                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user[$fieldName] ?>"/>
			                <?php if (isset($badFields[$fieldName])) { ?>
			                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
			                <?php } ?>
			              </div>
			            </div>
			            
			            <?php $fieldName = "email"; ?>
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Email</label>
			              <div class="controls">
			                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user[$fieldName] ?>"/>
			                <?php if (isset($badFields[$fieldName])) { ?>
			                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
			                <?php } ?>
			              </div>
			            </div>
			            
			            <?php $fieldName = "phone"; ?>
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Phone</label>
			              <div class="controls">
			                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user[$fieldName] ?>"/>
			                <?php if (isset($badFields[$fieldName])) { ?>
			                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
			                <?php } ?>
			              </div>
			            </div>
			            
			            <?php $fieldName = "password"; ?>
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Change Password</label>
			              <div class="controls">
			                <input type="password" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
			                <?php if (isset($badFields[$fieldName])) { ?>
			                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
			                <?php } ?>
			              </div>
			            </div>
			            
			            <?php $fieldName = "confirm"; ?>
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Confirm Password</label>
			              <div class="controls">
			                <input type="password" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
			                <?php if (isset($badFields[$fieldName])) { ?>
			                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
			                <?php } ?>
			              </div>
			            </div>

			        </div>

			        <div class="tab-pane fade" id="franchise">
	            
			            <?php if (isAdmin()) { ?>
			            <?php $fieldName = "name"; ?>
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Franchise Name</label>
			              <div class="controls">
			                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user[$fieldName] ?>"/>
			                <?php if (isset($badFields[$fieldName])) { ?>
			                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
			                <?php } ?>
			              </div>
			            </div>
			            <?php } ?>
			            
			            <div class="control-group">
			              <label for="inputEmail" class="control-label">Location</label>
			              <div class="controls nofield">
			                <?php echo $user['address'] ?><!-- -- <a href="/franchise/?action=doResetFranchiseLocation">Change</a> -->
			              </div>
			            </div>
			            
			            <?php $fieldName = "rechargeApiKey"; ?>
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Recharge API Key</label>
			              <div class="controls">
			                <input type="text" class="span5" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user[$fieldName] ?>"/>
			                <?php if (isset($badFields[$fieldName])) { ?>
			                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
			                <?php } ?>
			              </div>
			            </div>
			            
			            <?php $fieldName = "siblingdiscount"; ?>
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Sibling Discount %</label>
			              <div class="controls">
			                <input type="text" class="input-mini" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user[$fieldName] ?>"/>
			                <?php if (isset($badFields[$fieldName])) { ?>
			                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
			                <?php } ?>
			              </div>
			            </div>

			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Allow Cash/Check</label>
			              <div class="controls">
			                <select name="allow_cash" class="input-small">
			                	<option <?php echo ($user['allow_cash'] == 1 ? "selected" : "") ?> value="1">Yes</option>
			                	<option <?php echo ($user['allow_cash'] == 0 ? "selected" : "") ?> value="0">No</option>
			                </select>
			              </div>
			            </div>
			            
			        </div>

			        <div class="tab-pane fade" id="email">

			        	<?php
			        	$ps = $pdo->prepare("SELECT * FROM templates WHERE owner = 'admin' OR owner = ? ORDER BY owner");
						$ps->execute(array($uid));
						$templates = $ps->fetchAll();
			        	?>
	            
			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Welcome Email</label>
			              <div class="controls">
			                <select name="welcome">
			                	<?php foreach ($templates as $template) { ?>
			                	<option <?php echo ($user['welcome'] == $template['id'] ? "selected" : "") ?> value="<?php echo $template['id'] ?>"><?php echo $template['name'] ?></option>
			                	<?php } ?>
			                </select>
			              </div>
			            </div>

			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Course Registration</label>
			              <div class="controls">
			                <select name="registration">
			                	<?php foreach ($templates as $template) { ?>
			                	<option <?php echo ($user['registration'] == $template['id'] ? "selected" : "") ?> value="<?php echo $template['id'] ?>"><?php echo $template['name'] ?></option>
			                	<?php } ?>
			                </select>
			              </div>
			            </div>

			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Course Change</label>
			              <div class="controls">
			                <select name="change">
			                	<?php foreach ($templates as $template) { ?>
			                	<option <?php echo ($user['change'] == $template['id'] ? "selected" : "") ?> value="<?php echo $template['id'] ?>"><?php echo $template['name'] ?></option>
			                	<?php } ?>
			                </select>
			              </div>
			            </div>

			            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
			              <label for="inputEmail" class="control-label">Course Reminder</label>
			              <div class="controls">
			                <select name="reminder">
			                	<?php foreach ($templates as $template) { ?>
			                	<option <?php echo ($user['reminder'] == $template['id'] ? "selected" : "") ?> value="<?php echo $template['id'] ?>"><?php echo $template['name'] ?></option>
			                	<?php } ?>
			                </select>
			              </div>
			            </div>
			            
			        </div>

		        </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>
	</div>
</div>