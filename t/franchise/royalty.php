<div class="row">
<div class="row">
	

	<?php
	$ps = $pdo->prepare("SELECT * FROM royalty WHERE id = ?");
	$ps->execute(array($id));
	$record = $ps->fetch(PDO::FETCH_ASSOC);
	?>

	<?php if ($record) { ?>
	<div class="span12">
		<h3><?php echo $record['name'] ?></h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/employees/<?php echo $record['id'] ?>" method="POST">
				<input type="hidden" name="action" value="doUpdateEmployee" />
	            
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
	            
	            <?php $fieldName = "email"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Email</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $record['email'] ?>"/>
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
	                	<option<?php echo ($record['access'] == 2 ? " selected" : "") ?> value="2">Senior Employee</option>
	                	<option<?php echo ($record['access'] == 1 ? " selected" : "") ?> value="1">Junior Employee</option>
	                	<option<?php echo ($record['access'] == 0 ? " selected" : "") ?> value="0">Instructor</option>
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
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM royalty LIMIT $limit,$resultsPerPage");
			$ps->execute();
			$customers = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		} else {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM royalty WHERE name LIKE ? LIMIT $limit,$resultsPerPage");
			$ps->execute(array("%".$_GET['q']."%"));
			$customers = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		}

		$pages = ceil($rows / $resultsPerPage);

		?>



	<div class="span12">
		<h3>New Royalty Report</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/royalty/new" method="POST">
				<input type="hidden" name="action" value="doNewRoyaltyReport" />

				<h4>Select Month</h4>

				<select name="month">
					<option <?php echo (date('m') == 1 ? 'selected' : '') ?> >January</option>
					<option <?php echo (date('m') == 2 ? 'selected' : '') ?> >February</option>
					<option <?php echo (date('m') == 3 ? 'selected' : '') ?> >March</option>
					<option <?php echo (date('m') == 4 ? 'selected' : '') ?> >April</option>
					<option <?php echo (date('m') == 5 ? 'selected' : '') ?> >May</option>
					<option <?php echo (date('m') == 6 ? 'selected' : '') ?> >June</option>
					<option <?php echo (date('m') == 7 ? 'selected' : '') ?> >July</option>
					<option <?php echo (date('m') == 8 ? 'selected' : '') ?> >August</option>
					<option <?php echo (date('m') == 9 ? 'selected' : '') ?> >September</option>
					<option <?php echo (date('m') == 10 ? 'selected' : '') ?> >October</option>
					<option <?php echo (date('m') == 11 ? 'selected' : '') ?> >November</option>
					<option <?php echo (date('m') == 12 ? 'selected' : '') ?> >December</option>
				</select>


				<select name="year">
					<option>2012</option>
					<option selected>2013</option>
					<option>2014</option>
				</select>

				<hr>
	            
	            <h4>Revenue Sources</h4>

	            <table class="table table-striped">
	            	<thead>
	            		<tr>
	            			<th>Source</th>
	            			<th>Revenue</th>
	            			<th>Students</th>
	            			<th>Hourly Rate Per Student</th>
	            			<th>Classes</th>
	            			<th>Class Hours</th>
	            			<th>Advertising Costs</th>
	            		</tr>
	            	</thead>
	            	<?php
	            	$sources = array();
	            	$sources[] = 'Squiggles To Grins';
	            	$sources[] = 'Preschool Classes';
					$sources[] = 'Elementary Classes';
					$sources[] = 'Teenz Classes';
					$sources[] = 'Seniorz Classes';
					$sources[] = 'Art Innovators';
					$sources[] = 'Workshops';
					$sources[] = 'Grant Programs';
					$sources[] = 'Special Events';
					$sources[] = 'Birthday Parties';
					$sources[] = 'Core Programs';
					$sources[] = 'Corporate Events';
					$sources[] = 'Camps';
					$sources[] = 'Product Sales';
					$sources[] = 'KidzArt Product Sales (RF)';
					$sources[] = 'Other Revenue';
	            	?>
	            	<tbody>
	            		<?php foreach ($sources as $key => $value) { ?>
	            		<tr>
	            			<td><?php echo $value ?></td>
	            			<td><input type="text" class="input-small" name="revenue[]"></td>
	            			<td><input type="text" class="input-small" name="students[]"></td>
	            			<td><input type="text" class="input-small" name="hourlyrateperstudent[]"></td>
	            			<td><input type="text" class="input-small" name="classes[]"></td>
	            			<td><input type="text" class="input-small" name="classhours[]"></td>
	            			<td><input type="text" class="input-small" name="advertisingcosts[]"></td>
	            		</tr>
	            		<?php } ?>
	            	</tbody>
	            </table>

	            <hr>

	            <h4>Expenses</h4>

	            <table class="table table-striped">
	            	<?php
	            	$expenses = array();
	            	$expenses[] = 'Salaries';
					$expenses[] = 'Payroll';
					$expenses[] = 'Transportation & Travel';
					$expenses[] = 'Communications';
					$expenses[] = 'Accounting & Legal';
					$expenses[] = 'Rent & Utilities';
					$expenses[] = 'Licenses and Fees';
					$expenses[] = 'Insurance';
					$expenses[] = 'Site Fees';
					$expenses[] = 'Art Supplies';
					$expenses[] = 'Supplies';
					$expenses[] = 'Other';
					$expenses[] = 'Advertising and Marketing';
	            	?>
	            	<tbody>
	            		<?php foreach ($expenses as $key => $value) { ?>
	            		<tr>
	            			<td><?php echo $value ?></td>
	            			<td class=""><input type="text" class="input-medium" name="expenseamount[]"></td>
	            		</tr>
	            		<?php } ?>
	            	</tbody>
	            </table>

	            <hr>

	            <h4>Summary</h4>

	            <table class="table table-striped">
	            	<?php
	            	$summary = array();
	            	$summary[] = 'Number of Locations';
					$summary[] = 'Number of Current Employees';
					$summary[] = 'Number of Employees Lost';
					$summary[] = 'Minimum Royalty Due';
					$summary[] = 'Royalty Percentage';
					$summary[] = 'Minimum Ad Fund Due';
					$summary[] = 'Ad Fund Percentage';
	            	?>
	            	<tbody>
	            		<?php foreach ($summary as $key => $value) { ?>
	            		<tr>
	            			<td><?php echo $value ?></td>
	            			<td class=""><input type="text" class="input-medium" name="expenseamount[]"></td>
	            		</tr>
	            		<?php } ?>
	            	</tbody>
	            </table>

	            <hr>

	            <p class="well">
	            	I verify that the amounts on this report are true and correct to the best of my knowledge. By sending this report, and in conjunction with the Authorization for Direct Payment Form, I authorize that the funds are available and accessible for KidzArt Texas, LLC to deduct the amounts reported for the monthly royalty and ad fund contributions due on the 10th of each month.
	            </p>

	            <hr>
	            
	            <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
		</form>

	</div>
	<?php } else { ?>

	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
    
	<div class="span10">
    
		<h2>Royalty Reports</h2>
		
		<hr>
		
		<?php
		$ps = $pdo->prepare("SELECT * FROM royalty WHERE franchise = ?");
		$ps->execute(array($uid));
		$records = $ps->fetchAll();
		?>
        
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Month</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($records as $record) { ?>
                <tr>
                  <td><a href="/franchise/royalty/<?php echo $record['id'] ?>"><?php echo $record['month'] ?> <?php echo $record['year'] ?></a></td>
                  <td>    
                  	<div class="btn-group pull-right" style="">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/franchise/royalty/<?php echo $record['id'] ?>"><i class="icon-pencil"></i> View / Edit</a></li>
					    	<li class="divider"/>
					    	<li><a href="/franchise/?action=doDeleteRoyaltyReport&id=<?php echo $record['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$records) { ?>
                <tr>
                  <td colspan="3">No Royalty Reports Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <p><a href="/franchise/royalty/new" class="btn btn-primary"><i class="icon-plus icon-white"></i> New Royalty Report</a></p>
            		
	</div>
	<?php } ?>

</div>