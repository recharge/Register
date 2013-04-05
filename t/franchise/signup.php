<?php
$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$step = $_REQUEST['step'];

//echo $step;

if ($step == 1 && $_POST['process'] == 1) {
	$badFields = array();
	if ($_POST['name'] == "") {
	    $badFields["name"] = "Please enter a franchise name.";
	}
	
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	    $badFields["email"] = "That is not a valid email address.";
	} else {
		$ps = $pdo->prepare("SELECT id FROM franchises WHERE email = ?");
		$ps->execute(array($_POST['email']));
		$franchise = $ps->fetchColumn();
		
		if ($franchise) {
			$badFields["email"] = "That email is already taken.";
		}
	}
	
	if ($_POST['contact'] == "") {
	    $badFields["contact"] = "Please enter a main contact.";
	}
	
	if ($_POST['location'] == "") {
	    $badFields["location"] = "Please enter a class location.";
	} else {
		$geo = geocode($_POST['location']);
		list($longitude, $latitude, $altitude) = explode(",", $geo->Response->Placemark->Point->coordinates);
		
		if ($latitude == "" || $longitude == "") {
			$badFields["location"] = "Error getting location.";
		}
	}
	
	if ($_POST['phone'] == "") {
	    $badFields["phone"] = "Please enter a phone number.";
	} else {
		$_POST['phone'] = formatPhone($_POST['phone']);
	}
	
	if ($_POST['password'] == "") {
	    $badFields["password"] = "Please enter a password.";
	} else {
		if ($_POST['password'] != $_POST['password2']) {
			$badFields["password"] = "Passwords do not match.";
			$badFields["password2"] = "";
		}
	}
	
	
	if (count($badFields) == 0) {
		$insert = array();
		$insert[] = $uid = uniqid();
		$insert[] = $_POST['email'];
		$insert[] = crypt($_POST['password'], '$2a$10$stIapougoewluzOuylAQo$');
		$insert[] = $latitude;
		$insert[] = $longitude;
		$insert[] = $_POST['name'];
		$insert[] = $geo->Response->Placemark->address;
		$insert[] = $_POST['phone'];
		$insert[] = $_POST['contact'];
		$insert[] = $_POST['branding'];
		
		$ps = $pdo->prepare("INSERT INTO franchises (id, email, `password`, lat, lon, name, address, phone, contact, branding) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$ps->execute($insert);
		
		$_SESSION['FID'] = $uid;
		$step = 2;
	}
}

if ($step == 2 && $_POST['process'] == 2) {
	$badFields = array();
	foreach ($_POST as $key => $value) {
		if ($value == "") {
			$plainkey = str_replace("_", " ", $key);
			$badFields[$key] = "$plainkey must not be blank.";
		}
	}
	
	if (count($badFields) == 0) {
		// Defines the API URL for getting all products
		$url = "https://www.rechargebilling.com/kidzartsignup";
		
		// Defines fields for the POST
		$fields['Legal_Business_Name'] = $_POST['Legal_Business_Name'];
		$fields['DBA_Name'] = $user['name'];
		$fields['Legal_Business_Address'] = $_POST['Legal_Business_Address'];
		$fields['Location_Address'] = "";
		$fields['Legal_City'] = $_POST['Legal_City'];
		$fields['Legal_State'] = $_POST['Legal_State'];
		$fields['Legal_ZIP'] = $_POST['Legal_ZIP'];
		$fields['DBA_City'] = "";
		$fields['DBA_State'] = "";
		$fields['DBA_ZIP'] = "";
		$fields['Business_Phone'] = $user['phone'];
		$fields['Business_Fax'] = "";
		$fields['Years_In_Business'] = $_POST['Years_In_Business'];
		$fields['Business_Type'] = $_POST['Business_Type'];
		$fields['Number_Of_Locations'] = "1";
		$fields['Tax_ID'] = $_POST['Tax_ID'];
		$fields['Main_Contact'] = $user['contact'];
		$fields['Email_Address'] = $user['email'];
		$fields['What_are_you_selling'] = "Art Classes";
		$fields['American_Express_Merchant_ID'] = "";
		$fields['Estimated_Monthly_Visa_MC_Sales'] = $_POST['Estimated_Monthly_Visa_MC_Sales'];
		$fields['Estimated_Average_Ticket'] = $_POST['Estimated_Average_Ticket'];
		$fields['Owner_Officer_Name'] = $_POST['Owner_Officer_Name'];
		$fields['Owner_Officer_Title'] = $_POST['Owner_Officer_Title'];
		$fields['Ownership_Stake'] = $_POST['Ownership_Stake'];
		$fields['Owner_Officer_Home_Address'] = "";
		$fields['Owner_Officer_City'] = "";
		$fields['Owner_Officer_State'] = "";
		$fields['Owner_Officer_ZIP'] = "";
		$fields['Years_At_Address'] = "";
		$fields['Own_Or_Lease'] = "";
		$fields['Bank_Account_Number'] = $_POST['Bank_Account_Number'];
		$fields['Routing_Number'] = $_POST['Routing_Number'];
		$fields['Password'] = $pass = sha1(uniqid());
		$fields['Confirm_Password'] = $pass;
		$fields['doSubmit'] = "yes";
		$fields['webhook'] = $user['id'];
		
		// open curl connection
		$ch = curl_init();
		
		// tell curl to use ssl
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		
		// set the url
		curl_setopt($ch,CURLOPT_URL,$url);
		
		// define the fields
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
		
		// tell curl to pass the result when complete
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		
		// execute and store response into variable
		$result = curl_exec($ch);
		
		// close connection
		curl_close($ch);
		
		$step = 3;
	}
}
?>
<div class="row">
	<div class="span12">
		<?php if ($step == 1) { ?>
		<h3>Franchise Sign Up</h3>
		<form class="bs-docs-example form-horizontal" method="POST">
			<input type="hidden" name="step" value="1" />
			<input type="hidden" name="process" value="1" />
            <legend>Basic Information</legend>
            
            <?php $fieldName = "name"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Franchise Name</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "branding"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Franchise Type</label>
              <div class="controls">
                <select name="branding">
                	<option value="1">KidzArt</option>
                	<option value="2">Art Innovators</option>
                </select>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "email"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Email</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "contact"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Main Contact</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">Owner of the franchise or similar main contact</span>
              </div>
            </div>
            
            <?php $fieldName = "location"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Class Location</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">City Or ZIP Code where classes are held for the most part</span>
              </div>
            </div>
            
            <?php $fieldName = "phone"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Phone Number</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "password"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Password</label>
              <div class="controls">
                <input type="password" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "password2"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Confirm Password</label>
              <div class="controls">
                <input type="password" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <div class="form-actions">
			    <button type="submit" class="btn btn-primary">Next <i class="icon-arrow-right icon-white"></i></button>
    		</div>
        </form>
        <?php } ?>
        
        <?php if ($step == 2) { ?>
		<h3>Franchise Sign Up - Credit Card Setup <a href="/franchise" class="btn btn-primary">Skip This Step</a></h3>



		<form class="bs-docs-example form-horizontal" method="POST">
			<input type="hidden" name="step" value="2" />
			<input type="hidden" name="process" value="2" />
            <legend>Legal Business Information</legend>
            
            <?php $fieldName = "Legal_Business_Name"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Legal Business Name</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "Legal_Business_Address"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Legal Street Address</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">Address listed on your corporation or DBA </span>
              </div>
            </div>
            
            <?php $fieldName = "Legal_City"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">City</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "Legal_State"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">State</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "Legal_ZIP"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">ZIP</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <legend>Business History</legend>
            
            <?php $fieldName = "Business_Type"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Business Type</label>
              <div class="controls">
                <select name="<?php echo $fieldName ?>">
					<option>Corporation / LLC</option>
					<option>Sole Proprietor</option>
					<option>Partnership</option>
				</select>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "Years_In_Business"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label"># Of Years In Business</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "Tax_ID"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Tax ID</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">EIN for Corp/LLC; SSN for Sole Prop</span>
              </div>
            </div>
            
            <legend>Transaction Volume</legend>
            
            <?php $fieldName = "Estimated_Monthly_Visa_MC_Sales"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Estimated Monthly Sales</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "Estimated_Average_Ticket"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Estimated Average Sale</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <legend>Owner / Officer</legend>
            
            <?php $fieldName = "Owner_Officer_Name"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Name</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "Owner_Officer_Title"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Title</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "Ownership_Stake"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">% Stake in Business</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <legend>Deposit Information</legend>
            
            <?php $fieldName = "Bank_Account_Number"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Bank Account Number</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">Registration funds will be deposited directly into this bank account</span>
              </div>
            </div>
            
            <?php $fieldName = "Routing_Number"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Routing Number</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <div class="form-actions">
			    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Finish</button>
    		</div>
        </form>
        <?php } ?>
        
        <?php if ($step == 3) { ?>
		<h3>Finished!</h3>
		<a href="/franchise/" class="btn btn-success">Go Home</a>
        <?php } ?>
	</div>
</div>