<?php
include 'config/config.php';
session_start();

$uid = $_SESSION['FID'];
if ($uid) {
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
	$ps->execute(array($uid));
	$user = $ps->fetch(PDO::FETCH_ASSOC);
} else {
	header("Location: /");
}

include 'bin/functions.php';

$ps = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND franchise = ?");
$ps->execute(array($_GET['id'], $user['id']));
$class = $ps->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>KidzArt Printable Class Sheet | <?php echo $class['name'] ?></title>

    <!-- Le styles -->
    <link href="/css/bootstrap.css" rel="stylesheet">
    <style>
    body {
      font-size: 0.8em;
    }
    h1, h4, h5 {
	    margin-left: 8px;
	    margin-right: 8px;
    }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script src="/js/bootstrap.js"></script>
  </head>

  <body>

    <div class="container" style="width: 100%;">
    
    <?php if ($class) { ?>
    
    <h1><?php echo $class['name'] ?><span class="pull-right"><a href="#customize" role="button" data-toggle="modal" class="btn"><i class="icon-wrench"></i> Customize</a> <a href="/franchise" class="btn"><i class="icon-arrow-left"></i> Go Back</a></span></h1>
    <h4><?php echo date("m/d/Y", $class['startdate']) ?> - <?php echo date("m/d/Y", $class['enddate']) ?></h4>
    <h5><?php echo $user['name'] ?></h5>

    <?php
    $ps = $pdo->prepare("SELECT * FROM customfields_keys WHERE franchise = ?");
    $ps->execute(array($uid));
    $customfields = $ps->fetchAll();
    ?>

    <?php
    $prefs = $user['roster_prefs'];
    if ($prefs == "") {
      $prefs = $config['rosterprefs'];
    } else {
      $prefs = explode("|", $prefs);
    }
    ?>

      <table class="table table-striped">
              <thead>
                <tr>
                  <?php if (in_array("child[name]", $prefs)) { ?>
                    <th>Student Name</th>
                  <?php } ?>
                  <?php if (in_array("child[birthdate]", $prefs)) { ?>
                    <th>Birthdate</th>
                  <?php } ?>
                  <?php if (in_array("parent[name]", $prefs)) { ?>
                    <th>Parent Name</th>
                  <?php } ?>
                  <?php if (in_array("parent[phone]", $prefs)) { ?>
                    <th>Parent Phone</th>
                  <?php } ?>
                  <?php if (in_array("parent[email]", $prefs)) { ?>
                    <th>Parent Email</th>
                  <?php } ?>
                  <?php if (in_array("parent[emergency_contact]", $prefs)) { ?>
                    <th>Emergency Contact</th>
                  <?php } ?>
                  <?php if (in_array("parent[balance]", $prefs)) { ?>
                    <th>Account Balance</th>
                  <?php } ?>
                  <?php if (in_array("notes", $prefs)) { ?>
                    <th>Notes</th>
                  <?php } ?>

                  <?php foreach ($customfields as $field) { ?>
                    <?php if (in_array($field['id'], $prefs)) { ?>
                      <th><?php echo $field['name'] ?></th>
                    <?php } ?>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
              	<?php
              	$ps = $pdo->prepare("SELECT * FROM students WHERE class = ?");
        				$ps->execute(array($_GET['id']));
        				$students = $ps->fetchAll();
              	?>
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
                $ps = $pdo->prepare("SELECT sum(credit-debit) as sum FROM transactions WHERE user = ?");
                $ps->execute(array($student['parent']));
                $balance = $ps->fetch(PDO::FETCH_ASSOC);
                ?>
                <tr>
                  <?php if (in_array("child[name]", $prefs)) { ?>
                    <td><?php echo $child['name'] ?></td>
                  <?php } ?>
                  <?php if (in_array("child[birthdate]", $prefs)) { ?>
                    <td><?php echo date("m/d/Y", $child['birthdate']) ?> (<?php echo getAge($child['birthdate']) ?> years old)</td>
                  <?php } ?>
                  <?php if (in_array("parent[name]", $prefs)) { ?>
                    <td><?php echo $parent['name'] ?></td>
                  <?php } ?>
                  <?php if (in_array("parent[phone]", $prefs)) { ?>
                    <td><?php echo formatphone($parent['phone']) ?></td>
                  <?php } ?>
                  <?php if (in_array("parent[email]", $prefs)) { ?>
                    <td><?php echo $parent['email'] ?></td>
                  <?php } ?>
                  <?php if (in_array("parent[emergency_contact]", $prefs)) { ?>
                    <td><?php echo $parent['emergency_contact_name'] ?> <?php echo formatphone($parent['emergency_contact_phone']) ?></td>
                  <?php } ?>
                  <?php if (in_array("parent[balance]", $prefs)) { ?>
                    <td>$<?php echo number_format($balance['sum'], 2) ?></td>
                  <?php } ?>
                  <?php if (in_array("notes", $prefs)) { ?>
                    <td><?php echo $student['notes'].' '.$child['notes'] ?></td>
                  <?php } ?>

                  <?php foreach ($customfields as $field) { ?>
                    <?php if (in_array($field['id'], $prefs)) { ?>
                      <?php
                      $ps = $pdo->prepare("SELECT `value` FROM customfields_values WHERE student = ? AND `key` = ?");
                      $ps->execute(array($student['id'], $field['id']));
                      $value = $ps->fetchColumn();
                      ?>
                      <td><?php echo $value ?></td>
                    <?php } ?>
                  <?php } ?>
                </tr>
                <?php } ?>
                <?php if (!$students) { ?>
                <td colspan="<?php echo count($prefs) ?>">No Students Registered</td>
                <?php } ?>
              </tbody>
            </table>
            <?php } else { ?>
            <h2>Class Not Found</h2>
            <?php } ?>

    </div> <!-- /container -->

    <div id="customize" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <form class="form-horizontal" action="/franchise/" style="margin: 0px;" method="post">
        <input type="hidden" name="action" value="doSetRosterPrefs">
        <input type="hidden" name="roster" value="<?php echo $_GET['id'] ?>">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Select Fields</h3>
        </div>
        <div class="modal-body">
          
          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="child[name]" <?php echo (in_array("child[name]", $prefs) ? "checked='checked'" : "") ?> > Student Name
          </label>

          <br>
          
          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="child[birthdate]" <?php echo (in_array("child[birthdate]", $prefs) ? "checked='checked'" : "") ?> > Birthdate
          </label>

          <br>

          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="parent[name]" <?php echo (in_array("parent[name]", $prefs) ? "checked='checked'" : "") ?> > Parent Name
          </label>

          <br>
          
          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="parent[phone]" <?php echo (in_array("parent[phone]", $prefs) ? "checked='checked'" : "") ?> > Parent Phone
          </label>

          <br>
          
          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="parent[email]" <?php echo (in_array("parent[email]", $prefs) ? "checked='checked'" : "") ?> > Parent Email
          </label>

          <br>
          
          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="parent[emergency_contact]" <?php echo (in_array("parent[emergency_contact]", $prefs) ? "checked='checked'" : "") ?> > Emergency Contact
          </label>

          <br>
          
          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="parent[balance]" <?php echo (in_array("parent[balance]", $prefs) ? "checked='checked'" : "") ?> > Parent Account Balance
          </label>

          <br>
          
          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="notes" <?php echo (in_array("notes", $prefs) ? "checked='checked'" : "") ?> > Notes
          </label>

          <?php foreach ($customfields as $field) { ?>
          <br>
          
          <label class="checkbox inline">
            <input type="checkbox" name="prefs[]" value="<?php echo $field['id'] ?>" <?php echo (in_array($field['id'], $prefs) ? "checked='checked'" : "") ?>> <?php echo $field['name'] ?>
          </label>
          <?php } ?>
        </div>
        <div class="modal-footer">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          <button class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>

  </body>
</html>
