<div class="row">
<div class="row">
	

	<?php
	$ps = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
	$ps->execute(array($id));
	$child = $ps->fetch(PDO::FETCH_ASSOC);
	?>

	<?php if ($child) { ?>
	<div class="span12">
		<h3><?php echo $child['name'] ?></h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/employees/<?php echo $child['id'] ?>" method="POST">
				<input type="hidden" name="action" value="doUpdateEmployee" />
	            
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
	            
	            <?php $fieldName = "access"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Access Level</label>
	              <div class="controls">
	                <select name="<?php echo $fieldName ?>">
	                	<option<?php echo ($child['access'] == 2 ? " selected" : "") ?> value="2">Senior Employee</option>
	                	<option<?php echo ($child['access'] == 1 ? " selected" : "") ?> value="1">Junior Employee</option>
	                	<option<?php echo ($child['access'] == 0 ? " selected" : "") ?> value="0">Instructor</option>
	                </select>
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

	<?php
		$page = ($_GET['p'] == "" ? 1 : $_GET['p']);
		$resultsPerPage = 20;
		$limit = ($page-1) * $resultsPerPage;
	
		if ($_GET['q'] == "") {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM employees LIMIT $limit,$resultsPerPage");
			$ps->execute();
			$customers = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		} else {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM employees WHERE name LIKE ? LIMIT $limit,$resultsPerPage");
			$ps->execute(array("%".$_GET['q']."%"));
			$customers = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		}

		$pages = ceil($rows / $resultsPerPage);

		?>



	<div class="span12">
		<h3>New Employee</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/employees/new" method="POST">
				<input type="hidden" name="action" value="doAddEmployee" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST['name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "email"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Email</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST['email'] ?>"/>
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
	            
	            <?php $fieldName = "access"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Access Level</label>
	              <div class="controls">
	                <select name="<?php echo $fieldName ?>">
	                	<option value="2">Senior Employee</option>
	                	<option value="1">Junior Employee</option>
	                	<option value="0">Instructor</option>
	                </select>
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
    
		<h2>Employees</h2>
		
		<hr>
		
		<?php
		$ps = $pdo->prepare("SELECT * FROM employees WHERE franchise = ?");
		$ps->execute(array($uid));
		$children = $ps->fetchAll();
		?>
        
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Access Level</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($children as $child) { ?>
                <tr>
                  <td><a href="/franchise/employees/<?php echo $child['id'] ?>"><?php echo $child['name'] ?></a></td>
                  <td><?php echo $child['email'] ?></td>
                  <td>
                  	<?php echo ($child['access'] == 0) ? "Instructor" : "" ?>
                  	<?php echo ($child['access'] == 1) ? "Junior Employee" : "" ?>
                  	<?php echo ($child['access'] == 2) ? "Senior Employee" : "" ?>
                  </td>
                  <td>    
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/franchise/employees/<?php echo $child['id'] ?>"><i class="icon-pencil"></i> View / Edit</a></li>
					    	<li class="divider"/>
					    	<li><a href="/franchise/?action=doDeleteEmployee&id=<?php echo $child['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$children) { ?>
                <tr>
                  <td colspan="3">No Employees Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <p><a href="/franchise/employees/new" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add Employee</a></p>
            		
	</div>
	<?php } ?>

</div>