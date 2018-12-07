<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>IConnect | Login</title>

    <!-- Bootstrap -->
    <link href="<?php echo base_url(); ?>assets/new/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo base_url(); ?>assets/new/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?php echo base_url(); ?>assets/new/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="<?php echo base_url(); ?>assets/new/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo base_url(); ?>assets/new/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login" >
    <?php if ( trim (validation_errors()) != '' || trim ($auth_error) != '' ){?>
      <div class="alert alert-danger"><?php echo validation_errors();?>
      <?php echo $auth_error ; ?>
      </div>
    <?php }?>
    <div>
      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
              <form method="post" role="form">
                <h1>Login Form</h1>
                  <fieldset>
                      <div class="form-group">
                          <input class="form-control" placeholder="Email" name="email" type="text" value="<?php echo $this->input->post ('email');?>" autofocus>
                      </div>
                      <div class="form-group">
                          <input class="form-control" placeholder="Password" name="password" type="password" value="">
                      </div>
                    <div class="form-group">
                      <label for="sel1">Login as:</label>
                      <select class="form-control" name="position" id="sel1">
                        <option disabled selected>Select Login Type</option>
                        <option value="organization">Organization Owner</option>
                        <option value="employee">Employee</option>
                      </select>
                    </div>
                      <input style="margin-left: 0px;" value="Login" name="admin_login" type="submit" class="btn btn-lg btn-success btn-block" />
                  </fieldset>
                  <div class="separator">
                    <div class="clearfix"></div>
                    <br />
                    <div>
                      <h1><i class="fa fa-paw"></i> IConnect | Restaurant System </h1>
                      <p>©2016 All Rights Reserved.IConnect | Restaurant System! by PiTechnologies</p>
                    </div>
                  </div>
              </form>
          </section>
        </div>
      </div>
    </div>

    <script>
        window.onload = function() {
            history.replaceState("", "", "<?php echo base_url();?>organization/login");
        }
    </script>

  </body>
</html>
