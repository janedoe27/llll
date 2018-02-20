<?php
 
class User
{
	public $user_id;
    public $full_name;
    public $login_hash;
    public $facebook_id;
    public $twitter_id;
    public $google_id;
    public $email;
    public $deny_access;
    public $thumb_url;
    public $team;

    public $created_at;
    public $updated_at;
    public $is_deleted;

    public $username;
    public $password;

    // constructor
    function __construct() 
    {

    }
 
    // destructor
    function __destruct() 
    {
         
    }
}
 
?>