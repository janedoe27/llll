<?php 
    require_once 'header.php';
    $controllerCategory = new ControllerCategory();
    $categories = $controllerCategory->getCategories();

    if(!empty($_SERVER['QUERY_STRING'])) {
        $extras = new Extras();
        $category_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
        if( $category_id != null ) {
            $controllerCategory->deleteCategory($category_id, 1);
            header("Location: categories.php");
        }
    }

    $begin = 0;
    $page = 1;
    $count = count($categories);
    $pages = intval($count/Constants::NO_OF_ITEMS_PER_PAGE);
    $search_criteria = "";
    if($count%Constants::NO_OF_ITEMS_PER_PAGE != 0)
        $pages += 1;

    if( !empty($_GET['page']) ) {
        $page = $_GET['page'];
        $begin = ($page -1) * Constants::NO_OF_ITEMS_PER_PAGE;
        $end = Constants::NO_OF_ITEMS_PER_PAGE;
        $categories = $controllerCategory->getCategoriesAtRange($begin, $end);
    }
    else {
        $begin = ($page -1) * Constants::NO_OF_ITEMS_PER_PAGE;
        $end = Constants::NO_OF_ITEMS_PER_PAGE;
        $categories = $controllerCategory->getCategoriesAtRange($begin, $end);
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
                            <a href="categories.php" class="active"><i class="fa fa-tasks fa-fw"></i> Categories</a>
                        </li>
                        <li>
                            <a href="coupons.php"><i class="fa fa-tags fa-fw"></i> Coupons</a>
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
                    <h1 class="page-header">Categories</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-tasks fa-fw"></i> Categories
                            <div class="pull-right">
                                <div class="btn-group">
                                    <a href="category_insert.php" class="btn btn-primary btn-xs " >Add Category</a>
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
                                                    <th>Category</th>
                                                    <th>Show in Menu</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $ind = 1;
                                                    foreach ($categories as $category) {

                                                        $extras = new Extras();
                                                        $updateUrl = $extras->encryptQuery1(KEY_SALT, 'category_id', $category->category_id, 'category_update.php');
                                                        $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'category_id', $category->category_id, 'categories.php');
                                                        $showInMenu = $category->show_in_menu ? '<strong>Yes</strong>' : 'No';
                                                        echo "<tr>";
                                                        echo "<td>$ind</td>";
                                                        echo "<td>$category->category</td>";
                                                        echo "<td>$showInMenu</td>";
                                                        echo "<td>
                                                            <a class='btn btn-primary btn-xs' href='$updateUrl'><span class='glyphicon glyphicon-pencil'></span></a>
                                                            <button  class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_$category->category_id'><span class='glyphicon glyphicon-remove'></span></button>
                                                        </td>";
                                                        echo "</tr>";
                                                        $ind += 1;

                                                        //<!-- Modal -->
                                                        echo "<div class='modal fade' id='modal_$category->category_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                                                                <div class='modal-dialog'>
                                                                    <div class='modal-content'>
                                                                        <div class='modal-header'>
                                                                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                                            <h4 class='modal-title' id='myModalLabel'>Deleting Category</h4>
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
                                    echo "<a class='btn btn-primary btn-xs' href='categories.php?page=1'><span class='glyphicon glyphicon-chevron-left'></span></a>";
                                }
                                else {
                                    $newPage = $page -1;
                                    echo "<a class='btn btn-primary btn-xs' href='categories.php?page=$newPage'><span class='glyphicon glyphicon-chevron-left'></span></a>";
                                }
                                echo "<a class='btn btn-primary btn-xs' href='#'>$page/$pages</a>";
                                if($page == $pages) {
                                    echo "<a class='btn btn-primary btn-xs' href='categories.php?page=$pages'><span class='glyphicon glyphicon-chevron-right'></span></a>";
                                }
                                else {
                                    $newPage = $page + 1;
                                    echo "<a class='btn btn-primary btn-xs' href='categories.php?page=$newPage'><span class='glyphicon glyphicon-chevron-right'></span></a>";
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
