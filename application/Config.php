<?php

/**
 * Database config variables
 * Change this according to your server settings
 */

class Constants
{
	// Definitely your Database Host name
	const DB_HOST = "localhost";

	// change the user access, CPanel have user roles, when writing and reading files
	// set it to allow the certain User to read/write
	const DB_USER = "cleo";

	// change this according to your account credentials
	const DB_PASSWORD = "secret";

	// if you wish you create your own name for 
	// Database then change the word "db_couponsfinder"
	const DB_DATABASE = "db_couponsfinder";

	// If deployed in a web server, change this according to your configuration
	// For Example. the domain name is www.someUrl.com, then if the php files are stored in
	// a folder named as "responsive" then the complete url would be
	// www.someUrl.com/responsive/
	const ROOT_URL = "http://mg/couponsfinder/";

	// DO NOT CHANGE THIS
	// FOLDER DIRECTORY FOR IMAGES UPLOADED FROM
	// THE DESKTOP
	const IMAGE_UPLOAD_DIR = "upload_pic";

	// THIS KEY MUST MATCH TO YOUR IOS/Android key in Config.h/Config.java
	const API_KEY = "450908816KGdcae2aYMK";

	// NO OF ITEMS TO LOAD PER PAGINATION
	const NO_OF_ITEMS_PER_PAGE = 10;

	// Default latitude for the map to be set when it is loaded
	const MAP_DEFAULT_LATITUDE = 37.770185;

	// Default  longitude for the map to be set when it is loaded
	const MAP_DEFAULT_LONGITUDE = -122.419061;

	// Adjust map zoom for Store Insertion
	const MAP_DEFAULT_ZOOM_LEVEL = 18;
}


?>