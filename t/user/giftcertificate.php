<div class="row">
<?php
$ps = $pdo->prepare("SELECT *, sum(plus)-sum(minus) as balance FROM giftcertificates WHERE code = ?");
$ps->execute(array($params[1]));
$giftcertificate = $ps->fetch(PDO::FETCH_ASSOC);
?>
<?php if ($giftcertificate['id'] != "") { ?>
	<div class="span12">
		<h3>Gift Certificate Lookup</h3>
	<hr>
	<div class="row">
			<div class="span6">
			<dl class="dl-horizontal">
			    <dt>Code</dt>
			    <dd><?php echo $giftcertificate['code'] ?></dd>
			    <dt>Balance</dt>
			    <dd>$<?php echo number_format($giftcertificate['balance'], 2) ?></dd>
			</dl>
			</div>
			<div class="span6">
			<h5>Transaction History</h5>
			<table class="table table-bordered">
				<tr>
					<th>
						Date
					</th>
					<th>
						Credit
					</th>
					<th>
						Debit
					</th>
				</tr>
				<?php
				$ps = $pdo->prepare("SELECT * FROM giftcertificates WHERE code = ? ORDER BY id");
				$ps->execute(array($params[1]));
				$transactions = $ps->fetchAll();
				foreach ($transactions as $transaction) {
				?>
				<tr>
					<td>
						<?php echo date("m/d/Y g:i a", $transaction['ts']); ?>
					</td>
					<td>
						<?php echo ($transaction['plus'] > 0 ? "$".number_format($transaction['plus'], 2) : "") ?>
					</td>
					<td>
						<?php echo ($transaction['minus'] > 0 ? "$".number_format($transaction['minus'], 2) : "") ?>
					</td>
				</tr>
				<?php } ?>
			</table>
			</div>
	</div>
	</div>
<?php } else { ?>
	<div class="span12">
		<h3>Gift Certificate Not Found</h3>
	</div>
<?php } ?>

</div>