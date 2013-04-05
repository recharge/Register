<div style="max-width: 340px; padding: 8px 0pt;" class="well">
              <ul class="nav nav-list">

                <li class="nav-header">Admin Menu</li>
                <li<?php echo ($resource == "" ? " class='active'" : "") ?>><a href="/admin/"><i class="icon-home"></i> Home</a></li>
                <li<?php echo ($resource == "customers" ? " class='active'" : "") ?>><a href="/admin/customers"><i class="icon-user"></i> Customers</a></li>
                <li<?php echo ($resource == "franchises" ? " class='active'" : "") ?>><a href="/admin/franchises"><i class="icon-briefcase"></i> Franchises</a></li>
                <li<?php echo ($resource == "administrators" ? " class='active'" : "") ?>><a href="/admin/administrators"><i class="icon-certificate"></i> Admins</a></li>
                <li<?php echo ($resource == "email" ? " class='active'" : "") ?>><a href="/admin/email"><i class="icon-envelope"></i> Email</a></li>
                <li<?php echo ($resource == "bizcenter" ? " class='active'" : "") ?>><a href="/admin/bizcenter"><i class="icon-book"></i> Biz Center</a></li>
                <li<?php echo ($resource == "curriccenter" ? " class='active'" : "") ?>><a href="/admin/curriccenter"><i class="icon-leaf"></i> Curriculum</a></li>

              </ul>
            </div> <!-- /well -->