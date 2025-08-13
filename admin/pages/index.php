<?php
include_once dirname(__FILE__) . '/../../includes/common/CredsVerification.php';
include_once dirname(__FILE__) . '/../../includes/dao/VideoReadDao.php';
include_once dirname(__FILE__) . '/../../includes/dao/UserReadDao.php';
$uid = CredsVerification::checkAdminUserVerification();
if($uid==0){
  header("Location:" . Config::$SERVER_URL . "/"); exit();
}
$uread = new UserReadDao();
$admin_arr = $uread->getAdminUsers();
$admin = isset($admin_arr[$uid]) ? $admin_arr[$uid] : array();
$vread = new VideoReadDao();
$videos = $vread->getVideoListAdmin();
//print_r($videos);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Houseful - Under Review Videos</title>
    <link href="https://www.houseful.app/images/fav.png" rel="shortcut icon" type="image/x-icon"/>
    <link href="https://www.houseful.app/images/fav.png" rel="apple-touch-icon"/>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.html" class="site_title"><img src="https://www.houseful.app/images/fav.png" height="32" /> <span>Houseful</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="images/img.jpg" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?php echo $admin['fname']; ?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="index">Unreviewed Videos</a></li>
                      <!-- <li><a href="index2.html">Dashboard2</a></li>
                      <li><a href="index3.html">Dashboard3</a></li> -->
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
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
                    <img src="images/img.jpg" alt=""><?php echo $admin['fname']; ?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="javascript:;"> Profile</a></li>
                    <li>
                      <a href="javascript:;">
                        <span class="badge bg-red pull-right">50%</span>
                        <span>Settings</span>
                      </a>
                    </li>
                    <li><a href="javascript:;">Help</a></li>
                    <li><a href="login.html"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Under Review Videos</h3>
              </div>

              <!-- <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div> -->
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <!-- <div class="x_title">
                    <h2>Default Example <small>Users</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div> -->
                  <div class="x_content">
                    <!-- <p class="text-muted font-13 m-b-30">
                      DataTables has most features enabled by default, so all you need to do to use it with your own tables is to call the construction function: <code>$().DataTable();</code>
                    </p> -->
                    <table id="datatable" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Under Review Videos</th>
                        </tr>
                      </thead>


                      <tbody>
                      <?php 
                        foreach ($videos as $video) {
                      ?>    
                        <tr>
                          <td>
                            <div class="x_panel">
                              <div class="x_title">
                                <h2><strong>Name: </strong><?php echo $video['vname']; ?></h2>
                                <div class="clearfix"></div>
                              </div>
                              <div class="x_content">

                                <div class="col-md-7 col-sm-7 col-xs-12">
                                  <div class="product-image">
                                    <?php 
                                      $vidurl = 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $video['path'];
                                      if($video['iscdn']==1)  $vidurl = 'https://d1aku7gsvt7x3p.cloudfront.net' . $video['path'];
                                    ?>
                                    <a href="javascript:void(0);" onClick="playVideo(this,<?php echo $video['vid']; ?>);"><img src="<?php echo 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $video['thumb']; ?>" vidurl="<?php echo $vidurl; ?>" class="image1" alt="..."></a>
                                  </div>
                                </div>

                                <div class="col-md-5 col-sm-5 col-xs-12" style="border:0px solid #e5e5e5;">

                                  <h3 class="prod_title"><strong>Name: </strong><?php echo $video['vname']; ?></h3>

                                  <p><strong>Message: </strong><?php echo $video['vmessage']; ?></p>
                                  <br>

                                  <div class="">
                                      <ul class="list-unstyled msg_list">
                                        <li>
                                          <a>
                                            <span class="image">
                                              <img src="<?php echo 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $video['creator_pic']; ?>" width="50" alt="img">
                                            </span>
                                            <span>
                                              <span><?php echo $video['creator_name']; ?></span>
                                              <span class="time">Creator</span>
                                            </span>
                                            <span class="message"><?php echo $video['creator_bio']; ?></span>
                                          </a>
                                        </li>
                                        <li>
                                          <a>
                                            <span class="image">
                                              <img src="<?php echo 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $video['owner_pic']; ?>" width="50" alt="img">
                                            </span>
                                            <span>
                                              <span><?php echo $video['owner_name']; ?></span>
                                              <span class="time">Owner</span>
                                            </span>
                                            <span class="message"><?php echo $video['owner_bio']; ?></span>
                                          </a>
                                        </li>
                                      </ul>
                                  </div>
                                  <br>

                                  <div class="">
                                    <h2>Please Approve or Reject</h2>
                                    <ul class="list-inline prod_size">
                                      <li>
                                        <button type="button" class="btn btn-default btn-sm" onClick="javascript:videoStatusChanges(<?php echo $video['vid']; ?>,1);">Show</button>
                                      </li>
                                      <li>
                                        <button type="button" class="btn btn-default btn-sm" onClick="javascript:videoStatusChanges(<?php echo $video['vid']; ?>,2);">Hide</button>
                                      </li>
                                      <li>
                                        <button type="button" class="btn btn-default btn-sm" onClick="javascript:videoStatusChanges(<?php echo $video['vid']; ?>,3);">Reject</button>
                                      </li>
                                    </ul>
                                  </div>
                                  <div style="color:#ff0000; display:none;" id="vidError<?php echo $video['vid']; ?>">Error</div>
                                  <div style="color:#0000FF; display:none;" id="vidSucc<?php echo $video['vid']; ?>">Success</div>
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php 
                        //break;
                        }
                      ?>  
                    </tbody>
                    </table>
                  </div>
                </div>
              </div>

              

              

             
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script src="../vendors/iCheck/icheck.min.js"></script>
    <!-- Datatables -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>
    <script type="text/javascript">
    function playVideo(elm,vid){
      var width = $(elm).children("img").width();
      var height = $(elm).children("img").height();
      var video_url =$(elm).children("img").attr('vidurl');
      var parent = $(elm).parent();
      var html='<video width="' + width + '" height="' + height + '" id="video' + vid + '" controls><source src="' + video_url + '" type="video/mp4">Your browser does not support the video tag.</video>';
      $(parent).html(html);
      var myVideo = document.getElementById('video' + vid); 
      myVideo.play();
    }

    function videoStatusChanges(vid,status){
      $.ajax({
        url:"/apiv2/update-status.php",
        type: "POST",
        data:{"uid":"163980463033426744","status":status,"vid":vid},
        success:function(data)
        { 
          if(status==1) $("#vidSucc" + vid).html("Video approved successfully");
          else if(status==2) $("#vidSucc" + vid).html("Video hide successfully")
          else if(status==3) $("#vidSucc" + vid).html("Video rejected successfully")
          $("#vidSucc" + vid).show();
        },
        error:function(data){
          $("#vidError" + vid).html("Status Updation Failed");
          $("#vidError" + vid).show();
        }
      });
    }
    </script>

  </body>
</html>