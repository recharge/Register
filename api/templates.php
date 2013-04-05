<?php
include '../config/config.php'; 

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);

$ps = $pdo->prepare("SELECT subject, body FROM templates WHERE id = ?");
$ps->execute(array($_GET['id']));
$template = $ps->fetch(PDO::FETCH_ASSOC);

echo json_encode($template);
?>