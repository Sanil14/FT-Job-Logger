<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
   <div class="sidebar-inner slimscrollleft">
      <!-- User -->
      <div class="user-box">
         <div class="user-img">
            <img <?php echo 'src="'.$profilepic.'"'?> alt="user-img" id="sidebar_img" title="<?php echo $username?>" class="rounded-circle img-thumbnail img-responsive">
            <div class="user-status offline"><i class="mdi mdi-adjust"></i></div>
         </div>
         <h5><a href="#"><?php echo $username?></a> </h5>
         <ul class="list-inline">
            <li class="list-inline-item">
               <a href="profile" >
               <i class="mdi mdi-settings"></i>
               </a>
            </li>
            <li class="list-inline-item">
               <a href="signout" class="text-custom" title="Signout">
               <i class="mdi mdi-power"></i>
               </a>
            </li>
         </ul>
      </div>
      <!-- End User -->
      <!--- Sidemenu -->
      <div id="sidebar-menu">
         <ul>
            <li class="text-muted menu-title">Navigation</li>
            <li>
               <a href="index" class="waves-effect"><i class="mdi mdi-view-dashboard"></i> <span> Dashboard </span> </a>
            </li>
            <li class="has_sub">
               <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-truck-check"></i> <span> Jobs</span> <span class="menu-arrow"></span></a>
               <ul class="list-unstyled">
                  <li><a href="vtc-jobs">VTC Jobs</a></li>
                  <li><a href="#">User Jobs</a></li>
               </ul>
            </li>
            <li><a class="waves-effect" href="#"><i class="mdi mdi-newspaper"></i><span> VTC News </span></a></li>
            <li>
               <a href="vtc-members" class="waves-effect"><i class=" mdi mdi-account-group"></i><span> VTC Members </span></a>
            </li>
            <li><a class="waves-effect" href="#"><i class="mdi mdi-information"></i><span> About VTC </span></a></li>
         </ul>
         <div class="clearfix"></div>
      </div>
      <div id="sidebar-menu">
         <ul>
            <li class="text-muted menu-title">Settings</li>
            <li>
               <a href="profile" class="waves-effect"><i class="mdi mdi-account-plus"></i> <span> User settings </span> </a>
            </li>
            <li><a class="waves-effect" href="settings"><i class="mdi mdi-settings"></i><span> Regional settings </span></a></li>
         </ul>
         <div class="clearfix"></div>
      </div>
      <?php
        if($userstats["Permission"] == "Admin")
        {
          ?>
        <div id="sidebar-menu">
         <ul>
            <li class="text-muted menu-title">Admin Panel</li>
            <li>
               <a href="new-user" class="waves-effect"><i class="mdi mdi-account-plus"></i> <span> User Creation </span> </a>
            </li>
         </ul>
        <div class="clearfix"></div>
      </div>
      <?php
        }
      ?>
   </div>
</div>
<!-- Left Sidebar End -->