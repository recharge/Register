<div class="row">
<?php
$ps = $pdo->prepare("SELECT * FROM children WHERE id = ?");
$ps->execute(array($_GET['id']));
$child = $ps->fetch(PDO::FETCH_ASSOC);
?>
<?php if ($child) { ?>
	<div class="span12">
		<h3><?php echo $child['name'] ?></h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/children/<?php echo $child['id'] ?>" method="POST">
				<input type="hidden" name="action" value="doUpdateChild" />
	            
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
	            
	            <?php $fieldName = "grade"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Grade</label>
	              <div class="controls">
					<select name="<?php echo $fieldName ?>">
	              		<option value="">Choose...</option>
	              		<option <?php echo ($child['grade'] == "Under 4" ? "selected" : "") ?>>Under 4</option>
	              		<option <?php echo ($child['grade'] == "Pre-Kindergarten" ? "selected" : "") ?>>Pre-Kindergarten</option>
	              		<option <?php echo ($child['grade'] == "Kindergarten" ? "selected" : "") ?>>Kindergarten</option>
	              		<option <?php echo ($child['grade'] == "1st Grade" ? "selected" : "") ?>>1st Grade</option>
	              		<option <?php echo ($child['grade'] == "2nd Grade" ? "selected" : "") ?>>2nd Grade</option>
	              		<option <?php echo ($child['grade'] == "3rd Grade" ? "selected" : "") ?>>3rd Grade</option>
	              		<option <?php echo ($child['grade'] == "4th Grade" ? "selected" : "") ?>>4th Grade</option>
	              		<option <?php echo ($child['grade'] == "5th Grade" ? "selected" : "") ?>>5th Grade</option>
	              		<option <?php echo ($child['grade'] == "6th Grade" ? "selected" : "") ?>>6th Grade</option>
	              		<option <?php echo ($child['grade'] == "7th Grade" ? "selected" : "") ?>>7th Grade</option>
	              		<option <?php echo ($child['grade'] == "8th Grade" ? "selected" : "") ?>>8th Grade</option>
	              		<option <?php echo ($child['grade'] == "9th Grade" ? "selected" : "") ?>>9th Grade</option>
	              		<option <?php echo ($child['grade'] == "10th Grade" ? "selected" : "") ?>>10th Grade</option>
	              		<option <?php echo ($child['grade'] == "11th Grade" ? "selected" : "") ?>>11th Grade</option>
	              		<option <?php echo ($child['grade'] == "12th Grade" ? "selected" : "") ?>>12th Grade</option>
	              		<option <?php echo ($child['grade'] == "Adult" ? "selected" : "") ?>>Adult</option>
	              	</select>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "birthdate"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Birthdate</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo date("n/d/Y",$child['birthdate']) ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "notes"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Allergies / Special Notes</label>
	              <div class="controls">
	                <textarea class="input-xlarge" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"><?php echo $child['notes'] ?></textarea>
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
<?php } else if ($_GET['id'] == "new") { ?>
	<div class="span12">
		<h3>New Child</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/children/new" method="POST">
				<input type="hidden" name="action" value="doAddChild" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "grade"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Grade</label>
	              <div class="controls">
					<select name="<?php echo $fieldName ?>">
	              		<option value="">Choose...</option>
	              		<option>Under 4</option>
	              		<option>Pre-Kindergarten</option>
	              		<option>Kindergarten</option>
	              		<option>1st Grade</option>
	              		<option>2nd Grade</option>
	              		<option>3rd Grade</option>
	              		<option>4th Grade</option>
	              		<option>5th Grade</option>
	              		<option>6th Grade</option>
	              		<option>7th Grade</option>
	              		<option>8th Grade</option>
	              		<option>9th Grade</option>
	              		<option>10th Grade</option>
	              		<option>11th Grade</option>
	              		<option>12th Grade</option>
	              		<option>Adult</option>
	              	</select>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "birthdate"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Birthdate</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
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
		<h2>Children</h2>
		
		<hr>
		
		
		<?php
		$ps = $pdo->prepare("SELECT * FROM children WHERE parent = ?");
		$ps->execute(array($user['id']));
		$children = $ps->fetchAll();
		?>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Grade</th>
                  <th>Birthdate</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($children as $child) { ?>
                <tr>
                  <td><a href="/children/<?php echo $child['id'] ?>"><?php echo $child['name'] ?></a></td>
                  <td><?php echo $child['grade'] ?></td>
                  <td><?php echo date("m/d/Y", $child['birthdate']) ?> (<?php echo getAge($child['birthdate']) ?> years old)</td>
                  <td>    
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/children/<?php echo $child['id'] ?>"><i class="icon-pencil"></i> View / Edit</a></li>
				    	<li class="divider"/>
				    	<li><a href="/?action=doDeleteChild&id=<?php echo $child['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$children) { ?>
                <tr>
                  <td colspan="4">No Children Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <p><a href="/children/new" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add Child</a></p>
	</div>
	<?php } ?>

</div>