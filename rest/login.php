<?php

  require_once '../header_rest.php';
  $controllerUser = new ControllerUser();

  $api_key = "";
  if(!empty($_POST['api_key']))
      $api_key = $_POST['api_key'];

  $username = "";
  if(!empty($_POST['username']))
      $username = $_POST['username'];

  $password = "";
  if(!empty($_POST['password']))
      $password = md5($_POST['password']);

  if(Constants::API_KEY != $api_key) {
      $jsonArray = array();
      $jsonArray['status'] = errorCodeFormat( "3", "Invalid Access.");
      echo json_encode($jsonArray);
      return;
  }

  if( !empty($username) || !empty($password) ) {
      $user = $controllerUser->getUserByUsernameAndPassword($username, $password);
      if($user != null) {
          // update the hash
          $controllerUser->updateUserHash($user);
          $jsonArray = array();
          $jsonArray['user_info'] = translateJSON($user);
          $jsonArray['status'] = errorCodeFormat( "-1", "Success.");
          echo json_encode($jsonArray);
      }
      else {
            $jsonArray = array();
            $jsonArray['status'] = errorCodeFormat( "1", "Username/Password is incorrect. Try again.");
            echo json_encode($jsonArray);
      }
  }
  else {
      $jsonArray = array();
      $jsonArray['status'] = errorCodeFormat( "3", "Invalid Access.");
      echo json_encode($jsonArray);
  }

  function translateJSON($itm) {

      $controllerRest = new ControllerRest();
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

?>