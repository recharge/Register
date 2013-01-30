<?php
include '../config/config.php';

$id = $_GET['id'];
$key = $_GET['key'];

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
$ps = $pdo->prepare("UPDATE franchises SET rechargeApiKey = ? WHERE id = ?");
$ps->execute(array($key, $id));
?>