<?php
include '../config/config.php';

$id = uniqid();
$url = $_GET['url'];
$type = $_GET['type'];
$name = $_GET['name'];

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$ps = $pdo->prepare("INSERT INTO curriccenter_files (id, url, type, name) VALUES (?,?,?,?)");
$ps->execute(array($id, $url, $type, $name));
?>