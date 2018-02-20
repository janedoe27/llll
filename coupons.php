<?php 
    require_once 'header.php';
    $controllerCoupon = new ControllerCoupon();
    $controllerCouponClaim = new ControllerCouponClaim();

    if(!empty($_SERVER['QUERY_STRING'])) {
        $extras = new Extras();
        $coupon_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
        $featured = $extras->decryptQuery2(KEY_SALT, $_SERVER['QUERY_STRING']);

        if( $coupon_id != null ) {
          $controllerCoupon->deleteCoupon($coupon_id, 1);
          header("Location: coupons.php");
        }
        
        if($featured != null) {
            $itm = new Coupon();
            $itm->coupon_id = $featured[0];
            $itm->is_featured = $featured[1] == "1" ? 0 : 1;
            $res = $controllerCoupon->updateCouponFeatured($itm);
            header("Location: coupons.php");
        }
    }

    $coupons = $controllerCoupon->getCoupons();

    $begin = 0;
    $page = 1;
    $count = count($coupons);
    $pages = intval($count/Constants::NO_OF_ITEMS_PER_PAGE);
    $search_criteria = "";
    if( isset($_POST['button_search']) ) {
        $search_criteria = trim(strip_tags($_POST['search']));
        $coupons = $controllerCoupon->getCouponsBySearching($search_criteria);
    }
    else {
        if($count%Constants::NO_OF_ITEMS_PER_PAGE != 0)
            $pages += 1;

        if( !empty($_GET['page']) ) {
            $page = $_GET['page'];
            $begin = ($page -1) * Constants::NO_OF_ITEMS_PER_PAGE;
            $end = Constants::NO_OF_ITEMS_PER_PAGE;
            $coupons = $controllerCoupon->getCouponsAtRange($begin, $end);
        }
        else {
            $begin = ($page -1) * Constants::NO_OF_ITEMS_PER_PAGE;
            $end = Constants::NO_OF_ITEMS_PER_PAGE;
            $coupons = $controllerCoupon->getCouponsAtRange($begin, $end);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Coupons Finder</title>

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="dist/css/custom.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Coupons Finder</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <?php echo $_SESSION['name']; ?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="index.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="home.php"><i class="fa fa-dashboard fa-fw"></i> Home</a>
                        </li>
                        <li>
                            <a href="categories.php"><i class="fa fa-tasks fa-fw"></i> Categories</a>
                        </li>
                        <li>
                            <a href="coupons.php" class="active"><i class="fa fa-tags fa-fw"></i> Coupons</a>
                        </li>
                        <li>
                            <a href="admin_access.php"><i class="fa fa-key fa-fw"></i> Admin Access</a>
                        </li>
                        <li>
                            <a href="users.php"><i class="fa fa-users fa-fw"></i> Users</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Coupons</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-tags fa-fw"></i> Coupons
                            <div class="pull-right">
                                <div class="btn-group">
                                    <a href="coupon_insert.php" class="btn btn-primary btn-xs " >Add Coupon</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Title</th>
                                                    <th>Coupon Code</th>
                                                    <th>Claim Count</th>
                                                    <th>Date Expired</th>
                                                    <th>Featured</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $ind = 1;
                                                    foreach ($coupons as $coupon) {
                                                        $date = date_create($coupon->expiration_date);
                                                        $datetime =  date_format($date, "Y-m-d");

                                                        $claim_count = $controllerCouponClaim->getCouponClaimByCouponId($coupon->coupon_id);

                                                        $extras = new Extras();
                                                        $updateUrl = $extras->encryptQuery1(KEY_SALT, 'coupon_id', $coupon->coupon_id, 'coupon_update.php');
                                                        $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'coupon_id', $coupon->coupon_id, 'coupons.php');
                                                        $featuredUrl = $extras->encryptQuery2(KEY_SALT, 'coupon_id', $coupon->coupon_id, 'featured', $coupon->is_featured, 'coupons.php');

                                                        $featured = "<a class='btn btn-default btn-xs' href='$featuredUrl'>Featured</a>";
                                                        if($coupon->is_featured == 1) {
                                                            $featured = "<a class='btn btn-primary btn-xs' href='$featuredUrl'>Featured</a> ";
                                                        }

                                                        echo "<tr>";
                                                        echo "<td>$ind</td>";
                                                        echo "<td>$coupon->title</td>";
                                                        echo "<td>$coupon->coupon_code</td>";
                                                        echo "<td>$claim_count</td>";
                                                        echo "<td>$datetime</td>";
                                                        echo "<td>$featured</td>";
                                                        echo "<td>
                                                            <a class='btn btn-primary btn-xs' href='$updateUrl'><span class='glyphicon glyphicon-pencil'></span></a>
                                                            <button  class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_$coupon->coupon_id'><span class='glyphicon glyphicon-remove'></span></button>
                                                        </td>";
                                                        echo "</tr>";
                                                        $ind += 1;

                                                        //<!-- Modal -->
                                                        echo "<div class='modal fade' id='modal_$coupon->coupon_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                                                    <div class='modal-dialog'>
                                                                        <div class='modal-content'>
                                                                            <div class='modal-header'>
                                                                                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                                                <h4 class='modal-title' id='myModalLabel'>Deleting Coupon</h4>
                                                                            </div>
                                                                            <div class='modal-body'>
                                                                                <p>Deleting this is not irreversible. Do you wish to continue?
                                                                            </div>
                                                                            <div class='modal-footer'>
                                                                                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                                                                <a type='button' class='btn btn-primary' href='$deleteUrl'>Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>";
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->
                                </div>
                                <!-- /.col-lg-4 (nested) -->
                            </div>
                            <!-- /.row -->

                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->

                    <div class="btn-group pull-right">
                    <?php
                        if(empty($search_criteria)) {
                            if($pages != 0) {
                                if($page == 1) {
                                    echo "<a class='btn btn-primary btn-xs' href='coupons.php?page=1'><span class='glyphicon glyphicon-chevron-left'></span></a>";
                                }
                                else {
                                    $newPage = $page -1;
                                    echo "<a class='btn btn-primary btn-xs' href='coupons.php?page=$newPage'><span class='glyphicon glyphicon-chevron-left'></span></a>";
                                }
                                echo "<a class='btn btn-primary btn-xs' href='#'>$page/$pages</a>";
                                if($page == $pages) {
                                    echo "<a class='btn btn-primary btn-xs' href='coupons.php?page=$pages'><span class='glyphicon glyphicon-chevron-right'></span></a>";
                                }
                                else {
                                    $newPage = $page + 1;
                                    echo "<a class='btn btn-primary btn-xs' href='coupons.php?page=$newPage'><span class='glyphicon glyphicon-chevron-right'></span></a>";
                                }
                            }
                        }
                    ?>
                    </div>

                </div>
                <!-- /.col-lg-8 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

</body>

</html>
