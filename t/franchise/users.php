<div class="row">
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

        	<h4>Children</h4>

	        <table class="table table-striped">
				<thead>
					<tr>
						<th>Name</th>
						<th>Grade</th>
						<th>Birthdate</th>
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
					</tr>
					<?php } ?>
				</tbody>
	        </table>

        </div>

        <div class="span6">

        	<h4>Registrations</h4>

	        <table class="table table-striped">
				<thead>
					<tr>
						<th>Name</th>
						<th>Class</th>
						<th>Register Date</th>
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
	<?php } ?>

</div>

