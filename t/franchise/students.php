<div class="row">
  <?php
  $ps = $pdo->prepare("SELECT * FROM students WHERE id = ?");
  $ps->execute(array($id));
  $student = $ps->fetch(PDO::FETCH_ASSOC);
  ?>

  <?php if ($student) { ?>
    
    <?php
    $ps = $pdo->prepare("SELECT * FROM children WHERE id = ?");
    $ps->execute(array($student['child']));
    $child = $ps->fetch(PDO::FETCH_ASSOC);
    ?>

    <?php
    $ps = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $ps->execute(array($student['parent']));
    $parent = $ps->fetch(PDO::FETCH_ASSOC);
    ?>

    <?php
    $ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $ps->execute(array($student['class']));
    $class = $ps->fetch(PDO::FETCH_ASSOC);
    ?>

    <?php
    $ps = $pdo->prepare("SELECT student, name, value FROM `customfields_values` as v, customfields_keys as k WHERE v.student = ? and v.key = k.id");
    $ps->execute(array($student['id']));
    $customfields = $ps->fetchAll();
    ?>

        <script>
          $(document).ready(function() {
            $("#notes").change(function() {
                $.post("/api/notes.php", { id: $('#notes').data('id'), notes: this.value } );
            });
          });
        </script>

    <div class="span12">
    
      <h2><?php echo $child['name'] ?></h2>
      <h5>Registered For <?php echo $class['name'] ?> (<?php echo date("m/d/Y", $class['startdate']) ?> - <?php echo date("m/d/Y", $class['enddate']) ?>)<span class="pull-right"><a href="/franchise/students" class="btn">Go Back</a></span></h5>
          
      <hr>

      <div class="row">

        <div class="span6">

          <dl class="dl-horizontal">
                <dt>Parent Name</dt>
                <dd><a href="/franchise/users/<?php echo $parent['id'] ?>"><?php echo $parent['name'] ?>&nbsp;</a></dd>

                <dt>Parent Phone Number</dt>
                <dd><?php echo formatphone($parent['phone']) ?>&nbsp;</dd>

                <dt>Parent Email</dt>
                <dd><?php echo $parent['email'] ?>&nbsp;</dd>
          </dl>

          <dl class="dl-horizontal">
                <dt>Grade</dt>
                <dd><?php echo $child['grade'] ?>&nbsp;</dd>

                <dt>Birthdate</dt>
                <dd><?php echo date("m/d/Y", $child['birthdate']) ?> (<?php echo getAge($child['birthdate']) ?> years old)&nbsp;</dd>
                
                <dt>Parent Notes</dt>
                <dd><?php echo $child['notes'] ?>&nbsp;</dd>

                <dt>Private Notes</dt>
                <dd><textarea id="notes" data-id="<?php echo $student['id'] ?>" class="input-xlarge"><?php echo $student['notes'] ?></textarea></dd>
          </dl>

        </div>

        <div class="span6">

          <dl class="dl-horizontal">
            <?php foreach ($customfields as $field) { ?>
              
                <dt><?php echo $field['name'] ?></dt>
                <dd><?php echo $field['value'] ?>&nbsp;</dd>
            <?php } ?>
          </dl>
          
        </div>

      </div>

    </div>
  <?php } else { ?>

	<script type="text/javascript">
	$(function() {
		$( "#from" ).datepicker({
      changeMonth: true,
      changeYear: true,
			onSelect: function( selectedDate ) {
				$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#to" ).datepicker({
      changeMonth: true,
      changeYear: true
    });

    $(function () {
        $('.checkall').click(function () {
            $(this).parents('form:eq(0)').find(':checkbox').attr('checked', this.checked);
        });
    });

    $('#savedReports').change(function(){
        window.location.href = "/franchise/students?" + $(this).val();
    });
	});
</script>
    
	<?php
		$page = ($_GET['p'] == "" ? 1 : $_GET['p']);
		$resultsPerPage = 20;
		$limit = ($page-1) * $resultsPerPage;
		$q = "%".$_GET['q']."%";

		$classFilter = $_GET['class'];
    $locFilter = $_GET['location'];

    $quickDate = array();

    $quickDate[0]['label'] = "All Time";
    $quickDate[0]['start'] = 0;
    $quickDate[0]['end'] = strtotime("today 23:59:59");

    $quickDate[1]['label'] = "Today";
    $quickDate[1]['start'] = strtotime("today 0:00:00");
    $quickDate[1]['end'] = strtotime("today 23:59:59");

    $quickDate[2]['label'] = "Last 30 Days";
    $quickDate[2]['start'] = strtotime("-30 days");
    $quickDate[2]['end'] = strtotime("today 23:59:59");

    $quickDate[3]['label'] = "Last 3 Months";
    $quickDate[3]['start'] = strtotime("-3 months");
    $quickDate[3]['end'] = strtotime("today 23:59:59");

    $quickDate[4]['label'] = "This Year";
    $quickDate[4]['start'] = strtotime("first day of this year");
    $quickDate[4]['end'] = strtotime("last day of this year");

    $quickDate[5]['label'] = "Last Year";
    $quickDate[5]['start'] = strtotime("first day of last year");
    $quickDate[5]['end'] = strtotime("last day of last year");
		?>

	<div class="span12">
		<h2>
      Students 
      <div class="pull-right form-inline">
        <select id="savedReports">
          <option>View Saved Reports...</option>
          <?php
          $ps = $pdo->prepare("SELECT * FROM reports WHERE franchise = ?");
          $ps->execute(array($uid));
          $reports = $ps->fetchAll();

          foreach ($reports as $report) {
          ?>
          <option value="<?php echo $report['params'] ?>&reportID=<?php echo $report['id'] ?>"><?php echo $report['name'] ?></option>
          <?php } ?>
        </select>
        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">
          <i class="icon-filter icon-white"></i> Edit Filters
        </button>
      </div>
    </h2>
        
    <hr>

    <?php
    // filters
    $filtered = false;
    $filterText = "";
    $insert = array();
    $query = "";

      // class
      if ($_GET['class'] != "") {
        $filtered = true;

        $ps = $pdo->prepare("SELECT name FROM classes WHERE id = ?");
        $ps->execute(array($_GET['class']));
        $class = $ps->fetchColumn();
        $filterText .= "<li>Class: <b>$class</b></li>";

        $query .= " AND class = ? ";
        $insert[] = $_GET['class'];
      }

      // location
      if ($_GET['location'] != "") {
        $filtered = true;

        $ps = $pdo->prepare("SELECT name FROM meeting_places WHERE id = ?");
        $ps->execute(array($_GET['location']));
        $location = $ps->fetchColumn();
        $filterText .= "<li>Location: <b>$location</b></li>";

        $query .= " AND class IN (SELECT class FROM meetings WHERE location = ? GROUP by id) ";
        $insert[] = $_GET['location'];
      }

      // registration date
      if ($_GET['regDate'] > 0) {
        $filtered = true;

        $filterText .= "<li>Registration Date: <b>".date("m/d/Y", $quickDate[$_GET['regDate']]['start'])." - ".date("m/d/Y", $quickDate[$_GET['regDate']]['end'])."</b></li>";

        $query .= " AND registerdate BETWEEN ? AND ? ";
        $insert[] = $quickDate[$_GET['regDate']]['start'];
        $insert[] = $quickDate[$_GET['regDate']]['end'];
      }

      // student age
      if ($_GET['studentAge']['from'] != "" && $_GET['studentAge']['to'] != "") {
        $filtered = true;

        $filterText .= "<li>Student Age between <b>{$_GET['studentAge']['from']}</b> and <b>{$_GET['studentAge']['to']}</b></li>";

        $_GET['studentAge']['from'] = $_GET['studentAge']['from'] + 1;
        $start = strtotime("-{$_GET['studentAge']['from']} years");
        $end = strtotime("-{$_GET['studentAge']['to']} years");

        $query .= " AND child IN (SELECT id FROM children WHERE birthdate BETWEEN ? AND ?) ";
        $insert[] = $start;
        $insert[] = $end;
      }

      // birth date
      if ($_GET['studentBirthdate']['from'] != "" && $_GET['studentBirthdate']['to'] != "") {
        $filtered = true;

        $filterText .= "<li>Student Birthdate between <b>{$_GET['studentBirthdate']['from']}</b> and <b>{$_GET['studentBirthdate']['to']}</b></li>";

        $start = strtotime($_GET['studentBirthdate']['from']);
        $end = strtotime($_GET['studentBirthdate']['to'] . "23:59:59");
        
        $query .= " AND child IN (SELECT id FROM children WHERE birthdate BETWEEN ? AND ?) ";
        $insert[] = $start;
        $insert[] = $end;
      }

      // parent/student name
      if ($_GET['name'] != "") {
        $filtered = true;

        $filterText .= "<li>Student / Parent Name: <b>{$_GET['name']}</b></li>";

        $query .= " AND (parent IN (SELECT id FROM users WHERE name LIKE ? AND home_franchise = '$uid') OR child IN (SELECT id FROM children WHERE name LIKE ?)) ";
        $name = "%".$_GET['name']."%";
        $insert[] = $name;
        $insert[] = $name;
      }

      // parent email
      if ($_GET['email'] != "") {
        $filtered = true;

        $filterText .= "<li>Parent Email: <b>{$_GET['email']}</b></li>";

        $query .= " AND parent IN (SELECT id FROM users WHERE email = ? AND home_franchise = '$uid') ";
        $insert[] = $_GET['email'];
      }

      // distance
      if ($_GET['distance'] != "") {
        $filtered = true;

        $filterText .= "<li>Distance: Within <b>{$_GET['distance']}</b> miles</li>";

        $query .= " AND parent IN (SELECT id FROM (SELECT id, ( 3959 * acos( cos( radians(?) ) * cos( radians( lati ) ) * cos( radians( `long` ) - radians(?) ) + sin( radians(?) ) * sin( radians( lati ) ) ) ) AS distance FROM users) as found WHERE distance < ? ORDER BY distance) ";
        $insert[] = $user['lat'];
        $insert[] = $user['lon'];
        $insert[] = $user['lat'];
        $insert[] = $_GET['distance'];
      }
    ?>

    <?php
    $ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM students WHERE (SELECT home_franchise FROM users WHERE id = parent) = '$uid' $query ");
    $ps->execute($insert);
    $students = $ps->fetchAll();
    
    $ps = $pdo->prepare("SELECT FOUND_ROWS()");
    $ps->execute();
    $rows = $ps->fetchColumn();

    $pages = ceil($rows / $resultsPerPage);
    ?>

    <?php if ($filtered) { ?>
    <h4>
      <?php
      $ps = $pdo->prepare("SELECT name FROM reports WHERE id = ?");
      $ps->execute(array($_GET['reportID']));
      $report = $ps->fetchColumn();
      ?>
      <?php echo ($_GET['reportID'] != '' ? $report : "Filtered") ?>
      <div class="pull-right">
        <div class="btn-group">
          <?php if ($_GET['reportID'] != '') { ?>
          <a href="/franchise?action=doDeleteReport&id=<?php echo $_GET['reportID'] ?>" class="btn btn-small btn-danger">
              <i class="icon-trash icon-white"></i> Delete Report
          </a> 
          <?php } else { ?>
          <button data-toggle="modal" data-target="#saveReportModal" class="btn btn-small btn-success">
              <i class="icon-download-alt icon-white"></i> Save Report
          </button> 
          <?php } ?>
          <a href="/franchise/students" class="btn btn-small">
              <i class="icon-filter"></i> Clear Filters
          </a>
        </div>
      </div>
    </h4>

    <ul>
      <?php echo $filterText ?>
    </ul>

    <hr>
    <?php } ?>
        
    <form action="/franchise/email/compose" method="post">
		<table class="table table-striped">
              <thead>
                <tr>
                  <th><input type="checkbox" class="checkall"></th>
                  <th>Name</th>
                  <th>Parent</th>
                  <th>Birthdate</th>
                  <th>Class Name</th>
                  <th>Location</th>
                  <th>Register Date</th>
                </tr>
              </thead>
              
              <tbody>
              	
				<?php foreach ($students as $student) { ?>
              	
          <?php
          $ps = $pdo->prepare("SELECT * FROM children WHERE id = ?");
          $ps->execute(array($student['child']));
          $child = $ps->fetch(PDO::FETCH_ASSOC);
          ?>

          <?php
          $ps = $pdo->prepare("SELECT * FROM users WHERE id = ?");
          $ps->execute(array($student['parent']));
          $parent = $ps->fetch(PDO::FETCH_ASSOC);
          ?>
          
          <?php
          $ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
          $ps->execute(array($student['class']));
          $class = $ps->fetch(PDO::FETCH_ASSOC);
          ?>

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
                <tr>
                  <td><input type="checkbox" name="child[]" value="<?php echo $child['id'] ?>"></td>
                  <td><a href="/franchise/students/<?php echo $student['id'] ?>"><?php echo $child['name'] ?></a></td>
                  <td><a href="/franchise/users/<?php echo $parent['id'] ?>"><?php echo $parent['name'] ?></a></td>
                  <td><?php echo date("m/d/y", $child['birthdate']) ?> (<?php echo getAge($child['birthdate']) ?> years old)</td>
                  <td><?php echo $class['name'] ?></td>
                  <td><?php echo $meeting_locations ?></td>
                  <td><?php echo date("m/d/Y", $student['registerdate']) ?></td>
                </tr>
                <?php } ?>
                <?php if (!$students) { ?>
                <tr><td colspan="7">No Students Found</td></tr>
                <?php } ?>
              </tbody>
            </table>

            <hr>

            <button type="submit" class="btn btn-primary"><i class="icon-envelope icon-white"></i> Send Email</button>
            </form>

            
</div>



<div id="myModal" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Filter</h3>
  </div>
  <div class="modal-body">
        <form class="form-horizontal" action="/franchise/students" style="margin: 0px;">
          
          <div class="control-group">
            <?php
            $ps = $pdo->prepare("SELECT * FROM classes WHERE franchise = ? ORDER BY startdate DESC");
            $ps->execute(array($uid));
            $classes = $ps->fetchAll();
            ?>
            <label class="control-label" for="inputEmail">Class</label>
            <div class="controls">
              <select name="class">
                <option value="">All Classes</option>
                <?php foreach ($classes as $class) { ?>
                <option value="<?php echo $class['id'] ?>"><?php echo $class['name'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="control-group">
            <?php
            $ps = $pdo->prepare("SELECT * FROM meeting_places WHERE franchise = ? ORDER BY name");
            $ps->execute(array($uid));
            $locations = $ps->fetchAll();
            ?>
            <label class="control-label" for="inputEmail">Location</label>
            <div class="controls">
              <select name="location">
                <option value="">All Locations</option>
                <?php foreach ($locations as $location) { ?>
                <option value="<?php echo $location['id'] ?>"><?php echo $location['name'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="inputEmail">Registration Date</label>
            <div class="controls">
              <select name="regDate">
                <?php foreach ($quickDate as $i => $qd) { ?>
                <option value="<?php echo $i ?>"><?php echo $qd['label'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="inputEmail">Student Age</label>
            <div class="controls">
              <input type="text" class="input-small" placeholder="from" name="studentAge[from]">
              -
              <input type="text" class="input-small" placeholder="to" name="studentAge[to]">
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="inputEmail">Student Birthdate</label>
            <div class="controls">
              <input type="text" class="input-small" placeholder="from" id="from" name="studentBirthdate[from]">
              -
              <input type="text" class="input-small" placeholder="to" id="to" name="studentBirthdate[to]">
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="inputEmail">Student / Parent Name</label>
            <div class="controls">
              <input type="text" name="name">
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="inputEmail">Parent Email</label>
            <div class="controls">
              <input type="text" name="email">
            </div>
          </div>
          
          <div class="control-group">
            <label class="control-label" for="inputEmail">Distance</label>
            <div class="controls">
              <input type="text" name="distance" class="input-small"> miles
            </div>
          </div>
        
  </div>
  <div class="modal-footer">
    <a class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
    <button type="submit" class="btn btn-primary">Apply Filter</button>
    </form>
  </div>
</div>

  <div id="saveReportModal" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Save Report</h3>
  </div>
  <div class="modal-body">
        <form class="form-horizontal" action="/franchise/students" method="POST">
          <input type="hidden" name="action" value="doSaveReport">
          <input type="hidden" name="params" value="<?php echo $_SERVER['QUERY_STRING'] ?>">
          
          <div class="control-group">
            <label class="control-label" for="inputEmail">Name</label>
            <div class="controls">
              <input type="text" name="name">
            </div>
          </div>
        
  </div>
  <div class="modal-footer">
    <a class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
    <button type="submit" class="btn btn-primary">Save Report</button>
    </form>
  </div>
</div>
  <?php } ?>
</div>