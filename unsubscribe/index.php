<?php
include '../config/config.php';
include '../bin/functions.php';
include '../bin/actions.php';

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);

$ps = $pdo->prepare("UPDATE users SET unsubscribe = ? WHERE id = ?");
$ps->execute(array(mktime(), $_GET['id']));
?>

You have been unsubscribed from all emails!