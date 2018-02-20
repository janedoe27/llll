<?php
 
class ControllerUser
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

    public function registerUser($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_couponsfinder_users( 
                                        full_name,
                                        login_hash, 
                                        facebook_id,
                                        twitter_id,
                                        google_id,
                                        thumb_url,
                                        email,
                                        username,
                                        password,
                                        created_at,
                                        updated_at ) 
                                    VALUES( 
                                        :full_name,
                                        :login_hash,
                                        :facebook_id,
                                        :twitter_id,
                                        :google_id,
                                        :thumb_url,
                                        :email,
                                        :username,
                                        :password,
                                        :created_at,
                                        :updated_at )');
        
        $result = $stmt->execute(
                            array('full_name' => $itm->full_name, 
                                    'login_hash' => $this->hashSSHA($itm->login_hash),
                                    'facebook_id' => $itm->facebook_id,
                                    'twitter_id' => $itm->twitter_id,
                                    'google_id' => $itm->google_id,
                                    'thumb_url' => $itm->thumb_url,
                                    'email' => $itm->email,
                                    'username' => $itm->username,
                                    'password' => $itm->password,
                                    'created_at' => $itm->created_at,
                                    'updated_at' => $itm->updated_at ) );
        
        return $result ? true : false;
    }


    public function updateUser($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_users

                                    SET 
                                        full_name = :full_name,
                                        login_hash = :login_hash,
                                        facebook_id = :facebook_id,
                                        twitter_id = :twitter_id,
                                        google_id = :google_id,
                                        email = :email,
                                        username = :username,
                                        password = :password   
                                    
                                    WHERE user_id = :user_id');
        
        $result = $stmt->execute(
                            array('full_name' => $itm->full_name, 
                                    'login_hash' => $itm->login_hash,
                                    'facebook_id' => $itm->facebook_id,
                                    'twitter_id' => $itm->twitter_id,
                                    'google_id' => $itm->google_id,
                                    'email' => $itm->email,
                                    'username' => $itm->username,
                                    'password' => $itm->password,
                                    'user_id' => $itm->user_id ) );
        
        return $result ? true : false;
    }

    public function loginGooglePlus($google_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_users 
                                 WHERE google_id = :google_id AND deny_access = 0');

        $result = $stmt->execute( array('google_id' => $google_id ) );
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatUser($row);
            return $itm;
        }
        return null;
    }

    public function loginFacebook($facebook_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_users 
                                 WHERE facebook_id = :facebook_id AND deny_access = 0');

        $result = $stmt->execute( array('facebook_id' => $facebook_id ) );
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatUser($row);
            return $itm;
        }
        return null;
    }


    public function loginTwitter($twitter_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_users 
                                 WHERE twitter_id = :twitter_id AND deny_access = 0');

        $result = $stmt->execute( array('twitter_id' => $twitter_id ) );
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatUser($row);
            return $itm;
        }
        return null;
    }

    public function getUserByUsernameAndPassword($username, $password) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_users 
                                 WHERE username = :username AND password = :password AND deny_access = 0');

        $result = $stmt->execute( array('username' => $username, 'password' => $password ) );
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatUser($row);
            return $itm;
        }
        return null;
    }

    public function getUserByUsername($username) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_users 
                                 WHERE username = :username AND deny_access = 0');

        $result = $stmt->execute( array('username' => $username ) );
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatUser($row);
            return $itm;
        }
        return null;
    }

    public function getUserByUserId($user_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_users 
                                 WHERE user_id = :user_id');

        $result = $stmt->execute( array('user_id' => $user_id ) );
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatUser($row);
            return $itm;
        }
        return null;
    }

    public function updateUserHash($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_users 
                                        SET login_hash = :login_hash 
                                        WHERE user_id = :user_id');

        $result = $stmt->execute(
                            array('login_hash' => $itm->login_hash, 
                                    'user_id' => $itm->user_id) );
        
        return $result ? true : false;
    }

    public function isUserExist($username) 
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_users 
                                        WHERE username = :username');

        $result = $stmt->execute(
                            array('username' => $username) );
        
        foreach ($stmt as $row) 
        {
            return true;
        }
        return false;
    }

    public function isUserIdExistAndHash($user_id, $login_hash) 
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_users 
                                        WHERE user_id = :user_id AND login_hash = :login_hash');

        $result = $stmt->execute(
                            array('user_id' => $user_id,
                                    'login_hash' => $login_hash) );
        
        foreach ($stmt as $row) 
        {
            return true;
        }
        return false;
    }

    public function isEmailExist($email) 
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_users 
                                        WHERE email = :email');

        $result = $stmt->execute(
                            array('email' => $email) );
        
        foreach ($stmt as $row) 
        {
            return true;
        }
        return false;
    }

    public function isGoogleIdExist($google_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_users 
                                        WHERE google_id = :google_id');

        $result = $stmt->execute( array('google_id' => $google_id) );
        foreach ($stmt as $row) 
        {
            return true;
        }
        return false;
    }

    public function isFacebookIdExist($facebook_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_users 
                                        WHERE facebook_id = :facebook_id');

        $result = $stmt->execute( array('facebook_id' => $facebook_id) );
        foreach ($stmt as $row) 
        {
            return true;
        }
        return false;
    }

    public function isTwitterIdExist($twitter_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_users 
                                        WHERE twitter_id = :twitter_id');

        $result = $stmt->execute( array('twitter_id' => $twitter_id) );
        foreach ($stmt as $row) 
        {
            return true;
        }
        return false;
    }

     /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        // $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $encrypted;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }

    public function getUsers() 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_users ORDER BY full_name ASC');

        $result = $stmt->execute( );
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatUser($row);
            $array[$ind] = $itm;
            $ind++;
        }

        return $array;
    }

    public function updateUserFullNameAndPasswordThumbUrl($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_users
                                    SET 
                                        full_name = :full_name,
                                        password = :password,
                                        thumb_url = :thumb_url  
                                    
                                    WHERE user_id = :user_id');
        
        $result = $stmt->execute(
                            array('full_name' => $itm->full_name,
                                    'user_id' => $itm->user_id,
                                    'thumb_url' => $itm->thumb_url,
                                    'password' => $itm->password ) );
        
        return $result ? true : false;
    }

    public function updateUserFullName($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_users
                                    SET 
                                        full_name = :full_name 
                                    
                                    WHERE user_id = :user_id');
        
        $result = $stmt->execute(
                            array('full_name' => $itm->full_name,
                                    'user_id' => $itm->user_id ) );
        
        return $result ? true : false;
    }

    public function updateUserAccess($user_id, $deny_access) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_users 
                                        SET deny_access = :deny_access 
                                        WHERE user_id = :user_id');

        $result = $stmt->execute(
                            array('deny_access' => $deny_access, 
                                    'user_id' => $user_id) );
        
        return $result ? true : false;
    }

    public function getUsersBySearching($search) 
    {
        $stmt = $this->pdo->prepare("SELECT * 
                                        FROM tbl_couponsfinder_users 
                                        WHERE deny_access = 0 AND full_name LIKE :search ORDER BY full_name ASC");

        $stmt->execute( array('search' => '%'.$search.'%'));
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatUser($row);
            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function updateUserPhoto($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_users
                                        SET 
                                            photo_url = :photo_url, 
                                            thumb_url = :thumb_url

                                            WHERE user_id = :user_id');

        $result = $stmt->execute(
                            array('photo_url' => $itm->photo_url,
                                    'thumb_url' => $itm->thumb_url,
                                    'user_id' => $itm->user_id) );
        
        return $result ? true : false;
    }

    public function formatUser($row) {
        $itm = new User();
        $itm->user_id = $row['user_id'];
        $itm->login_hash = $row['login_hash'];
        $itm->facebook_id = $row['facebook_id'];
        $itm->twitter_id = $row['twitter_id'];
        $itm->google_id = $row['google_id'];
        $itm->email = $row['email'];
        $itm->full_name = $row['full_name'];
        $itm->deny_access = $row['deny_access'];
        $itm->thumb_url = $row['thumb_url'];
        $itm->username = $row['username'];
        $itm->password = $row['password'];
        return $itm;
    }

    public function getUsersAtRange($begin, $end) {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_users WHERE is_deleted = 0 ORDER BY full_name ASC LIMIT :beg, :end');
        $stmt->execute( array('beg' => $begin, 'end' => $end) );
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            $array[$ind] = $this->formatUser($row);
            $ind += 1;
        } 
        return $array;
    }
}
 
?>