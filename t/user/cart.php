<?php if ($_GET['action'] == "checkout" && $_SESSION['cartTotal'] > 0) { ?>
<div class="row">
	<div class="span12">
		<h2>Shopping Cart</h2>
		
		<hr>
	</div>
</div>
<div class="row">
	<div class="span3">
		<h4 style="margin-top: 0px;">Order Summary</h4>
		<div class="well">
			<table class="cart-totals">
				<?php $_SESSION['grandTotal'] = $_SESSION['cartTotal'] ?>
				<tr>
					<td class="left">Items (<?php echo count($_SESSION['cart']) ?>)</td>
					<td>$<?php echo number_format($_SESSION['grandTotal'], 2) ?></td>
				</tr>
				<?php 
	              if (count($_SESSION['gift']) > 0) {
	              	foreach ($_SESSION['gift'] as $gift) {
	            ?>
				<tr>
					<td class="left">Gift Certificate</td>
					<td>-$<?php echo number_format($gift['amount'], 2) ?></td>
				</tr>
				<?php $_SESSION['grandTotal'] -= $gift['amount']; ?>
				<?php if ($_SESSION['grandTotal'] < 0) { $_SESSION['grandTotal'] = 0; } ?>
				<?php } ?>
				<?php } ?>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td class="left"><strong>Total Due Now</strong></td>
					<td><strong >$<?php echo number_format($_SESSION['grandTotal'], 2) ?></strong></td>
				</tr>
				<?php if ($_SESSION['cartMonthly'] > 0) { ?>
				<tr>
					<td class="left"><strong>Total Monthly</strong></td>
					<td><strong >$<?php echo number_format($_SESSION['cartMonthly'], 2) ?></strong></td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
	<div class="span9">
		<h4 style="margin-top: 0px;">Payment Info</h4>
		
		<ul id="myTab" class="nav nav-tabs">
			  <?php
			  	$ps = $pdo->prepare("SELECT count(id) FROM paymethods WHERE user = ?");
				$ps->execute(array($uid));
				$paymethodCount = $ps->fetchColumn();
				
				if ($paymethodCount > 0) {
					$tab = 1;
				} else {
					$tab = 2;
				}
				
				if ($_POST['tab'] == 1 && $paymethodCount > 0) {
					$tab = 1;
				} else if ($_POST['tab'] == 2) {
					$tab = 2;
				}
			  ?>
			  <?php if ($paymethodCount > 0) { ?>
			  	<li class="<?php echo ($tab == 1 ? "active" : "") ?>"><a href="#paymethod" data-toggle="tab">Saved Credit Card</a></li>
			  	<li class="<?php echo ($tab == 2 ? "active" : "") ?>"><a href="#creditcard" data-toggle="tab">New Credit Card</a></li>
			  <?php } else { ?>
			  	<li class="active"><a href="#creditcard" data-toggle="tab">Credit Card</a></li>
			  <?php } ?>
              <li class=""><a href="#giftcertificate" data-toggle="tab">Gift Certificate</a></li>
              <?php if ($franchise['allow_cash'] == 1) { ?>
              <li class=""><a href="#checkcash" data-toggle="tab">Check / Cash</a></li>
              <?php } ?>
            </ul>
            <div id="myTabContent" class="tab-content">
              <div class="tab-pane fade <?php echo ($tab == 2 ? "active in" : "") ?>" id="creditcard">
                
                <form class="form-horizontal" action="/cart/checkout" method="post">
							<input type="hidden" name="action" value="doCheckout">
							<input type="hidden" name="method" value="cc">
							<input type="hidden" name="tab" value="2">
				            
				            <?php $fieldName = "card"; ?>
				            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
				              <label for="input_<?php echo $fieldName ?>" class="control-label">Card Number</label>
				              <div class="controls">
				                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
				                <?php if (isset($badFields[$fieldName])) { ?>
				                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
				                <?php } else { ?>
				                <span class="help-inline">
				                	<img src="https://www.rechargebilling.com/images/cc_visa.png">
				                	<img src="https://www.rechargebilling.com/images/cc_mc.png">
				                	<img src="https://www.rechargebilling.com/images/cc_discover.png">
				                </span>
				                <?php } ?>
				              </div>
				            </div>
				            
				            <div class="control-group">
				              <label class="control-label" for="inputEmail">Expiration Date</label>
				              <div class="controls">
				                <select name="expm" class="span2">
				                <?php
				                $m = 1;
				                $mo = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
				                while ($m < 13) {
				                ?>
				                	<option value="<?php echo str_pad($m, 2, "0", STR_PAD_LEFT) ?>"><?php echo $m ?> - <?php echo $mo[$m-1] ?></option>
				                <?php $m++; ?>
				                <?php } ?>
				                </select>
				                <select name="expy" class="span2">
				                <?php
				                $y = 0;
				                $st = date("Y");
				                while ($y < 10) {
				                ?>
				                	<option value="<?php echo substr($st+$y, -2, 2) ?>"><?php echo $st+$y ?></option>
				                <?php $y++; ?>
				                <?php } ?>
				                </select>
				              </div>
				            </div>
				            
				            <?php $fieldName = "billingZIP"; ?>
				            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
				              <label for="input_<?php echo $fieldName ?>" class="control-label">Billing ZIP Code</label>
				              <div class="controls">
				                <input type="text" class="input-mini" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
				                <?php if (isset($badFields[$fieldName])) { ?>
				                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
				                <?php } ?>
				              </div>
				            </div>
				            
				            <?php $fieldName = "cvv2"; ?>
				            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
				              <label for="input_<?php echo $fieldName ?>" class="control-label">CVV2</label>
				              <div class="controls">
				                <input type="text" class="input-mini" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
				                <?php if (isset($badFields[$fieldName])) { ?>
				                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
				                <?php } ?>
				              </div>
				            </div>
				            
				            <div class="control-group">
				              <label class="control-label" for="inputEmail"></label>
				              <div class="controls">
				                <button type="submit" class="btn btn-success">Place Your Order</button>
				              </div>
				            </div>
			          </form>
                
              </div>
              <div class="tab-pane fade <?php echo ($tab == 1 ? "active in" : "") ?>" id="paymethod">
                
                <form class="form-horizontal" action="/cart/checkout" method="post">
							<input type="hidden" name="action" value="doCheckout">
							<input type="hidden" name="method" value="cc">
							<input type="hidden" name="tab" value="1">
				            
				            <div class="control-group">
				              <label class="control-label" for="inputEmail">Saved Card</label>
				              <div class="controls">
				                <select name="paymethod" class="span2">
				                <?php
				                $ps = $pdo->prepare("SELECT * FROM paymethods WHERE user = ? ORDER BY id DESC");
								$ps->execute(array($uid));
								$paymethods = $ps->fetchAll();
				                foreach ($paymethods as $paymethod) {
				                ?>
				                	<option value="<?php echo $paymethod['id'] ?>">
				                		<?php echo $paymethod['cardtype'] ?> XXXX<?php echo $paymethod['number'] ?>
				                	</option>
				                <?php } ?>
				                </select>
				              </div>
				            </div>
				            
				            <div class="control-group">
				              <label class="control-label" for="inputEmail"></label>
				              <div class="controls">
				                <button type="submit" class="btn btn-success">Place Your Order</button>
				              </div>
				            </div>
			          </form>
                
              </div>
              <div class="tab-pane fade" id="giftcertificate">
                
                <form class="form-horizontal" action="/cart/checkout" method="post">
							<input type="hidden" name="action" value="doCheckout">
							<input type="hidden" name="method" value="gc">
				            
				            <?php $fieldName = "gccode"; ?>
				            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
				              <label for="input_<?php echo $fieldName ?>" class="control-label">Gift Certificate Code</label>
				              <div class="controls">
				                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>

				                <span class="help-inline"><a href="#giftCardBalanceInquiry" data-toggle="modal">Balance Inquiry</a></span>
				              </div>
				            </div>
				            
				            <div class="control-group">
				              <label class="control-label" for="inputEmail"></label>
				              <div class="controls">
				                <button type="submit" class="btn btn-success">Place Your Order</button>
				              </div>
				            </div>
			          </form>
                
              </div>
              <div class="tab-pane fade" id="checkcash">
                
                <form class="form-horizontal" action="/cart/checkout" method="post">
							<input type="hidden" name="action" value="doCheckout">
							<input type="hidden" name="method" value="ch">
							
							<div style="margin-left: 13px;">
							
							<p>Select this option if you will be paying by Check or Cash. <br><br></p>
				            
				            <p><button type="submit" class="btn btn-success">Place Your Order</button></p>
				            
							</div>
			          </form>
                
              </div>
            </div>
		
	</div>
</div>

<?php } else { ?>

<?php if (count($_SESSION['cart']) > 0) { ?>
<div class="row">
<div class="span12">
		<h2>Shopping Cart</h2>
		
		<hr>
		<table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th style="width: 85px;"></th>
                  <th style="width: 450px;">Class</th>
                  <th style="width: 160px;">Student</th>
                  <th style="width: 125px;" class="right">Price</th>
                  <th style="width: 24px;"></th>
                </tr>
              </thead>
              <tbody>
	              <?php 
	              if (count($_SESSION['cart']) > 0) {
	              	$total = 0;
	              	$monthly = 0;
	              	$monthlytotal = 0;
	              	
	              	$totalDiscount = 0;
	              	$totalMonthlyDiscount = 0;
	              	$totalMonthlyTotalDiscount = 0;
	              	
	              	$siblings = false;
	              	
	              	// detect duplicate classes
	              	$dupClasses = array();
	              	foreach ($_SESSION['cart'] as $item) {
	              		$dupClasses[] = $item['class'];
	              	}
	              	$dupClasses = array_count_values($dupClasses);
	              	
	              	foreach ($_SESSION['cart'] as $itemid => $item) {
	              
		              $ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
		              $ps->execute(array($item['class']));
		              $class = $ps->fetch(PDO::FETCH_ASSOC);

		              if ($item['pricing'] == 1) {
		              	$pricing = calculateMonthlyPayments($class['startdate'], $class['enddate'], $class['payments_price']);
		              	$price = $pricing['amount'];
		              	$permo = " / month";

		              	$monthly += round($price, 2);
				        $monthlytotal += $class['payments_price'];
		              } else {
			          	$price = $class['price'];
			          	$permo = "";
		              }

		              // proration
		              if (mktime() > $class['startdate'] && $franchise['allow_prorate'] == 1) {
		              	// set flag
		              	$prorated = true;

		              	// figure out total days in class
	              	    $classLength = $class['enddate'] - $class['startdate'];
	              	    $classLength = floor($classLength/(60*60*24));

	              	    // figure out how many days are left in class
	              	    $daysLeft = $class['enddate'] - mktime();
	              	    $daysLeft = floor($daysLeft/(60*60*24));

	              	    // change price
	              	    if ($item['pricing'] == 1) {
	              	    	// recurring
	              	    	//$class['payments_price'] = round($class['payments_price'] * ($daysLeft / $classLength), 2);
	              	    	//$pricing = calculateMonthlyPayments(mktime(), $class['enddate'], $class['payments_price']);
	              	    	//$price = $pricing['amount'] + 0.01;

				            //$monthly += round($price, 2);
				            //$monthlytotal += $class['payments_price'];
	              	    } else {
	              	    	// one-time
	              	    	$price = round($price * ($daysLeft / $classLength), 2);
	              	    }
		              } else {
		              	$prorated = false;
		              }

		              $total += round($price, 2);
	              ?>
	              <?php
	              $ps = $pdo->prepare("SELECT * FROM children WHERE id = ?");
	              $ps->execute(array($item['child']));
	              $child = $ps->fetch(PDO::FETCH_ASSOC);
	              ?>
                <tr>
                  <td>
                  	<?php if ($class['img'] == "") { ?>
						<img src="https://www.filepicker.io/api/file/EwFIXBqYRyOq_P3Pcak2" class="img-polaroid" style="height: 75px;">
					<?php } else { ?>
						<img src="/img/uploads/<?php echo $class['img'] ?>" class="img-polaroid" style="height: 75px;">
					<?php } ?>
                  </td>
                  <td>
                  	<strong><a href="/class/<?php echo $class['id'] ?>"><?php echo $class['name'] ?></a></strong><br>
                  	<?php echo date("F jS Y", $class['startdate']) ?> - <?php echo date("F jS Y", $class['enddate']) ?><br>
                  	<?php if ($prorated) { ?>
                  	<i>Eligible for pro-rated price; class <?php echo number_format(100 - (($daysLeft / $classLength) * 100), 0) ?>% complete</i>
                  	<?php } ?>
                  </td>
                  <td><?php echo $child['name'] ?></td>
                  <?php $siblingDiscount = 0;
                  if ($dupClasses[$item['class']] > 1) {
                  	$ps = $pdo->prepare("SELECT siblingdiscount FROM franchises WHERE id = (SELECT franchise FROM classes WHERE id = ?)");
					$ps->execute(array($item['class']));
					$siblingDiscount = $ps->fetchColumn();
                  }
                  if ($siblingDiscount > 0) {
					$siblingDiscount = $siblingDiscount / 100;
					
					if ($item['pricing'] == 1) {
						$totalMonthlyDiscount += round($price * $siblingDiscount, 2);
						$totalMonthlyTotalDiscount += round($class['payments_price'] * $siblingDiscount, 2);
					}
					
					$totalDiscount += round($price * $siblingDiscount, 2);
					
					$siblings = true;
					
                  ?>
                  <td class="right">
                  	<del class="muted">$<?php echo number_format($price, 2) ?><?php echo $permo ?></del><br>
                  	<span class="green">$<?php echo number_format($price - round($price * $siblingDiscount, 2), 2) ?><?php echo $permo ?></span>
                  	<?php $_SESSION['cart'][$itemid]['amount'] = $price - round($price * $siblingDiscount, 2); ?>
                  </td>
                  <?php } else { ?>
                  <td class="right">$<?php echo number_format($price, 2) ?><?php echo $permo ?></td>
                  <?php $_SESSION['cart'][$itemid]['amount'] = $price; ?>
                  <?php } ?>
                  <td><a class="btn btn-small btn-danger" href="/?action=doDeleteCartItem&id=<?php echo $itemid ?>"><i class="icon-trash icon-white"></i></a></td>
                </tr>
                <?php } ?>
                <?php } ?>
              </tbody>
            </table>
	</div>
</div>

<div class="row">
	<div class="span12">
		<div class="span4 well pull-right">
			<table class="cart-totals">
				<tr>
					<?php $subtotal = $total; ?>
					<td>Subtotal</td>
					<td>$<?php echo number_format($total, 2) ?></td>
				</tr>
				
				<?php
				if (count($_SESSION['cart']) > 0) {
					$dupClasses = array();
	              	foreach ($_SESSION['cart'] as $item) {
	              		$dupClasses[] = $item['class'];
	              	}
	              	$dupClasses = array_count_values($dupClasses);
	              	//print_r($dupClasses);
	              	if ($siblings) {
	            ?>
	            <tr>
					<td>Multi Sibling Discount</td>
					<td>-$<?php echo number_format($totalDiscount, 2) ?></td>
				</tr>
				<?php $total -= $totalDiscount; ?>
				<?php $monthly -= $totalMonthlyDiscount; ?>
				<?php $monthlytotal -= $totalMonthlyTotalDiscount; ?>
		        <?php      	
	              	}
	            }
				?>
				
				<?php 
	              if (count($_SESSION['discount']) > 0) {
	              	foreach ($_SESSION['discount'] as $discount) {
	              		if ($discount['type'] == 'p') {
	              			$discount['amount'] = $subtotal * ($discount['amount'] / 100);
		              		$total -= round($discount['amount'], 2);
	              		} else if ($discount['type'] == 'd') {
		              		$total -= round($discount['amount'], 2);
	              		}
	            ?>
				<tr>
					<td class="right"><p class="discount-description"><?php echo $discount['description'] ?></p></td>
					<td>-$<?php echo number_format($discount['amount'], 2) ?></td>
				</tr>
				<?php } ?>
				<?php } ?>
				
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td><strong>Total Due Today</strong></td>
					<td><strong>$<?php echo number_format($total, 2) ?></strong></td>
					<?php $_SESSION['cartTotal'] = $total; ?>
				</tr>
				<?php if (round(($monthlytotal / $monthly) - 1, 0) > 0) { ?>
				<tr>
					<td><strong>Plus <?php echo round(($monthlytotal / $monthly) - 1, 0) ?> additional payments of</strong></td>
					<td><strong>$<?php echo number_format($monthly, 2) ?></strong></td>
				</tr>
				<?php } ?>
				<?php $_SESSION['cartMonthly'] = $monthly; ?>
				<?php $_SESSION['cartBillingEnd'] = $monthlytotal; ?>
			</table>
			
			<p>&nbsp;</p>
			
			<p><a href="/cart/checkout" class="btn btn-large btn-block btn-primary">Check Out</a></p>
		</div>
		<div class="span4 well pull-right">
			    <div class="input-prepend input-append">
			    	<form action="/" method="post" style="margin-bottom: 0px;">
			    		<input type="hidden" name="action" value="doApplyCoupon">
					    <span class="add-on">Coupon Code</span>
					    <input class="span2" id="appendedPrependedInput" size="16" type="text" name="cid">
					    <button class="btn" type="submit">Apply</button>
			    	</form>
				</div>
		</div>
	</div>
</div>
<?php } else { ?>
<div class="row">
<div class="span12">
		<h2>Shopping Cart</h2>
		
		<hr>
		
		    <div class="alert alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <h4>Shopping Cart Empty</h4>
    Your shopping cart is empty. <a href="/">Click here</a> to add a class to your shopping cart.
    </div>
</div>
</div>
<?php } // end of shopping cart if ?>
<?php } // end of action if ?>

<div class="modal hide fade" id="giftCardBalanceInquiry">
	<form action="/" method="post" style="margin:0;">
		<input type="hidden" name="action" value="doLookupGiftCard" />
	    <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		    <h3>Balance Inquiry</h3>
	    </div>
	    <div class="modal-body">
            <div class="control-group">
              <label for="inputEmail" class="control-label">Gift Certificate Code</label>
              <div class="controls">
                <input type="text" name="code"/>
              </div>
            </div>
	    </div>
	    <div class="modal-footer">
		    <a href="#" class="btn" data-dismiss="modal">Close</a>
		    <button type="submit" class="btn btn-primary">Check Balance</button>
	    </div>
	</form>
</div>