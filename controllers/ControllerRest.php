<?php

 
class ControllerRest
{
 
    private $db;
    private $pdo;
    function __construct() 
    {
        // connecting to database
        $this->db = new DB_Connect();
        $this->pdo = $this->db->connect();
    }
 
    function __destruct() { }

    public function getResultCategories() {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_categories WHERE is_deleted = 0 ORDER BY category ASC');
        $stmt->execute();
        return $stmt;
    }

    public function getResultCategoriesShowInMenu() {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_categories WHERE is_deleted = 0 AND show_in_menu = 1 ORDER BY category ASC');
        $stmt->execute();
        return $stmt;
    }

    public function getResultClaimedCouponsByUserId($lat, $lon, $user_id) {
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_couponsfinder_coupons.category_id, 
                                        tbl_couponsfinder_categories.category,
                                        tbl_couponsfinder_coupons.coupon_id,
                                        tbl_couponsfinder_coupons.lat, 
                                        tbl_couponsfinder_coupons.lon, 
                                        tbl_couponsfinder_coupons.title, 
                                        tbl_couponsfinder_coupons.subtitle, 
                                        tbl_couponsfinder_coupons.description AS description_, 
                                        tbl_couponsfinder_coupons.photo_url, 
                                        tbl_couponsfinder_coupons.expiration_date,
                                        tbl_couponsfinder_coupons.coupon_url,
                                        tbl_couponsfinder_coupons.coupon_code,
                                        tbl_couponsfinder_coupons.created_at,
                                        tbl_couponsfinder_coupons.updated_at, 
                                        tbl_couponsfinder_coupons.is_deleted,
                                        tbl_couponsfinder_coupons.is_featured,    
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_couponsfinder_coupons.lat ) ) * 
                                        cos( radians( tbl_couponsfinder_coupons.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_couponsfinder_coupons.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_couponsfinder_coupons 
                                    INNER JOIN tbl_couponsfinder_categories 
                                    ON tbl_couponsfinder_coupons.category_id = tbl_couponsfinder_categories.category_id 
                                    INNER JOIN tbl_couponsfinder_coupons_claimed 
                                    ON tbl_couponsfinder_coupons.coupon_id = tbl_couponsfinder_coupons_claimed.coupon_id 
                                    WHERE tbl_couponsfinder_coupons.is_deleted = 0 AND tbl_couponsfinder_coupons_claimed.user_id = :user_id 
                                    AND tbl_couponsfinder_coupons_claimed.is_deleted = 0  
                                    ORDER BY created_at DESC');

        $stmt->execute( array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'user_id' => $user_id ));
        return $stmt;
    }

    public function getResultCouponsByCategoryId($lat, $lon, $radius, $category_id) {
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_couponsfinder_coupons.category_id, 
                                        tbl_couponsfinder_categories.category,
                                        tbl_couponsfinder_coupons.coupon_id,
                                        tbl_couponsfinder_coupons.lat, 
                                        tbl_couponsfinder_coupons.lon, 
                                        tbl_couponsfinder_coupons.title, 
                                        tbl_couponsfinder_coupons.subtitle, 
                                        tbl_couponsfinder_coupons.description AS description_, 
                                        tbl_couponsfinder_coupons.photo_url, 
                                        tbl_couponsfinder_coupons.expiration_date,
                                        tbl_couponsfinder_coupons.coupon_url,
                                        tbl_couponsfinder_coupons.coupon_code,
                                        tbl_couponsfinder_coupons.created_at,
                                        tbl_couponsfinder_coupons.updated_at, 
                                        tbl_couponsfinder_coupons.is_deleted,
                                        tbl_couponsfinder_coupons.is_featured,    
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_couponsfinder_coupons.lat ) ) * 
                                        cos( radians( tbl_couponsfinder_coupons.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_couponsfinder_coupons.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_couponsfinder_coupons 
                                    INNER JOIN tbl_couponsfinder_categories 
                                    ON tbl_couponsfinder_coupons.category_id = tbl_couponsfinder_categories.category_id 
                                    WHERE tbl_couponsfinder_coupons.is_deleted = 0 AND tbl_couponsfinder_coupons.category_id = :category_id 
                                    HAVING distance <= :radius 
                                    ORDER BY distance ASC');

        $stmt->execute( array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'radius' => $radius, 'category_id' => $category_id ));
        return $stmt;
    }

    public function getResultCouponsFeatured($lat, $lon, $radius) {
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_couponsfinder_coupons.category_id, 
                                        tbl_couponsfinder_categories.category,

                                        tbl_couponsfinder_coupons.coupon_id,
                                        tbl_couponsfinder_coupons.lat, 
                                        tbl_couponsfinder_coupons.lon, 
                                        tbl_couponsfinder_coupons.title, 
                                        tbl_couponsfinder_coupons.subtitle, 
                                        tbl_couponsfinder_coupons.description AS description_, 
                                        tbl_couponsfinder_coupons.photo_url, 
                                        tbl_couponsfinder_coupons.expiration_date,
                                        tbl_couponsfinder_coupons.coupon_url,
                                        tbl_couponsfinder_coupons.coupon_code,
                                        tbl_couponsfinder_coupons.created_at,
                                        tbl_couponsfinder_coupons.updated_at, 
                                        tbl_couponsfinder_coupons.is_deleted,
                                        tbl_couponsfinder_coupons.is_featured,    
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_couponsfinder_coupons.lat ) ) * 
                                        cos( radians( tbl_couponsfinder_coupons.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_couponsfinder_coupons.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_couponsfinder_coupons 
                                    INNER JOIN tbl_couponsfinder_categories 
                                    ON tbl_couponsfinder_coupons.category_id = tbl_couponsfinder_categories.category_id 
                                    WHERE tbl_couponsfinder_coupons.is_deleted = 0 AND tbl_couponsfinder_coupons.is_featured = 1
                                    HAVING distance <= :radius 
                                    ORDER BY distance ASC');

        $stmt->execute( array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'radius' => $radius ));
        return $stmt;
    }

    public function getResultCouponsRadius($lat, $lon, $radius) {
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_couponsfinder_coupons.category_id, 
                                        tbl_couponsfinder_categories.category,
                                        tbl_couponsfinder_coupons.coupon_id,
                                        tbl_couponsfinder_coupons.lat, 
                                        tbl_couponsfinder_coupons.lon, 
                                        tbl_couponsfinder_coupons.title, 
                                        tbl_couponsfinder_coupons.subtitle, 
                                        tbl_couponsfinder_coupons.description AS description_, 
                                        tbl_couponsfinder_coupons.photo_url, 
                                        tbl_couponsfinder_coupons.expiration_date,
                                        tbl_couponsfinder_coupons.coupon_url,
                                        tbl_couponsfinder_coupons.coupon_code,
                                        tbl_couponsfinder_coupons.created_at,
                                        tbl_couponsfinder_coupons.updated_at, 
                                        tbl_couponsfinder_coupons.is_deleted,
                                        tbl_couponsfinder_coupons.is_featured,    
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_couponsfinder_coupons.lat ) ) * 
                                        cos( radians( tbl_couponsfinder_coupons.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_couponsfinder_coupons.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_couponsfinder_coupons 
                                    INNER JOIN tbl_couponsfinder_categories 
                                    ON tbl_couponsfinder_coupons.category_id = tbl_couponsfinder_categories.category_id 
                                    WHERE tbl_couponsfinder_coupons.is_deleted = 0 
                                    HAVING distance <= :radius 
                                    ORDER BY distance ASC');

        $stmt->execute( array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'radius' => $radius ));
        return $stmt;
    }
    
    public function getMaxDistanceFound($lat, $lon) {
        $stmt = $this->pdo->prepare('SELECT COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_couponsfinder_coupons.lat ) ) * 
                                            cos( radians( tbl_couponsfinder_coupons.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                            sin( radians( tbl_couponsfinder_coupons.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_couponsfinder_coupons 
                                    WHERE is_deleted = 0 
                                    ORDER BY distance DESC
                                    LIMIT 0, 1');

        $stmt->execute( array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat) );
        foreach ($stmt as $row) {
            return $row['distance'];
        }
        return 0;
    }

    public function getMaxDistanceFoundDefault($lat, $lon, $default_count_to_find_distance) 
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_couponsfinder_coupons.lat ) ) * 
                                            cos( radians( tbl_couponsfinder_coupons.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                            sin( radians( tbl_couponsfinder_coupons.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_couponsfinder_coupons 
                                    WHERE is_deleted = 0  
                                    ORDER BY distance ASC
                                    LIMIT 0, :default_count_to_find_distance');

        $stmt->execute( array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'default_count_to_find_distance' => $default_count_to_find_distance) );
        
        $distance = 0;
        foreach ($stmt as $row) {
            $distance = $row['distance'];
        }
        return $distance;
    }

    public function searchCouponsResults($lat, $lon, $radius, $category_id, $keywords) {

        $sql = 'SELECT 
                    tbl_couponsfinder_coupons.category_id, 
                    tbl_couponsfinder_categories.category,
                    tbl_couponsfinder_coupons.coupon_id,
                    tbl_couponsfinder_coupons.lat, 
                    tbl_couponsfinder_coupons.lon, 
                    tbl_couponsfinder_coupons.title, 
                    tbl_couponsfinder_coupons.subtitle, 
                    tbl_couponsfinder_coupons.description AS description_, 
                    tbl_couponsfinder_coupons.photo_url, 
                    tbl_couponsfinder_coupons.expiration_date,
                    tbl_couponsfinder_coupons.coupon_url,
                    tbl_couponsfinder_coupons.coupon_code,
                    tbl_couponsfinder_coupons.created_at,
                    tbl_couponsfinder_coupons.updated_at, 
                    tbl_couponsfinder_coupons.is_deleted,
                    tbl_couponsfinder_coupons.is_featured,    
                    COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_couponsfinder_coupons.lat ) ) * 
                    cos( radians( tbl_couponsfinder_coupons.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                    sin( radians( tbl_couponsfinder_coupons.lat ) ) ) ), 0) AS distance 
                        
                FROM tbl_couponsfinder_coupons 
                INNER JOIN tbl_couponsfinder_categories 
                ON tbl_couponsfinder_coupons.category_id = tbl_couponsfinder_categories.category_id 
                WHERE tbl_couponsfinder_coupons.is_deleted = 0 ';


        $ptr = array();
        $ptr['lat_params'] = $lat;
        $ptr['lon_params'] = $lon;
        $ptr['lat_params1'] = $lat;
        $ptr['radius'] = $radius;

        if(strlen($keywords) > 0) {
            $sql .= 'AND (title LIKE :keywords1 OR :keywords2 LIKE title OR tbl_couponsfinder_coupons.description LIKE :keywords3 OR :keywords4 LIKE tbl_couponsfinder_coupons.description OR subtitle LIKE :keywords5 OR :keywords6 LIKE subtitle OR coupon_code LIKE :keywords7 OR :keywords8 LIKE coupon_code) ';
            $ptr['keywords1'] = '%'.$keywords.'%';
            $ptr['keywords2'] = '%'.$keywords.'%';
            $ptr['keywords3'] = '%'.$keywords.'%';
            $ptr['keywords4'] = '%'.$keywords.'%';
            $ptr['keywords5'] = '%'.$keywords.'%';
            $ptr['keywords6'] = '%'.$keywords.'%';
            $ptr['keywords7'] = '%'.$keywords.'%';
            $ptr['keywords8'] = '%'.$keywords.'%';
        }

        if($category_id > 0) {
            $sql .= 'AND tbl_couponsfinder_coupons.category_id = :category_id ';
            $ptr['category_id'] = $category_id;
        }

        $sql .= 'HAVING distance <= :radius  ORDER BY distance ASC ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute( $ptr );
        return $stmt;
    }

    public function getCouponClaimCount($coupon_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_coupons_claimed WHERE is_deleted = 0 AND coupon_id = :coupon_id');
        $stmt->execute( array('coupon_id' => $coupon_id) );
        return $stmt->rowCount();
    }

    public function getResultCouponsDefaultDistance($lat, $lon, $default_count_to_find_distance) {
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_couponsfinder_coupons.category_id, 
                                        tbl_couponsfinder_categories.category,
                                        tbl_couponsfinder_coupons.coupon_id,
                                        tbl_couponsfinder_coupons.lat, 
                                        tbl_couponsfinder_coupons.lon, 
                                        tbl_couponsfinder_coupons.title, 
                                        tbl_couponsfinder_coupons.subtitle, 
                                        tbl_couponsfinder_coupons.description AS description_, 
                                        tbl_couponsfinder_coupons.photo_url, 
                                        tbl_couponsfinder_coupons.expiration_date,
                                        tbl_couponsfinder_coupons.coupon_url,
                                        tbl_couponsfinder_coupons.coupon_code,
                                        tbl_couponsfinder_coupons.created_at,
                                        tbl_couponsfinder_coupons.updated_at, 
                                        tbl_couponsfinder_coupons.is_deleted,
                                        tbl_couponsfinder_coupons.is_featured,    
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_couponsfinder_coupons.lat ) ) * 
                                        cos( radians( tbl_couponsfinder_coupons.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_couponsfinder_coupons.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_couponsfinder_coupons 
                                    INNER JOIN tbl_couponsfinder_categories 
                                    ON tbl_couponsfinder_coupons.category_id = tbl_couponsfinder_categories.category_id 
                                    WHERE tbl_couponsfinder_coupons.is_deleted = 0 
                                    ORDER BY distance ASC 
                                    LIMIT 0, :default_count_to_find_distance');

        $stmt->execute( array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'default_count_to_find_distance' => $default_count_to_find_distance ));
        return $stmt;
    }

}
 
?>