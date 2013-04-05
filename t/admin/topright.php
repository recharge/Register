<?php if ($uid == "") { ?>

<?php } else { ?>
	<form class="navbar-form pull-right">
		<ul class="nav nav-pills" style="margin-right: 0px;">
			<li class="disabled">
				<a href="#"> 
					<strong>Welcome, <?php echo $user['name']; ?></strong>
				</a>
			</li>
		</ul>
		<a class="btn" href="/?action=doLogout">Sign Out</a>
	</form>
<?php } ?>