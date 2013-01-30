<?php
include '../config/config.php';
session_start();

$uid = $_SESSION['FID'];

$insert = array();
$insert[] = uniqid();
$insert[] = $uid;
$insert[] = $_GET['name'];
$insert[] = $_GET['address'];

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
$ps = $pdo->prepare("INSERT INTO meeting_places (id, franchise, name, address) VALUES (?, ?, ?, ?)");
$ps->execute($insert);
?>