<?php 
    require_once 'header.php';
    $controller = new ControllerAuthentication();

    $extras = new Extras();
    $authentication_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    $user = $controller->getAccessUserByAuthenticationId($authentication_id);

    if($authentication_id != null) {
        if( isset($_POST['submit']) ) {
            $itm = new Authentication();
            $itm->authentication_id = $user->authentication_id;
            $itm->name = trim(strip_tags($_POST['name']));
            $itm->username = $user->username;

            $pass = trim(strip_tags($_POST['password']));
            $password_confirm = trim(strip_tags($_POST['password_confirm']));
            $password_current = trim(strip_tags($_POST['password_current']));
            $itm->password = md5( $pass );

            if(strlen($pass) < 8) {
                echo "<script >alert('Password field must be atleast 8 alphanumeric characters.');</script>";
            }
            else if($user->password != md5($password_current)) {
                echo "<script >alert('Current password does not match.');</script>";
            }
            else if($pass != $password_confirm) {
                echo "<script >alert('Password does not match.');</script>";
            }
            else {
                $controller->updateAccessUser($itm);
                header('Location: admin_access.php');
            }
        }
    }
    else {
        header('Location: 403.php');
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
                            <a href="categories.php" ><i class="fa fa-tasks fa-fw"></i> Categories</a>
                        </li>
                        <li>
                            <a href="coupons.php"><i class="fa fa-tags fa-fw"></i> Coupons</a>
                        </li>
                        <li>
                            <a href="admin_access.php" class="active"><i class="fa fa-key fa-fw"></i> Admin Access</a>
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
                    <h1 class="page-header">Update Admin Access</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Admin Access Details
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST">
                                        <div class="form-group">
                                            <label>Current Password</label>
                                            <input class="form-control" type="password" placeholder="Current Password" name="password_current" onkeypress='validateField(event)' required>
                                        </div>

                                        <div class="form-group">
                                            <label>Password</label>
                                            <input class="form-control" type="password" placeholder="Password" name="password" onkeypress='validateField(event)' required>
                                        </div>

                                        <div class="form-group">
                                            <label>Confirm Password</label>
                                            <input class="form-control" type="password" placeholder="Confirm Password" name="password_confirm" onkeypress='validateField(event)' required>
                                        </div>

                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input class="form-control" type="text" placeholder="Full Name" name="name" required value="<?php echo $user->name; ?>">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary" type="submit" name="submit">Save</button>
                                        <a href="admin_access.php" class="btn btn-danger">Cancel</a>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
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
