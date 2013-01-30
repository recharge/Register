<?php if ($uid == "") { ?>
<form class="navbar-form pull-right" action="" method="POST">
			  <input type="hidden" name="action" value="doLogin" />
              <input class="span2" type="text" placeholder="Email" name="ka_email">
              <input class="span2" type="password" placeholder="Password" name="ka_password">
              <button type="submit" class="btn">Sign In</button>
            </form>
<?php } else { ?>
<div class="pull-right">
			  <ul class="nav nav-pills" style="margin-right: 0px;"><li class="disabled"><a href="#"><strong>Welcome, <?php echo $user['name']; ?></strong></a></li></ul>
			  <div class="btn-group" style="float: left;">
                <button class="btn <?php echo (count($_SESSION['cart']) > 0 ? "btn-success" : "") ?> dropdown-toggle" data-toggle="dropdown"><i class="icon-shopping-cart <?php echo (count($_SESSION['cart']) > 0 ? "icon-white" : "") ?>"></i> Cart (<?php echo count($_SESSION['cart']) ?>) <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  
                  <?php 
                  if (count($_SESSION['cart']) > 0) {
                  	$total = 0;
                  	foreach ($_SESSION['cart'] as $item) {
	              ?>
	              <?php
	              $ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
	              $ps->execute(array($item['class']));
	              $class = $ps->fetch(PDO::FETCH_ASSOC);
	              if ($item['pricing'] == 1) {
	              	$pricing = calculateMonthlyPayments($class['startdate'], $class['enddate'], $class['payments_price']);
	              	$price = $pricing['amount'];
	              } else {
		          	$price = $class['price'];
	              }
	              $total += $price;
	              ?>
	              <?php
	              $ps = $pdo->prepare("SELECT * FROM children WHERE id = ?");
	              $ps->execute(array($item['child']));
	              $child = $ps->fetch(PDO::FETCH_ASSOC);
	              ?>
                  <li class="cart-item">
                  	<div class="top-row">
                  		<span class="item-title"><?php echo $class['name'] ?></span>
                  		<span class="item-price"><span class="label label-success">$<?php echo number_format($price, 2) ?></span></span>
                  	</div>
                  	<div class="bottom-row">
                  		<span class="muted"><?php echo $child['name'] ?></span>
                  	</div>
                  </li>
                  <?php } ?>
                  
                  <li class="divider"></li>
                  
                  <li style="color: black;">
                  	<div class="line-item" style="white-space: normal;">
                  		<strong>Subtotal</strong> <span class="pull-right" style="color: #339900; font-weight: bold;">$<?php echo number_format($total, 2) ?></span>
                  	</div>	
                  </li>
                  
                  <li class="divider"></li>
                  
                  <li><a href="/cart/"><i class="icon-shopping-cart"></i> View Cart / Checkout</a></li>
                  
                  <li class="divider"></li>
                  
                  <li><a href="/?action=doEmptyCart"><i class="icon-trash"></i> Empty Cart</a></li>
                  
                  <?php } else { ?>
                  
                  <li><a href="#">Cart Empty!</a></li>
                  
                  <?php } ?>
                  	
                </ul>
              </div><!-- /btn-group -->
              <div class="btn-group" style="float: left;">
                <a class="btn" href="/?action=doLogout">Sign Out</a>
              </div><!-- /btn-group -->
            </div>
<?php } ?>