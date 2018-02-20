<?php
 
class Category {

	public $category_id;
    public $category;
    public $category_icon;
    public $photo_url;
    public $created_at;
    public $updated_at;
    public $is_deleted;
    public $show_in_menu;

    // constructor
    function __construct() {

    }
 
    // destructor
    function __destruct() {
         
    }
}
 
?>