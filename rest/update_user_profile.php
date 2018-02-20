<?php

    require '../header_rest.php';
    $controllerRest = new ControllerRest();
    $controllerUser = new ControllerUser();

    $full_name = "";
    if( !empty($_POST['full_name']) )
        $full_name = $_POST['full_name'];

    $email = "";
    if( !empty($_POST['email']) )
        $email = $_POST['email'];

    $user_id ="";
    if( !empty($_POST['user_id']) )
        $user_id = $_POST['user_id'];

    $password ="";
    if( !empty($_POST['password']) )
        $password = md5($_POST['password']);

    $login_hash ="";
    if( !empty($_POST['login_hash']) )
        $login_hash = $_POST['login_hash'];

    $api_key = "";
    if(!empty($_POST['api_key']))
        $api_key = $_POST['api_key'];

    $thumb_url = "";
    $key = "uploaded_file";
    if(!empty($_FILES[$key]["name"]) ) {
        $thumb_url = getPhoto('thumb_url_', $key);
    }

    if(Constants::API_KEY != $api_key) {
        $jsonArray = array();
        $jsonArray['status'] = errorCodeFormat( "3", "Invalid Access.");
        echo json_encode($jsonArray);
        return;
    }

    if(!$controllerUser->isUserIdExistAndHash($user_id, $login_hash)) {
        $jsonArray = array();
        $jsonArray['status'] = errorCodeFormat( "3", "Invalid Access.");
        echo json_encode($jsonArray);
    }
    else {
        $itm = $controllerUser->getUserByUserId($user_id);
        if($itm != null) {
            $itm->full_name = $full_name;
            $itm->password = empty($password) ? $itm->password : $password;
            $itm->thumb_url = empty($thumb_url) ? $itm->thumb_url : $thumb_url;
            $controllerUser->updateUserFullNameAndPasswordThumbUrl($itm);

            $itm = $controllerUser->getUserByUserId($user_id);
            $jsonArray = array();
            $jsonArray['user_info'] = translateJSON($itm);
            $jsonArray['status'] = errorCodeFormat( "-1", "Success.");
            echo json_encode($jsonArray);
        }
    
    }

    function translateJSON($itm) {
        $jsonArray = array('user_id' => "$itm->user_id", 
                            'login_hash' => "$itm->login_hash",
                            'facebook_id' => "$itm->facebook_id",
                            'twitter_id' => "$itm->twitter_id",
                            'google_id' => "$itm->google_id",
                            'full_name' => "$itm->full_name",
                            'thumb_url' => "$itm->thumb_url",
                            'email' => "$itm->email",
                            'username' => "$itm->username",
                            'password' => "$itm->password",
                            'created_at' => "$itm->created_at",
                            'updated_at' => "$itm->updated_at",
                            'team' => "$itm->team");
        return $jsonArray;
    }

    function errorCodeFormat($status_code, $status_text) {
        $jsonArray = array('status_code' => "$status_code", 'status_text' => "$status_text");
        return $jsonArray;
    }

    function getPhoto($file_prefix_name, $obj_name) {
          $file_path = "../".Constants::IMAGE_UPLOAD_DIR."/";
          $file_name = $_FILES[$obj_name]['name'];
          $split = explode(".", $file_name);
          $ext = end( $split );

          $new_file_name = $file_prefix_name . basename(uniqid()) . "." . $ext;
          $file_path = $file_path . $new_file_name;
          $photo_path = "../".Constants::IMAGE_UPLOAD_DIR."/";
          if(move_uploaded_file($_FILES[$obj_name]['tmp_name'], $file_path)) {
              return Constants::ROOT_URL."".Constants::IMAGE_UPLOAD_DIR."/".$new_file_name;
          }
          return "";
    }
?>