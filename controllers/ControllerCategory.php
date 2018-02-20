<?php
 
class ControllerCategory
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
 
    public function updateCategory($itm) 
    {
        
        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_categories 
                                        SET category = :category,
                                            updated_at = :updated_at,
                                            show_in_menu = :show_in_menu

                                        WHERE category_id = :category_id');

        $result = $stmt->execute(
                            array('category' => $itm->category, 
                                    'updated_at' => $itm->updated_at,
                                    'show_in_menu' => $itm->show_in_menu,
                                    'category_id' => $itm->category_id) );
        
        return $result ? true : false;
    }

    public function deleteCategory($category_id, $is_deleted) 
    {

        $stmt = $this->pdo->prepare('UPDATE tbl_couponsfinder_categories 
                                        SET is_deleted = :is_deleted
                                        WHERE category_id = :category_id');


        $result = $stmt->execute(
                            array('is_deleted' => $is_deleted, 
                                    'category_id' => $category_id) );
        
        return $result ? true : false;
    }

    public function insertCategory($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_couponsfinder_categories( 
                                            category,
                                            created_at,
                                            updated_at,
                                            show_in_menu,
                                            is_deleted) 
                                        VALUES( 
                                            :category,
                                            :created_at,
                                            :updated_at,
                                            :show_in_menu,
                                            0 )');
        
        $result = $stmt->execute(
                            array('category' => $itm->category,
                                    'created_at' => $itm->created_at,
                                    'updated_at' => $itm->updated_at,
                                    'show_in_menu' => $itm->show_in_menu ) );
        
        return $result ? true : false;
    }
 
    public function getCategories() {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_couponsfinder_categories 
                                 WHERE is_deleted = 0 ORDER BY category ASC');

        $stmt->execute();

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatCategory($row);
            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function getCategoriesBySearching($search) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_couponsfinder_categories 
                                        WHERE is_deleted = 0 AND category LIKE :search ORDER BY category ASC');

        $stmt->execute( array('search' => '%'.$search.'%'));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = $this->formatCategory($row);
            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }


    public function getCategoryByCategoryId($category_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_categories WHERE category_id = :category_id');
        $stmt->execute( array('category_id' => $category_id));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = $this->formatCategory($row);
            return $itm;
        }
        return null;
    }

    public function formatCategory($row) {
        $itm = new Category();
        $itm->category_id = $row['category_id'];
        $itm->category = $row['category'];
        $itm->created_at = $row['created_at'];
        $itm->updated_at = $row['updated_at'];
        $itm->is_deleted = $row['is_deleted'];
        $itm->show_in_menu = $row['show_in_menu'];
        return $itm;
    }

    public function getCategoriesAtRange($begin, $end) {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_couponsfinder_categories WHERE is_deleted = 0 ORDER BY category ASC LIMIT :beg, :end');
        $stmt->execute( array('beg' => $begin, 'end' => $end) );
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            $array[$ind] = $this->formatCategory($row);
            $ind += 1;
        } 
        return $array;
    }
}
 
?>