<div class="row">
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
    
	<?php
		$page = ($_GET['p'] == "" ? 1 : $_GET['p']);
		$resultsPerPage = 20;
		$limit = ($page-1) * $resultsPerPage;
		$q = "%".$_GET['q']."%";
	
		if ($_GET['q'] == "") {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM meeting_places WHERE name LIKE ? OR tags LIKE ? ORDER BY name LIMIT $limit,$resultsPerPage");
			$ps->execute(array($q, $q));
			$meeting_places = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		} else {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM meeting_places WHERE name LIKE ? LIMIT $limit,$resultsPerPage");
			$ps->execute(array("%".$_GET['q']."%"));
			$meeting_places = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		}

		$pages = ceil($rows / $resultsPerPage);
		?>
    
<?php
$ps = $pdo->prepare("SELECT * FROM meeting_places WHERE id = ? AND franchise = ?");
$ps->execute(array($id, $uid));
$child = $ps->fetch(PDO::FETCH_ASSOC);
?>
<?php if ($child) { ?>
	<div class="span9">
		<h3><?php echo $child['name'] ?></h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/venues/<?php echo $child['id'] ?>" method="POST">
				<input type="hidden" name="action" value="doUpdateVenue" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $child[$fieldName] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "address"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Address</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $child[$fieldName] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
					<span class="help-block">Entering a correct address will help parents find the location and display a nice looking map!</span>

	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>

	</div>
<?php } else if ($id == "new") { ?>
	<div class="span10">
		<h3>New Venue</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/venues/new" method="POST">
				<input type="hidden" name="action" value="doAddVenue" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST['name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "address"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Address</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST['email'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	                <span class="help-block">Entering a correct address will help parents find the location and display a nice looking map!</span>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>

	</div>
	<?php } else { ?>
	<div class="span10">
		<h2>Venues <small>Showing <?php echo $limit+1 ?>-<?php echo min($limit+$resultsPerPage, $rows) ?> of <?php echo $rows ?></small></h2>
		
        <div class="pull-right" style="margin-top: -30px; margin-bottom: -20px;">
		<form class="form-search" action="/franchise/venues/" method="get">
		    <div class="input-append">
			    <input type="text" class="span2 search-query" name="q">
			    <button type="submit" class="btn">Search</button>
		    </div>
		</form>
		</div>
		
		<hr>
		
		
		<?php
		$ps = $pdo->prepare("SELECT * FROM meeting_places WHERE franchise = ?");
		$ps->execute(array($uid));
		$children = $ps->fetchAll();
		?>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Address</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($children as $child) { ?>
                <tr>
                  <td><a href="/franchise/venues/<?php echo $child['id'] ?>"><?php echo $child['name'] ?></a></td>
                  <td><?php echo $child['address'] ?></td>
                  <td>    
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/franchise/venues/<?php echo $child['id'] ?>"><i class="icon-pencil"></i> View / Edit</a></li>
					    	<li class="divider"/>
					    	<li><a href="/franchise/?action=doDeleteVenue&id=<?php echo $child['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$children) { ?>
                <tr>
                  <td colspan="3">No Venues Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <p><a href="/franchise/venues/new" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add Venue</a></p>
	</div>
	<?php } ?>

</div>