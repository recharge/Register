<?php
session_start();
include '../config/config.php'; 

$uid = $_SESSION['FID'];

if ($uid) {

	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);

	$ps = $pdo->prepare("UPDATE students SET notes = ? WHERE id = ?");
	$ps->execute(array($_POST['notes'], $_POST['id']));

}
?>