<?php
    require '../header_rest.php';
    $controllerRest = new ControllerRest();

    $api_key = "";
    if(!empty($_POST['api_key']))
        $api_key = $_POST['api_key'];

    $lat = 0;
    if(!empty($_POST['lat']))
        $lat = str_replace(",", ".", $_POST['lat']);

    $lon = 0;
    if(!empty($_POST['lon']))
        $lon = str_replace(",", ".", $_POST['lon']);

    $radius = 0;
    if(!empty($_POST['radius']))
        $radius = $_POST['radius'];

    $category_id = 0;
    if(!empty($_POST['category_id']))
        $category_id = $_POST['category_id'];

    $keywords = "";
    if(!empty($_POST['keywords']))
        $keywords = $_POST['keywords'];
    
    if(Constants::API_KEY != $api_key) {
        $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Invalid Access.');
        echo json_encode($arrayJSON);
        return;
    }

    if($lat == 0 || $lon == 0 || $radius <= 0) {
        $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Invalid Location parameters.');
        echo json_encode($arrayJSON);
        return;
    }

    $results = $controllerRest->searchCouponsResults($lat, $lon, $radius, $category_id, $keywords);

    $ind = 0;
    $arrayObjs = array();
    foreach ($results as $row) {
        $arrayObj = array();
        foreach ($row as $columnName => $field) {
            if(!is_numeric($columnName)) {
                $arrayObj[$columnName] = $field;
            }
        }

        if(!empty($arrayObj['coupon_id'])) {
            $arrayObj['claim_count'] = $controllerRest->getCouponClaimCount($arrayObj['coupon_id']);
        }
        
        $arrayObjs[$ind] = $arrayObj;
        $ind += 1;
    }

    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['coupons'] = $arrayObjs;

    $max_distance = $controllerRest->getMaxDistanceFound($lat, $lon);
    $arrayJSON['max_distance'] = $max_distance;

    $arrayJSON['status'] = array('status_code' => '-1', 'status_text' => 'Success.');
    echo json_encode($arrayJSON);

    function getObj($results) {
        $arrayObj = array();
        foreach ($results as $row) {
            foreach ($row as $columnName => $field) {
                if(!is_numeric($columnName)) {
                    $arrayObj[$columnName] = $field;
                }
            }
            break;
        }
        return $arrayObj;
    }

    function getArrayObjs($results) {
        $ind = 0;
        $arrayObjs = array();
        foreach ($results as $row) {
            $arrayObj = array();
            foreach ($row as $columnName => $field) {
                if(!is_numeric($columnName)) {
                    $arrayObj[$columnName] = $field;
                }
            }
            $arrayObjs[$ind] = $arrayObj;
            $ind += 1;
        }
        return $arrayObjs;
    }

?>