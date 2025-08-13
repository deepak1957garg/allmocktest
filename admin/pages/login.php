<?php
include_once dirname(__FILE__) . '/../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../../includes/common/CredsVerification.php';
include_once dirname(__FILE__) . '/../../includes/dao/UserReadDao.php';
$uid = CredsVerification::checkAdminUserVerification();
$error = "";
if($uid!=0){
  header("Location:" . Config::$SERVER_URL . "/admin/pages/index"); exit();
}
else{
  $uname = isset($_REQUEST['uname'])  ? $_REQUEST['uname'] : "";
  $password = isset($_REQUEST['password'])  ? $_REQUEST['password'] : "";
  if($uname!="" && $password!=""){
    $uread = new UserReadDao();
    $admin_arr = $uread->getAdminUsers();
    foreach($admin_arr as $admin){
      if($uname==$admin['uname'] && $password==$admin['pwd']){
        CredsVerification::setAdminUser($admin['uid']);
        header("Location:" . Config::$SERVER_URL . "/admin/pages/index"); exit();
      }
    }
    $error = "Either Username or Password is wrong";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Houseful - Admin Workbench</title>
    <link href="https://www.houseful.app/images/fav.png" rel="shortcut icon" type="image/x-icon"/>
    <link href="https://www.houseful.app/images/fav.png" rel="apple-touch-icon"/>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form method="post">
              <h1>Login Form</h1>
              <div>
                <?php if($error!="") echo '<div style="color:red;text-align: left;padding-bottom: 5px;">' . $error . '</div>'; ?>
              </div>
              <div>
                <input type="text" class="form-control" name="uname" placeholder="Username" required="" />
              </div>
              <div>
                <input type="password" class="form-control" name="password" placeholder="Password" required="" />
              </div>
              <div>
                <input type="submit" class="btn btn-default submit" value="Log in" />
                <!-- <a class="btn btn-default submit" href="index.html">Log in</a> -->
                <!-- <a class="reset_pass" href="#">Lost your password?</a> -->
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <!-- <p class="change_link">New to site?
                  <a href="#signup" class="to_register"> Create Account </a>
                </p> -->

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1>Houseful</h1>
                  <p>©2016 All Rights Reserved. Gentelella Alela! is a Bootstrap 3 template. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form">
          <section class="login_content">
            <form>
              <h1>Create Account</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input type="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <a class="btn btn-default submit" href="index.html">Submit</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member ?
                  <a href="#signin" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-paw"></i> Gentelella Alela!</h1>
                  <p>©2016 All Rights Reserved. Gentelella Alela! is a Bootstrap 3 template. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
