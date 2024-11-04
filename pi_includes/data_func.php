<?php
    
class DB_Class{
    #CPANEL
    // private $localhost = "marigold";
    // private $database = "sitsngco_amugrv";
    // private $username = "sitsngco_bash";
    // private $password = "bash2022*#*#";

    private $localhost = "localhost";
    private $database = "amu_v12";
    private $username = "root";
    private $password = "";

    function __construct(){
        $this->dbConn = new PDO("mysql:host=$this->localhost;dbname=$this->database",$this->username,$this->password);
        $this->dbConn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
        date_default_timezone_set('Africa/Lagos');
    }
    
    // CASHIER NAME
    function casherName($user){
        $bind = $this->dbConn->prepare("SELECT * FROM tbl_users WHERE id=?");
        $bind->bindParam(1,$user);
        $bind->execute();
        
        if($bind->rowCount()>0){
            $result = $bind->fetch();
            return $result;
            }
    }
   
     #DELETE PRODUCT
    function deleteProduct($delete_key)
    {
        $delProduct = $this->bind = $this->dbConn->prepare("DELETE FROM pi_tbl_products WHERE id=?");
        $this->bind->bindParam(1,$delete_key);
        $this->bind->execute();

        if ($delProduct->rowCount() > 0) 
        {
            return TRUE;
        }

    }

    function addNewProduct($category,$product,$price){
        $addProduct = $this->dbConn->prepare("INSERT INTO pi_tbl_products (category,name,unit_price) VALUES(?,?,?)");
        $addProduct->bindParam(1,$category);
        $addProduct->bindParam(2,$product);
        $addProduct->bindParam(3,$price);
        $addProduct->execute();

        if ($addProduct->rowCount() > 0) {
            return TRUE;
        }

    }

    function addNewStock($product_id,$stock_in){
        $addProduct = $this->dbConn->prepare("INSERT INTO pi_tbl_stock (product_id,items_avail) VALUES(?,?)");
        $addProduct->bindParam(1,$product_id);
        $addProduct->bindParam(2,$stock_in);
        $addProduct->execute();

        if ($addProduct->rowCount() > 0) {
            return TRUE;
        }

    }
    // SAVE TRANSACTION
    function saveTransaction($user_id,$invoice_no,$paymode,$total_amount){
        $addProduct = $this->dbConn->prepare("INSERT INTO pi_tbl_transactions (user_id,invoice_no,paymode,total_amount) VALUES(?,?,?,?)");
        $addProduct->bindParam(1,$user_id);
        $addProduct->bindParam(2,$invoice_no);
        $addProduct->bindParam(3,$paymode);
        $addProduct->bindParam(4,$total_amount);
        $addProduct->execute();

        if ($addProduct->rowCount() > 0) {
            return TRUE;
        }

    }
    // VIEW TRANSACTION DATA fetchTransact
    function fetchTransact($ivc_no){
        $getInvoice = $this->dbConn->prepare("SELECT * FROM pi_tbl_transactions WHERE invoice_no=?");
        $getInvoice->bindParam(1,$ivc_no);
        $getInvoice->execute();
        
        if($getInvoice->rowCount()>0){
            $result = $getInvoice->fetch();
            return $result;
            }
    }

    // INSERT TO CART
    function insertToCart($product_id,$invoice_no,$product_cat,$product_name,$price,$qty,$amount,$status){
        $addProduct = $this->dbConn->prepare("INSERT INTO pi_tbl_carts (product_id,invoice_no,product_cat,product_name,price,qty,amount,status) VALUES(?,?,?,?,?,?,?,?)");
        $addProduct->bindParam(1,$product_id);
        $addProduct->bindParam(2,$invoice_no);
        $addProduct->bindParam(3,$product_cat);
        $addProduct->bindParam(4,$product_name);
        $addProduct->bindParam(5,$price);
        $addProduct->bindParam(6,$qty);
        $addProduct->bindParam(7,$amount);
        $addProduct->bindParam(8,$status);
        $addProduct->execute();

        if ($addProduct->rowCount() > 0) {
            return TRUE;
        }

    }

    #PRODUCT LIST
    function product_list(){
        $data = $this->dbConn->query("SELECT * FROM pi_tbl_products ORDER BY id DESC");
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    #STOCK LIST
    function stock_list(){
        $getStock = $this->dbConn->query("SELECT * FROM pi_tbl_products ORDER BY id DESC");
        if($getStock->rowCount()>0){
            $result = $getStock->fetchAll(PDO::FETCH_ASSOC);
            return $result;
            }
    }

    // INVOICE QUERY
    function invoiceInfo($invoice){
        $getInvoice = $this->dbConn->prepare("SELECT * FROM pi_tbl_carts WHERE invoice_no=?");
        $getInvoice->bindParam(1,$invoice);
        $getInvoice->execute();
        
        if($getInvoice->rowCount()>0){
            $result = $getInvoice->fetchAll();
            return $result;
            }
    }

    function totalAmount($invoice){
        $getInvoice = $this->dbConn->prepare("SELECT SUM(amount) AS allMoney FROM pi_tbl_carts WHERE invoice_no=?");
        $getInvoice->bindParam(1,$invoice);
        $getInvoice->execute();
        
        if($getInvoice->rowCount()>0){
            $result = $getInvoice->fetch();
            return $result;
            }
    }
    #GENERATE AVAILABLE STOCK
    function totalStock($id){
        $tStock = $this->dbConn->prepare("SELECT SUM(items_avail) AS total_stock FROM pi_tbl_stock WHERE product_id=?");
        $tStock->bindParam(1,$id);
        $tStock->execute();
        
        if($tStock->rowCount()>0){
            $result = $tStock->fetch();
            return $result;
            }
    }

    function totalCart($id){
        $tCart = $this->dbConn->prepare("SELECT SUM(qty) AS total_cart FROM pi_tbl_carts WHERE product_id=? AND status='Approved'");
        $tCart->bindParam(1,$id);
        $tCart->execute();
        
        if($tCart->rowCount()>0){
            $result = $tCart->fetch();
            return $result;
            }
    }

    // CART DATA
    function cartData($id){
        $cartD = $this->dbConn->prepare("SELECT * FROM pi_tbl_products WHERE id=?");
        $cartD->bindParam(1,$id);
        $cartD->execute();
        
        if($cartD->rowCount()>0){
            $result = $cartD->fetch();
            return $result;
            }
    }

    #PRODUCT DROPDOWN
    function product_dropdown(){
        $getProduct = $this->dbConn->query("SELECT * FROM pi_tbl_products GROUP BY category");
        
        if($getProduct->rowCount()>0){
            $result = $getProduct->fetchAll(PDO::FETCH_ASSOC);
            return $result;
            }
        
    }
    
    function ajax_product($productName){

        if(!empty($productName)){
            $getCategories = $this->dbConn->prepare("SELECT * FROM pi_tbl_products WHERE category=?");
            $getCategories->bindParam(1,$productName);
            $getCategories->execute();

            if($getCategories->rowCount()>0){
                $res = $getCategories->fetchAll();
                return $res;
            }
        }
    }
    // REMOVE ITEM FROM CART
    function removeCart($delete_key){
        $getCategories = $this->dbConn->prepare("DELETE FROM pi_tbl_carts WHERE id=?");
            $getCategories->bindParam(1,$delete_key);
            $getCategories->execute();

            if($getCategories->rowCount()>0){
            return TRUE;
            }

    }
    #ADMIN SALES LIST
    function adminSalesView(){
        $viewSales = $this->dbConn->query("SELECT * FROM pi_tbl_transactions ORDER BY id DESC");
        if($viewSales->rowCount()>0){
            $result = $viewSales->fetchAll(PDO::FETCH_ASSOC);
            return $result;
            }
    }
    #USER SALES LIST
    function userSalesView($saler_id){
        $viewSales = $this->dbConn->prepare("SELECT * FROM pi_tbl_transactions WHERE saler_id=? AND date=CURDATE() ORDER BY id DESC");
        $viewSales->bindParam(1,$saler_id);
        $viewSales->execute();
        
        if($viewSales->rowCount()>0){
            $result = $viewSales->fetchAll();
            return $result;
            }
    }
     
    #UPDATE PRICE
    function updatePrice($product_id, $price){
        $updatePriceQuery = $this->dbConn->prepare("UPDATE pi_tbl_products SET unit_price=? WHERE id=?");
        $updatePriceQuery->bindParam(1,$price);
        $updatePriceQuery->bindParam(2,$product_id);
        $updatePriceQuery->execute();
        
        if($updatePriceQuery->rowCount()>0){
            return true;
        }
    }

}


?>