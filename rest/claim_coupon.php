<?php

    require '../header_rest.php';
    $controllerRest = new ControllerRest();
    $controllerUser = new ControllerUser();
    $controllerCouponClaim = new ControllerCouponClaim();
    
    $api_key = "";
    if(!empty($_POST['api_key']))
        $api_key = $_POST['api_key'];

    $coupon_id = 0;
    if(!empty($_POST['coupon_id']))
        $coupon_id = $_POST['coupon_id'];

    $user_id = 0;
    if(!empty($_POST['user_id']))
        $user_id = $_POST['user_id'];

    $login_hash = "";
    if(!empty($_POST['login_hash']))
        $login_hash = $_POST['login_hash'];

    
    if(Constants::API_KEY != $api_key || $coupon_id == 0) {
        $arrayJSON = array('status' => array('status_code' => '3', 'status_text' => 'Invalid Access.') );
        echo json_encode($arrayJSON);
        return;
    }

    $isExist = $controllerUser->isUserIdExistAndHash($user_id, $login_hash);
    if(!$isExist) {
        $arrayJSON = array('status' => array('status_code' => '3', 'status_text' => 'Invalid Access. Login timeout. Please relogin again.') );
        echo json_encode($arrayJSON);
        return;
    }

    $claimCoupon = $controllerCouponClaim->getCouponClaim($coupon_id, $user_id);
    if($claimCoupon != null) {
        $arrayJSON = array('status' => array('status_code' => '3', 'status_text' => 'Already claimed the coupon.') );
        echo json_encode($arrayJSON);
        return;
    }

    $itm = new CouponClaim();
    $itm->created_at = time();
    $itm->updated_at = time();
    $itm->is_deleted = 0;
    $itm->user_id = $user_id;
    $itm->coupon_id = $coupon_id;

    $controllerCouponClaim->insertCouponClaim($itm);

    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => '-1', 'status_text' => 'Success.');
    echo json_encode($arrayJSON);
?>