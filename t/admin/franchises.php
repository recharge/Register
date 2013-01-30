<div class="row">
<?php
$ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
$ps->execute(array($id));
$franchise = $ps->fetch(PDO::FETCH_ASSOC);
?>
<?php if ($franchise) { ?>
	<div class="span12">
		<h3><?php echo $franchise['name'] ?></h3>
	
	<hr>
		
	<dl class="dl-horizontal">
	    <dt>Main Contact</dt>
	    <dd><?php echo $franchise['contact'] ?></dd>
	    
	    <dt>Phone</dt>
	    <dd><?php echo $franchise['phone'] ?></dd>
	    
	    <dt>Email</dt>
	    <dd><?php echo $franchise['email'] ?></dd>
	    
	    <dt>Location</dt>
	    <dd><?php echo $franchise['address'] ?></dd>
	</dl>
	
	<dl class="dl-horizontal">
		<?php
		$ps = $pdo->prepare("SELECT count(DISTINCT child) FROM students WHERE class IN (SELECT id FROM classes WHERE franchise = ?)");
		$ps->execute(array($franchise['id']));
		$count = $ps->fetchColumn();
		?>
	    <dt>Number Of Students</dt>
	    <dd><?php echo $count ?></dd>
	    
	    <?php
		$ps = $pdo->prepare("SELECT count(DISTINCT parent) FROM students WHERE class IN (SELECT id FROM classes WHERE franchise = ?)");
		$ps->execute(array($franchise['id']));
		$count = $ps->fetchColumn();
		?>
	    <dt>Number Of Customers</dt>
	    <dd><?php echo $count ?></dd>
	</dl>
	
	<dl class="dl-horizontal">
	    <dt>&nbsp;</dt>
	    <dd>
	    	<a href="/admin/franchises/" class="btn btn-primary"><i class="icon-chevron-left icon-white"></i>Back</a>
	    	<a href="?action=doLoginAsFranchise&id=<?php echo $franchise['id'] ?>" class="btn"><i class="icon-eye-open"></i> Login As</a>
	    	<?php if ($franchise['active']) { ?>
          		<a href="/admin/franchise?action=doToggleEnabledFranchise&id=<?php echo $franchise['id'] ?>" class="btn btn-danger"><i class="icon-lock icon-white"></i> Disable Account</a>
          	<?php } else { ?>
          		<a href="/admin/franchise?action=doToggleEnabledFranchise&id=<?php echo $franchise['id'] ?>" class="btn btn-success"><i class="icon-lock icon-white"></i> Enable Account</a>
          	<?php } ?>
          	<?php if ($franchise['live']) { ?>
          		<a href="/admin/franchise?action=doToggleStorefront&id=<?php echo $franchise['id'] ?>" class="btn btn-danger"><i class="icon-lock icon-white"></i> Disable Store</a>
          	<?php } else { ?>
          		<a href="/admin/franchise?action=doToggleStorefront&id=<?php echo $franchise['id'] ?>" class="btn btn-success"><i class="icon-lock icon-white"></i> Enable Store</a>
          	<?php } ?>
	    </dd>
	</dl>

	</div>
	<?php } else { ?>
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
	<?php
		$page = ($_GET['p'] == "" ? 1 : $_GET['p']);
		$resultsPerPage = 20;
		$limit = ($page-1) * $resultsPerPage;
	
		if ($_GET['q'] == "") {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM franchises LIMIT $limit,$resultsPerPage");
			$ps->execute();
			$franchises = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		} else {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM franchises WHERE name LIKE ? LIMIT $limit,$resultsPerPage");
			$ps->execute(array("%".$_GET['q']."%"));
			$franchises = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		}

		$pages = ceil($rows / $resultsPerPage);

		?>
	<div class="span10">
		<h2>Franchises <small>Showing <?php echo $limit+1 ?>-<?php echo min($limit+$resultsPerPage, $rows) ?> of <?php echo $rows ?></small></h2>
		
		<div class="pull-right" style="margin-top: -30px; margin-bottom: -20px;">
		<form class="form-search" action="/admin/franchises/" method="get">
		    <div class="input-append">
			    <input type="text" class="span2 search-query" name="q">
			    <button type="submit" class="btn">Search</button>
		    </div>
		</form>
		</div>
		
		<hr>
		
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>Email</th>
                  <th>Account</th>
                  <th>Storefront</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($franchises as $franchise) { ?>
              	
                <tr>
                  <td><a href="/admin/franchises/<?php echo $franchise['id'] ?>"><?php echo $franchise['name'] ?></a></td>
                  <td><?php echo $franchise['contact'] ?></td>
                  <td><a href="mailto:<?php echo $franchise['email'] ?>"><?php echo $franchise['email'] ?></a></td>
                  <td>
                  	<?php if ($franchise['active']) { ?>
                  		<span class="badge badge-success">Active</span>
                  	<?php } else { ?>
                  		<span class="badge badge-important">Locked</span>
                  	<?php } ?>
                  </td>
                  <td>
                  	<?php if ($franchise['live']) { ?>
                  		<span class="badge badge-success">Active</span>
                  	<?php } else { ?>
                  		<span class="badge badge-important">Inactive</span>
                  	<?php } ?>
                  </td>
                  <td>    
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/admin/franchise?action=doLoginAsFranchise&id=<?php echo $franchise['id'] ?>"><i class="icon-eye-open"></i> Login As</a></li>
				    	<li class="divider"/>
				    	<li><a href="/admin/franchise?action=doToggleEnabledFranchise&id=<?php echo $franchise['id'] ?>"><i class="icon-lock"></i> Lock / Unlock Account</a></li>
				    	<li><a href="/admin/franchise?action=doToggleStorefront&id=<?php echo $franchise['id'] ?>"><i class="icon-shopping-cart"></i> Lock / Unlock Store</a></li>
				    	<li class="divider"/>
				    	<li><a href="/admin/franchise?action=doDeleteFranchise&id=<?php echo $franchise['id'] ?>"><i class="icon-trash"></i> Delete Franchise</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$franchises) { ?>
                <tr>
                  <td colspan="4">No Franchises Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <div class="pagination">
              <ul>
              	<?php if ($page > 1) { ?>
                <li><a href="?q=<?php echo $_GET['q'] ?>&p=<?php echo $page-1 ?>">« Prev</a></li>
                <?php } else { ?>
                <li class="disabled"><a href="#">« Prev</a></li>
                <?php } ?>
                
                <?php $p = 1; while ($p <= $pages) { ?>
                <li <?php echo ($p == $page ? 'class="active"' : "") ?>><a href="?q=<?php echo $_GET['q'] ?>&p=<?php echo $p ?>"><?php echo $p ?></a></li>
                <?php $p++; ?>
                <?php } ?>
                
                <?php if ($page < $pages) { ?>
                <li><a href="?q=<?php echo $_GET['q'] ?>&p=<?php echo $page+1 ?>">Next »</a></li>
                <?php } else { ?>
                <li class="disabled"><a href="#">Next »</a></li>
                <?php } ?>
             </ul>
            </div>
            
	</div>
	<?php } ?>

</div>