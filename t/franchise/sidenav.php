<div style="max-width: 340px; padding: 8px 0pt;" class="well">
              <ul class="nav nav-list">

                <li class="nav-header">Registration</li>

                <li<?php echo ($resource == "" ? " class ='active'" : "") ?>><a href="/franchise/"><i class="icon-pencil"></i> Classes</a></li>
                <?php if ($eid == "" || $employee['access'] >= 2) { ?>
                    <li<?php echo ($resource == "users" ? " class='active'" : "") ?>>
                    <a href="/franchise/users"><i class="icon-user"></i> Users</a></li>
                <?php } ?>
                <?php if ($eid == "" || $employee['access'] >= 1) { ?>
                    <li<?php echo ($resource == "students" ? " class='active'" : "") ?>>
                    <a href="/franchise/students"><i class="icon-book"></i> Students</a></li>
                <?php } ?>
                <?php if ($eid == "" || $employee['access'] >= 2) { ?>
                    <li<?php echo ($resource == "venues" ? " class='active'" : "") ?>>
                	<a href="/franchise/venues/"><i class="icon-globe"></i> Venues</a></li>
                <?php } ?>
				<?php if ($eid == "" || $employee['access'] >= 2) { ?>
                    <li<?php echo ($resource == "email" ? " class='active'" : "") ?>>
                	<a href="/franchise/email/"><i class="icon-envelope"></i> Templates</a></li>
                <?php } ?>
                <?php if ($eid == "" || $employee['access'] >= 2) { ?>
                    <li<?php echo ($resource == "transactions" ? " class='active'" : "") ?>>
                    <a href="/franchise/transactions/"><i class="icon-barcode"></i> Transactions</a></li>
                <?php } ?>
				<?php if ($eid == "" || $employee['access'] >= 2) { ?>
                    <li<?php echo ($resource == "coupons" ? " class='active'" : "") ?>>
                    <a href="/franchise/coupons"><i class="icon-tags"></i> Coupons</a></li>
                <?php } ?>
                
                <li class="nav-header">Intranet</li>
                                
				<?php if ($eid == "") { ?>
                    <li<?php echo ($resource == "bizcenter" ? " class='active'" : "") ?>>
                    <a href="/franchise/bizcenter"><i class="icon-book"></i> Biz Center</a></li>
                <?php } ?>
				<?php if ($eid == "") { ?>
                    <li<?php echo ($resource == "curriccenter" ? " class='active'" : "") ?>>
                    <a href="/franchise/curriccenter"><i class="icon-leaf"></i> Curriculum</a></li>
                <?php } ?>

                <li class="nav-header">Settings</li>

                <?php if ($eid == "") { ?>
                    <li<?php echo ($resource == "profile" ? " class='active'" : "") ?>>
                    <a href="/franchise/profile"><i class="icon-list"></i> Profile</a></li>
                <?php } ?>
                <?php if ($eid == "") { ?>
                    <li<?php echo ($resource == "customfields" ? " class='active'" : "") ?>>
                    <a href="/franchise/customfields"><i class="icon-tasks"></i> Fields</a></li>
                <?php } ?>
                <?php if (isadmin() && $eid == "" || $employee['access'] >= 2) { ?>
                    <li<?php echo ($resource == "employees" ? " class='active'" : "") ?>>
                    <a href="/franchise/employees"><i class="icon-user"></i> Employees</a></li>
                <?php } ?>

              </ul>
            </div> <!-- /well -->