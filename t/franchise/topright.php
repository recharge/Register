<?php if ($uid == "") { ?>
<form class="navbar-form pull-right" action="" method="POST">
	<input type="hidden" name="action" value="doLoginFranchise" />
	<input class="span2" type="text" placeholder="Email" name="ka_email">
	<input class="span2" type="password" placeholder="Password" name="ka_password">
	<button type="submit" class="btn">Sign In</button>
</form>
<?php } else { ?>
<form class="navbar-form pull-right">
	<?php if ($eid == "") { ?>
	<ul class="nav nav-pills" style="margin-right: 0px;">
		<li class="disabled">
			<a href="#"> <strong>Welcome, <?php echo $user['name']; ?></strong>
			</a>
		</li>
	</ul>
	<?php } else { ?>
	<ul class="nav nav-pills" style="margin-right: 0px;">
		<li class="disabled">
			<a href="#"> <strong>Welcome, <?php echo $employee['name']; ?> (<?php echo $user['name']; ?>)</strong>
			</a>
		</li>
	</ul>
	<?php } ?>
	<a class="btn" href="?action=doLogout">Sign Out</a>
</form>
<?php } ?>