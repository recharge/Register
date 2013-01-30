<?php
function setError($type, $message) {
	switch ($type) {
		case 1:
			$type = "alert-error";
			break;
		case 2:
			$type = "alert-success";
			break;
		case 3:
			$type = "alert-info";
			break;
	}
	$_SESSION['error']['type'] = $type;
	$_SESSION['error']['message'] = $message;
}
function clearError() {
	unset($_SESSION['error']);
}
function redirect($location) {
	header("Location: /" .$location);
	exit;
}
function formatPhone($phone = '', $convert = false, $trim = true)
{
	// If we have not entered a phone number just return empty
	if (empty($phone)) {
		return '';
	}
	
	// Strip out any extra characters that we do not need only keep letters and numbers
	$phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);
	
	// Do we want to convert phone numbers with letters to their number equivalent?
	// Samples are: 1-800-TERMINIX, 1-800-FLOWERS, 1-800-Petmeds
	if ($convert == true) {
		$replace = array('2'=>array('a','b','c'),
				 '3'=>array('d','e','f'),
			         '4'=>array('g','h','i'),
				 '5'=>array('j','k','l'),
                                 '6'=>array('m','n','o'),
				 '7'=>array('p','q','r','s'),
				 '8'=>array('t','u','v'),								 '9'=>array('w','x','y','z'));
		
		// Replace each letter with a number
		// Notice this is case insensitive with the str_ireplace instead of str_replace 
		foreach($replace as $digit=>$letters) {
			$phone = str_ireplace($letters, $digit, $phone);
		}
	}
	
	// If we have a number longer than 11 digits cut the string down to only 11
	// This is also only ran if we want to limit only to 11 characters
	if ($trim == true && strlen($phone)>11) {
		$phone = substr($phone, 0, 11);
	}						 
	
	// Perform phone number formatting here
	if (strlen($phone) == 7) {
		return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1-$2", $phone);
	} elseif (strlen($phone) == 10) {
		return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "($1) $2-$3", $phone);
	} elseif (strlen($phone) == 11) {
		return preg_replace("/([0-9a-zA-Z]{1})([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "($2) $3-$4", $phone);
	}
}

function getAge($birthdate) {
	$now = new DateTime();
	$birthdate = new DateTime(date("Y-m-d",$birthdate));
	
	return $birthdate->diff($now)->format('%y');
}

function calculateClassLength($start, $end) {
	$start = new DateTime(date("Y-m-d", $start));
	$end = new DateTime(date("Y-m-d", $end));
	
	return $start->diff($end)->format('%m') + 1;
}

function calculateMonthlyPayments($start, $end, $amt) {
	$start = new DateTime(date("Y-m-d", $start));
	$end = new DateTime(date("Y-m-d", $end));
	
	$return = array();
	
	$return["months"] = $start->diff($end)->format('%m') + 1;
	$return["plural"] = ($return["months"] == 1 ? "payment" : "payments");
	$return["amount"] = round($amt / $return["months"], 2);
	
	return $return;
}
function isAdmin() {
	global $config;
	
	$id = $_SESSION['AID'];
	
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$ps = $pdo->prepare("SELECT count(id) FROM admin WHERE id = ?");
	$ps->execute(array($id));
	return (bool)$ps->fetchColumn();
}

function rechargeCall($api_key, $resource, $method, $data=null) {
	
	// open curl connection
	$ch = curl_init();
	
	// tell curl to use ssl
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
	
	// Defines the API URL
	$url = "https://www.rechargebilling.com/API/v2/$resource";
	
	// set the url
	curl_setopt($ch,CURLOPT_URL,$url);
	
	if ($method == "POST") {
		// define the fields
		curl_setopt($ch,CURLOPT_POST,count($data));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	}
	
	// send the API key over HTTP Authentication
	curl_setopt($ch, CURLOPT_USERPWD,"$api_key:");
	
	// tell curl to pass the result when complete
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
	
	// execute and store response into variable
	$result = curl_exec($ch);
	
	// close connection
	curl_close($ch);
		
	
	// Puts the XML result into the SimpleXML format for easy data retrieval
	// Read about SimpleXML here: http://php.net/manual/en/simplexml.examples-basic.php
	return new SimpleXMLElement($result);

}

function rechargeFindCustomer($key, $id=null) {
	$result = rechargeCall($key, "customers/$id", "GET");
	
	if ($result->result->resultCode == 000) {
		return $result;
	} else {
		return false;
	}
}

function rechargeAddCustomer($key, $data) {
	$result = rechargeCall($key, "customers", "POST", $data);
	
	return $result->customers->customer;
}

function rechargeAddPayMethod($key, $data) {
	$result = rechargeCall($key, "paymethods", "POST", $data);
	
	return $result;
}

function rechargeAddCharge($key, $data) {
	$result = rechargeCall($key, "charges", "POST", $data);
	
	return $result;
}

function rechargeChargeCard($key, $data) {
	$result = rechargeCall($key, "purchase", "POST", $data);
	
	return $result;
}

function rechargeGetTransactions($key, $customer=null) {
	$result = rechargeCall($key, "transactions/$customer", "GET");
	
	return $result;
}

function processUpload() {
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	$extension = end(explode(".", $_FILES["image"]["name"]));
	if ((($_FILES["image"]["type"] == "image/gif")
	|| ($_FILES["image"]["type"] == "image/png")
	|| ($_FILES["image"]["type"] == "image/jpeg")
	|| ($_FILES["image"]["type"] == "image/pjpeg"))
	&& ($_FILES["image"]["size"] < 2097152)
	&& in_array($extension, $allowedExts))
	  {
	  if ($_FILES["image"]["error"] > 0)
	    {
	    //echo "Return Code: " . $_FILES["image"]["error"] . "<br />";
	    }
	  else
	    {
	    //echo "Upload: " . $_FILES["image"]["name"] . "<br />";
	    //echo "Type: " . $_FILES["image"]["type"] . "<br />";
	    //echo "Size: " . ($_FILES["image"]["size"] / 1024) . " Kb<br />";
	    //echo "Temp file: " . $_FILES["image"]["tmp_name"] . "<br />";

	    if (file_exists("img/uploads/" . $_FILES["image"]["name"]))
	      {
	      //echo $_FILES["image"]["name"] . " already exists. ";
	      }
	    else
	      {
	      $imageName = uniqid("img") . ".$extension";
	      move_uploaded_file($_FILES["image"]["tmp_name"],
	      "img/uploads/$imageName");
	      //echo "Stored in: " . "img/uploads/" . $_FILES["image"]["name"];

	      return $imageName;
	      }
	    }
	  }
	else
	  {
	  //echo "Invalid file ";
	  //echo "Ext: $extension";
	  }
}

function prettyMeetingInfo($class) {
	global $pdo;

	$ps = $pdo->prepare("SELECT * FROM meetings WHERE class = ? ORDER BY day");
	$ps->execute(array($class));
	$meetings = $ps->fetchAll();

	$days = array('Sundays','Mondays','Tuesdays','Wednesdays','Thursdays','Fridays','Saturdays');

	$text = "";
	
	foreach ($meetings as $meeting) {
		$ps = $pdo->prepare("SELECT name FROM meeting_places WHERE id = ?");
		$ps->execute(array($meeting['location']));
		$meeting['location'] = $ps->fetchColumn();

		$text .= $days[$meeting['day']-1] . ": " . date("g:i a", $meeting['time']) . " at " . $meeting['location'] . "<br>\n";
	}

	return $text;
}

function replaceEmailFields($text, $franchise = NULL, $class = NULL, $student = NULL) {
	$replace = array();

	//      ['FIND']               REPLACE
	$replace['%FRANCHISE NAME%'] = $franchise['name'];
	$replace['%FRANCHISE MAIN CONTACT%'] = $franchise['contact'];
	$replace['%FRANCHISE PHONE%'] = $franchise['phone'];
	$replace['%FRANCHISE EMAIL%'] = $franchise['email'];

	$replace['%CLASS NAME%'] = $class['name'];
	$replace['%CLASS START DATE%'] = date("l F j Y", $class['startdate']);
	$replace['%CLASS END DATE%'] = date("l F j Y", $class['enddate']);
	$replace['%CLASS MEETING INFO%'] = prettyMeetingInfo($class['id']);

	$replace['%STUDENT NAME%'] = $student['name'];

	foreach ($replace as $find => $replace) {
		$text = str_replace($find, $replace, $text);
	}

	return $text;
}

function addUnsubscribe($text, $user) {
	return $text."<br><br><small>This message is being sent to you because of your interaction with this KidzArt/Art Innovators franchise. We respect your privacy -- you may <a href='http://registration.kidzart.com/unsubscribe/?id={$user['id']}'>unsubscribe instantly by clicking here</a>.</small>";
}

function sendWelcomeEmail($franchise) {
	global $config, $pdo, $user;

	$ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
	$ps->execute(array($franchise));
	$franchise = $ps->fetch(PDO::FETCH_ASSOC);

	print_r($franchise);

	if ($franchise['welcome'] == "") {
		$franchise['welcome'] = $config['email']['default_welcome'];
	}

	$ps = $pdo->prepare("SELECT subject, body FROM templates WHERE id = ?");
	$ps->execute(array($franchise['welcome']));
	$template = $ps->fetch(PDO::FETCH_ASSOC);

	$from = "{$franchise['name']} <{$franchise['email']}>";
    $headers  = "From: $from\r\n";
    $headers .= "Content-type: text/html\r\n";

    $template['subject'] = replaceEmailFields($template['subject'], $franchise);
    $template['body'] = replaceEmailFields($template['body'], $franchise);

    $template['body'] = addUnsubscribe($template['body'], $user);

	mail($user['email'], $template['subject'], $template['body'], $headers);
}

function sendRegistrationEmail($student) {
	global $config, $pdo, $user;

	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	// get student (roster) info
	$ps = $pdo->prepare("SELECT * FROM students WHERE id = ?");
	$ps->execute(array($student));
	$student = $ps->fetch(PDO::FETCH_ASSOC);

	// get student (personal) info
	$ps = $pdo->prepare("SELECT * FROM children WHERE id = ?");
	$ps->execute(array($student['child']));
	$child = $ps->fetch(PDO::FETCH_ASSOC);

	// get class info
	$ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
	$ps->execute(array($student['class']));
	$class = $ps->fetch(PDO::FETCH_ASSOC);

	// get email
	$ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
	$ps->execute(array($class['franchise']));
	$franchise = $ps->fetch(PDO::FETCH_ASSOC);

	if ($franchise['registration'] == "") {
		$franchise['registration'] = $config['email']['default_registration'];
	}

	$ps = $pdo->prepare("SELECT subject, body FROM templates WHERE id = ?");
	$ps->execute(array($franchise['registration']));
	$template = $ps->fetch(PDO::FETCH_ASSOC);

	$from = "{$franchise['name']} <{$franchise['email']}>";
    $headers  = "From: $from\r\n";
    $headers .= "Content-type: text/html\r\n";

    $template['subject'] = replaceEmailFields($template['subject'], $franchise, $class, $child);
    $template['body'] = replaceEmailFields($template['body'], $franchise, $class, $child);

    $template['body'] = addUnsubscribe($template['body'], $user);

    if ($user['unsubscribe'] == "") {
		mail($user['email'], $template['subject'], $template['body'], $headers);
	}
}

function sendClassChangeEmail($class) {
	global $config, $pdo, $user;

	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	// get class info
	$ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
	$ps->execute(array($class));
	$class = $ps->fetch(PDO::FETCH_ASSOC);

	if ($user['change'] == "") {
		$user['change'] = $config['email']['default_change'];
	}

	$ps = $pdo->prepare("SELECT subject, body FROM templates WHERE id = ?");
	$ps->execute(array($user['change']));
	$template = $ps->fetch(PDO::FETCH_ASSOC);

	$from = "{$user['name']} <{$user['email']}>";
    $headers  = "From: $from\r\n";
    $headers .= "Content-type: text/html\r\n";

	$template['subject'] = replaceEmailFields($template['subject'], $user, $class);
    $template['body'] = replaceEmailFields($template['body'], $user, $class);

    // get all students in this class
    $ps = $pdo->prepare("SELECT * FROM students WHERE class = ?");
	$ps->execute(array($class['id']));
	$students = $ps->fetchAll();

	foreach ($students as $student) {
		// get parent info
		$ps = $pdo->prepare("SELECT * FROM users WHERE id = ?");
		$ps->execute(array($student['parent']));
		$parent = $ps->fetch(PDO::FETCH_ASSOC);

		$template['body'] = addUnsubscribe($template['body'], $parent);

		if ($parent['unsubscribe'] == "") {
			mail($parent['email'], $template['subject'], $template['body'], $headers);
		}
	}
}

function sendClassReminderEmail($class) {
	global $config, $pdo;

	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	// get class info
	$ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
	$ps->execute(array($class['id']));
	$class = $ps->fetch(PDO::FETCH_ASSOC);

	// get franchise info
	$ps = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
	$ps->execute(array($class['franchise']));
	$franchise = $ps->fetch(PDO::FETCH_ASSOC);

	if ($franchise['reminder'] == "") {
		$franchise['reminder'] = $config['email']['default_reminder'];
	}

	$ps = $pdo->prepare("SELECT subject, body FROM templates WHERE id = ?");
	$ps->execute(array($franchise['reminder']));
	$template = $ps->fetch(PDO::FETCH_ASSOC);

	$from = "{$franchise['name']} <{$franchise['email']}>";
    $headers  = "From: $from\r\n";
    $headers .= "Content-type: text/html\r\n";

	$template['subject'] = replaceEmailFields($template['subject'], $franchise, $class);
    $template['body'] = replaceEmailFields($template['body'], $franchise, $class);

    // get all students in this class
    $ps = $pdo->prepare("SELECT * FROM students WHERE class = ?");
	$ps->execute(array($class['id']));
	$students = $ps->fetchAll();

	foreach ($students as $student) {
		// get parent info
		$ps = $pdo->prepare("SELECT * FROM users WHERE id = ?");
		$ps->execute(array($student['parent']));
		$parent = $ps->fetch(PDO::FETCH_ASSOC);

		$template['body'] = addUnsubscribe($template['body'], $parent);

		if ($parent['unsubscribe'] == "") {
			mail($parent['email'], $template['subject'], $template['body'], $headers);
		}
	}
}
?>