<div class="row">
<?php
$ps = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
$ps->execute(array($id));
$child = $ps->fetch(PDO::FETCH_ASSOC);
?>
<?php if ($child) { ?>
	<div class="span12">
		<h3><?php echo $child['name'] ?></h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/admin/administrator/<?php echo $child['id'] ?>" method="POST">
				<input type="hidden" name="action" value="doUpdateAdmin" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $child['name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "email"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Email</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $child['email'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "password"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Change Password</label>
	              <div class="controls">
	                <input type="password" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "confirm"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Confirm Password</label>
	              <div class="controls">
	                <input type="password" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>

	</div>
<?php } else if ($id == "new") { ?>
	<div class="span12">
		<h3>New Administrator</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/admin/administrators/new" method="POST">
				<input type="hidden" name="action" value="doAddAdmin" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $child['name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "email"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Email</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $child['email'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "password"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Password</label>
	              <div class="controls">
	                <input type="password" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "confirm"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Confirm Password</label>
	              <div class="controls">
	                <input type="password" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>

	</div>
	<?php } else { ?>
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
	<div class="span10">
		<h2>Administrators</h2>
		
		<hr>
		
		
		<?php
		$ps = $pdo->prepare("SELECT * FROM admin");
		$ps->execute(array($user['id']));
		$children = $ps->fetchAll();
		?>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($children as $child) { ?>
                <tr>
                  <td><a href="/admin/administrators/<?php echo $child['id'] ?>"><?php echo $child['name'] ?></a></td>
                  <td><?php echo $child['email'] ?></td>
                  <td>    
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/admin/administrators/<?php echo $child['id'] ?>"><i class="icon-pencil"></i> View / Edit</a></li>
					    	<li class="divider"/>
					    	<li><a href="/admin/?action=doDeleteAdmin&id=<?php echo $child['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$children) { ?>
                <tr>
                  <td colspan="4">No Admins Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <p><a href="/admin/administrators/new" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add Administrator</a></p>
	</div>
	<?php } ?>

</div>