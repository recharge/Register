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
      $ps = $pdo->prepare("SELECT * FROM classes WHERE franchise = ? AND enddate <= ? ORDER BY startdate");
      $ps->execute(array($uid, strtotime("tomorrow")));
    } else {
      $ps = $pdo->prepare("SELECT * FROM classes WHERE franchise = ? AND enddate >= ? ORDER BY startdate");
      $ps->execute(array($uid, strtotime("tomorrow")));
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
              <li><a href="/franchise/?action=doCopyClass&id=<?php echo $class['id'] ?>"><i class="icon-plus-sign"></i> Copy</a></li>
				    	<li><a href="/franchise/?action=doDeleteClass&id=<?php echo $class['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
              <?php } ?>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <tr>
                  <?php if ($_GET['show'] != "oldclasses") { ?>
                    <td colspan="5"><a href="/franchise/?show=oldclasses"><i class="icon-folder-open"></i> Old Classes</a></td>
                  <?php } else { ?>
                    <td colspan="5"><a href="/franchise/"><i class="icon-folder-open"></i> Current Classes</a></td>
                  <?php } ?>
                </tr>
                <?php if (!$classes) { ?>
                <tr><td colspan="5">No Classes Found</td></tr>
                <?php } ?>
              </tbody>
            </table>
            
            <?php if ($eid == "" || $employee['access'] >= 2) { ?>
           <p><a href="/franchise/class/new" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> Add New Class</a></p>
           <?php } ?>
	</div>
</div>

<div id="copyModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form class="form-horizontal" action="/franchise/" style="margin: 0px;" method="post">
      <input type="hidden" name="action" value="doCopyClass">
      <input type="hidden" name="id" value="<?php echo $record['id'] ?>">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Accept Payment</h3>
      </div>
      <div class="modal-body">
        
        <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#cc" data-toggle="tab">Credit Card</a></li>
                <li class=""><a href="#ck" data-toggle="tab">Check</a></li>
                <li class=""><a href="#ca" data-toggle="tab">Cash</a></li>
              </ul>

              <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="cc">
                  
                  <div class="control-group">
                    <label class="control-label" for="inputEmail">Amount</label>
                    <div class="controls">
                      <input type="text" name="ccamount">
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label" for="inputEmail">Credit Card Number</label>
                    <div class="controls">
                      <input type="text" name="cc">
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label" for="inputEmail">Exp Date</label>
                    <div class="controls">
                      <select name="expm" class="input-small">
                        <option value="01">1 - Jan</option>
                        <option value="02">2 - Feb</option>
                        <option value="03">3 - Mar</option>
                        <option value="04">4 - Apr</option>
                        <option value="05">5 - May</option>
                        <option value="06">6 - Jun</option>
                        <option value="07">7 - Jul</option>
                        <option value="08">8 - Aug</option>
                        <option value="09">9 - Sep</option>
                        <option value="10">10 - Oct</option>
                        <option value="11">11 - Nov</option>
                        <option value="12">12 - Dec</option>
                      </select>
                      <select name="expy" class="input-small">
                        <?php $i=0; $now=date('y'); while ($i <10) { ?>
                        <option value="<?php echo $now+$i ?>">20<?php echo $now+$i ?></option>
                        <?php $i++; ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                </div>
                <div class="tab-pane fade" id="ck">
                  
                  <div class="control-group">
                    <label class="control-label" for="inputEmail">Amount</label>
                    <div class="controls">
                      <input type="text" name="ckamount">
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label" for="inputEmail">Check Number</label>
                    <div class="controls">
                      <input type="text" name="cknumber">
                    </div>
                  </div>

                </div>
                <div class="tab-pane fade" id="ca">

                  <div class="control-group">
                    <label class="control-label" for="inputEmail">Amount</label>
                    <div class="controls">
                      <input type="text" name="caamount">
                    </div>
                  </div>

                </div>
              </div>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary">Process Payment</button>
      </div>
    </form>
  </div>