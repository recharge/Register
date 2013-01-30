<div class="row">
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
	<?php
		$page = ($_GET['p'] == "" ? 1 : $_GET['p']);
		$resultsPerPage = 20;
		$limit = ($page-1) * $resultsPerPage;
	
		if ($_GET['q'] == "") {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM users LIMIT $limit,$resultsPerPage");
			$ps->execute();
			$customers = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		} else {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM users WHERE name LIKE ? LIMIT $limit,$resultsPerPage");
			$ps->execute(array("%".$_GET['q']."%"));
			$customers = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		}

		$pages = ceil($rows / $resultsPerPage);

		?>
	<div class="span10">
		<h2>Customers <small>Showing <?php echo $limit+1 ?>-<?php echo min($limit+$resultsPerPage, $rows) ?> of <?php echo $rows ?></small></h2>
		
		<div class="pull-right" style="margin-top: -30px; margin-bottom: -20px;">
		<form class="form-search" action="/admin/customers/" method="get">
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
                  <th>Email</th>
                  <th>Home Franchise</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($customers as $customer) { ?>
              	<?php
				$ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
				$ps->execute(array($customer['home_franchise']));
				$franchise = $ps->fetch(PDO::FETCH_ASSOC);
				?>
                <tr>
                  <td><?php echo $customer['name'] ?></td>
                  <td><a href="mailto:<?php echo $customer['email'] ?>"><?php echo $customer['email'] ?></a></td>
                  <td><a href="/admin/franchises/<?php echo $franchise['id'] ?>"><?php echo $franchise['name'] ?></a></td>
                  <td>
                  	<?php if ($customer['active']) { ?>
                  		<span class="badge badge-success">Active</span></td>
                  	<?php } else { ?>
                  		<span class="badge badge-important">Locked</span></td>
                  	<?php } ?>
                  	
                  <td>    
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="?action=doLoginAsUser&id=<?php echo $customer['id'] ?>"><i class="icon-eye-open"></i> Login As</a></li>
				    	<li><a href="?action=doToggleEnabledUser&id=<?php echo $customer['id'] ?>"><i class="icon-lock"></i> Lock / Unlock</a></li>
				    	<li class="divider"/>
				    	<li><a href="?action=doDeleteUser&id=<?php echo $customer['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$customers) { ?>
                <tr>
                  <td colspan="4">No Customers Found</td>
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
</div>