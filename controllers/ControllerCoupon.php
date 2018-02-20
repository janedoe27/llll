<?php
 
class ControllerCoupon
{
 
    private $db;
    private $db_path;
    private $pdo;
    function __construct() 
    {
        // connecting to database
        $this->db = new DB_Connect();
        $this->pdo = $this->db->connect();
    }
 
    function __destruct() { }

    public function insertCoupon($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_couponsfinder_coupons( 
                                        lat,
                                        lon, 
                                        title, 
                                        subtitle, 
                                        description, 
                                        photo_url, 
                                        expiration_date, 
                                        coupon_url, 
                                        coupon_code, 
                                        category_id, 
                                        is_featured, 
                                        created_at,
                                        updated_at,
                                        is_deleted ) 
                                    VALUES( 
                                        :lat,
                                        :lon, 
                                        :title, 
                                        :subtitle, 
                                        :description, 
                                        :photo_url, 
                                        :expiration_date, 
                                        :coupon_url, 
                                        :coupon_code, 
                                        :category_id, 
                                        :is_featured, 
                                        :created_at,
                                        :updated_at,
                                        :is_deleted )');
        
        $result = $stmt->execute(
                            array(  'lat' => $itm->lat, 
                                    'lon' => $itm->lon,
                                    'title' => $itm->title,
                                    'subtitle' => $itm->subtitle,
                                    'description' => $itm->description,
                                    'photo_url' => $itm->photo_url,
                                    'expiration_date' => $itm->expiration_date,
                                    'coupon_url' => $itm->coupon_url,
                                    'coupon_code' => $itm->coupon_code,
                                    'category_id' => $itm->category_id,
                                    'is_featured' => $itm->is_featured,
                                    'created_at' => $itm->created_at,
                                    'updated_at' => $itm->updated_at,
                                    'is_deleted' => $itm->is_deleted ) );
        
        return $result ? true : false;
    }

    public function updateCoupon($itm) {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_coupons 
                                        SET 
                                            lat = :lat,
                                            lon = :lon,
                                            title = :title,
                                            subtitle = :subtitle,
                                            description = :description,
                                            photo_url = :photo_url,
                                            expiration_date = :expiration_date,
                                            coupon_url = :coupon_url,
                                            coupon_code = :coupon_code,
                                            category_id = :category_id,
                                            is_featured = :is_featured,
                                            updated_at = :updated_at 
                                        WHERE coupon_id = :coupon_id');

        $result = $stmt->execute(
                            array('lat' => $itm->lat, 
                                    'lon' => $itm->lon,
                                    'title' => $itm->title,
                                    'subtitle' => $itm->subtitle,
                                    'description' => $itm->description,
                                    'photo_url' => $itm->photo_url,
                                    'expiration_date' => $itm->expiration_date,
                                    'coupon_url' => $itm->coupon_url,
                                    'coupon_code' => $itm->coupon_code,
                                    'category_id' => $itm->category_id,
                                    'is_featured' => $itm->is_featured,
                                    'updated_at' => $itm->updated_at,
                                    'coupon_id' => $itm->coupon_id) );
        
        return $result ? true : false;
    }

    public function getCouponByCouponId($coupon_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_coupons WHERE coupon_id = :coupon_id');

        $result = $stmt->execute( array('coupon_id' => $coupon_id) );
        foreach ($stmt as $row) {
            // do something with $row
            $itm = $this->formatCoupon($row);
            return $itm;
        }
        return null;
    }

    public function formatCoupon($row) {
        $itm = new Coupon();
        $itm->lat = $row['lat'];
        $itm->lon = $row['lon'];
        $itm->title = $row['title'];
        $itm->subtitle = $row['subtitle'];
        $itm->description = $row['description'];
        $itm->photo_url = $row['photo_url'];
        $itm->expiration_date = $row['expiration_date'];
        $itm->coupon_url = $row['coupon_url'];
        $itm->coupon_code = $row['coupon_code'];

        $itm->is_featured = $row['is_featured'];

        $itm->created_at = $row['created_at'];
        $itm->updated_at = $row['updated_at'];
        $itm->is_deleted = $row['is_deleted'];
        $itm->coupon_id = $row['coupon_id'];
        $itm->category_id = $row['category_id'];
        return $itm;
    }

    public function getCoupons() {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_coupons WHERE is_deleted = 0 ORDER BY title ASC');

        $result = $stmt->execute();
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $array[$ind] = $this->formatCoupon($row);
            $ind += 1;
        }
        return $array;
    }

    public function getCouponsFeatured() {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_coupons WHERE is_deleted = 0 AND is_featured = 1');
        $result = $stmt->execute();
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $array[$ind] = $this->formatCoupon($row);
            $ind += 1;
        }
        return $array;
    }

    public function deleteCoupon($coupon_id, $is_deleted) {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_coupons SET is_deleted = :is_deleted WHERE coupon_id = :coupon_id');
        $result = $stmt->execute( array('coupon_id' => $coupon_id, 'is_deleted' => $is_deleted) );
        return $result ? true : false;
    }

    public function updateCouponFeatured($itm) {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_coupons SET is_featured = :is_featured WHERE coupon_id = :coupon_id');
        $result = $stmt->execute( array('coupon_id' => $itm->coupon_id, 'is_featured' => $itm->is_featured) );
        return $result ? true : false;
    }

    public function getCouponsBySearching($search) {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_coupons WHERE is_deleted = 0 AND title LIKE :title ORDER BY title ASC');
        $stmt->execute( array('search' => '%'.$search.'%'));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            $array[$ind] = $this->formatCoupon($row);
            $ind += 1;
        } 
        return $array;
    }

    public function getCouponsAtRange($begin, $end) {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_coupons WHERE is_deleted = 0 ORDER BY title ASC LIMIT :beg, :end');
        $stmt->execute( array('beg' => $begin, 'end' => $end) );
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            $array[$ind] = $this->formatCoupon($row);
            $ind += 1;
        } 
        return $array;
    }
}
 
?>