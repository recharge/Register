<?php
include 'config/config.php';

mysql_connect($config['db']['host'], $config['db']['un'], $config['db']['pw']) or die(mysql_error());
mysql_select_db($config['db']['db']) or die(mysql_error());

$timespan = strtotime('-24 hours');

$ids = mysql_query("SELECT *, (SELECT name FROM franchises WHERE id=hits.user) as franchise, (SELECT name FROM users WHERE id=hits.user) as users FROM hits WHERE user LIKE '%{$_GET['seller']}%' AND `timestamp` > $timespan ORDER BY hits.timestamp DESC") or die(mysql_error());

function random_color(){
	mt_srand((double)microtime()*1000000);
	$c = '';
	while(strlen($c)<6){
		$c .= sprintf("%02X", mt_rand(0, 255));
	}
	return $c;
}


?> 
<style type="text/css">
<!--
td {
	font-size: 0.8em;
	font-family: Arial, Helvetica, sans-serif;
}
tr {
	border-bottom: 1px solid #DDD;
}
-->
</style>
<table width="100%" cellpadding="5">
  <tr> 
    <td><strong>Date</strong></td>
    <td><strong>IP Address</strong></td>
    <td><strong>User ID</strong></td>
    <td><strong>Page</strong></td>
    <td><strong>Referer</strong></td>
  </tr>
  <?php
  	$ips = array();
	while ($row = mysql_fetch_array($ids)) {
		$id = $row['emailid'];
		$company = $id;
		$time = date("m/d/Y g:i A", $row['timestamp']);
		$hits = $row['hits'];
		
		$company = @mysql_result(mysql_query("SELECT name FROM accounts WHERE id = '$company'"), 0);

		$curr_ip = $row['ip'];
		
		$color = $ips[$curr_ip];
		if ($color == '') {
			$color = random_color();
			$ips[$curr_ip] = $color;
		}
		
		parse_str($row['page'], $simple);
			if ($simple['/app?p'] != '') {
				switch ($simple['/app?p']) {
					case "da":
						$page = "Dashboard";
						break;
					case "pr":
						if ($simple['a'] == "v") {
							$page = "Viewing Products";
						} else if ($simple['a'] == "e") {
							$page = "Editing Product ".$simple['id'];
						} else if ($simple['a'] == "a") {
							$page = "Adding Product";
						} else {
							$page = $row['page'];
						}
						break;
					case "cu":
						if ($simple['a'] == "v") {
							$page = "Viewing Customers";
						} else if ($simple['a'] == "d") {
							$page = "Viewing Customer ".$simple['id'];
						} else if ($simple['a'] == "e") {
							$page = "Editing Customer ".$simple['id'];
						} else if ($simple['a'] == "a") {
							$page = "Adding Customer";
						} else if ($simple['a'] == "pm_a") {
							$page = "Adding PayMethod to Customer ".$simple['id'];
						} else if ($simple['a'] == "pm_d") {
							$page = "Deleting PayMethod from Customer ".$simple['id'];
						} else if ($simple['a'] == "rc_a") {
							$page = "Adding Charge to Customer ".$simple['id'];
						} else if ($simple['a'] == "rc_e") {
							$page = "Editing Charge for Customer ".$simple['id'];
						} else if ($simple['a'] == "rc_d") {
							$page = "Deleting Charge from Customer ".$simple['id'];
						} else if ($simple['a'] == "b") {
							$page = "Bulk Adding Customers";
						} else if ($simple['a'] == "otc_a") {
							$page = "Adding One-Time to Customer ".$simple['id'];
						} else {
							$page = $row['page'];
						}
						break;
					case "se":
						if ($simple['a'] == "c" || $simple['a'] == "") {
							$page = "Contact Settings";
						} else if ($simple['a'] == "m") {
							$page = "Merchant Settings";
						} else if ($simple['a'] == "p") {
							$page = "Payment Settings";
						} else if ($simple['a'] == "hp") {
							$page = "Hosted Payment Settings";
						} else if ($simple['a'] == "api") {
							$page = "API Settings";
						} else if ($simple['a'] == "em") {
							$page = "Email Settings";
						} else {
							$page = $row['page'];
						}
						break;
					case "re":
						$page = "Reports";
						break;
					default:
						$page = $row['page'];
				}
				$row['page'] = $page;
			} else {
				if ($row['page'] == "/app.php") {
					$row['page'] = "Main App";
				}
				if (strpos($row['page'], "/hp/") !== FALSE) {
					$hp = str_replace("/hp/", "", $row['page']);
					$hp = mysql_query("SELECT products.name as name, sellers.name as seller FROM products, sellers WHERE products.id = '$hp' AND products.seller = sellers.id");
					$hp = mysql_fetch_assoc($hp);
					$row['page'] = "HP ".$hp['seller']." | ".$hp['name'];
				}
			}
		if (date("d", $row['timestamp']) != $day & $day != "") {
			echo "<tr><td colspan=\"5\"><hr></td></tr>";
		}
	?>
  <tr> 
    <td><?php echo date("m/d/y g:i a", $row['timestamp']); ?></td>
    <td><font color="#<?php echo $color; ?>"><?php echo $row['ip']; ?></font></td>
    <td><font color="#<?php echo $color; ?>"><?php echo $row['users']; ?><?php echo $row['franchise']; ?></font></td>
    <td><font color="#<?php echo $color; ?>"><?php echo wordwrap($row['page'], 50, "<br />\n", TRUE); ?></font></td>
    <td><font color="#<?php echo $color; ?>"><?php echo wordwrap($row['referer'], 50, "<br />\n", TRUE); ?></font></td>
  </tr>
  <?php $day = date("d", $row['timestamp']); ?>
  <?php } ?>
</table>
