<!DOCTYPE html>
<html>
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
    <link href="https://colorlib.com/polygon/gentelella/css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo base_url(); ?>assets/new/build/css/custom.min.css" rel="stylesheet">
  </head>
<style media="screen">
html {
height: 105%;
/*Image only BG fallback*/
background: url('http://thecodeplayer.com/uploads/media/gs.png');
/*background = gradient + image pattern combo*/
background:
  linear-gradient(rgba(196, 102, 0, 0.2), rgba(155, 89, 182, 0.2)),
  url('http://thecodeplayer.com/uploads/media/gs.png');
}
.login_content {
    text-shadow: 0 0px 0 #000;
}
</style>
  <body class="login">

    <div>
      <div class="login_wrapper" style="margin-top: 1%;">
        <div class="animate form login_form" style=" margin: 10px;">
          <div style="background-color: rgba(0, 0, 0, 0.2);padding: 10px;border-radius: 10px;">
          <section class="login_content">
            <img src="<?php echo base_url(); ?>assets/nav/<?php echo $logo;?>" alt="Logo" style="width: 130px;height: 130px;" />

              <form method="post" role="form" action="<?php echo base_url(); ?>mobile/doLogin">
                <h1>Login Form</h1>
                  <fieldset>
                      <div class="form-group" style="padding-bottom: 50px;">
                          <input class="form-control" pattern="[0][1][0|1|2][0-9]{8}" placeholder="Phone Number ex:01xxxxxxxxx" name="phone" type="text" value="" autofocus required>
                      </div>

                      <input style="margin-left: 0px;" value="Sign In" name="admin_login" type="submit" class="btn btn-lg btn-success btn-block" />
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
    </div>
  </body>
</html>
