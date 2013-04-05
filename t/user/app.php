<?php

?>
<script>
var address = "";
function geocode(lat, lon) {
	$.ajax({
      url: "/api/geocode",
      data: {
        lat: lat,
        lon: lon
      },
      success: function(data){
      //alert("hi");
        $("#user_location_field").val(data);
      }});
}
function find_user_location() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position){
          geocode(position.coords.latitude, position.coords.longitude);
        }, function() {
          alert('There was a problem detecting your location. Please type in an address instead');
          }
        );
    
      } else {
        alert('Your browser is too out of date to allow us to detect your location. Please type in an address instead');
      }
}
</script>
<div class="row">
		<?php if ($user['lati'] == "" || $user['long'] == "") { ?>
		
	<div class="span12">
		<script>
		find_user_location();
		</script>
		<h1>Welcome!</h1>
		<h4>Looks like we haven't met.</h4>
		
		<hr>
		
		<h5>Enter your address to select your home franchise:</h5>
		<form action="" method="POST">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-globe"></i></span>
				<input type="hidden" name="action" value="doFindHomeFranchiseByZIP">
				<input class="input-xxlarge" id="user_location_field" size="16" type="text" name="zip" placeholder="Home Address"><button class="btn" type="submit">Go!</button>
			</div>
		</form>
	</div>
		<?php } else if ($user['lati'] != "" && $user['long'] != "" && $user['home_franchise'] == "") { ?>
		
	<div class="span12">
		<h1><?php echo $user['location']; ?></h1>
		<?php if ($user['location'] != "") { ?>
		<h4>Nice place! <small><a href="/?action=doResetLocation">Not Correct?</a></small></h4>
		
		<hr>
		<?php } ?>
		
		<?php
			$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$ps = $pdo->prepare("SELECT * FROM (SELECT id, name, address, phone, ( 3959 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(?) ) + sin( radians(?) ) * sin( radians( lat ) ) ) ) AS distance FROM franchises WHERE live = 1 AND active = 1) as found WHERE distance < 25 ORDER BY distance");
			$ps->execute(array($user['lati'], $user['long'], $user['lati']));
			$franchises = $ps->fetchAll();
		?>
		<?php if ($franchises) { ?>
			<h5>We found <?php echo $ps->rowCount() ?> locations near you:</h5>
			    <table class="table table-striped">
	              <thead>
	                <tr>
	                  <th>Distance</th>
	                  <th>Name</th>
	                  <th>Address</th>
	                  <th>Phone Number</th>
	                  <th>Select Franchise</th>
	                </tr>
	              </thead>
	              <tbody>
	              	<?php foreach ($franchises as $franchise) { ?>
	                <tr>
	                  <td><?php echo round($franchise['distance'], 1); ?> miles</td>
	                  <td><?php echo $franchise['name']; ?></td>
	                  <td><?php echo $franchise['address']; ?></td>
	                  <td><?php echo $franchise['phone']; ?></td>
	                  <td><a href="/?action=doSetHomeFranchise&id=<?php echo $franchise['id']; ?>" class="btn btn-success"><i class="icon-home icon-white"></i> Set As Home</a></td>
	                </tr>
	                <?php } ?>
	              </tbody>
	            </table>
	      <?php } else { ?>
	      		<h5>We found 0 locations near you. <a href="/?action=doResetLocation">Try a different location?</a></h5>
	      <?php } ?>
	</div>
		<?php } else { ?>
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
	<div class="span10">
		<h2>Dashboard</h2>
		<?php
		$ps = $pdo->prepare("SELECT name FROM franchises WHERE id = ?");
		$ps->execute(array($user['home_franchise']));
		$franchise = $ps->fetchColumn();
		?>
		<hr>
		
<?php if ($resource != "venue") { ?>
	

	<h4>Class Locations for <?php echo $franchise ?> <small><a href="/?action=doResetHomeFranchise">Change Franchise</a></small></h4>

	<br>

	<?php
	$ps = $pdo->prepare("SELECT * FROM (
   SELECT location, class, 
      (SELECT count(id) FROM meeting_places WHERE id = meetings.location) AS count, 
      (SELECT name FROM meeting_places WHERE id = meetings.location) AS name, 
      (SELECT address FROM meeting_places WHERE id = meetings.location) AS address, 
      (SELECT id FROM meeting_places WHERE id = meetings.location) AS id
   FROM meetings WHERE franchise = ?
) AS x WHERE count > 0 AND class IN (
   SELECT id FROM classes WHERE franchise = ? AND enddate > ?
) GROUP BY location");
	$ps->execute(array($user['home_franchise'],$user['home_franchise'],mktime()));
	$venues = $ps->fetchAll();
	$i = 0;
	?>

	<?php
	$row = 0;
	$col = 0;
	?>
	<?php foreach ($venues as $venue) { ?>

		<?php if ($col == 0) { ?>
				<div class="row-fluid">
					<ul class="thumbnails">
		<?php } ?>
		              <li class="span4">
		                <div class="thumbnail" style="height: 240px; background: url(http://maps.googleapis.com/maps/api/staticmap?center=<?php echo urlencode($venue['address']) ?>&zoom=13&size=250x270&maptype=roadmap&markers=color:red|<?php echo urlencode($venue['address']) ?>&sensor=false)">
		                  <div class="caption top">
		                    <h3><?php echo $venue['name'] ?></h3>
		                  </div>
		                  <div class="caption bottom">
		                    <p><a href="/venue/<?php echo $venue['location'] ?>" class="btn btn-primary">See Classes</a></p>
		                  </div>
		                </div>
		              </li>
		              <?php $col++; ?>
		<?php if ($col > 2) { ?>
		            </ul>
		        </div>
		        <?php $col = 0; ?>
		<?php } ?>
	<?php } ?>

	<?php if ($col != 0) { ?>
		<?php while ($col < 3) { ?>
			<li class="span4">&nbsp;</li>
			<?php $col++; ?>
		<?php } ?>

		<?php if ($col > 2) { ?>
	            </ul>
	        </div>
	    	<?php $col = 0; ?>
		<?php } ?>
	<?php } ?>

<?php } else if ($resource == "venue" && $params[1] != "") { ?>

	<?php
	$ps = $pdo->prepare("SELECT id, name, address FROM meeting_places WHERE id = ?");
	$ps->execute(array($params[1]));
	$venues = $ps->fetchAll();
	$i = 0;
	?>

			<?php foreach ($venues as $venue) { ?>
                <?php
						$ps = $pdo->prepare("SELECT * FROM 
							(SELECT *, 
								(SELECT count(id) FROM students WHERE class = classes.id) as count,
								(SELECT count(id) FROM meetings WHERE class = classes.id AND location = ?) as meeting
							FROM classes WHERE franchise = 
								(SELECT id FROM franchises WHERE id = ? AND active = 1 AND live = 1)
							AND active = 1 AND enddate > ?)
						as x WHERE (count < size_limit OR size_limit = 0) AND meeting > 0 ORDER BY startdate");
						$ps->execute(array($venue['id'], $user['home_franchise'],mktime()));
						$classes = $ps->fetchAll();
						?>

						<h4>Classes at <?php echo $venue['name'] ?></h4>
						
						<table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>Name</th>
			                  <th>Dates</th>
			                  <th>Meeting Days</th>
			                  <th></th>
			                </tr>
			              </thead>
			              <tbody>
			              	<?php foreach ($classes as $class) { ?>
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
			                  <td><a href="/class/<?php echo $class['id'] ?>"><?php echo $class['name'] ?></a></td>
			                  <td><?php echo date("M d Y", $class['startdate']) ?> - <?php echo date("M d Y", $class['enddate']) ?></td>
			                  <td><?php echo $meeting_days ?></td>
			                  <td><a href="/class/<?php echo $class['id'] ?>" class="btn"><i class="icon-ok-sign"></i> Register</a></td>
			                </tr>
			                <?php } ?>
			                <?php if (!$classes) { ?>
			                <tr><td colspan="4">No Classes Found</td></tr>
			                <?php } ?>
			              </tbody>
			            </table>
            <?php $i++; ?>
            <?php } ?>

<?php } ?>
        
		
	</div>
	<?php } ?>
</div>