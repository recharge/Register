<?php

?>
<div class="row">
	<div class="span2">
    <?php include("sidenav.php"); ?>
  </div>
	<div class="span10">
		<h2>Classes</h2>
		
		<hr>
    
		<?php
    if ($_GET['show'] == "oldclasses") {
      $ps = $pdo->prepare("SELECT * FROM classes WHERE franchise = ? AND enddate < ? ORDER BY startdate");
      $ps->execute(array($uid, mktime()));
    } else {
      $ps = $pdo->prepare("SELECT * FROM classes WHERE franchise = ? AND enddate > ? ORDER BY startdate");
      $ps->execute(array($uid, mktime()));
    }
		$classes = $ps->fetchAll();
		?>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Dates</th>
                  <th>Students</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($classes as $class) { ?>
              	<?php 
              	$ps = $pdo->prepare("SELECT count(id) FROM students WHERE class = ?");
				$ps->execute(array($class['id']));
				$studentCount = $ps->fetchColumn();
				?>
                <tr>
                  <td>
                  	<?php if ($eid == "" || $employee['access'] >= 1) { ?>
                  	<a href="/franchise/class/<?php echo $class['id'] ?>"><?php echo $class['name'] ?></a>
                  	<?php } else { ?>
                  	<?php echo $class['name'] ?>
                  	<?php } ?>
                  </td>
                  <td><?php echo date("M d Y", $class['startdate']) ?> - <?php echo date("M d Y", $class['enddate']) ?></td>
                  <td><?php echo $studentCount ?></td>
                  <td>
                  	<?php if ($class['active'] == 1) {?>
                  		<?php if ($studentCount >= $class['size_limit'] && $class['size_limit'] > 0) {?>
                  			<span class="badge badge-warning">Full</span>
                  		<?php } else { ?>
                  			<span class="badge badge-success">Active</span>
                  		<?php } ?>
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
				    	<?php if ($eid == "" || $employee['access'] >= 1) { ?>
				    	<li><a href="/franchise/class/<?php echo $class['id'] ?>"><i class="icon-pencil"></i> View / Edit</a></li>
				    	<?php } ?>
				    	
				    	<?php if ($eid == "" || $employee['access'] >= 0) { ?>
				    	<li><a href="/classsheet/<?php echo $class['id'] ?>"><i class="icon-print"></i> Print Roster</a></li>
				    	<?php } ?>
				    	
				    	
				    	<?php if ($eid == "" || $employee['access'] >= 2) { ?>
				    	<li class="divider"/>
				    	<li><a href="/franchise/?action=doDeleteClass&id=<?php echo $class['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    	<?php } ?>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                
                <?php if (!$classes) { ?>
                <tr><td colspan="5">No Classes Found</td></tr>
                <?php } ?><tr>
                  <?php if ($_GET['show'] != "oldclasses") { ?>
                    <td colspan="5"><a href="/franchise/?show=oldclasses"><i class="icon-folder-open"></i> Old Classes</a></td>
                  <?php } else { ?>
                    <td colspan="5"><a href="/franchise/"><i class="icon-folder-open"></i> Current Classes</a></td>
                  <?php } ?>
                </tr>
              </tbody>
            </table>
            
            <?php if ($eid == "" || $employee['access'] >= 2) { ?>
           <p><a href="/franchise/class/new" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> Add New Class</a></p>
           <?php } ?>
	</div>
</div>