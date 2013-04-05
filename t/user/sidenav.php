<div style="max-width: 340px; padding: 8px 0pt;" class="well">
              <ul class="nav nav-list">
              	
              
                <li class="nav-header">User Menu</li>
                <li<?php echo ($_GET['page'] == "" ? " class='active'" : "") ?>><a href="/"><i class="icon-home"></i> Home</a></li>
                <li<?php echo ($_GET['page'] == "class" ? " class='active'" : "") ?>><a href="/class/"><i class="icon-th"></i> My Classes</a></li>
                <li<?php echo ($_GET['page'] == "billing" ? " class='active'" : "") ?>><a href="/billing/"><i class="icon-barcode"></i> Billing</a></li>

                <li class="nav-header">Settings</li>
                <li<?php echo ($_GET['page'] == "profile" ? " class='active'" : "") ?>><a href="/profile"><i class="icon-list"></i> Profile</a></li>
                <li<?php echo ($_GET['page'] == "children" ? " class='active'" : "") ?>><a href="/children/"><i class="icon-user"></i> Children</a></li>
                <li class="divider"/>
                <li><a href="#"><i class="icon-question-sign"></i> Help</a></li>
              </ul>
            </div> <!-- /well -->