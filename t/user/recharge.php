<?php
	$ps = $pdo->prepare("SELECT rechargeApiKey FROM franchises WHERE id = ?");
	$ps->execute(array($user['home_franchise']));
	$key = $ps->fetchColumn();
	
	// list all
	print_r(rechargeFindCustomer($key));
	
	// list one
	print_r(rechargeFindCustomer($key, "id"));
	
	// add one
	$data['firstName'] = "Jonsi";
	$data['lastName'] = "Birgisson";
	print_r(rechargeAddCustomer($key, $data));
?>