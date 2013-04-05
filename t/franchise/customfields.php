<div class="row">
<div class="row">

	<?php
	$ps = $pdo->prepare("SELECT * FROM customfields_keys WHERE id = ?");
	$ps->execute(array($id));
	$record = $ps->fetch(PDO::FETCH_ASSOC);
	?>

	<?php if ($record) { ?>
	<div class="span12">
		<h3><?php echo $record['name'] ?></h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/customfields/<?php echo $record['id'] ?>" method="POST">
				<input type="hidden" name="action" value="doUpdateCustomField" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $record['name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "type"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Type</label>
	              <div class="controls">
	                <select name="<?php echo $fieldName ?>">
	                	<option <?php echo ($record['type'] == 0) ? "selected" : "" ?> value="0">Text</option>
	                	<option <?php echo ($record['type'] == 1) ? "selected" : "" ?> value="1">Drop-Down</option>
	                	<option <?php echo ($record['type'] == 2) ? "selected" : "" ?> value="2">Multiple Choice</option>
	                </select>
	              </div>
	            </div>
	            
	            <?php $fieldName = "helptext"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Help Text</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $record['helptext'] ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	                <span class="help-block">Text that goes under the field to explain it, just like this!</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "values"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Values</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $record['values'] ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	                <span class="help-block">For Drop-Down or Multiple Choice, enter the possible values here, separated by commas</span>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>

	</div>
<?php } else if ($id == "new") { ?>

	<div class="span12">
		<h3>New Custom Field</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/customfields/new" method="POST">
				<input type="hidden" name="action" value="doAddCustomField" />
	            
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
	            
	            <?php $fieldName = "type"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Type</label>
	              <div class="controls">
	                <select name="<?php echo $fieldName ?>">
	                	<option value="0">Text</option>
	                	<option value="1">Drop-Down</option>
	                	<option value="2">Multiple Choice</option>
	                </select>
	              </div>
	            </div>
	            
	            <?php $fieldName = "helptext"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Help Text</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	                <span class="help-block">Text that goes under the field to explain it, just like this!</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "values"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Values</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	                <span class="help-block">For Drop-Down or Multiple Choice, enter the possible values here, separated by commas</span>
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
    
		<h2>Custom Fields</h2>
		
		<hr>
		
		<?php
		$ps = $pdo->prepare("SELECT * FROM customfields_keys WHERE franchise = ?");
		$ps->execute(array($uid));
		$fields = $ps->fetchAll();
		?>
        
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Help Text</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($fields as $field) { ?>
                <tr>
                  <td><a href="/franchise/customfields/<?php echo $field['id'] ?>"><?php echo $field['name'] ?></a></td>
                  <td>
                  	<?php echo ($field['type'] == 0) ? "Text" : "" ?>
                  	<?php echo ($field['type'] == 1) ? "Drop-Down" : "" ?>
                  	<?php echo ($field['type'] == 2) ? "Multiple Choice" : "" ?>
                  </td>
                  <td><?php echo $field['helptext'] ?></td>
                  <td>    
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/franchise/customfields/<?php echo $field['id'] ?>"><i class="icon-pencil"></i> View / Edit</a></li>
					    	<li class="divider"/>
					    	<li><a href="/franchise/?action=doDeleteCustomField&id=<?php echo $field['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$fields) { ?>
                <tr>
                  <td colspan="3">No Custom Fields Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <p><a href="/franchise/customfields/new" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add Field</a></p>
            		
	</div>
	<?php } ?>

</div>