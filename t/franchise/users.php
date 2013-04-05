<div class="row">

	<?php
	$ps = $pdo->prepare("SELECT * FROM users WHERE id = ? AND home_franchise = ?");
	$ps->execute(array($id, $uid));
	$record = $ps->fetch(PDO::FETCH_ASSOC);
	?>

	<?php if ($record) { ?>
	<?php
	$ps = $pdo->prepare("SELECT sum(credit-debit) FROM transactions WHERE user = ?");
	$ps->execute(array($record['id']));
	$balance = round($ps->fetchColumn(), 2);
	?>
	<div class="span12">
		<h3>
			<?php echo $record['name'] ?>
			<?php if ($balance > 0) { ?>
				<span class="badge badge-success" style="font-size: 16px;">
					$<?php echo number_format($balance, 2, ".", ",") ?>
				</span>
			<?php } else if ($balance < 0) { ?>
				<span class="badge badge-important" style="font-size: 16px;">
					$<?php echo number_format($balance, 2, ".", ",") ?>
				</span>
			<?php } else { ?>
				<span class="badge" style="font-size: 16px;">
					$<?php echo number_format($balance, 2, ".", ",") ?>
				</span>
			<?php } ?>
			<span class="pull-right">
				<a href="/franchise/users" class="btn">Go Back</a>
			</span>
		</h3>
	<hr>
		
	<div class="row">

        <div class="span6">

        	<h4>
        		Children
        		<button class="btn btn-mini" data-toggle="modal" data-target="#addChildModal">
        			<i class="icon icon-plus"></i>
        		</button>
			</h4>

	        <table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Name</th>
						<th>Grade</th>
						<th>Birthdate</th>
						<th>Delete</th>
					</tr>
				</thead>
				<?php
				$ps = $pdo->prepare("SELECT * FROM children WHERE parent = ?");
				$ps->execute(array($record['id']));
				$children = $ps->fetchAll();
				?>
				<tbody>
					<?php foreach ($children as $child) { ?>
					<tr>
						<td><?php echo $child['name'] ?></td>
						<td><?php echo $child['grade'] ?></td>
						<td><?php echo date("F j, Y", $child['birthdate']) ?></td>
						<td>
							<a class="btn btn-mini" href="/franchise/users/<?php echo $record['id'] ?>?action=doFranchiseDeleteChild&id=<?php echo $child['id'] ?>&parent=<?php echo $record['id'] ?>">
			        			<i class="icon icon-trash"></i>
			        		</a>
        				</td>	
					</tr>
					<?php } ?>
					<?php if (!$children) { ?>
						<tr>
							<td colspan="4">No Children</td>
						</tr>
					<?php } ?>
				</tbody>
	        </table>

        </div>

        <div class="span6">

        	<h4>
        		Registrations
        		<button class="btn btn-mini" data-toggle="modal" data-target="#addRegistrationModal">
        			<i class="icon icon-plus"></i>
        		</button>
			</h4>

	        <table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Name</th>
						<th>Class</th>
						<th>Register Date</th>
						<th>Delete</th>
					</tr>
				</thead>
				<?php
				$ps = $pdo->prepare("SELECT * FROM students WHERE parent = ? ORDER BY registerdate DESC");
				$ps->execute(array($record['id']));
				$students = $ps->fetchAll();
				?>
				<tbody>
					<?php foreach ($students as $student) { ?>
					<?php
					$ps = $pdo->prepare("SELECT name FROM children WHERE id = ?");
					$ps->execute(array($student['child']));
					$child = $ps->fetch(PDO::FETCH_ASSOC);
					?>

					<?php
					$ps = $pdo->prepare("SELECT name FROM classes WHERE id = ?");
					$ps->execute(array($student['class']));
					$class = $ps->fetch(PDO::FETCH_ASSOC);
					?>
					<tr>
						<td><?php echo $child['name'] ?></td>
						<td><?php echo $class['name'] ?></td>
						<td><?php echo date("F j, Y", $student['registerdate']) ?></td>
						<td>
							<a class="btn btn-mini" href="/franchise/users/<?php echo $record['id'] ?>?action=doFranchiseDeleteRegistration&id=<?php echo $student['id'] ?>&parent=<?php echo $record['id'] ?>">
			        			<i class="icon icon-trash"></i>
			        		</a>
        				</td>
					</tr>
					<?php } ?>
					<?php if (!$students) { ?>
						<tr>
							<td colspan="4">No Registrations</td>
						</tr>
					<?php } ?>
				</tbody>
	        </table>

        </div>

    </div>

    <hr>

    <div class="row">

        <div class="span12">

        	<h4>
        		Billing History
        		<span class="pull-right">
					<a href="#paymentModal" role="button" data-toggle="modal" class="btn btn-primary btn-small">Accept Payment</a>
				</span>
			</h4>

	        <table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Date</th>
						<th>Description</th>
						<th>Credit</th>
						<th>Debit</th>
					</tr>
				</thead>
				<?php
				$ps = $pdo->prepare("SELECT * FROM transactions WHERE user = ? ORDER BY id DESC");
				$ps->execute(array($record['id']));
				$transactions = $ps->fetchAll();
				?>
				<tbody>
					<?php foreach ($transactions as $transaction) { ?>
						<?php if ($transaction['credit'] > 0) { ?>
						<tr>
							<td><?php echo date("F j, Y g:i a", $transaction['randate']) ?></td>
							<td>
								Payment <?php echo $transaction['cardType'] ?> <?php echo $transaction['maskedAcctNum'] ?> 
								<?php if ($transaction['approvalCode'] != "") { ?> 
									Approval #<?php echo $transaction['approvalCode'] ?> 
								<?php } else { ?>
									<?php echo $transaction['result'] ?> 
								<?php } ?>
							</td>
							<td>+$<?php echo number_format($transaction['credit'], 2, ".", ",") ?></td>
							<td></td>
						</tr>
						<?php } else { ?>
						<?php
						$ps = $pdo->prepare("SELECT name FROM classes WHERE id = ?");
						$ps->execute(array($transaction['class']));
						$class = $ps->fetch(PDO::FETCH_ASSOC);
						?>
						<?php
						$ps = $pdo->prepare("SELECT name FROM children WHERE id = ?");
						$ps->execute(array($transaction['child']));
						$child = $ps->fetch(PDO::FETCH_ASSOC);
						?>
						<tr>
							<td><?php echo date("F j, Y g:i a", $transaction['randate']) ?></td>
							<td><?php echo $class['name'] ?> / <?php echo $child['name'] ?></td>
							<td></td>
							<td>-$<?php echo number_format($transaction['debit'], 2, ".", ",") ?></td>
						</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
	        </table>
        </div>
        
    </div>

	</div>

	<div id="paymentModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form class="form-horizontal" action="/franchise/users/<?php echo $record['id'] ?>" style="margin: 0px;" method="post">
			<input type="hidden" name="action" value="doProcessCustomerPayment">
			<input type="hidden" name="id" value="<?php echo $record['id'] ?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Accept Payment</h3>
			</div>
			<div class="modal-body">
				
				<ul id="myTab" class="nav nav-tabs">
	              <li class="active"><a href="#cc" data-toggle="tab">Credit Card</a></li>
	              <li class=""><a href="#ck" data-toggle="tab">Check</a></li>
	              <li class=""><a href="#ca" data-toggle="tab">Cash</a></li>
	              <li class=""><a href="#ad" data-toggle="tab">Account Adjustment</a></li>
	            </ul>

	            <div id="myTabContent" class="tab-content">
	              <div class="tab-pane fade in active" id="cc">
	                
	                <div class="control-group">
	                  <label class="control-label" for="inputEmail">Amount</label>
	                  <div class="controls">
	                    <input type="text" name="ccamount">
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="inputEmail">Credit Card Number</label>
	                  <div class="controls">
	                    <input type="text" name="cc">
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="inputEmail">Exp Date</label>
	                  <div class="controls">
	                    <select name="expm" class="input-small">
	                    	<option value="01">1 - Jan</option>
	                    	<option value="02">2 - Feb</option>
	                    	<option value="03">3 - Mar</option>
	                    	<option value="04">4 - Apr</option>
	                    	<option value="05">5 - May</option>
	                    	<option value="06">6 - Jun</option>
	                    	<option value="07">7 - Jul</option>
	                    	<option value="08">8 - Aug</option>
	                    	<option value="09">9 - Sep</option>
	                    	<option value="10">10 - Oct</option>
	                    	<option value="11">11 - Nov</option>
	                    	<option value="12">12 - Dec</option>
	                    </select>
	                    <select name="expy" class="input-small">
	                    	<?php $i=0; $now=date('y'); while ($i <10) { ?>
	                    	<option value="<?php echo $now+$i ?>">20<?php echo $now+$i ?></option>
	                    	<?php $i++; ?>
	                    	<?php } ?>
	                    </select>
	                  </div>
	                </div>

	              </div>
	              <div class="tab-pane fade" id="ck">
	              	
	              	<div class="control-group">
	              	  <label class="control-label" for="inputEmail">Amount</label>
	              	  <div class="controls">
	              	    <input type="text" name="ckamount">
	              	  </div>
	              	</div>

	              	<div class="control-group">
	              	  <label class="control-label" for="inputEmail">Check Number</label>
	              	  <div class="controls">
	              	    <input type="text" name="cknumber">
	              	  </div>
	              	</div>

	              </div>
	              <div class="tab-pane fade" id="ca">

	              	<div class="control-group">
	              	  <label class="control-label" for="inputEmail">Amount</label>
	              	  <div class="controls">
	              	    <input type="text" name="caamount">
	              	  </div>
	              	</div>

	              </div>
	              <div class="tab-pane fade" id="ad">

	              	<div class="control-group">
	              	  <label class="control-label" for="inputEmail">Amount</label>
	              	  <div class="controls">
	              	    <input type="text" name="adamount">
	              	  </div>
	              	</div>

	              	<div class="control-group">
	              	  <label class="control-label" for="inputEmail">Note</label>
	              	  <div class="controls">
	              	    <input type="text" name="note">
	              	  </div>
	              	</div>

	              </div>
	            </div>
			</div>
			<div class="modal-footer">
				<a class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
				<button class="btn btn-primary">Process Payment</button>
			</div>
		</form>
	</div>

	<div id="addChildModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form class="form-horizontal" action="/franchise/users/new" style="margin: 0px;" method="post">
			<input type="hidden" name="action" value="doFranchiseAddChild">
			<input type="hidden" name="parent" value="<?php echo $record['id'] ?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Add Child</h3>
			</div>
			<div class="modal-body">

				            <?php $fieldName = "name"; ?>
				            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
				              <label for="inputEmail" class="control-label">Name</label>
				              <div class="controls">
				                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
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
				                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
				                <?php if (isset($badFields[$fieldName])) { ?>
				                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
				                <?php } ?>
				              </div>
				            </div>

			</div>
			<div class="modal-footer">
				<a class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
				<button class="btn btn-primary">Add Child</button>
			</div>
		</form>
	</div>

	<div id="addRegistrationModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form class="form-horizontal" action="/franchise/users/new" style="margin: 0px;" method="post">
			<input type="hidden" name="action" value="doFranchiseRegisterChild">
			<input type="hidden" name="parent" value="<?php echo $record['id'] ?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">New Registration</h3>
			</div>
			<div class="modal-body">

				            <?php $fieldName = "child"; ?>
				            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
				              <label for="inputEmail" class="control-label">Child</label>
				              <div class="controls">
								<select name="<?php echo $fieldName ?>">
									<?php
									$ps = $pdo->prepare("SELECT * FROM children WHERE parent = ?");
									$ps->execute(array($record['id']));
									$children = $ps->fetchAll();
									?>
				              		<option value="">Choose...</option>
				              		<?php foreach ($children as $child) { ?>
				              		<option value="<?php echo $child['id'] ?>"><?php echo $child['name'] ?></option>
				              		<?php } ?>
				              	</select>
				                <?php if (isset($badFields[$fieldName])) { ?>
				                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
				                <?php } ?>
				              </div>
				            </div>
				            
				            <?php $fieldName = "class"; ?>
				            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
				              <label for="inputEmail" class="control-label">Class</label>
				              <div class="controls">
								<select name="<?php echo $fieldName ?>">
									<?php
									$ps = $pdo->prepare("SELECT * FROM classes WHERE franchise = ? AND enddate > ?");
									$ps->execute(array($uid, mktime()));
									$classes = $ps->fetchAll();
									?>
				              		<option value="">Choose...</option>
				              		<?php foreach ($classes as $class) { ?>
				              		<option value="<?php echo $class['id'] ?>"><?php echo $class['name'] ?> (<?php echo date("m/d/Y", $class['startdate']) ?> - <?php echo date("m/d/Y", $class['enddate']) ?>) -- $<?php echo number_format($class['price'], 2) ?></option>
				              		<?php } ?>
				              	</select>
				                <?php if (isset($badFields[$fieldName])) { ?>
				                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
				                <?php } ?>
				              </div>
				            </div>

				                <?php
				            	$ps = $pdo->prepare("SELECT * FROM customfields_keys WHERE franchise = ?");
				            	$ps->execute(array($uid));
				            	$customfields = $ps->fetchAll();
				            	?>

				            	<?php foreach ($customfields as $field) { ?>

				            	<div class="control-group">
				                  <label for="inputEmail" class="control-label"><?php echo $field['name'] ?></label>
				                  <div class="controls">
				                  	<?php if ($field['type'] == 0) { ?>
				                    	<input type="text" name="custom[<?php echo $field['id'] ?>]">
				                    <?php } ?>
				                    <?php if ($field['type'] == 1) { ?>
				                    	<select name="custom[<?php echo $field['id'] ?>]">
				                    		<?php $options = explode(",", $field['values']); ?>
				                    			<option value="">Choose...</option>
				                    		<?php foreach ($options as $option) { ?>
				                    			<option><?php echo $option ?></option>
				                    		<?php } ?>
				                    	</select>
				                    <?php } ?>
				                    <?php if ($field['type'] == 2) { ?>
				                    	<?php $options = explode(",", $field['values']); ?>
				                		<?php foreach ($options as $option) { ?>
				                			<label class="checkbox inline">
				            					<input type="checkbox" name="custom[<?php echo $field['id'] ?>][]" value="<?php echo $option ?>"> <?php echo $option ?>
				            				</label>
				                		<?php } ?>
				                    <?php } ?>
				                    <?php if ($field['helptext'] != "") { ?>
				                    	<div class="help-block"><?php echo $field['helptext'] ?></div>
				                    <?php } ?>
				                  </div>
				                </div>

				                <?php } ?>

			</div>
			<div class="modal-footer">
				<a class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
				<button class="btn btn-primary">Register</button>
			</div>
		</form>
	</div>

	<?php } else { ?>

		<script type="text/javascript">
		$(function () {
	        $('.checkall').click(function () {
	            $(this).parents('form:eq(0)').find(':checkbox').attr('checked', this.checked);
	        });
	    });
	</script>

	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
    
	<div class="span10">
    
		<h2>Users</h2>

		<div class="pull-right" style="margin-top: -30px; margin-bottom: -20px;">
			<form class="form-search" action="/franchise/users/" method="get">
			<button type="button" class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
				<i class="icon icon-white icon-plus"></i> 
				Add User
			</button>
				<div class="input-append">
					<input type="text" class="span2 search-query" name="q">		
					<button type="submit" class="btn">Search</button>
				</div>
			</form>
		</div>
		
		<hr>
		
		<?php
		$ps = $pdo->prepare("SELECT *, (SELECT count(id) FROM children WHERE parent = users.id) as students FROM users WHERE home_franchise = ? AND name LIKE ?");
		$ps->execute(array($uid, "%{$_GET['q']}%"));
		$fields = $ps->fetchAll();
		?>
        <form action="/franchise/email/compose" method="post">
		<table class="table table-striped">
              <thead>
                <tr>
                  <th><input type="checkbox" class="checkall"></th>
                  <th>Name</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Students</th>
                  <th>Account Balance</th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($fields as $field) { ?>
              	<?php
              	$ps = $pdo->prepare("SELECT sum(credit-debit) FROM transactions WHERE user = ?");
              	$ps->execute(array($field['id']));
				$balance = round($ps->fetchColumn(), 2);
              	?>
                <tr>
                  <td><input type="checkbox" name="parent[]" value="<?php echo $field['id'] ?>"></td>
                  <td><a href="/franchise/users/<?php echo $field['id'] ?>"><?php echo $field['name'] ?></a></td>
                  <td><?php echo formatphone($field['phone']) ?></td>
                  <td><?php echo $field['email'] ?></td>
                  <td><?php echo $field['students'] ?></td>
                  <td>
                  	<?php if ($balance > 0) { ?>
                  		<span class="badge badge-success">
                  			$<?php echo number_format($balance, 2, ".", ",") ?>
                  		</span>
                  	<?php } else if ($balance < 0) { ?>
                  		<span class="badge badge-important">
                  			$<?php echo number_format($balance, 2, ".", ",") ?>
                  		</span>
                  	<?php } else { ?>
                  		<span class="badge">
                  			$<?php echo number_format($balance, 2, ".", ",") ?>
                  		</span>
                  	<?php } ?>
                  </td>
                </tr>
                <?php } ?>
                <?php if (!$fields) { ?>
                <tr>
                  <td colspan="5">No Users Associated With Your Franchise</td>
                </tr>
                <?php } ?>
              </tbody>
        </table>
            
        <button type="submit" class="btn btn-primary"><i class="icon-envelope icon-white"></i> Send Email</button>
        </form>	
	</div>

	<div id="addUserModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form class="form-horizontal" action="/franchise/users/new" style="margin: 0px;" method="post">
			<input type="hidden" name="action" value="doFranchiseAddUser">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Add User</h3>
			</div>
			<div class="modal-body">

				<h4>Primary Parent</h4>

				<div class="control-group">
					<label class="control-label" for="inputEmail">Name</label>
					<div class="controls">
						<input type="text" name="parent[name]">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputEmail">Email</label>
					<div class="controls">
						<input type="text" name="parent[email]">
					</div>
				</div>

				<hr>

				<h4>Children</h4>

				<table class="table table-striped">
					<tbody>
						<tr>
							<td>
								<input type="text" class="span2" placeholder="Name" name="name[]">
							</td>
							<td>
								<select name="grade[]" class="span3">
				              		<option value="">Grade...</option>
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
							</td>
							<td>
								<input type="text" class="span2" placeholder="Birthdate"  name="birthdate[]">
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" class="span2" placeholder="Name" name="name[]">
							</td>
							<td>
								<select name="grade[]" class="span3">
				              		<option value="">Grade...</option>
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
							</td>
							<td>
								<input type="text" class="span2" placeholder="Birthdate"  name="birthdate[]">
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" class="span2" placeholder="Name" name="name[]">
							</td>
							<td>
								<select name="grade[]" class="span3">
				              		<option value="">Grade...</option>
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
							</td>
							<td>
								<input type="text" class="span2" placeholder="Birthdate"  name="birthdate[]">
							</td>
						</tr>
					</tbody>
				</table>

			</div>
			<div class="modal-footer">
				<a class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
				<button class="btn btn-primary">Add User</button>
			</div>
		</form>
	</div>
	<?php } ?>

</div>
