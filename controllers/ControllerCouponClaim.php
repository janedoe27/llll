<?php
 
class ControllerCouponClaim
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

    public function insertCouponClaim($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_couponsfinder_coupons_claimed( 
                                        user_id,
                                        coupon_id, 
                                        created_at,
                                        updated_at,
                                        is_deleted ) 
                                    VALUES( 
                                        :user_id,
                                        :coupon_id, 
                                        :created_at,
                                        :updated_at,
                                        :is_deleted )');
        
        $result = $stmt->execute(
                            array('user_id' => $itm->user_id, 
                                    'coupon_id' => $itm->coupon_id,
                                    'is_deleted' => $itm->is_deleted,
                                    'created_at' => $itm->created_at,
                                    'updated_at' => $itm->updated_at ) );
        
        return $result ? true : false;
    }

    public function getCouponClaim($coupon_id, $user_id) {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_coupons_claimed 
                                 WHERE coupon_id = :coupon_id AND user_id = :user_id AND is_deleted = 0');

        $result = $stmt->execute( array('coupon_id' => $coupon_id, 'user_id' => $user_id ) );
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatCouponClaim($row);
            return $itm;
        }
        return null;
    }

    public function formatCouponClaim($row) {
        $itm = new CouponClaim();
        $itm->user_id = $row['user_id'];
        $itm->coupon_id = $row['coupon_id'];
        $itm->claim_id = $row['claim_id'];
        $itm->created_at = $row['created_at'];
        $itm->updated_at = $row['updated_at'];
        $itm->is_deleted = $row['is_deleted'];
        return $itm;
    }

    public function getCouponClaimByCouponId($coupon_id) {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_coupons_claimed 
                                 WHERE coupon_id = :coupon_id AND is_deleted = 0');

        $stmt->execute( array('coupon_id' => $coupon_id) );
        return $stmt->rowCOunt();
    }
}
 
?>