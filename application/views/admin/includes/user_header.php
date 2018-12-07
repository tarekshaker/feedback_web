<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>IConnect - <?php echo isset ($page_title) ? $page_title : ' Admin'; ?></title>

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

    <script>
        $(document).ready(function () {
            $('ul.nav li.dropdown').hover(function () {
                $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
            }, function () {
                $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
            });
        });

    </script>
</head>

<body class="nav-sm">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="<?php echo base_url(); ?>organization/dashboard" class="site_title"><i
                                class="fa fa-paw"></i>
                        <span><?= ucwords($this->session->userdata("user_full_name")) ?></span></a>
                </div>

                <div class="clearfix"></div>
                <?php
                $logo = base_url() . 'assets/nav/' . $this->session->userdata('user_logo'); ?>
                <!-- menu profile quick info -->
                <div class="profile">
                    <div class="profile_pic">
                        <?php if ($this->session->userdata('user_logo') != '') { ?>
                            <img src="<?php echo $logo; ?>" alt="Profile image" class="img-circle profile_img">
                        <?php } else { ?>
                            <img src="<?php echo base_url(); ?>assets/no_image.png" alt="Profile image"
                                 class="img-circle profile_img">
                        <?php } ?>
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2><?= ucwords($this->session->userdata("user_full_name")) ?></h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->

                <br/>

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3 style="visibility: hidden;">.</h3>
                        <ul class="nav side-menu nav-stacked">
                            <li><a href="<?php echo base_url(); ?>organization/dashboard"><i class="fa fa-home"></i>
                                    Home </a></li>
                            <li><a href="<?php echo base_url(); ?>organization/accountSettings"><i
                                            class="fa fa-user"></i> Account Settings </a></li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-copy"></i>
                                    Reports
                                </a>

                                <ul class="dropdown-menu nav side-menu nav-stacked" aria-labelledby="navbarDropdown" role="menu">
                                    <?php if ($this->session->userdata('customerBehaviourSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                        <li><a href="<?php echo base_url(); ?>organization/customerBehaviour"><i
                                                        class="fa fa-smile-o"></i> Customer Behavior</a></li>
                                    <?php } ?>

                                    <?php if ($this->session->userdata('reportSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                        <li><a href="<?php echo base_url(); ?>organization/reportSection"><i
                                                        class="fa fa-file"></i> General Report </a></li>
                                    <?php } ?>

                                    <?php if ($this->session->userdata('employeeReport') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                        <li><a href="<?php echo base_url(); ?>organization/employeeSection"><i
                                                        class="fa fa-file-text"></i>Employees Reports </a></li>

                                    <?php } ?>

                                    <?php if ($this->session->userdata('birthdateSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                        <li><a href="<?php echo base_url(); ?>organization/birthdateSection"><i
                                                        class="fa fa-birthday-cake"></i> Birthday Module </a></li>
                                    <?php } ?>

                                    <?php if ($this->session->userdata('suggestedBranchReport') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                        <li><a href="<?php echo base_url(); ?>organization/suggestedBranches"><i
                                                        class="fa fa-birthday-cake"></i> Suggested Branches Report
                                            </a></li>
                                    <?php } ?>

                                    <?php if ($this->session->userdata('reviewsComments') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                        <li><a href="<?php echo base_url(); ?>organization/reviewsComments"><i
                                                        class="fa fa-birthday-cake"></i> Comments Report </a></li>
                                    <?php } ?>
                                </ul>

                            </li>

                            <?php if ($this->session->userdata('questionSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                <li><a href="<?php echo base_url(); ?>organization/questionSection"><i
                                                class="fa fa-bank"></i> Bank of Questions </a></li>
                            <?php } ?>

                            <?php if ($this->session->userdata('suggestedBranchSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                <li><a href="<?php echo base_url(); ?>organization/suggestedBranchSection"><i
                                                class="fa fa-cutlery"></i> Suggested Branches </a></li>
                            <?php } ?>
                            <?php if ($this->session->userdata('screenSaverSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                <li><a href="<?php echo base_url(); ?>organization/screenSaverSection"><i
                                                class="fa fa-picture-o"></i> Screen Saver</a></li>
                            <?php } ?>
                            <?php if ($this->session->userdata('employeeSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                <li><a href="<?php echo base_url(); ?>organization/employee"><i class="fa fa-users"></i>
                                        Employees Management </a></li>
                            <?php } ?>

                            <?php if ($this->session->userdata('backLogSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                <li><a href="<?php echo base_url(); ?>organization/backLogSection"><i
                                                class="fa fa-birthday-cake"></i> Backlog Module </a></li>
                            <?php } ?>
                            <?php if ($this->session->userdata('webviewSection') != '' || !$this->session->has_userdata('empl_id')) { ?>
                                <li><a href="<?php echo base_url(); ?>organization/webview"><i
                                                class="fa fa-birthday-cake"></i> WebView Links Module </a></li>
                            <?php } ?>
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
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                               aria-expanded="false">
                                <?php if ($this->session->userdata('user_logo') != '') { ?>
                                    <img src="<?php echo $logo; ?>"
                                         alt=""><?= ucwords($this->session->userdata("user_full_name")) ?>
                                <?php } else { ?>
                                    <img src="<?php echo base_url(); ?>assets/no_image.png"
                                         alt=""><?= ucwords($this->session->userdata("user_full_name")) ?>
                                <?php } ?>
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>

                            </ul>
                        </li>
                        <li role="presentation" class="dropdown">
                            <a href="javascript;" class="dropdown-toggle info-number" data-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-envelope-o"></i>
                                <span class="badge bg-green"><?php if ($this->session->userdata('expiremsg')) {
                                        echo '1';
                                    } ?></span>
                            </a>
                            <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                                <?php if ($this->session->userdata('expiremsg')) {
                                    if ($this->session->userdata('user_logo') != '') {
                                        echo '<li><a><span class="image"><img src="' . $logo . '" alt="Profile Image" /></span>

                       <span><span>Expiration Notification</span></span><span class="message">'
                                            . $this->session->userdata('expiremsg') . '  </span></a></li>';
                                    } else {
                                        echo '<li><a><span class="image"><img src="' . base_url() . '/assets/no_image.png" alt="Profile Image" /></span>

                       <span><span>Expiration Notification</span></span><span class="message">'
                                            . $this->session->userdata('expiremsg') . '  </span></a></li>';
                                    }
                                } ?>
                                <li>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->
