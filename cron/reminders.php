<?php
include '../config/config.php';
include '../bin/functions.php';
include '../bin/actions.php';

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);

$timespan = strtotime("+1 week");

$ps = $pdo->prepare("SELECT * FROM classes WHERE startdate < $timespan AND reminder_sent = ''");
//$ps = $pdo->prepare("SELECT * FROM classes WHERE id = '5052d5c77403c' AND reminder_sent = ''");
$ps->execute();
$classes = $ps->fetchAll();

foreach ($classes as $class) {
	print_r($class);
	sendClassReminderEmail($class);
	$ps = $pdo->prepare("UPDATE classes SET reminder_sent = ? WHERE id = ?");
	$ps->execute(array(mktime(), $class['id']));
}
?>