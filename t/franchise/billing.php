<script type="text/javascript">
	$(function() {
		$( "#from" ).datepicker({
	      	changeMonth: true,
	      	changeYear: true,
			onSelect: function( selectedDate ) {
				$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#to" ).datepicker({
	      	changeMonth: true,
	      	changeYear: true
	    });
	});
</script>
<script language="JavaScript">
	<!--
	function MM_jumpMenu(targ,selObj,restore){ //v3.0
	eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	if (restore) selObj.selectedIndex=0;
	}
	//-->
</script>

<div class="row">
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
        
	<div class="span10">
		<h2>Transactions</h2>
		
		<hr>

		<?php
		$quickDate = array();

		$quickDate[0]['label'] = "Today";
		$quickDate[0]['start'] = strtotime("today 0:00:00");
		$quickDate[0]['end'] = strtotime("today 23:59:59");

		$quickDate[1]['label'] = "Last 7 Days";
		$quickDate[1]['start'] = strtotime("-7 days");
		$quickDate[1]['end'] = strtotime("today 23:59:59");

		$quickDate[2]['label'] = "This Month";
		$quickDate[2]['start'] = strtotime("first day of this month");
		$quickDate[2]['end'] = strtotime("last day of this month 23:59:59");

		$quickDate[3]['label'] = "This Year";
		$quickDate[3]['start'] = strtotime("first day of this year");
		$quickDate[3]['end'] = strtotime("december 31 23:59:59");

		$s = $quickDate[0]['start'];
		$e = $quickDate[0]['end'];

		if ($_GET['qd'] != "") {
			$s = $quickDate[$_GET['qd']]['start'];
			$e = $quickDate[$_GET['qd']]['end'];
		}
		if ($_GET['s'] != "" && $_GET['e'] != "") {
			$s = strtotime($_GET['s']);
			$e = strtotime($_GET['e'] . " 23:59:59");
		}
		?>

		<div class="navbar">
            <div class="navbar-inner">
                <a class="brando" href="#">Filter</a>
                <ul class="nav">
                	<li>
	                	<form class="form-inline" style="margin: 6px 0px 0px 0px;" action="/franchise/transactions">
	                		<select class="span2" onChange="MM_jumpMenu('parent',this,1)">
	                			<?php foreach ($quickDate as $i => $qd) { ?>
	                			<option <?php if ($_GET['qd'] == $i) { echo 'selected'; } ?> value="/franchise/transactions?qd=<?php echo $i ?>"><?php echo $qd['label'] ?></option>
	                			<?php } ?>
	                		</select>

	                		<div class="input-prepend">
        		            	<span class="add-on"><i class="icon-calendar"></i></span>
        		            	<input class="span2" type="text" name="s" id="from" value="<?php echo ($s != 0 ? date("m/d/Y", $s) : "") ?>">
        		            </div>
        		            -
        		            <div class="input-prepend">
        		            	<span class="add-on"><i class="icon-calendar"></i></span>
        		            	<input class="span2" type="text" name="e" id="to" value="<?php echo ($e != 0 ? date("m/d/Y", $e) : "") ?>">
        		            </div>

        		            <button type="submit" class="btn btn-primary" style="margin: 0px;">Filter</button>
	                	</form>
                	</li>
                </ul>
            </div>
        </div>
		
		
		<?php
		// get franchise API key
		$ps = $pdo->prepare("SELECT * FROM transactions WHERE franchise = ? AND randate BETWEEN $s AND $e AND credit > 0 ORDER BY randate DESC");
		$ps->execute(array($uid));
		$transactions = $ps->fetchAll();
		
		?>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Customer</th>
                  <th>Paid By</th>
                  <th>Result</th>
                  <th>Amount</th>
                  <th>App Code</th>
                  <th>Date / Time</th>
                </tr>
              </thead>
              <tbody>
              	<?php $total = 0; ?>
              	<?php foreach ($transactions as $transaction) { ?>
              	<?php
              	// get customer name
              	$ps = $pdo->prepare("SELECT name FROM users WHERE id = ?");
              	$ps->execute(array($transaction['user']));
              	$transaction['userName'] = $ps->fetchColumn();              	?>
	                <tr>
	                  <td><a href="/franchise/users/<?php echo $transaction['user'] ?>"><?php echo $transaction['userName'] ?></a></td>
	                  <td><?php echo $transaction['cardType'] ?> <?php echo $transaction['maskedAcctNum'] ?></td>
	                  <td><?php echo $transaction['result'] ?></td>
	                  <td>$<?php echo number_format($transaction['credit'], 2) ?></td> <?php $total += $transaction['credit']; ?>
	                  <td><?php echo $transaction['approvalCode'] ?></td>
	                  <td><?php echo date("m/d/Y g:i a", $transaction['randate']) ?></td>
	                </tr>
                <?php } ?>
                <?php if (count($transactions) == 0) { ?>
                <tr>
                  <td colspan="6">No Transactions Found</td>
                </tr>
                <?php } else { ?>
                <tr>
                  <td><strong>Total</strong></td>
                  <td colspan="2"></td>
                  <td><strong>$<?php echo number_format($total, 2) ?></strong></td>
                  <td colspan="2"></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
	</div>

</div>