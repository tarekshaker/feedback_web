<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>IConnect - <?php echo isset ($page_title)?$page_title: ' Admin'  ;?></title>

    <!-- Bootstrap -->
    <link href="<?php echo base_url(); ?>assets/new/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo base_url(); ?>assets/new/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="<?php echo base_url(); ?>assets/new/build/css/custom.min.css" rel="stylesheet">
    <!-- jQuery -->
      <script src="<?php echo base_url(); ?>assets/new/vendors/jquery/dist/jquery.min.js"></script>
      <!-- Bootstrap -->
      <script src="<?php echo base_url(); ?>assets/new/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyD4SXag76nAkgO9EyA3qy2GdcieU3szR3M&libraries=places" async="" defer="defer" type="text/javascript"></script>
  </head>

  <body class="nav-sm">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?php echo base_url(); ?>" class="site_title"><i class="fa fa-paw"></i> <span><?=ucwords($this->session->userdata("admin_full_name"))?></span></a>
            </div>

            <div class="clearfix"></div>
            <?php
             $logo = base_url() . 'assets/nav/'. $this -> session -> userdata('logo');?>
            <!-- menu profile quick info -->
            <div class="profile">
              <div class="profile_pic">
                <img src="<?php echo $logo;?>" alt="Profile image" class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?=ucwords($this->session->userdata("admin_full_name"))?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3 style="visibility: hidden;">.</h3>
                <ul class="nav side-menu">
                  <li><a href="<?php echo base_url(); ?>admin/organizations" ><i class="fa fa-sitemap"></i> Organizations </a></li>
                    <li><a href="<?php echo base_url(); ?>admin/adminUsers" ><i class="fa fa-user"></i> Admin Users </a></li>
                    <li><a href="<?php echo base_url(); ?>admin/branches" ><i class="fa fa-map-marker"></i> Branches </a></li>
                    <li><a href="<?php echo base_url(); ?>admin/employee" ><i class="fa fa-male"></i> Employees </a></li>
                    <li><a href="<?php echo base_url(); ?>admin/groups" ><i class="fa fa-folder-open"></i> Groups of Questions</a></li>
                    <li><a href="<?php echo base_url(); ?>admin/questions" ><i class="fa fa-question-circle"></i> Questions </a></li>
                    <li><a href="<?php echo base_url(); ?>admin/jobs" ><i class="fa fa-briefcase"></i> Jobs </a></li>
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a href="logout" data-placement="top" title="Logout">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $logo;?>" alt=""><?=ucwords($this->session->userdata("admin_full_name"))?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>

                  </ul>
                </li>


              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->
