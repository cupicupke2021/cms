 <?php
 /*
 $link = site_url().'/systems/Trs_Approval/List' ;
 $link2 = site_url().'/systems/Trs_Approval_Out/List' ;
 $id_reference = $obj->uri->segment(4);
 $modul_name   = $obj->uri->segment(2);

 $journal       = site_url().'/systems/Trs_Journal_Entry/List/'.$id_reference ;
 $attachment    = site_url().'/systems/trs_attachment/List/'.$id_reference ;
 $userid        = $context['userpropmain']['userid'];
 */
 ?>
 <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
		  <!-- Notifications: style can be found in dropdown.less -->

      <?php if(isset($id_reference)){ ?>
		  <li class="dropdown notifications-menu journal-top">
            <a href="#" class="dropdown-toggle text-shadow"" onclick="window:open('<?php echo $journal?>','popUpWindow','width=700,height=400');">
              <i class="fa fa-book"></i>&nbsp;Journal
              <span class="label label-warning"></span>
            </a>
          </li>
          <li class="dropdown notifications-menu journal-top">
            <a href="#" class="dropdown-toggle text-shadow"" onclick="window:open('<?php echo $attachment?>','popUpWindow','width=700,height=400');">
              <i class="fa fa-book"></i>&nbsp;Attachment
              <span class="label label-warning"></span>
            </a>
          </li>
      <?php } ?>
          <li class="dropdown notifications-menu approval-top"">
            <a href="#" class="dropdown-toggle text-shadow" onclick="window:open('<?php echo $link?>','popUpWindow','width=700,height=400');">
              <i class="fa fa-inbox"></i>&nbsp;Inbox
              <span class="label label-warning"></span>
            </a>
		      </li>
          <li class="dropdown notifications-menu approval-top"">
            <a href="#" class="dropdown-toggle text-shadow" onclick="window:open('<?php echo $link2?>','popUpWindow','width=700,height=400');">
              <i class="fa fa-envelope-o"></i>&nbsp;Outbox
              <span class="label label-warning"></span>
            </a>
		      </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle text-shadow" data-toggle="dropdown">
              <!-- <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
              <i class="fa fa-user-circle-o"></i>
			        <?php 
			        echo ''.ucfirst($context['userpropmain']['userid']).'&nbsp;|&nbsp;'.ucfirst($context['userpropmain']['emp_number']);
			        ?>
            </a>
		     </li>
         <li class="dropdown notifications-menu approval-top"">
            <a href="#" class="dropdown-toggle text-shadow" onclick="window:open('<?php echo $link?>','popUpWindow','width=700,height=400');">
              <i class="fa fa-users"></i>&nbsp;<?php echo ucfirst($context['userpropmain']['comp_id']) ?>
              <span class="label label-warning"></span>
            </a>
		      </li>
          <!-- Control Sidebar Toggle Button -->
        </ul>
      </div>