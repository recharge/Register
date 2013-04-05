<script type="text/javascript">
	$(function() {
		$( "#input_expdate" ).datepicker();
		$( "#input_enddate" ).datepicker();
	});
</script>
<div class="row">
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>
    
	<?php
		$page = ($_GET['p'] == "" ? 1 : $_GET['p']);
		$resultsPerPage = 20;
		$limit = ($page-1) * $resultsPerPage;
		$q = "%".$_GET['q']."%";
	
		if ($_GET['q'] == "") {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM coupons WHERE name LIKE ? OR tags LIKE ? ORDER BY name LIMIT $limit,$resultsPerPage");
			$ps->execute(array($q, $q));
			$coupons = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		} else {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM coupons WHERE name LIKE ? LIMIT $limit,$resultsPerPage");
			$ps->execute(array("%".$_GET['q']."%"));
			$coupons = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		}

		$pages = ceil($rows / $resultsPerPage);
		?>
    
<?php
$ps = $pdo->prepare("SELECT * FROM coupons WHERE id = ?");
$ps->execute(array($id));
$coupon = $ps->fetch(PDO::FETCH_ASSOC);
?>
<?php if ($coupon) { ?>
	<div class="span12">
		<h3><?php echo $coupon['name'] ?></h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/coupons/<?php echo $coupon['id'] ?>" method="POST">
				<input type="hidden" name="action" value="doUpdateCoupon" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $coupon[$fieldName] ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Internal name not visible to customers</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "code"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Coupon Code</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $coupon[$fieldName] ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">The code your customers will enter to take advantage of this discount</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "description"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Description</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $coupon[$fieldName] ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Short discount description that will be displayed to customers</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "discount"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Discount</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $coupon[$fieldName] ?>" />
	                <select name="discounttype" class="input-small">
	                	<option value="p" <?php echo ($coupon['discounttype'] == "p" ? 'selected' : "")?>>percent</option>
	                	<option value="d" <?php echo ($coupon['discounttype'] == "d" ? 'selected' : "")?>>dollars</option>
	                </select>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Flat USD or percentage discount</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "expdate"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Expiration Date</label>
	              <div class="controls">
	              <?php if ($coupon['expdate'] > 0) { ?>
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo date("m/d/Y",$coupon[$fieldName]) ?>" />
	                <?php } else { ?>
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" />
	                <?php } ?>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Coupon will expire on this date, leave blank for no expiration</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "expcount"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Maximum Uses</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $coupon[$fieldName] ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Coupon will expire after this many customers redeem it, leave blank for no expiration</span>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>

	</div>
<?php } else if ($id == "new") { ?>
	<div class="span10">
		<h3>New Coupon</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/coupons/new" method="POST">
				<input type="hidden" name="action" value="doAddCoupon" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Internal name not visible to customers</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "code"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Coupon Code</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">The code your customers will enter to take advantage of this discount</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "description"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Description</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Short discount description that will be displayed to customers</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "discount"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Discount</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
	                <select name="discounttype" class="input-small">
	                	<option value="p">percent</option>
	                	<option value="d">dollars</option>
	                </select>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Flat USD or percentage discount</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "expdate"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Expiration Date</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Coupon will expire on this date, leave blank for no expiration</span>
	              </div>
	            </div>
	            
	            <?php $fieldName = "expcount"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Maximum Uses</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
			        <span class="help-block">Coupon will expire after this many customers redeem it, leave blank for no expiration</span>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>

	</div>
	<?php } else { ?>
	<div class="span10">
		<h2>Coupons <small>Showing <?php echo $limit+1 ?>-<?php echo min($limit+$resultsPerPage, $rows) ?> of <?php echo $rows ?></small></h2>
		
        <div class="pull-right" style="margin-top: -30px; margin-bottom: -20px;">
		<form class="form-search" action="/franchise/coupons/" method="get">
		    <div class="input-append">
			    <input type="text" class="span2 search-query" name="q">
			    <button type="submit" class="btn">Search</button>
		    </div>
		</form>
		</div>
		
		<hr>
		
		
		<?php
		$ps = $pdo->prepare("SELECT * FROM coupons WHERE franchise = ?");
		$ps->execute(array($user['id']));
		$coupons = $ps->fetchAll();
		?>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Code</th>
                  <th>Discount</th>
                  <th>Expiration</th>
                  <th>Uses</th>
                  <th>Max Uses</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($coupons as $coupon) { ?>
                <tr>
                  <td><a href="/franchise/coupons/<?php echo $coupon['id'] ?>"><?php echo $coupon['name'] ?></a></td>
                  <td><?php echo $coupon['code'] ?></td>
                  <td>
                  	<?php echo ($coupon['discounttype'] == "d" ? '$' : "")?>
                  	<?php echo $coupon['discount'] ?>
                  	<?php echo ($coupon['discounttype'] == "p" ? '%' : "")?>
                  </td>
                  <td>
                  	<?php if ($coupon['expdate'] > 0) { ?>
                  	<?php echo date("m/d/Y", $coupon['expdate']) ?>
                  	<?php } ?>
                  </td>
                  <td><?php echo $coupon['uses'] ?></td>
                  <td><?php echo $coupon['expcount'] ?></td>
                  <td>    
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/franchise/coupons/<?php echo $coupon['id'] ?>"><i class="icon-pencil"></i> View / Edit</a></li>
				    	<li class="divider"/>
				    	<li><a href="/?action=doDeleteCoupon&id=<?php echo $coupon['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$coupons) { ?>
                <tr>
                  <td colspan="7">No Coupons Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <p><a href="/franchise/coupons/new" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add Coupon</a></p>
	</div>
	<?php } ?>

</div>