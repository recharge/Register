<div class="row">
<?php
$ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$ps->execute(array($_GET['id']));
$class = $ps->fetch(PDO::FETCH_ASSOC);

$ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
$ps->execute(array($class['franchise']));
$franchise = $ps->fetch(PDO::FETCH_ASSOC);
?>
<?php if ($ps->rowCount() != 0) { ?>
	<div class="span3">
		<div style="">
			<?php if ($class['img'] == "") { ?>
				<img src="http://s3.amazonaws.com/register_core/firm/registration_branding_decorators/logos/4fb9/60f4/ea2f/d900/0700/0003/medium/ka_logo.png?1337549105" class="img-polaroid">
			<?php } else { ?>
				<img src="/img/uploads/<?php echo $class['img'] ?>" class="img-polaroid">
			<?php } ?>
		</div>
	</div>
	<div class="span8 offset">
		<h2><?php echo $class['name'] ?></h2>
		<h5>Offered by <?php echo $franchise['name'] ?><span class="pull-right"><a href="/" class="btn">Go Back</a></span></h5>
	
	<hr>
	
	<dl class="dl-horizontal">
	    <dt>Name</dt>
	    <dd><?php echo $class['name'] ?></dd>
	    
	    <dt>Description</dt>
	    <dd><?php echo $class['description'] ?>&nbsp;</dd>
    
    	<dt>Dates</dt>
	    <dd><strong><?php echo date("l F jS Y", $class['startdate']) ?></strong> <small>through</small> <strong><?php echo date("l F jS Y", $class['enddate']) ?></strong></dd>
	    
	    <?php
		$ps = $pdo->prepare("SELECT * FROM meetings WHERE class = ? ORDER BY day");
		$ps->execute(array($class['id']));
		$meetings = $ps->fetchAll();
		$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
		$meeting_days = array();
		$meeting_locations = array();
		foreach ($meetings as $meeting) {
			$meeting_days[] = $days[$meeting['day']-1];
			
			$ps = $pdo->prepare("SELECT name FROM meeting_places WHERE id = ?");
			$ps->execute(array($meeting['location']));
			$meeting['location'] = $ps->fetchColumn();
			if (!in_array($meeting['location'], $meeting_locations)) {
				$meeting_locations[] = $meeting['location'];

			}
		}
		$meeting_days = implode(", ", $meeting_days);
		$meeting_locations = implode(", ", $meeting_locations);
		?>
	    <dt>Meeting Days</dt>
	    <dd><?php echo $meeting_days ?> <font size="2"><b><u><a href="#details" data-toggle="modal">Show Details</a></u></b></font></dd>
	    
	    <dt>Location</dt>
	    <dd><?php echo $meeting_locations ?></dd> 
	    
	    <dt>Price</dt>
	    <dd>
	    	<strong >$<?php echo $class['price'] ?></strong>
	    	<?php if ($class['payments_price'] > 0) { ?>
		    	<?php $pricing = calculateMonthlyPayments($class['startdate'], $class['enddate'], $class['payments_price']); ?>
		    	<?php if ($pricing['months'] > 1) { ?>
		    	 	or <strong><?php echo $pricing['months'] ?> monthly <?php echo $pricing['plural'] ?> of $<?php echo $pricing['amount'] ?></strong>
		    	 <?php } ?>
	    	<?php } ?>
	    </dd> 
		
	</dl>
	
	<legend>Register for this class</legend>
	
	<form class="bs-docs-example form-horizontal" action="" method="post" id="registerForm">
	<input type="hidden" name="action" value="doRegisterChild">
	<input type="hidden" name="id" value="<?php echo $class['id'] ?>">
	
	<?php
	$ps = $pdo->prepare("SELECT * FROM children WHERE parent = ? AND id NOT IN (SELECT child FROM students WHERE class = ?) ORDER BY birthdate");
	$ps->execute(array($user['id'], $class['id']));
	$children = $ps->fetchAll();
	?>
	
	<div class="control-group <?php if (!$children) { echo "error"; } ?>">
      <label for="inputEmail" class="control-label">Student Name</label>
      <div class="controls">
        <select name="child">
        <?php foreach ($children as $child) { ?>
        	<option value="<?php echo $child['id'] ?>"><?php echo $child['name'] ?></option>
        <?php } ?>
        </select>
        <?php if (!$children) { ?>
        	<span class="help-block"><strong>All your children have been registered for this class.<br><a href="/children/new">Click here to add a child.</a></strong></span>
        <?php } ?>
      </div>
    </div>
    
    <script>
    	$(document).ready(function() {
		    $("#pricing").change(function() {
		        $("#onetime").toggle();
		        $("#payments").toggle();
		    });
		    $("#registerForm").submit(function() {
		        if (!$("#tc").is(':checked')) {
		        	alert("You must accept the Terms and Conditions.");
		        	return false;
		        }
		    });
		});
    </script>
    
    <?php if ($children) { ?>
    
    <div class="control-group">
      <label for="inputEmail" class="control-label">Price Option</label>
      <div class="controls">
        <select name="pricing" id="pricing">
        	<option value="0">1-time payment of $<?php echo $class['price'] ?></option>
        	<?php if ($class['payments_price'] > 0) { ?>
        	<option value="1"><?php echo $pricing['months'] ?> monthly <?php echo $pricing['plural'] ?> of $<?php echo $pricing['amount'] ?></option>
        	<?php } ?>
        </select>
        <span class="help-block">
	        <div id="onetime">Your card will be charged $<?php echo $class['price'] ?> today</div>
	        <div id="payments" class="hide">Your card will be charged $<?php echo $pricing['amount'] ?> today</div>
        </span>
      </div>
    </div>

    <?php
	$ps = $pdo->prepare("SELECT * FROM customfields_keys WHERE franchise = ?");
	$ps->execute(array($class['franchise']));
	$customfields = $ps->fetchAll();
	?>

	<?php foreach ($customfields as $field) { ?>

	<div class="control-group">
      <label for="inputEmail" class="control-label"><?php echo $field['name'] ?></label>
      <div class="controls">
      	<?php if ($field['type'] == 0) { ?>
        	<input type="text" name="custom[<?php echo $field['id'] ?>]">
        <?php } ?>
        <?php if ($field['type'] == 1) { ?>
        	<select name="custom[<?php echo $field['id'] ?>]">
        		<?php $options = explode(",", $field['values']); ?>
        			<option value="">Choose...</option>
        		<?php foreach ($options as $option) { ?>
        			<option><?php echo $option ?></option>
        		<?php } ?>
        	</select>
        <?php } ?>
        <?php if ($field['type'] == 2) { ?>
        	<?php $options = explode(",", $field['values']); ?>
    		<?php foreach ($options as $option) { ?>
    			<label class="checkbox inline">
					<input type="checkbox" name="custom[<?php echo $field['id'] ?>][]" value="<?php echo $option ?>"> <?php echo $option ?>
				</label>
    		<?php } ?>
        <?php } ?>
        <?php if ($field['helptext'] != "") { ?>
        	<div class="help-block"><?php echo $field['helptext'] ?></div>
        <?php } ?>
      </div>
    </div>

    <?php } ?>
    
    <div class="control-group">
      <label for="inputEmail" class="control-label"><strong>Terms and Conditions</strong></label>
      <div class="controls">
      	<label class="checkbox inline">
        <input type="checkbox" id="tc" value="1"> I have read and agreed to the <a href="#toc" data-toggle="modal">KidzArt Terms and Conditions</a>.
      	</label>
      </div>
    </div>
            
    <div class="form-actions">
    	<?php if ($franchise['rechargeApiKey'] == "" || $franchise['active'] != 1 || $franchise['live'] != 1) { ?>
	    Sorry, this franchise is currently not accepting registrations. <br>
	    Please check back later.
	    <?php } else { ?>
	    <button type="submit" class="btn btn-success"><i class="icon-ok-sign icon-white"></i> Register</button>
	    <?php } ?>
	</div>
	
	<?php } ?>
    
	</form>
	

	</div>
	
	<div class="modal hide fade" id="details">
	    <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		    <h3>Class Details</h3>
	    </div>
	    <div class="modal-body">
               <dl class="dl-horizontal">
               	<?php
				$ps = $pdo->prepare("SELECT * FROM meetings WHERE class = ? ORDER BY day");
				$ps->execute(array($class['id']));
				$meetings = $ps->fetchAll();
				$days = array('Sundays','Mondays','Tuesdays','Wednesdays','Thursdays','Fridays','Saturdays');
				?>
				<?php foreach ($meetings as $meeting) { ?>
				<?php 
				$ps = $pdo->prepare("SELECT name FROM meeting_places WHERE id = ?");
				$ps->execute(array($meeting['location']));
				$meeting['location'] = $ps->fetchColumn();
				?>
			    <dt><?php echo $days[$meeting['day']-1] ?></dt>
			    <dd><strong><?php echo date("g:i a", $meeting['time']) ?></strong> at <strong ><?php echo $meeting['location'] ?></strong></dd>
			    <?php } ?>
			   </dl>
  	    </div>
	    <div class="modal-footer">
		    <a href="#" class="btn" data-dismiss="modal">Close</a>
	    </div>
</div>

	<?php } else { ?>
	
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
	<div class="span10">
		<h2>My Classes</h2>
		
		<hr>
		
		
		<?php
		$ps = $pdo->prepare("SELECT * FROM students WHERE parent = ?");
		$ps->execute(array($user['id']));
		$registrations = $ps->fetchAll();
		?>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Class Name</th>
                  <th>Student Name</th>
                  <th>Dates</th>
                  <th>Meeting Days</th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($registrations as $registration) { ?>
              		<?php
					$ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
					$ps->execute(array($registration['class']));
					$class = $ps->fetch(PDO::FETCH_ASSOC);
					?>
					<?php
					$ps = $pdo->prepare("SELECT * FROM children WHERE id = ?");
					$ps->execute(array($registration['child']));
					$child = $ps->fetch(PDO::FETCH_ASSOC);
					?>
              		<?php
					$ps = $pdo->prepare("SELECT * FROM meetings WHERE class = ? ORDER BY day");
					$ps->execute(array($class['id']));
					$meetings = $ps->fetchAll();
					$days = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
					$meeting_days = array();
					foreach ($meetings as $meeting) {
						$meeting_days[] = $days[$meeting['day']-1];
					}
					$meeting_days = implode(", ", $meeting_days);
					?>
                <tr>
                  <td><?php echo $class['name'] ?></td>
                  <td><?php echo $child['name'] ?></td>
                  <td><?php echo date("M d Y", $class['startdate']) ?> - <?php echo date("M d Y", $class['enddate']) ?></td>
                  <td><?php echo $meeting_days ?></td>
                </tr>
                <?php } ?>
                <?php if (!$registrations) { ?>
                <tr>
                  <td colspan="4">You haven't registered for any classes!</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
	</div>
	<?php } ?>

</div>

<div class="modal hide fade" id="toc">
	    <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		    <h3>Terms and Conditions</h3>
	    </div>
	    <div class="modal-body">
            <p>
            	I waive any right to claim against KidzArt/Art Innovators owners, staff and teachers in the event of an accident, injury or loss of personal items.
            </p>
            <p>
            	I understand I am committing to participation in KidzArt/Art Innovators and reserving a place in class for the designated session below, KidzArt/Art Innovators does not offer refunds for tuition paid but will provide a credit towards future KidzArt/Art Innovators programs when warranted.
            </p>
            <p>
            	I understand it is my responsibility to pick up my child from the designated location at the designated end time unless other arrangements have been made.
            </p>
            <p>
            	I authorize the release of my child's artwork for display purposes by KidzArt/Art Innovators.
            </p>
	    </div>
	    <div class="modal-footer">
		    <a href="#" class="btn" data-dismiss="modal">Close</a>
	    </div>
</div>