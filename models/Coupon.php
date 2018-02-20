<?php
 
class Coupon {

	public $coupon_id;
    public $lat;
    public $lon;
    public $title;
    public $subtitle;
    public $description;
    public $photo_url;
    public $expiration_date;
    public $coupon_url;
    public $coupon_code;

    public $created_at;
    public $updated_at;
    public $is_deleted;

    public $is_featured;
    public $is_ignore_location;
    public $category_id;

    // constructor
    function __construct() {

    }
 
    // destructor
    function __destruct() {
         
    }
}
 
?>