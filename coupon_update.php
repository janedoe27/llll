<?php 
    require_once 'header.php';
    $controllerCategory = new ControllerCategory();
    $controllerCoupon = new ControllerCoupon();
    $categories = $controllerCategory->getCategories();

    $extras = new Extras();
    $coupon_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    if($coupon_id != null) {
        $coupon = $controllerCoupon->getCouponByCouponId($coupon_id);
        if( isset($_POST['submit']) ) {
            $coupon->title = htmlspecialchars(trim(strip_tags($_POST['title'])));
            $coupon->subtitle = htmlspecialchars(trim(strip_tags($_POST['subtitle'])));
            $coupon->description = htmlspecialchars(trim(strip_tags($_POST['description'])));
            $coupon->photo_url = htmlspecialchars(trim(strip_tags($_POST['photo_url'])));
            
            $coupon->lat = $_POST['lat'];
            $coupon->lon = $_POST['lon'];
            $coupon->expiration_date = $_POST['expiration_date'] . " 00:00:00";
            $coupon->coupon_url = $_POST['coupon_url'];
            $coupon->coupon_code = $_POST['coupon_code'];
            $coupon->category_id = $_POST['category_id'];
            $coupon->updated_at = time();
            $coupon->is_deleted = 0;

            if( !empty($_FILES["file_upload"]["name"]) ) {
                $photo_url = uploadFile("file_upload", "coupon_photo");
                if($photo_url != null)
                    $coupon->photo_url = $photo_url;
            }

            $is_featured = 0;
            if(isset($_POST['is_featured']))
                $is_featured = 1;

            $coupon->is_featured = $is_featured;

            $controllerCoupon->updateCoupon($coupon);
            header('Location: coupons.php');
        }
    }
    else {
        header('Location: 403.php');
    }

    if($coupon == null)
        header('Location: 403.php');
    

    function uploadFile($key, $prefix) {
        $desired_dir = Constants::IMAGE_UPLOAD_DIR;
        $errors = array();
        $file_name = $_FILES[$key]['name'];
        $file_size = $_FILES[$key]['size'];
        $file_tmp = $_FILES[$key]['tmp_name'];
        $file_type = $_FILES[$key]['type'];
        
        $timestamp =  uniqid();
        $temp = explode(".", $_FILES[$key]["name"]);
        $extension = end($temp);
        if(strcasecmp($extension, "png") == 0 || strcasecmp($extension, "jpg") == 0 || 
            strcasecmp($extension, "jpeg") == 0 || strcasecmp($extension, "gif") == 0 || strcasecmp($extension, "bmp") == 0) {
            
            $new_file_name = $desired_dir."/".$prefix."_".$timestamp.".".$extension;
            if(empty($errors) == true) {
                if(is_dir($desired_dir) == false) {
                    // Create directory if it does not exist
                    mkdir("$desired_dir", 0700);        
                }
                if(is_dir($file_name) == false) {
                    // rename the file if another one exist
                    move_uploaded_file($file_tmp, $new_file_name);
                }
                else {                                  
                    $new_dir = $new_file_name.time();
                    rename($file_tmp, $new_dir);     
                    $new_file_name = $new_dir;          
                }
                return Constants::ROOT_URL.$new_file_name;
            }
        }
        return null;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="vendor/datetime/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
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

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBB7Tce0Xd3GEb838FF5uRcIe8MQIRdQSo&sensor=false"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(function(){
            var mapDiv = document.getElementById('map');
                var map = new google.maps.Map(mapDiv, {
                  center: new google.maps.LatLng(<?php echo Constants::MAP_DEFAULT_LATITUDE . "," . Constants::MAP_DEFAULT_LONGITUDE; ?> ),
                  zoom: <?php echo Constants::MAP_DEFAULT_ZOOM_LEVEL; ?>,
                  mapTypeId: google.maps.MapTypeId.ROADMAP,

                });

            var latLng = new google.maps.LatLng( <?php echo "$coupon->lat, $coupon->lon";?>, true );
            var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: 'Here!'
                });

            map.setCenter(latLng);

            google.maps.event.addListener(map, 'click', function (mouseEvent) {

                if(marker != null)
                  marker.setMap(null);

                var lat = document.getElementById('latitude');
                var longi = document.getElementById('longitude');
                lat.value = mouseEvent.latLng.lat(); 
                longi.value = mouseEvent.latLng.lng();

                marker = new google.maps.Marker({
                    position: mouseEvent.latLng,
                    map: map,
                    title: 'Here!'
                });
            });

        });

        function validateLatLng(evt) {
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode( key );
            if(theEvent.keyCode == 8 || theEvent.keyCode == 127) {
                
            }
            else {
                var regex = /[0-9.]|\./;
                if( !regex.test(key) ) {
                  theEvent.returnValue = false;
                  if(theEvent.preventDefault) theEvent.preventDefault();
                }  
            }
        }
    </script>

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
                    <h1 class="page-header">Update Coupon</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Coupon Details
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Coupon Title</label>
                                            <input class="form-control" placeholder="Coupon Title" name="title" required value="<?php echo $coupon->title; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Subtitle</label>
                                            <input class="form-control" placeholder="Coupon Subtitle" name="subtitle" required value="<?php echo $coupon->subtitle; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Coupon Code</label>
                                            <input class="form-control" placeholder="Coupon Code" name="coupon_code" value="<?php echo $coupon->coupon_code; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Coupon URL</label>
                                            <input class="form-control" placeholder="Coupon URL" name="coupon_url" value="<?php echo $coupon->coupon_url; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Expiration Date</label>
                                            <?php 
                                                $newDate = date("Y-m-d", strtotime($coupon->expiration_date) );
                                            ?>
                                            <div class="input-group date form_datetime col-md-12" data-date-format="yyyy-mm-dd" data-link-field="dtp_input1">
                                                <input class="form-control" size="16" type="text" value="<?php echo $newDate ?>" readonly>
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                            </div>
                                            <input class="form-control" value="<?php echo $newDate; ?>" name="expiration_date" id="dtp_input1" type="hidden">
                                        </div>

                                        <div class="form-group">
                                            <label>Photo URL</label>
                                            <input class="form-control" placeholder="Photo URL" name="photo_url" value="<?php echo $coupon->photo_url; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Photo Upload</label>
                                            <input type="file" name="file_upload" >
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="5" name="description"><?php echo $coupon->description; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Coupon Featured ?</label>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="is_featured" <?php echo $coupon->is_featured == 1 ? "checked" : ""; ?> > Check this if featured
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select class="form-control" name="category_id">
                                                <option value="0">No Category</option>
                                                <option>2</option>
                                                <?php

                                                    foreach ($categories as $category) {

                                                        $selected = "";
                                                        if($category->category_id == $coupon->category_id)
                                                            $selected = "selected";

                                                        echo "<option value='$category->category_id' $selected>$category->category</option>";
                                                     } 
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Latitude</label>
                                            <input class="form-control" placeholder="Click on the Map for Longitude" name="lon" onkeypress='validateLatLng(event)' id="longitude" required value="<?php echo $coupon->lon; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Longitude</label>
                                            <input class="form-control" placeholder="Click on the Map for Latitude" name="lat" onkeypress='validateLatLng(event)' id="latitude" required value="<?php echo $coupon->lat; ?>">
                                        </div>

                                        <button type="submit" class="btn btn-primary" type="submit" name="submit">Save</button>
                                        <a href="coupons.php" class="btn btn-danger">Cancel</a>
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
                <!-- /.col-lg-6 -->
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Coupon Location
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form">
                                        <div class="form-group">
                                            <label>Click the Map to get latitude/longitude:</label>
                                            <div id="map" style="width:100%; height:400px"></div>
                                        </div>
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

    <script type="text/javascript" src="vendor/datetime/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script type="text/javascript" src="vendor/datetime/js/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
    <script type="text/javascript">
        $('.form_datetime').datetimepicker({
            //language:  'fr',
            weekStart: 1,
            minView: 2,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1,
            format: 'yyyy-mm-dd'
        });
    </script>

</body>

</html>
