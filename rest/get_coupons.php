<?php

    require '../header_rest.php';
    $controllerRest = new ControllerRest();
    
    $api_key = "";
    if(!empty($_GET['api_key']))
        $api_key = $_GET['api_key'];

    $lat = 0;
    if(!empty($_GET['lat']))
        $lat = str_replace(",", ".", $_GET['lat']);

    $lon = 0;
    if(!empty($_GET['lon']))
        $lon = str_replace(",", ".", $_GET['lon']);

    $radius = 0;
    if(!empty($_GET['radius']))
        $radius = $_GET['radius'];

    $get_categories_menu = 0;
    if(!empty($_GET['get_categories_menu']))
        $get_categories_menu = $_GET['get_categories_menu'];

    $get_categories = 0;
    if(!empty($_GET['get_categories']))
        $get_categories = $_GET['get_categories'];

    $get_featured = 0;
    if(!empty($_GET['get_featured']))
        $get_featured = $_GET['get_featured'];

    $category_id = 0;
    if(!empty($_GET['category_id']))
        $category_id = $_GET['category_id'];

    $user_id = 0;
    if(!empty($_GET['user_id']))
        $user_id = $_GET['user_id'];

    $default_count_to_find_distance = 10;
    if(!empty($_GET['default_count_to_find_distance']))
        $default_count_to_find_distance = $_GET['default_count_to_find_distance'];

    if(Constants::API_KEY != $api_key) {
        $arrayJSON = array('status' => array('status_code' => '3', 'status_text' => 'Invalid Access.') );
        echo json_encode($arrayJSON);
        return;
    }

    $arrayJSON = array();
    if($lat != 0 && $lon != 0  && $radius > 0 && $get_featured == 1) {
        $resultItems = $controllerRest->getResultCouponsFeatured($lat, $lon, $radius);
        $no_of_rows = $resultItems->rowCount();
        $arrayJSON['result_count'] = ''.$no_of_rows.'';
        $arrayJSON['coupons'] = getArray($resultItems);
    }
    else if($lat != 0 && $lon != 0  && $radius > 0 && $category_id > 0 ) {
        $resultItems = $controllerRest->getResultCouponsByCategoryId($lat, $lon, $radius, $category_id);
        $no_of_rows = $resultItems->rowCount();
        $arrayJSON['result_count'] = ''.$no_of_rows.'';
        $arrayJSON['coupons'] = getArray($resultItems);
    }
    else if($lat != 0 && $lon != 0  && $user_id > 0) {
        $resultItems = $controllerRest->getResultClaimedCouponsByUserId($lat, $lon, $user_id);
        $no_of_rows = $resultItems->rowCount();
        $arrayJSON['result_count'] = ''.$no_of_rows.'';
        $arrayJSON['coupons'] = getArray($resultItems);
    }
    else if($lat != 0 && $lon != 0  && $radius == 0 && $default_count_to_find_distance > 0) {
    	$resultItems = $controllerRest->getResultCouponsDefaultDistance($lat, $lon, $default_count_to_find_distance);
        $no_of_rows = $resultItems->rowCount();
        $arrayJSON['result_count'] = ''.$no_of_rows.'';
        $arrayJSON['coupons'] = getArray($resultItems);
    }
    else if($lat != 0 && $lon != 0  && $radius > 0) {
    	$resultItems = $controllerRest->getResultCouponsRadius($lat, $lon, $radius);
        $no_of_rows = $resultItems->rowCount();
        $arrayJSON['result_count'] = ''.$no_of_rows.'';
        $arrayJSON['coupons'] = getArray($resultItems);
    }
    

    if($get_categories == 1) {
        $results = $controllerRest->getResultCategories();
        $arrayJSON['categories'] = getArray($results);
    }
    if($get_categories_menu == 1) {
        $results = $controllerRest->getResultCategoriesShowInMenu();
        $arrayJSON['categories_menu'] = getArray($results);
    }

    $max_distance = $controllerRest->getMaxDistanceFound($lat, $lon);
    $default_distance = $controllerRest->getMaxDistanceFoundDefault($lat, $lon, $default_count_to_find_distance);
    $arrayJSON['max_distance'] = $max_distance;
    $arrayJSON['default_distance'] = $default_distance;

    echo json_encode($arrayJSON);



    function getPhotos($results) {
        $ind = 0;
        $arrayPhotos = array();
        foreach ($results as $row) {
            $arrayPhoto = array();
            foreach ($row as $columnName => $field) {
                if(!is_numeric($columnName)) {
                    $arrayPhoto[$columnName] = $field;
                }
            }
            $arrayPhotos[$ind] = $arrayPhoto;
            $ind += 1;
        }
        return $arrayPhotos;
    }

    function getUser($results) {
        $arrUser = array();
        foreach ($results as $row) {
            foreach ($row as $columnName => $field) {
                if(!is_numeric($columnName)) {
                    if($columnName == "created_at" || $columnName == "last_logged") {
                        $arrUser[$columnName] = time() - intval($field);
                    }
                    else {
                        $arrUser[$columnName] = $field;
                    }
                }
            }
            break;
        }
        return $arrUser;
    }

    function getArray($resultItems) {

        $controllerRest = new ControllerRest();
        $ind = 0;
        $arrayItems = array();
        foreach ($resultItems as $row) {
            $arrayItem = array();
            foreach ($row as $columnName => $field) {
                if(!is_numeric($columnName)) {
                    $arrayItem[$columnName] = $field;
                }
            }

            if(!empty($arrayItem['coupon_id'])) {
                $arrayItem['claim_count'] = $controllerRest->getCouponClaimCount($arrayItem['coupon_id']);
            }
            $arrayItems[$ind] = $arrayItem;
            $ind += 1;
        }

        return $arrayItems;
    }

?>