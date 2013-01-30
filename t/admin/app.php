<?php

?>

<div class="row">
	<div class="span2">
		<?php include("t/admin/sidenav.php"); ?>
	</div>
	<div class="span10">
		<h2>Stats</h2>
		
		<hr>
		<div class="row-fluid">
			<div class="span4">
				<div class="well">
					<?php
					$ps = $pdo->prepare("SELECT count(id) as count FROM users");
					$ps->execute();
					$count = $ps->fetchColumn();
					?>
					<span class="pull-left"><strong><a href="/admin/customers/">Customers</a></strong></span>
					<span class="pull-right"><?php echo $count ?></span>
				</div>
			</div>
			<div class="span4">
				<div class="well">
					<?php
					$ps = $pdo->prepare("SELECT count(id) as count FROM franchises");
					$ps->execute();
					$count = $ps->fetchColumn();
					?>
					<span class="pull-left"><strong><a href="/admin/franchises/">Franchises</a></strong></span>
					<span class="pull-right"><?php echo $count ?></span>
				</div>
			</div>
			<div class="span4">
				<div class="well">
					<?php
					$ps = $pdo->prepare("SELECT count(id) as count FROM admin");
					$ps->execute();
					$count = $ps->fetchColumn();
					?>
					<span class="pull-left"><strong><a href="/admin/administrators/">Admins</a></strong></span>
					<span class="pull-right"><?php echo $count ?></span>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<h5>All Franchise Revenue Last 31 Days</h5>
				<div id="monthChart"></div>
			</div>
			<div class="span6">
				<h5>All Franchise Revenue Last 12 Months</h5>
				<div id="sixmonthChart"></div>
			</div>
		</div>
	</div>
</div>

<script src="/js/dashboardcharts.php"></script>