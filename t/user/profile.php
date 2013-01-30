<div class="row">
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
	<div class="span10">

	<h2>User Profile</h2>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/profile" method="POST">
				<input type="hidden" name="action" value="doUpdateProfile" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user['name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "email"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Email</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user['email'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "phone"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Phone</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user['phone'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <div class="control-group">
	              <label for="inputEmail" class="control-label">Location</label>
	              <div class="controls nofield">
	                <?php echo $user['location'] ?> -- <a href="/?action=doResetLocation">Change</a>
	              </div>
	            </div>
	            
	            <?php
	            $ps = $pdo->prepare("SELECT name FROM franchises WHERE id = ?");
				$ps->execute(array($user['home_franchise']));
				$franchise = $ps->fetch(PDO::FETCH_ASSOC);
	            ?>
	            <div class="control-group">
	              <label for="inputEmail" class="control-label">Home Franchise</label>
	              <div class="controls nofield">
	              <?php if ($franchise) { ?>
	              	<p><?php echo $franchise['name'] ?> -- <a href="/?action=doResetHomeFranchise">Change</a></p>
	              <? } else { ?>
	              	<p><a href="/?action=doResetHomeFranchise">Set Home Franchise</a></p>
	              <?php } ?>
	                
	              </div>
	            </div>
	            
	            <?php $fieldName = "emergency_contact_name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Emergency Contact Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user['emergency_contact_name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "emergency_contact_phone"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Emergency Contact Phone</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $user['emergency_contact_phone'] ?>"/>
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

	            <div class="control-group">
	              <label for="inputEmail" class="control-label">Email Subscriptions</label>
	              <div class="controls nofield">
	                <?php if ($user['unsubscribe'] == "") { ?>
	                	Subscribed to all email notifications.
	                	<a href="/?action=doUnsubscribe" class="btn btn-small">Unsubscribe</a>
	                <?php } else { ?>
	                	You unsubscribed on <?php echo date("m/d/Y", $user['unsubscribe']) ?> at <?php echo date("g:i a", $user['unsubscribe']) ?>.
	                	<a href="/?action=doResubscribe" class="btn btn-small">Re-Subscribe</a>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>
	</div>
</div>