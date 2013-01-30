<div class="row">
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
	<div class="span10">
		<h2>Billing</h2>
		
		<hr>
		
		
		<?php
		// get franchise API key
		$ps = $pdo->prepare("SELECT * FROM transactions WHERE user = ? AND debit = 0 ORDER BY randate DESC");
		$ps->execute(array($uid));
		$transactions = $ps->fetchAll();
		
		?>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Result</th>
                  <th>Amount</th>
                  <th>Approval Code</th>
                  <th>Date / Time</th>
                </tr>
              </thead>
              <tbody>
              	<?php $total = 0; ?>
              	<?php foreach ($transactions as $transaction) { ?>
              	<?php $data = $transaction['data']; ?>
	                <tr>
	                  <td><?php echo $transaction['cardType'] ?> <?php echo $transaction['maskedAcctNum'] ?></td>
	                  <td><?php echo $transaction['result'] ?></td>
	                  <td>$<?php echo number_format($transaction['credit'], 2) ?></td> <?php $total += $transaction['credit']; ?>
	                  <td><?php echo $transaction['approvalCode'] ?></td>
	                  <td><?php echo date("m/d/Y g:i a", $transaction['randate']) ?></td>
	                </tr>
                <?php } ?>
                <?php if (count($transactions) == 0) { ?>
                <tr>
                  <td colspan="5">No Transactions Found</td>
                </tr>
                <?php } else { ?>
                <tr>
                  <td><strong>Total</strong></td>
                  <td colspan="1"></td>
                  <td><strong>$<?php echo $total ?></strong></td>
                  <td colspan="2"></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
	</div>

</div>