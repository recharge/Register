<?php
include_once 'googlemaps.php';

$action = $_REQUEST['action'];

if ($action == "doLogin") {
	$ok_user = $ok_password = FALSE;
	
	global $config;
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	// user
	$ps = $pdo->prepare("SELECT id FROM users WHERE email = ? AND active = 1");
	$ps->execute(array($_POST['ka_email']));
	$user = $ps->fetchColumn();
	if ($user) {
		$ok_user = TRUE;
	} else {
		setError(1, "Username Or Password Incorrect");
		redirect("");
	}
	
	// password
	if ($ok_user) {
		$password = crypt($_POST['ka_password'], '$2a$10$stIapougoewluzOuylAQo$');
		$ps = $pdo->prepare("SELECT id FROM users WHERE id = ? AND `password` = ?");
		$ps->execute(array($user, $password));
		$user = $ps->fetchColumn();
		if ($user) {
			$ok_password = TRUE;
		} else {
			setError(1, "Username Or Password Incorrect");
			redirect("");
		}
	}
	
	if ($ok_user && $ok_password) {
		$_SESSION['UID'] = $user;
		redirect("");
	}
}

if ($action == "doLoginFranchise") {
	$ok_user = $ok_password = FALSE;
	
	global $config;
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	// user
	$ps = $pdo->prepare("SELECT id FROM franchises WHERE email = ? AND active = 1");
	$ps->execute(array($_POST['ka_email']));
	$user = $ps->fetchColumn();
	if ($user) {
		$ok_user = TRUE;
	}
	
	// password
	if ($ok_user) {
		$password = crypt($_POST['ka_password'], '$2a$10$stIapougoewluzOuylAQo$');
		$ps = $pdo->prepare("SELECT id FROM franchises WHERE id = ? AND `password` = ?");
		$ps->execute(array($user, $password));
		$user = $ps->fetchColumn();
		if ($user) {
			$ok_password = TRUE;
		}
	}
	
	// check employees
	if (!$ok_user || !$ok_password) {
		// user
		$ps = $pdo->prepare("SELECT id FROM employees WHERE email = ?");
		$ps->execute(array($_POST['ka_email']));
		$user = $ps->fetchColumn();
		if ($user) {
			$ok_user = TRUE;
			$employee = $user;
		}
		
		// password
		if ($ok_user) {
			$password = crypt($_POST['ka_password'], '$2a$10$stIapougoewluzOuylAQo$');
			$ps = $pdo->prepare("SELECT franchise FROM employees WHERE id = ? AND `password` = ?");
			$ps->execute(array($user, $password));
			$user = $ps->fetchColumn();
			if ($user) {
				$ok_password = TRUE;
				$_SESSION['EID'] = $employee;
			}
		}
	}
	
	if ($ok_user && $ok_password) {
		$_SESSION['FID'] = $user;
		redirect("franchise/");
	} else {
		setError(1, "Username Or Password Incorrect");
		redirect("franchise/");
	}
}

if ($action == "doLoginAdmin") {
	$ok_user = $ok_password = FALSE;
	
	global $config;
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	// user
	$ps = $pdo->prepare("SELECT id FROM admin WHERE email = ?");
	$ps->execute(array($_POST['ka_email']));
	$user = $ps->fetchColumn();
	if ($user) {
		$ok_user = TRUE;
	}
	
	// password
	if ($ok_user) {
		$password = crypt($_POST['ka_password'], '$2a$10$stIapougoewluzOuylAQo$');
		$ps = $pdo->prepare("SELECT id FROM admin WHERE id = ? AND `password` = ?");
		$ps->execute(array($user, $password));
		$user = $ps->fetchColumn();
		if ($user) {
			$ok_password = TRUE;
		}
	}
	
	// check against superadmin
	if (!$ok_user && !$ok_password && $_POST['ka_email'] == $config['admin']['email'] && crypt($_POST['ka_password'], '$2a$10$stIapougoewluzOuylAQo$') == $config['admin']['password']) {
		clearError();
		
		$user = uniqid();
		$ps = $pdo->prepare("INSERT INTO admin (id, name, email, password) VALUES (?,?,?,?)");
		$ps->execute(array($user, $config['admin']['name'], $config['admin']['email'], $config['admin']['password']));
		
		$ok_user = $ok_password = TRUE;
	}
	
	if ($ok_user && $ok_password) {
		$_SESSION['AID'] = $user;
		redirect("admin");
	} else {
		setError(1, "Username Or Password Incorrect");
		redirect("admin");
	}
}
if ($action == "doRegister") {
	global $config;
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	// check email
	$ps = $pdo->prepare("SELECT id FROM users WHERE email = ?");
	$ps->execute(array($_POST['ka_email']));
	$user = $ps->fetchColumn();
	if ($user) {
		setError(1, "That email address is in use.");
		redirect("");
	}
	
	if (strlen($_POST['ka_password']) < 7) {
		setError(1, "The password must be at least 8 characters long.");
		redirect("");
	}
	
	
	$insert = array();
	$insert[] = $uid = uniqid();
	$insert[] = $_POST['ka_name'];
	$insert[] = $_POST['ka_email'];
	$insert[] = crypt($_POST['ka_password'], '$2a$10$stIapougoewluzOuylAQo$');
	
	$ps = $pdo->prepare("INSERT INTO users (id, name, email, `password`) VALUES (?, ?, ?, ?)");
	$ps->execute($insert);
	
	$_SESSION['UID'] = $uid;
	
	redirect("");
}
if ($action == "doLogout") {
	session_unset();
	redirect("");
}

if ($action == "doFindHomeFranchiseByZIP") {
	$zip = $_POST['zip'];
	
	$geo = geocode($zip);
	
	list($longitude, $latitude, $altitude) = explode(",", $geo->Response->Placemark->Point->coordinates);
	$city = $geo->Response->Placemark->address;
	
	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	$insert = array();
	$insert[] = $latitude;
	$insert[] = $longitude;
	$insert[] = $city;
	$insert[] = $uid;
	
	$ps = $pdo->prepare("UPDATE users SET `lati` = ?, `long` = ?, `location` = ? WHERE id = ?");
	$ps->execute($insert);
	
	redirect("");
}

if ($action == "doAddClass") {
	if ($_POST['refresh'] == 0) {
		$badFields = array();
		if ($_POST['name'] == "") {
		    $badFields["name"] = "Please enter a franchise name.";
		}
		if ($_POST['startdate'] == "") {
		    $badFields["startdate"] = "Please enter a start date.";
		}
		if ($_POST['enddate'] == "") {
		    $badFields["enddate"] = "Please enter an end date.";
		}
		if (count($_POST['meeting_day']) == 0) {
		    $badFields["meeting_day"] = "Please select at least one meeting date.";
		}
	
		if (count($badFields) == 0) {
			$insert = array();
			$insert[] = $classID = uniqid();
			$insert[] = $user['id'];
			$insert[] = $_POST['name'];
			$insert[] = strtotime($_POST['startdate']);
			$insert[] = strtotime($_POST['enddate']);
			$insert[] = $_POST['active'];
			
			$ps = $pdo->prepare("INSERT INTO classes (id, franchise, name, startdate, enddate, active) VALUES (?, ?, ?, ?, ?, ?)");
			$ps->execute($insert);
			
			foreach ($_POST['meeting_day'] as $day) {
				$insert = array();
				$insert[] = uniqid();
				$insert[] = $user['id'];
				$insert[] = $classID;
				$insert[] = $day;
				$insert[] = $_POST['meeting_place'];
				$insert[] = strtotime($_POST['hour'].":".$_POST['minute']." ".$_POST['ampm']);
				$ps = $pdo->prepare("INSERT INTO meetings (id, franchise, class, day, location, time) VALUES (?, ?, ?, ?, ?, ?)");
				$ps->execute($insert);
			}
			
			
				redirect("franchise/class/$classID");
			
		}
	}
}

if ($action == "doEditClass") {
	$insert = array();
	$insert[] = $_POST['name'];
	$insert[] = strtotime($_POST['startdate']);
	$insert[] = strtotime($_POST['enddate']);
	$insert[] = $_POST['active'];
	$insert[] = $_POST['price'];
	$insert[] = $_POST['payments_price'];
	$insert[] = $_POST['description'];
	$insert[] = $_POST['size_limit'];
	$insert[] = $_POST['id'];
	
	$ps = $pdo->prepare("UPDATE classes SET name = ?, startdate = ?, enddate = ?, active = ?, price = ?, payments_price = ?, description = ?, size_limit = ? WHERE id = ?");
	$ps->execute($insert);
	
	$i=0;
	foreach ($_POST['meeting'] as $meeting) {
		$insert = array();
		$insert[] = $_POST['meeting_place'][$i];
		$insert[] = strtotime($_POST['hour'][$i].":".$_POST['minute'][$i]." ".$_POST['ampm'][$i]);
		$insert[] = $meeting;
		$ps = $pdo->prepare("UPDATE meetings SET location=?, time=? WHERE id=?");
		$ps->execute($insert);
		$i++;
	}

	if ($_FILES['image'] != "") {
		$insert = array();
		$insert[] = processUpload();
		$insert[] = $_POST['id'];
		
		$ps = $pdo->prepare("UPDATE classes SET img = ? WHERE id = ?");
		$ps->execute($insert);
	}
	
	setError(2, '<h4 class="alert-heading">Saved</h4><p>Class info successfully saved.</p><p><a class="btn btn-success" href="/franchise?action=doClassChangeNotification&id='.$_POST['id'].'">Go Back and Notify Students of Changes</a> <a class="btn btn-success" href="/franchise">Just Go Back</a> </p>');
	
	redirect("franchise/class/".$_POST['id']);
}

if ($action == "doAddMeetingPlace") {
	$insert = array();
	$insert[] = uniqid();
	$insert[] = $user['id'];
	$insert[] = $_POST['name'];
	$insert[] = $_POST['address'];
	
	$ps = $pdo->prepare("INSERT INTO meeting_places (id, franchise, name, address) VALUES (?, ?, ?, ?)");
	$ps->execute($insert);
	
	header("Location: ". $_POST['return']);
}

if ($action == "doDeleteMeeting") {
	$ps = $pdo->prepare("DELETE FROM meetings WHERE id = ?");
	$ps->execute(array($_GET['id']));
	
	redirect("franchise/class/".$_GET['class']);
}

if ($action == "doAddMeeting") {
	$insert = array();
	$insert[] = uniqid();
	$insert[] = $user['id'];
	$insert[] = $_POST['class'];
	$insert[] = $_POST['day'];
	$insert[] = $_POST['meeting_place'];
	$insert[] = strtotime($_POST['hour'].":".$_POST['minute']." ".$_POST['ampm']);
	$ps = $pdo->prepare("INSERT INTO meetings (id, franchise, class, day, location, time) VALUES (?, ?, ?, ?, ?, ?)");
	$ps->execute($insert);
	
	header("Location: ". $_POST['return']);
}

if ($action == "doSetHomeFranchise") {
	$insert = array();
	$insert[] = $_GET['id'];
	$insert[] = $user['id'];
	
	$ps = $pdo->prepare("UPDATE users SET home_franchise = ? WHERE id = ?");
	$ps->execute($insert);

	sendWelcomeEmail($_GET['id']);
	
	redirect("");
}

if ($action == "doUpdateProfile") {
	$badFields = array();
	if ($_POST['password'] != $_POST['confirm']) {
	    $badFields["password"] = "Passwords do not match.";
	    $badFields["confirm"] = "";
	}
	
	if (strlen($_POST['password']) < 7) {
			$badFields["password"] = "The password must be at least 8 characters long.";
	}

	if (count($badFields) == 0) {
		$insert = array();
		$insert[] = $_POST['name'];
		$insert[] = $_POST['email'];
		$insert[] = formatPhone($_POST['phone']);
		$insert[] = $_POST['emergency_contact_name'];
		$insert[] = formatPhone($_POST['emergency_contact_phone']);
		$insert[] = $user['id'];
		
		$ps = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, emergency_contact_name = ?, emergency_contact_phone = ? WHERE id = ?");
		$ps->execute($insert);

		if ($_POST['password'] != "" && ($_POST['password'] == $_POST['confirm'])) {
			$insert = array();
			$insert[] = crypt($_POST['password'], '$2a$10$stIapougoewluzOuylAQo$');
			$insert[] = $user['id'];
			
			$ps = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
			$ps->execute($insert);
		}
		
		redirect("");
	}
	
}

if ($action == "doUpdateFranchiseProfile") {
	$badFields = array();
	if ($_POST['password'] != $_POST['confirm']) {
	    $badFields["password"] = "Passwords do not match.";
	    $badFields["confirm"] = "";
	}
	
/* (Commented out, needs fixed to allow blank fields when not updating password and editing other information. )

if (strlen($_POST['password']) < 7) {
			$badFields["password"] = "The password must be at least 8 characters long.";

	 */
	
	if (count($badFields) == 0) {

		$insert = array();
		$insert[] = ($_POST['name'] == "" ? $user['name'] : $_POST['name']);
		$insert[] = $_POST['contact'];
		$insert[] = $_POST['email'];
		$insert[] = formatPhone($_POST['phone']);
		$insert[] = $_POST['rechargeApiKey'];
		$insert[] = $_POST['siblingdiscount'];
		$insert[] = $_POST['welcome'];
		$insert[] = $_POST['registration'];
		$insert[] = $_POST['change'];
		$insert[] = $_POST['reminder'];
		$insert[] = $_POST['allow_cash'];
		$insert[] = $_POST['allow_prorate'];
		$insert[] = $user['id'];
		
		$ps = $pdo->prepare("UPDATE franchises SET name = ?, contact = ?, email = ?, phone = ?, rechargeApiKey = ?, siblingdiscount = ?, `welcome` = ?, `registration` = ?, `change` = ?, `reminder` = ?, `allow_cash` = ?, `allow_prorate` = ? WHERE id = ?");
		$ps->execute($insert);

		if ($_POST['password'] != "" && ($_POST['password'] == $_POST['confirm'])) {
			$insert = array();
			$insert[] = crypt($_POST['password'], '$2a$10$stIapougoewluzOuylAQo$');
			$insert[] = $user['id'];
			
			$ps = $pdo->prepare("UPDATE franchises SET password = ? WHERE id = ?");
			$ps->execute($insert);
		}
		
		redirect("franchise");
	}
}

if ($action == "doUpdateChild") {
	$insert = array();
	$insert[] = $_POST['name'];
	$insert[] = $_POST['grade'];
	$insert[] = strtotime($_POST['birthdate']);
	$insert[] = $_POST['notes'];
	$insert[] = $_GET['id'];
	
	$ps = $pdo->prepare("UPDATE children SET name = ?, grade = ?, birthdate = ?, notes = ? WHERE id = ?");
	$ps->execute($insert);
	
	redirect("children/");
}

if ($action == "doAddChild") {

	$badFields = array();
	if ($_POST['name'] == "") {
	    $badFields["name"] = "Please enter a name.";
	}
	if ($_POST['birthdate'] == "") {
	    $badFields["birthdate"] = "Please enter a birthdate.";
	}

	if (count($badFields) == 0) {
		$insert = array();
		$insert[] = uniqid();
		$insert[] = $user['id'];
		$insert[] = $_POST['name'];
		$insert[] = $_POST['grade'];
		$insert[] = strtotime(str_replace("-", "/", $_POST['birthdate']));
		
		$ps = $pdo->prepare("INSERT INTO children (id, parent, name, grade, birthdate) VALUES (?,?,?,?,?)");
		$ps->execute($insert);
		
		redirect("children/");
	}
}

if ($action == "doDeleteChild") {
	$insert = array();
	$insert[] = $_GET['id'];
	
	$ps = $pdo->prepare("DELETE FROM children WHERE id = ?");
	$ps->execute($insert);
	
	redirect("children/");
}

if ($action == "doResetLocation") {
	$ps = $pdo->prepare("UPDATE users SET `lati` = '', `long` = '', `home_franchise` = '' WHERE id = ?");
	$ps->execute(array($user['id']));
	
	redirect("");
}

if ($action == "doResetFranchiseLocation") {
	$ps = $pdo->prepare("UPDATE franchises SET `lat` = '', `lon` = '' WHERE id = ?");
	$ps->execute(array($uid));
	
	redirect("franchise");
}

if ($action == "doResetHomeFranchise") {
	$ps = $pdo->prepare("UPDATE users SET `home_franchise` = '' WHERE id = ?");
	$ps->execute(array($user['id']));
	
	unset($_SESSION['cart'], $_SESSION['discount'], $_SESSION['cartTotal'], $_SESSION['grandTotal'], $_SESSION['gift']);
	
	redirect("");
}

if ($action == "doRegisterChild") {

	$error = false;

	if ($_POST['doNewAccount']) {
		// new user, create an account for them now
		
		// check email
		$ps = $pdo->prepare("SELECT id FROM users WHERE email = ?");
		$ps->execute(array($_POST['email']));
		$user = $ps->fetchColumn();


		if ($user) {
			$badFields["email"] = "That email is already in use.";
		}
		if ($_POST['email'] == "") {
		    $badFields["email"] = "Please enter an email.";
		}
		if ($_POST['password'] == "") {
		    $badFields["password"] = "Please enter a password.";
		}
		if ($_POST['password_confirm'] != $_POST['password']) {
		    $badFields["password_confirm"] = "Passwords do not match.";
		}
		
		if (count($badFields) == 0) {
			// get franchise ID from class ID
			/// set it as home franchise so they don't have to set it again later
			$ps = $pdo->prepare("SELECT franchise FROM classes WHERE id = ?");
			$ps->execute(array($_POST['id']));
			$franchise = $ps->fetchColumn();


			// insert new user into DB
			$insert = array();
			$insert[] = $uid = uniqid();
			$insert[] = $_POST['email'];
			$insert[] = $_POST['email'];
			$insert[] = crypt($_POST['password'], '$2a$10$stIapougoewluzOuylAQo$');
			$insert[] = $franchise;
			
			$ps = $pdo->prepare("INSERT INTO users (id, name, email, `password`, home_franchise) VALUES (?, ?, ?, ?, ?)");
			$ps->execute($insert);
			
			$_SESSION['UID'] = $uid;
			$ps = $pdo->prepare("SELECT * FROM users WHERE id = ? AND active = 1");
			$ps->execute(array($uid));
			$user = $ps->fetch(PDO::FETCH_ASSOC); 
		} else {
			$error = true;
		}
	}

	if ($_POST['doNewChild']) {
		if ($_POST['student'] == "") {
		    $badFields["student"] = "Please enter a name.";
		}
		if ($_POST['birthdate'] == "") {
		    $badFields["birthdate"] = "Please enter a birthdate.";
		}
		if (strtotime(str_replace("-", "/", $_POST['birthdate'])) == 0) {
		    $badFields["birthdate"] = "Please enter a birthdate like ".date('m/d/Y');
		}

		if (count($badFields) == 0) {
			$insert = array();
			$insert[] = $_POST['child'] = uniqid();
			$insert[] = $user['id'];
			$insert[] = $_POST['student'];
			$insert[] = $_POST['grade'];
			$insert[] = strtotime(str_replace("-", "/", $_POST['birthdate']));
			
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$ps = $pdo->prepare("INSERT INTO children (id, parent, name, grade, birthdate) VALUES (?,?,?,?,?)");
			$ps->execute($insert);
		} else {
			$error = true;
		}
	}

	if (!$error) {
		// add to cart
		$id = uniqid();
		$_SESSION['cart'][$id]['class'] = $_POST['id'];
		$_SESSION['cart'][$id]['child'] = $_POST['child'];
		$_SESSION['cart'][$id]['pricing'] = $_POST['pricing'];
		$_SESSION['cart'][$id]['custom'] = $_POST['custom'];
	
		redirect("cart");
	}

}

if ($action == "doDeleteClass") {
	$ps = $pdo->prepare("DELETE FROM classes WHERE id = ? AND franchise = ?");
	$ps->execute(array($_GET['id'], $user['id']));
	
	$ps = $pdo->prepare("DELETE FROM meetings WHERE class = ?");
	$ps->execute(array($_GET['id']));
	
	$ps = $pdo->prepare("DELETE FROM students WHERE class = ?");
	$ps->execute(array($_GET['id']));
	
	redirect("franchise/");
}

if ($action == "doAddToCart") {
	
	$id = uniqid();
	$_SESSION['cart'][$id]['class'] = "5052adb707aee";
	$_SESSION['cart'][$id]['child'] = "5052d63af0419";
	$_SESSION['cart'][$id]['pricing'] = rand(0,1);
	
	//redirect("");
}

if ($action == "doEmptyCart") {
	
	unset($_SESSION['cart'], $_SESSION['discount'], $_SESSION['cartTotal'], $_SESSION['grandTotal'], $_SESSION['gift']);
	
	redirect("");
}

if ($action == "doDeleteCartItem") {
	
	unset($_SESSION['cart'][$_GET['id']]);
	
	redirect("cart");
}

if ($action == "doApplyCoupon") {

	$cid = $_POST['cid'];
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$ps = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND franchise = ? AND (expdate = 0 OR expdate > ?) AND (expcount = 0 OR uses < expcount)");
	$ps->execute(array($cid, $user['home_franchise'], mktime()));
	$coupon = $ps->fetch(PDO::FETCH_ASSOC);
	
	function in_array_r($needle, $haystack, $strict = true) {
	    foreach ($haystack as $item) {
	        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
	            return true;
	        }
	    }
	
	    return false;
	}

	if (in_array_r($coupon['id'], $_SESSION['discount'])) {
		setError(1, "That coupon has already been applied.");
		redirect("cart");
	}
	
	if ($coupon) {
		setError(2, "Coupon applied!");
		
		$id = uniqid();
		$_SESSION['discount'][$id]['amount'] = $coupon['discount'];
		$_SESSION['discount'][$id]['type'] = $coupon['discounttype'];
		$_SESSION['discount'][$id]['description'] = $coupon['description'];
		$_SESSION['discount'][$id]['code'] = $coupon['id'];
		
		$ps = $pdo->prepare("UPDATE coupons SET uses = uses+1 WHERE id = ?");
		$ps->execute(array($coupon['id']));
	} else {
		setError(1, "That coupon is invalid.");
	}
	
	redirect("cart");
}

if ($action == "doCheckout") {
	
	$paid = false;
	
	if ($_POST['method'] == "cc") {
		if ($_POST['paymethod'] == "") {
			$badFields = array();
			if ($_POST['card'] == "") {
			    $badFields["card"] = "Please enter your credit card number.";
			}
			if ($_POST['billingZIP'] == "") {
			    $badFields["billingZIP"] = "Please enter your ZIP code.";
			}
			if ($_POST['cvv2'] == "") {
			    $badFields["cvv2"] = "Please enter the 3-digit card security code.";
			}
		}
		
		if (count($badFields) == 0) {
			// get franchise API key
			$ps = $pdo->prepare("SELECT rechargeApiKey FROM franchises WHERE id = ?");
			$ps->execute(array($user['home_franchise']));
			$key = $ps->fetchColumn();
			
			list($fname, $lname) = explode(' ', $user['name'],2);
			
			// check for existing customer
			if (!rechargeFindCustomer($key, $user['rechargeid']) || $user['rechargeid'] == "") {
				// customer not found in Recharge, add customer
				$data['firstName'] = $fname;
				$data['lastName'] = $lname;
				$data['emailOption'] = 0;
				$customer = rechargeAddCustomer($key, $data);
				
				$ps = $pdo->prepare("UPDATE users SET rechargeid = ? WHERE id = ?");
				$ps->execute(array($customer->id,$user['id']));
				
				$user['rechargeid'] = $customer->id;
			}
			
			$error = false;

			
			if ($_POST['paymethod'] == "") {
				// add pay method
				$data['customer'] = $user['rechargeid'];
				$data['nameOnCard'] = "$fname $lname";
				$data['cardNumber'] = $_POST['card'];
				$data['expDate'] = $_POST['expm'].$_POST['expy'];
				$data['cvv2'] = $_POST['cvv2'];
				$data['zip'] = $_POST['billingZIP'];
				
				//mail('ericcardin@gmail.com', 'actions.php 625', print_r($data, true));
				
				$paymethod = rechargeAddPayMethod($key, $data);
				if ($paymethod->result->resultCode != 000) {
					$error = true;
					setError(1, "Try again: ".$paymethod->result->resultDescription);
					$paid = false;
				} else {
					// save paymethod in db for future
					$insert = array();
					$insert[] = uniqid();
					$insert[] = $uid;
					$insert[] = $paymethod->payMethods->payMethod->cardType;
					$insert[] = $paymethod->payMethods->payMethod->last4;
					$insert[] = $paymethod->payMethods->payMethod->id;
					$ps = $pdo->prepare("INSERT INTO paymethods (id, user, cardtype, number, rechargeid) VALUES (?,?,?,?,?)");
					$ps->execute($insert);
					
					$paymethodID = $paymethod->payMethods->payMethod->id;
				}
			} else {
				$ps = $pdo->prepare("SELECT rechargeid FROM paymethods WHERE id = ?");
				$ps->execute(array($_POST['paymethod']));
				$paymethodID = $ps->fetchColumn();
			}
			
			if (!$error) {
				// charge card for today's charge if the amount > 0
				if ($_SESSION['grandTotal'] > 0) {
					unset($data);
					$data['customer'] = $user['rechargeid'];
					$data['payMethod'] = $paymethodID;
					$data['billingStartDate'] = "today";
					$data['billingEndAmount'] = $_SESSION['grandTotal'];
					$data['price'] = $_SESSION['grandTotal'];
					$data['intervalValue'] = 1;
					$data['intervalUnit'] = "m";
					$data['oneTime'] = 1;
					$charge = rechargeAddCharge($key, $data);
					if ($charge->result->resultCode != 000) {
						$error = true;
						setError(1, "Try again: ".$charge->result->resultDescription);
						$paid = false;
					} else {
						$paid = true;

						// insert into transactions database
						$insert = array();
						$insert[] = uniqid();
						$insert[] = $user['home_franchise'];
						$insert[] = $uid;
						$insert[] = mktime();
						$insert[] = $charge->transactions[0]->transaction->responseDescription;
						$insert[] = $charge->transactions[0]->transaction->amount;
						$insert[] = $charge->transactions[0]->transaction->cardType;
						$insert[] = $charge->transactions[0]->transaction->maskedAcctNum;
						$insert[] = $charge->transactions[0]->transaction->expDate;
						$insert[] = $charge->transactions[0]->transaction->approvalCode;
						$ps = $pdo->prepare("INSERT INTO transactions (id, franchise, user, randate, result, credit, cardType, maskedAcctNum, expDate, approvalCode, invoiceitem) VALUES (?,?,?,?,?,?,?,?,?,?,0)");
						$ps->execute($insert);
					}
				}
			}
				
			if (!$error && $_SESSION['cartMonthly'] > 0) {
				// charge card for any monthly charges
				unset($data);
				$data['customer'] = $user['rechargeid'];
				$data['payMethod'] = $paymethodID;
				$data['billingStartDate'] = "+1 month";
				$data['billingEndAmount'] = $_SESSION['cartBillingEnd'] - $_SESSION['cartMonthly'];
				$data['price'] = $_SESSION['cartMonthly'];
				$data['intervalValue'] = 1;
				$data['intervalUnit'] = "m";
				$charge = rechargeAddCharge($key, $data);
				
				if ($charge->result->resultCode != 000) {
					$error = true;
					setError(1, "Try again: ".$charge->result->resultDescription);
					$paid = false;
				} else {
					$paid = true;
				}
			}
			
		}
		
		// clean up paymethods missing from recharge
		if ($charge->result->resultDescription == "paymethod not found") {
			$ps = $pdo->prepare("DELETE FROM paymethods WHERE rechargeid = ?");
			$ps->execute(array($paymethodID));
		}
		
	}
	
	if ($_POST['method'] == "gc") {
		
		// check gift card
		$ps = $pdo->prepare("SELECT sum(plus)-sum(minus) FROM giftcertificates WHERE code = ?");
		$ps->execute(array($_POST['gccode']));
		$balance = $ps->fetchColumn();

		if ($balance) {
			$id = uniqid();
			$credit = min($_SESSION['grandTotal'], $balance);
			$_SESSION['gift'][$id]['amount'] = $credit;

			$ps = $pdo->prepare("INSERT INTO giftcertificates (id, ts, code, minus) VALUES (?,?,?,?)");
			$ps->execute(array($id, mktime(), $_POST['gccode'], $credit));
		} else {
			setError(1, "Invalid Gift Certificate");
		}
		
		
		if ($_SESSION['grandTotal'] + $_SESSION['cartMonthly'] - $_SESSION['gift'][$id]['amount'] > 0) {
			$paid = false;
		} else {
			$paid = true;
		}
	}
	
	if ($_POST['method'] == "ch") {
		// cash / check
		$paid = true;
	}
	
	if ($paid) {
		foreach ($_SESSION['cart'] as $itemid => $item) {
			$insert = array();
			$insert[] = $student = uniqid();
			$insert[] = $user['id'];
			$insert[] = $item['child'];
			$insert[] = $item['class'];
			$insert[] = $item['pricing'];
			$insert[] = $item['amount'];
			$insert[] = mktime();
			
			$ps = $pdo->prepare("INSERT INTO students (id, parent, child, class, pricing, amount, registerdate) VALUES (?,?,?,?,?,?,?)");
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$ps->execute($insert);

			// insert into transactions database
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $user['home_franchise'];
			$insert[] = $uid;
			$insert[] = $item['child'];
			$insert[] = $item['class'];
			$insert[] = mktime();
			$insert[] = $item['amount'];
			$ps = $pdo->prepare("INSERT INTO transactions (id, franchise, user, child, class, randate, debit) VALUES (?,?,?,?,?,?,?)");
			$ps->execute($insert);

			// custom fields
			if (count($item['custom']) > 0) {

				foreach ($item['custom'] as $key => $value) {
					$insert = array();
					$insert[] = uniqid();
					$insert[] = $user['home_franchise'];
					$insert[] = $student;
					$insert[] = $key;
					if (is_array($value)) {
						$insert[] = implode(", ", $value);
					} else {
						$insert[] = $value;
					}

					if ($value != "") {
						$ps = $pdo->prepare("INSERT INTO customfields_values (id, owner, student, `key`, `value`) VALUES (?,?,?,?,?)");
						$ps->execute($insert);
					}
				}
			}

			sendRegistrationEmail($student);
			
			unset($_SESSION['cart'][$itemid]);
		}
		
		unset($_SESSION['cart'], $_SESSION['discount'], $_SESSION['cartTotal'], $_SESSION['grandTotal'], $_SESSION['gift']);
	
		setError(2, "<strong>Order Complete</strong> You have been registered for all classes!");
		
		redirect("class/");
	}
	
	

}

if ($action == "doAddCoupon") {
	$insert = array();
	$insert[] = $cid = uniqid();
	$insert[] = $user['id'];
	$insert[] = $_POST['name'];
	$insert[] = $_POST['code'];
	$insert[] = $_POST['description'];
	$insert[] = $_POST['discount'];
	$insert[] = $_POST['discounttype'];
	$insert[] = strtotime($_POST['expdate']);
	$insert[] = $_POST['expcount'];
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$ps = $pdo->prepare("INSERT INTO coupons (id, franchise, name, code, description, discount, discounttype, expdate, expcount) VALUES (?,?,?,?,?,?,?,?,?)");
	$ps->execute($insert);
	
	redirect("franchise/coupons/");
}

if ($action == "doUpdateCoupon") {
	$insert = array();
	$insert[] = $_POST['name'];
	$insert[] = $_POST['code'];
	$insert[] = $_POST['description'];
	$insert[] = $_POST['discount'];
	$insert[] = $_POST['discounttype'];
	$insert[] = strtotime($_POST['expdate']);
	$insert[] = $_POST['expcount'];
	$insert[] = $id;
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$ps = $pdo->prepare("UPDATE coupons SET name=?, code=?, description=?, discount=?, discounttype=?, expdate=?, expcount=? WHERE id=?");
	$ps->execute($insert);
	
	redirect("franchise/coupons/");
}

if ($action == "doDeleteCoupon") {
	$ps = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
	$ps->execute(array($_GET['id']));
	
	redirect("franchise/coupons/");
}

if ($action == "doLoginAsUser") {
	if (isAdmin()) {
		$_SESSION['UID'] = $_GET['id'];
		redirect("");
	} else {
		redirect("");
	}
}

if ($action == "doLoginAsFranchise") {
	if (isAdmin()) {
		$_SESSION['FID'] = $_GET['id'];
		$_SESSION['EID'] = "";
		redirect("franchise/");
	} else {
		redirect("");
	}
}

if ($action == "doToggleEnabledUser") {
	if (isAdmin()) {
		$ps = $pdo->prepare("UPDATE users SET active = IF(active = 1, 0, 1) WHERE id = ?");
		$ps->execute(array($_GET['id']));
		redirect("admin/customers/");
	} else {
		redirect("");
	}
}

if ($action == "doToggleEnabledFranchise") {
	if (isAdmin()) {
		$ps = $pdo->prepare("UPDATE franchises SET active = IF(active = 1, 0, 1) WHERE id = ?");
		$ps->execute(array($_GET['id']));
		redirect("admin/franchises/");
	} else {
		redirect("");
	}
}

if ($action == "doToggleStorefront") {
	if (isAdmin()) {
		$ps = $pdo->prepare("UPDATE franchises SET live = IF(live = 1, 0, 1) WHERE id = ?");
		$ps->execute(array($_GET['id']));
		redirect("admin/franchises/");
	} else {
		redirect("");
	}
}

if ($action == "doDeleteUser") {
	if (isAdmin()) {
		$ps = $pdo->prepare("DELETE FROM users WHERE id = ?");
		$ps->execute(array($_GET['id']));
		redirect("admin/customers/");
	} else {
		redirect("");
	}
}

if ($action == "doDeleteFranchise") {
	if (isAdmin()) {
		$ps = $pdo->prepare("DELETE FROM franchises WHERE id = ?");
		$ps->execute(array($_GET['id']));

		$ps = $pdo->prepare("DELETE FROM classes WHERE franchise = ?");
		$ps->execute(array($_GET['id']));

		$ps = $pdo->prepare("DELETE FROM coupons WHERE franchise = ?");
		$ps->execute(array($_GET['id']));

		$ps = $pdo->prepare("DELETE FROM employees WHERE franchise = ?");
		$ps->execute(array($_GET['id']));

		$ps = $pdo->prepare("DELETE FROM meetings WHERE franchise = ?");
		$ps->execute(array($_GET['id']));

		$ps = $pdo->prepare("DELETE FROM meeting_places WHERE franchise = ?");
		$ps->execute(array($_GET['id']));

		redirect("admin/franchises/");
	} else {
		redirect("");
	}
}

if ($action == "doAddAdmin") {
	if (isAdmin()) {
		$badFields = array();
		if ($_POST['name'] == "") {
		    $badFields["name"] = "Please enter a name.";
		}
		if ($_POST['email'] == "") {
		    $badFields["email"] = "Please enter an email address.";
		}
		if ($_POST['password'] == "") {
		    $badFields["password"] = "Please enter a password.";
		}
		if ($_POST['password'] != $_POST['confirm']) {
		    $badFields["password"] = "Passwords do not match.";
		    $badFields["confirm"] = "";
		}
		
		if (count($badFields) == 0) {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $_POST['name'];
			$insert[] = $_POST['email'];
			$insert[] = crypt($_POST['password'], '$2a$10$stIapougoewluzOuylAQo$');
			
			$ps = $pdo->prepare("INSERT INTO admin (id, name, email, password) VALUES (?,?,?,?)");
			$ps->execute($insert);
			
			redirect("admin/administrators");
		}
		
		
	} else {
		redirect("");
	}
}

if ($action == "doUpdateAdmin") {
	if (isAdmin()) {
		$insert = array();
		$insert[] = $_POST['name'];
		$insert[] = $_POST['email'];
		$insert[] = $id;
		
		$ps = $pdo->prepare("UPDATE admin SET name = ?, email = ? WHERE id = ?");
		$ps->execute($insert);
		
		if ($_POST['password'] != "" && ($_POST['password'] == $_POST['confirm'])) {
			$insert = array();
			$insert[] = crypt($_POST['password'], '$2a$10$stIapougoewluzOuylAQo$');
			$insert[] = $id;
			
			$ps = $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?");
			$ps->execute($insert);
		}
		
		redirect("admin/administrators");
	} else {
		redirect("");
	}
}

if ($action == "doDeleteAdmin") {
	if (isAdmin()) {
		$ps = $pdo->prepare("DELETE FROM admin WHERE id = ?");
		$ps->execute(array($_GET['id']));
		
		redirect("admin/administrators");
	} else {
		redirect("");
	}
}

if ($action == "doAddEmployee") {
		$badFields = array();
		if ($_POST['name'] == "") {
		    $badFields["name"] = "Please enter a name.";
		}
		if ($_POST['email'] == "") {
		    $badFields["email"] = "Please enter an email address.";
		}
		if ($_POST['password'] == "") {
		    $badFields["password"] = "Please enter a password.";
		}
		if ($_POST['password'] != $_POST['confirm']) {
		    $badFields["password"] = "Passwords do not match.";
		    $badFields["confirm"] = "";
		}
		
		if (count($badFields) == 0) {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $uid;
			$insert[] = $_POST['name'];
			$insert[] = $_POST['email'];
			$insert[] = crypt($_POST['password'], '$2a$10$stIapougoewluzOuylAQo$');
			$insert[] = $_POST['access'];
			
			$ps = $pdo->prepare("INSERT INTO employees (id, franchise, name, email, password, access) VALUES (?,?,?,?,?,?)");
			$ps->execute($insert);
			
			redirect("franchise/employees");
		}
}

if ($action == "doUpdateEmployee") {
	
		$insert = array();
		$insert[] = $_POST['name'];
		$insert[] = $_POST['email'];
		$insert[] = $_POST['access'];
		$insert[] = $id;
		
		$ps = $pdo->prepare("UPDATE employees SET name = ?, email = ?, access = ? WHERE id = ?");
		$ps->execute($insert);
		
		if ($_POST['password'] != "" && ($_POST['password'] == $_POST['confirm'])) {
			$insert = array();
			$insert[] = crypt($_POST['password'], '$2a$10$stIapougoewluzOuylAQo$');
			$insert[] = $id;
			
			$ps = $pdo->prepare("UPDATE employees SET password = ? WHERE id = ?");
			$ps->execute($insert);
		}
		
		redirect("franchise/employees/");
}

if ($action == "doDeleteEmployee") {
		$ps = $pdo->prepare("DELETE FROM employees WHERE id = ? AND franchise = ?");
		$ps->execute(array($_GET['id'], $uid));
		
		redirect("franchise/employees");
}

if ($action == "doAddCustomField") {
		$badFields = array();
		if ($_POST['name'] == "") {
		    $badFields["name"] = "Please enter a name.";
		}
		
		if (count($badFields) == 0) {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $uid;
			$insert[] = $_POST['name'];
			$insert[] = $_POST['type'];
			$insert[] = $_POST['helptext'];
			$insert[] = $_POST['values'];
			
			$ps = $pdo->prepare("INSERT INTO customfields_keys (id, franchise, name, type, helptext, `values`) VALUES (?,?,?,?,?,?)");
			$ps->execute($insert);
			
			redirect("franchise/customfields");
		}
}

if ($action == "doUpdateCustomField") {
	
		$insert = array();
		$insert[] = $_POST['name'];
		$insert[] = $_POST['type'];
		$insert[] = $_POST['helptext'];
		$insert[] = $_POST['values'];
		$insert[] = $id;
		
		$ps = $pdo->prepare("UPDATE customfields_keys SET name = ?, type = ?, helptext = ?, `values` = ? WHERE id = ?");
		$ps->execute($insert);
		
		redirect("franchise/customfields/");
}

if ($action == "doDeleteCustomField") {
		$ps = $pdo->prepare("DELETE FROM customfields_keys WHERE id = ? AND franchise = ?");
		$ps->execute(array($_GET['id'], $uid));
		
		redirect("franchise/customfields");
}

if ($action == "doForgotPasswordUser") {
		$newPassword = substr(sha1(uniqid()), 0, 8);
		$hashPassword = crypt($newPassword, '$2a$10$stIapougoewluzOuylAQo$');

		$ps = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
		$ps->execute(array($hashPassword, $_POST['email']));

		$ps = $pdo->prepare("SELECT email FROM users WHERE email = ? AND active = 1");
		$ps->execute(array($_POST['email']));
		$user = $ps->fetchColumn();

		if ($user) {
			mail($user, "Your New KidzArt Password", "Hello,\n\nYour password has been changed for the KidzArt Registration System.\n\nYour new password is: $newPassword\n\nThanks,\nKidzArt/Art Innovators");
		}
		
		setError(2, "A new password has been sent to your email address.");
		redirect("");
}

if ($action == "doForgotPasswordFranchise") {
		$newPassword = substr(sha1(uniqid()), 0, 8);
		$hashPassword = crypt($newPassword, '$2a$10$stIapougoewluzOuylAQo$');

		$ps = $pdo->prepare("UPDATE franchises SET password = ? WHERE email = ?");
		$ps->execute(array($hashPassword, $_POST['email']));

		$ps = $pdo->prepare("SELECT email FROM franchises WHERE email = ? AND active = 1");
		$ps->execute(array($_POST['email']));
		$user = $ps->fetchColumn();

		if ($user) {
			mail($user, "Your New KidzArt Password", "Hello,\n\nYour password has been changed for the KidzArt Registration System.\n\nYour new password is: $newPassword\n\nThanks,\nKidzArt/Art Innovators");
		}
		
		setError(2, "A new password has been sent to your email address.");
		redirect("franchise");
}

if ($action == "doAddVenue") {
		$badFields = array();
		if ($_POST['name'] == "") {
		    $badFields["name"] = "Please enter a name.";
		}
		if ($_POST['address'] == "") {
		    $badFields["address"] = "Please enter an address.";
		}
		
		if (count($badFields) == 0) {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $uid;
			$insert[] = $_POST['name'];
			$insert[] = $_POST['address'];
			
			$ps = $pdo->prepare("INSERT INTO meeting_places (id, franchise, name, address) VALUES (?,?,?,?)");
			$ps->execute($insert);
			
			redirect("franchise/venues");
		}
}

if ($action == "doUpdateVenue") {
	
		$insert = array();
		$insert[] = $_POST['name'];
		$insert[] = $_POST['address'];
		$insert[] = $id;
		
		$ps = $pdo->prepare("UPDATE meeting_places SET name = ?, address = ? WHERE id = ?");
		$ps->execute($insert);
		
		setError(2, "Venue info saved.");
		redirect("franchise/venues/");
}

if ($action == "doDeleteVenue") {
		$ps = $pdo->prepare("DELETE FROM meeting_places WHERE id = ? AND franchise = ?");
		$ps->execute(array($_GET['id'], $uid));
		
		redirect("franchise/venues");
}

if ($action == "doLookupGiftCard") {
		redirect("giftcertificates/".$_POST['code']);
}

if ($action == "doSendEmail") {
	
	$ps = $pdo->prepare("SELECT subject, body FROM templates WHERE id = ?");
	$ps->execute(array($_GET['id']));
	$template = $ps->fetch(PDO::FETCH_ASSOC);

	foreach ($_POST['to'] as $key => $value) {
		$ps = $pdo->prepare("SELECT email FROM users WHERE id = ?");
	    $ps->execute(array($value));
	    $email = $ps->fetchColumn();

	    $to = $email;
	    $from = "{$user['name']} <{$user['email']}>";
	    $subject = $_POST['subject'];
	    $message = $_POST['body'];
	    $headers  = "From: $from\r\n";
	    $headers .= "Content-type: text/html\r\n";

	    mail($to, $subject, $message, $headers);
	}

    

    setError(2, "Email has been sent.");
    redirect("franchise");
}

if ($action == "doEditBizCenterFile") {
	$ps = $pdo->prepare("UPDATE bizcenter_files SET name = ?, tags = ? WHERE id = ?");
	$ps->execute(array($_POST['name'], $_POST['tags'], $_POST['id']));

	if ($_POST['thumbnail'] != "") {
		$ps = $pdo->prepare("UPDATE bizcenter_files SET thumbnail = ? WHERE id = ?");
		$ps->execute(array($_POST['thumbnail'], $_POST['id']));
	}
	
	redirect("admin/bizcenter");
}

if ($action == "doDeleteBizCenterFile") {
	$ps = $pdo->prepare("DELETE FROM bizcenter_files WHERE id = ?");
	$ps->execute(array($_GET['id']));
	
	redirect("admin/bizcenter");
}

if ($action == "doEditCurricCenterFile") {
	$ps = $pdo->prepare("UPDATE curriccenter_files SET name = ?, tags = ? WHERE id = ?");
	$ps->execute(array($_POST['name'], $_POST['tags'], $_POST['id']));

	if ($_POST['thumbnail'] != "") {
		$ps = $pdo->prepare("UPDATE curriccenter_files SET thumbnail = ? WHERE id = ?");
		$ps->execute(array($_POST['thumbnail'], $_POST['id']));
	}
	
	redirect("admin/curriccenter");
}

if ($action == "doDeleteCurricCenterFile") {
	$ps = $pdo->prepare("DELETE FROM curriccenter_files WHERE id = ?");
	$ps->execute(array($_GET['id']));
	
	redirect("admin/curriccenter");
}

if ($action == "doEditKaWebinarsFile") {
	$ps = $pdo->prepare("UPDATE kawebinars_files SET name = ?, tags = ? WHERE id = ?");
	$ps->execute(array($_POST['name'], $_POST['tags'], $_POST['id']));

	if ($_POST['thumbnail'] != "") {
		$ps = $pdo->prepare("UPDATE kawebinars_files SET thumbnail = ? WHERE id = ?");
		$ps->execute(array($_POST['thumbnail'], $_POST['id']));
	}
	
	redirect("admin/kawebinars");
}

if ($action == "doDeleteKaWebinarsFile") {
	$ps = $pdo->prepare("DELETE FROM kawebinars_files WHERE id = ?");
	$ps->execute(array($_GET['id']));
	
	redirect("admin/kawebinars");
}

if ($action == "doAddAdminTemplate") {
	if (isAdmin()) {
		$insert = array();
		$insert[] = uniqid();
		$insert[] = $_POST['name'];
		$insert[] = $_POST['subject'];
		$insert[] = $_POST['body'];
		
		$ps = $pdo->prepare("INSERT INTO templates (id, owner, name, subject, body) VALUES (?,'admin',?,?,?)");
		$ps->execute($insert);
		
		redirect("admin/email");
	} else {
		redirect("");
	}
}

if ($action == "doUpdateAdminTemplate") {
	if (isAdmin()) {
		$insert = array();
		$insert[] = $_POST['name'];
		$insert[] = $_POST['subject'];
		$insert[] = $_POST['body'];
		$insert[] = $id;
		
		$ps = $pdo->prepare("UPDATE templates SET name = ?, subject = ?, body = ? WHERE id = ?");
		$ps->execute($insert);

		setError(2, "Email template edited successfully.");
		
		redirect("admin/email");
	} else {
		redirect("");
	}
}

if ($action == "doDeleteAdminEmail") {
	if (isAdmin()) {
		$insert = array();
		$insert[] = $_GET['id'];
		
		$ps = $pdo->prepare("DELETE FROM templates  WHERE id = ?");
		$ps->execute($insert);

		setError(2, "Email template deleted successfully.");
		
		redirect("admin/email");
	} else {
		redirect("");
	}
}

if ($action == "doAddFranchiseTemplate") {
		$insert = array();
		$insert[] = uniqid();
		$insert[] = $uid;
		$insert[] = $_POST['name'];
		$insert[] = $_POST['subject'];
		$insert[] = $_POST['body'];
		
		$ps = $pdo->prepare("INSERT INTO templates (id, owner, name, subject, body) VALUES (?,?,?,?,?)");
		$ps->execute($insert);
		
		redirect("franchise/email");
}

if ($action == "doUpdateFranchiseTemplate") {
		// check to see if the franchisee owns the templates
		$ps = $pdo->prepare("SELECT owner FROM templates WHERE id = ?");
		$ps->execute(array($id));
		$owner = $ps->fetchColumn();

		if ($owner == $uid) {
			$insert = array();
			$insert[] = $_POST['name'];
			$insert[] = $_POST['subject'];
			$insert[] = $_POST['body'];
			$insert[] = $id;

			$ps = $pdo->prepare("UPDATE templates SET name = ?, subject = ?, body = ? WHERE id = ?");
			$ps->execute($insert);
		} else {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $uid;
			$insert[] = $_POST['name'];
			$insert[] = $_POST['subject'];
			$insert[] = $_POST['body'];
			
			$ps = $pdo->prepare("INSERT INTO templates (id, owner, name, subject, body) VALUES (?,?,?,?,?)");
			$ps->execute($insert);
		}

		setError(2, "Email template edited successfully.");
		
		redirect("franchise/email");
}

if ($action == "doDeleteFranchiseEmail") {
		$insert = array();
		$insert[] = $_GET['id'];
		$insert[] = $uid;
		
		$ps = $pdo->prepare("DELETE FROM templates WHERE id = ? AND owner = ?");
		$ps->execute($insert);

		setError(2, "Email template deleted successfully.");
		
		redirect("franchise/email");
}

if ($action == "doCopyEmail") {
		$newID = uniqid();

		$insert = array();
		$insert[] = $_GET['id'];
		$insert[] = $newID;
		$insert[] = $uid;
		$insert[] = $_GET['id'];
		$insert[] = $newID;
		
		$ps = $pdo->prepare("CREATE TEMPORARY TABLE tmptable SELECT * FROM templates WHERE id = ?; UPDATE tmptable SET id = ?, owner = ? WHERE id = ?; INSERT INTO templates SELECT * FROM tmptable WHERE id = ?;");
		//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		$ps->execute($insert);

		setError(2, "Email template copied successfully.");
		
		redirect("franchise/email");
}

if ($action == "doClassChangeNotification") {
	sendClassChangeEmail($_GET['id']);
	
	setError(2, 'Class change email sent.');
	
	redirect("franchise/");
}

if ($action == "doUnsubscribe") {
	$ps = $pdo->prepare("UPDATE users SET unsubscribe = ? WHERE id = ?");
	$ps->execute(array(mktime(), $uid));

	setError(2, 'You have been unsubscribed from all emails.');
	
	redirect("");
}

if ($action == "doResubscribe") {
	$ps = $pdo->prepare("UPDATE users SET unsubscribe = '' WHERE id = ?");
	$ps->execute(array($uid));

	setError(2, 'You have been re-subscribed to all emails.');
	
	redirect("");
}

if ($action == "doProcessCustomerPayment") {
	
	if ($_POST['ccamount'] > 0) {
		$data = array();
		$data['Amount'] = $_POST['ccamount'];
		$data['AcctNum'] = $_POST['cc'];
		$data['ExpDate'] = $_POST['expm'].$_POST['expy'];
		$charge = rechargeChargeCard($user['rechargeApiKey'], $data);

		if ($charge->transactions[0]->transaction->responseCode == "000") {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $uid;
			$insert[] = $id;
			$insert[] = mktime();
			$insert[] = $charge->transactions[0]->transaction->responseDescription;
			$insert[] = $charge->transactions[0]->transaction->amount;
			$insert[] = $charge->transactions[0]->transaction->cardType;
			$insert[] = $charge->transactions[0]->transaction->maskedAcctNum;
			$insert[] = $charge->transactions[0]->transaction->expDate;
			$insert[] = $charge->transactions[0]->transaction->approvalCode;
			$ps = $pdo->prepare("INSERT INTO transactions (id, franchise, user, randate, result, credit, cardType, maskedAcctNum, expDate, approvalCode) VALUES (?,?,?,?,?,?,?,?,?,?)");
			$ps->execute($insert);

			setError(2, $charge->transactions[0]->transaction->responseDescription);
		} else {
			setError(1, $charge->transactions[0]->transaction->responseDescription);
		}
	}

	if ($_POST['ckamount'] > 0) {
		$insert = array();
		$insert[] = uniqid();
		$insert[] = $uid;
		$insert[] = $id;
		$insert[] = mktime();
		$insert[] = "Check Payment";
		$insert[] = $_POST['ckamount'];
		$insert[] = "Check #".$_POST['cknumber'];
		$ps = $pdo->prepare("INSERT INTO transactions (id, franchise, user, randate, result, credit, cardType) VALUES (?,?,?,?,?,?,?)");
		$ps->execute($insert);

		setError(2, "Check applied successfully.");
	}

	if ($_POST['caamount'] > 0) {
		$insert = array();
		$insert[] = uniqid();
		$insert[] = $uid;
		$insert[] = $id;
		$insert[] = mktime();
		$insert[] = "Cash Payment";
		$insert[] = $_POST['caamount'];
		$insert[] = "Cash";
		$ps = $pdo->prepare("INSERT INTO transactions (id, franchise, user, randate, result, credit, cardType) VALUES (?,?,?,?,?,?,?)");
		$ps->execute($insert);

		setError(2, "Cash applied successfully.");
	}

	if ($_POST['adamount'] > 0) {
		$insert = array();
		$insert[] = uniqid();
		$insert[] = $uid;
		$insert[] = $id;
		$insert[] = mktime();
		$insert[] = $_POST['note'];
		$insert[] = $_POST['adamount'];
		$insert[] = "Adjustment";
		$ps = $pdo->prepare("INSERT INTO transactions (id, franchise, user, randate, result, credit, cardType) VALUES (?,?,?,?,?,?,?)");
		$ps->execute($insert);

		setError(2, "Adjustment applied successfully.");
	}
	
	redirect("franchise/users/$id");
}

if ($action == "doSetRosterPrefs") {
	$insert = array();
	$insert[] = implode("|", $_POST['prefs']);
	$insert[] = $uid;
	$ps = $pdo->prepare("UPDATE franchises SET roster_prefs = ? WHERE id = ?");
	$ps->execute($insert);
	
	redirect("classsheet/".$_POST['roster']);
}

if ($action == "doCopyClass") {
		// class
		$newID = uniqid();

		$insert = array();
		$insert[] = $_GET['id'];
		$insert[] = $newID;
		$insert[] = mktime();
		$insert[] = strtotime("+1 month");
		$insert[] = $_GET['id'];
		$insert[] = $newID;
		
		$ps = $pdo->prepare("CREATE TEMPORARY TABLE tmptable SELECT * FROM classes WHERE id = ?; UPDATE tmptable SET id = ?, startdate = ?, enddate = ?, active = 0, name = CONCAT(name, ' COPY') WHERE id = ?; INSERT INTO classes SELECT * FROM tmptable WHERE id = ?;");
		//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		$ps->execute($insert);

		// meetings
		$ps = $pdo->prepare("SELECT * FROM meetings WHERE class = ?");
		$ps->execute(array($_GET['id']));
		$meetings = $ps->fetchAll();

		foreach ($meetings as $meeting) {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $meeting['franchise'];
			$insert[] = $newID;
			$insert[] = $meeting['day'];
			$insert[] = $meeting['location'];
			$insert[] = $meeting['time'];

			$ps = $pdo->prepare("INSERT INTO meetings (id, franchise, class, day, location, time) VALUES (?,?,?,?,?,?)");
			$ps->execute($insert);
		}

		setError(2, "Class copied successfully.");
		
		redirect("franchise/class/$newID");
}

if ($action == "doSaveReport") {
	$insert = array();
	$insert[] = $id = uniqid();
	$insert[] = $uid;
	$insert[] = $_POST['name'];
	$insert[] = $_POST['params'];
	$ps = $pdo->prepare("INSERT INTO reports (id, franchise, name, params) VALUES (?,?,?,?)");
	$ps->execute($insert);
	
	redirect("franchise/students?".$_POST['params']."&reportID=$id");
}

if ($action == "doDeleteReport") {
	$insert = array();
	$insert[] = $_GET['id'];
	$ps = $pdo->prepare("DELETE FROM reports WHERE id = ?");
	$ps->execute($insert);
	
	redirect("franchise/students");
}

if ($action == "doNewRoyaltyReport") {

	$badFields = array();

	if (count($badFields) == 0) {
		$insert = array();
		$insert[] = $report = uniqid();
		$insert[] = $user['id'];
		$insert[] = $_POST['month'];
		$insert[] = $_POST['year'];
		
		$ps = $pdo->prepare("INSERT INTO royalty (id, franchise, month, year) VALUES (?,?,?,?)");
		$ps->execute($insert);

		foreach ($_POST['revenue'] as $key => $value) {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $report;
			$insert[] = $key;
			$insert[] = $_POST['revenue'][$key];
			$insert[] = $_POST['students'][$key];
			$insert[] = $_POST['hourlyrateperstudent'][$key];
			$insert[] = $_POST['classes'][$key];
			$insert[] = $_POST['classhours'][$key];
			$insert[] = $_POST['advertisingcosts'][$key];
			
			$ps = $pdo->prepare("INSERT INTO royalty_revenue (id, report, `key`, revenue, `students`, `hourlyrateperstudent`, `classes`, `classhours`, `advertisingcosts`) VALUES (?,?,?,?,?,?,?,?,?)");
			$ps->execute($insert);
		}

		foreach ($_POST['expenseamount'] as $key => $value) {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $report;
			$insert[] = $key;
			$insert[] = $_POST['expenseamount'][$key];
			
			$ps = $pdo->prepare("INSERT INTO royalty_expenses (id, report, `key`, value) VALUES (?,?,?,?)");
			$ps->execute($insert);
		}
		
		redirect("franchise/royalty/");
	}
}

if ($action == "doDeleteRoyaltyReport") {

	$badFields = array();

	if (count($badFields) == 0) {
		$insert = array();
		$insert[] = $_GET['id'];
		
		$ps = $pdo->prepare("DELETE FROM royalty WHERE id = ?");
		$ps->execute($insert);

		$ps = $pdo->prepare("DELETE FROM royalty_revenue WHERE report = ?");
		$ps->execute($insert);

		$ps = $pdo->prepare("DELETE FROM royalty_expenses WHERE report = ?");
		$ps->execute($insert);
		
		redirect("franchise/royalty/");
	}
}

if ($action == "doFranchiseAddUser") {

	$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	// check email
	$ps = $pdo->prepare("SELECT id FROM users WHERE email = ?");
	$ps->execute(array($_POST['parent']['email']));
	$email = $ps->fetchColumn();

	if ($email) {
		setError(1, "That email address is in use.");
		redirect("franchise/users/");
	}

	// SET TEMP PASSWORD
	$pw = substr(sha1(uniqid()), 0, 8);
	
	// insert user
	$insert = array();
	$insert[] = $newUserId = uniqid();
	$insert[] = $_POST['parent']['name'];
	$insert[] = $_POST['parent']['email'];
	$insert[] = crypt($pw, '$2a$10$stIapougoewluzOuylAQo$');
	$insert[] = $uid;
	
	$ps = $pdo->prepare("INSERT INTO users (id, name, email, `password`, home_franchise) VALUES (?,?,?,?,?)");
	$ps->execute($insert);

	// insert children
	foreach ($_POST['name'] as $key => $value) {
		if ($value != '') {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $newUserId;
			$insert[] = $value;
			$insert[] = $_POST['grade'][$key];
			$insert[] = strtotime(str_replace("-", "/", $_POST['birthdate'][$key]));
			
			$ps = $pdo->prepare("INSERT INTO children (id, parent, name, grade, birthdate) VALUES (?,?,?,?,?)");
			$ps->execute($insert);
		}
	}

	// send mail to new user with login info
	$from = "{$user['name']} <{$user['email']}>";
    $headers  = "From: $from\r\n";

    $subject = "Your Login Info for " . $user['name'];
    $body = "Hello {$_POST['parent']['name']},

We've set up a new account for you on our online registration system!

You may log in by going to this link:
https://registration.kidzart.com

Your username is: {$_POST['parent']['email']}
Your password is: $pw

Our secure online registration system makes it easy to browse, sign up, and pay for classes.

Thank you,

{$user['name']}
";

	mail($_POST['parent']['email'], $subject, $body, $headers);
	
	
	setError(2, "User added successfully!");
	redirect("franchise/users/$newUserId");
}

if ($action == "doFranchiseAddChild") {

	if (count($badFields) == 0) {
		$insert = array();
		$insert[] = uniqid();
		$insert[] = $_POST['parent'];
		$insert[] = $_POST['name'];
		$insert[] = $_POST['grade'];
		$insert[] = strtotime(str_replace("-", "/", $_POST['birthdate']));
		
		$ps = $pdo->prepare("INSERT INTO children (id, parent, name, grade, birthdate) VALUES (?,?,?,?,?)");
		$ps->execute($insert);
		
		redirect("franchise/users/{$_POST['parent']}");
	}
}

if ($action == "doFranchiseDeleteChild") {

	$ps = $pdo->prepare("DELETE FROM children WHERE id = ?");
	$ps->execute(array($_GET['id']));
		
	redirect("franchise/users/{$_GET['parent']}");
}

if ($action == "doFranchiseAddChild") {

	if (count($badFields) == 0) {
		$insert = array();
		$insert[] = uniqid();
		$insert[] = $_POST['parent'];
		$insert[] = $_POST['name'];
		$insert[] = $_POST['grade'];
		$insert[] = strtotime(str_replace("-", "/", $_POST['birthdate']));
		
		$ps = $pdo->prepare("INSERT INTO children (id, parent, name, grade, birthdate) VALUES (?,?,?,?,?)");
		$ps->execute($insert);
		
		redirect("franchise/users/{$_POST['parent']}");
	}
}

if ($action == "doFranchiseRegisterChild") {

	// get class info
	$ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
	$ps->execute(array($_POST['class']));
	$class = $ps->fetch(PDO::FETCH_ASSOC);

	$insert = array();
	$insert[] = $student = uniqid();
	$insert[] = $_POST['parent'];
	$insert[] = $_POST['child'];
	$insert[] = $class['id'];
	$insert[] = 0;
	$insert[] = $class['price'];
	$insert[] = mktime();
	
	$ps = $pdo->prepare("INSERT INTO students (id, parent, child, class, pricing, amount, registerdate) VALUES (?,?,?,?,?,?,?)");
	$ps->execute($insert);

	// insert into transactions database
	$insert = array();
	$insert[] = uniqid();
	$insert[] = $uid;
	$insert[] = $_POST['parent'];
	$insert[] = $_POST['child'];
	$insert[] = $class['id'];
	$insert[] = mktime();
	$insert[] = $class['price'];
	$ps = $pdo->prepare("INSERT INTO transactions (id, franchise, user, child, class, randate, debit) VALUES (?,?,?,?,?,?,?)");
	$ps->execute($insert);

	// custom fields
	if (count($_POST['custom']) > 0) {

		foreach ($_POST['custom'] as $key => $value) {
			$insert = array();
			$insert[] = uniqid();
			$insert[] = $uid;
			$insert[] = $student;
			$insert[] = $key;
			if (is_array($value)) {
				$insert[] = implode(", ", $value);
			} else {
				$insert[] = $value;
			}

			if ($value != "") {
				$ps = $pdo->prepare("INSERT INTO customfields_values (id, owner, student, `key`, `value`) VALUES (?,?,?,?,?)");
				$ps->execute($insert);
			}
		}
	}

	sendRegistrationEmail($student);

	redirect("franchise/users/{$_POST['parent']}");
}

if ($action == "doFranchiseDeleteRegistration") {

	$ps = $pdo->prepare("DELETE FROM students WHERE id = ?");
	$ps->execute(array($_GET['id']));
		
	redirect("franchise/users/{$_GET['parent']}");
}
?>