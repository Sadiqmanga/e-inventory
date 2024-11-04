<?php
    
class DB_Func{
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
    

    #User Login Function
    function login_func($user,$pass){
        $bind = $this->dbConn->prepare("SELECT * FROM tbl_login WHERE tailor_user=? AND tailor_pass=? ");
        $bind->bindParam(1,$user);
        $bind->bindParam(2,$pass);
        $bind->execute();
        
        if ($bind->fetchColumn() > 0){
            return TRUE;
        }
        else{
            return FALSE;
        }
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
    #Admin Login function
    function admin_func($user,$pass){
        $bind = $this->dbConn->prepare("SELECT * FROM admintbl_login WHERE admin_user=? AND admin_pass=? ");
        $bind->bindParam(1,$user);
        $bind->bindParam(2,$pass);
        $bind->execute();
        
        if ($bind->fetchColumn() > 0){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
     #ADD NEW SYSTEM USER
    function addUser($name,$user,$pass,$mail){
        $bind = $this->dbConn->prepare("INSERT INTO tbl_login (full_name,tailor_user,tailor_pass,tailor_email) VALUES(?,?,?,?)");
        $bind->bindParam(1,$name);
        $bind->bindParam(2,$user);
        $bind->bindParam(3,$pass);
        $bind->bindParam(4,$mail);
        $bind->execute();

        if ($bind->rowCount() > 0) {
            return TRUE;
        }

    }
    #VIEW SYSTEM USERS
    function users_list(){
        $data = $this->dbConn->query("SELECT * FROM tbl_login ORDER BY sn DESC");
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    #DELETE SYSTEM USER
    function deleteUser($delete_key){
        $data = $this->bind = $this->dbConn->prepare("DELETE FROM tbl_login WHERE $delete_key IN(sn)");
        $this->bind->bindParam(1,$delete_key);
        $this->bind->execute();

        if ($data->rowCount() > 0) {
            return TRUE;
        }

    }
     #DELETE PRODUCT
    function deleteProduct($delete_key)
    {
        $delProduct = $this->bind = $this->dbConn->prepare("DELETE FROM tbl_products WHERE id=?");
        $this->bind->bindParam(1,$delete_key);
        $this->bind->execute();

        if ($delProduct->rowCount() > 0) 
        {
            return TRUE;
        }

    }

    function addNewProduct($category,$product,$price){
        $addProduct = $this->dbConn->prepare("INSERT INTO tbl_products (category,name,unit_price) VALUES(?,?,?)");
        $addProduct->bindParam(1,$category);
        $addProduct->bindParam(2,$product);
        $addProduct->bindParam(3,$price);
        $addProduct->execute();

        if ($addProduct->rowCount() > 0) {
            return TRUE;
        }

    }

    function addNewStock($product_id,$stock_in){
        $addProduct = $this->dbConn->prepare("INSERT INTO tbl_stock (product_id,items_avail) VALUES(?,?)");
        $addProduct->bindParam(1,$product_id);
        $addProduct->bindParam(2,$stock_in);
        $addProduct->execute();

        if ($addProduct->rowCount() > 0) {
            return TRUE;
        }

    }
    // SAVE TRANSACTION
    function saveTransaction($user_id,$invoice_no,$paymode,$total_amount){
        $addProduct = $this->dbConn->prepare("INSERT INTO tbl_transactions (user_id,invoice_no,paymode,total_amount) VALUES(?,?,?,?)");
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
        $getInvoice = $this->dbConn->prepare("SELECT * FROM tbl_transactions WHERE invoice_no=?");
        $getInvoice->bindParam(1,$ivc_no);
        $getInvoice->execute();
        
        if($getInvoice->rowCount()>0){
            $result = $getInvoice->fetch();
            return $result;
            }
    }

    // INSERT TO CART
    function insertToCart($product_id,$invoice_no,$product_cat,$product_name,$price,$qty,$amount,$status){
        $addProduct = $this->dbConn->prepare("INSERT INTO tbl_carts (product_id,invoice_no,product_cat,product_name,price,qty,amount,status) VALUES(?,?,?,?,?,?,?,?)");
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
        $data = $this->dbConn->query("SELECT * FROM tbl_products ORDER BY id DESC");
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    #STOCK LIST
    function stock_list(){
        $getStock = $this->dbConn->query("SELECT * FROM tbl_products ORDER BY id DESC");
        if($getStock->rowCount()>0){
            $result = $getStock->fetchAll(PDO::FETCH_ASSOC);
            return $result;
            }
    }

    // INVOICE QUERY
    function invoiceInfo($invoice){
        $getInvoice = $this->dbConn->prepare("SELECT * FROM tbl_carts WHERE invoice_no=?");
        $getInvoice->bindParam(1,$invoice);
        $getInvoice->execute();
        
        if($getInvoice->rowCount()>0){
            $result = $getInvoice->fetchAll();
            return $result;
            }
    }

    function totalAmount($invoice){
        $getInvoice = $this->dbConn->prepare("SELECT SUM(amount) AS allMoney FROM tbl_carts WHERE invoice_no=?");
        $getInvoice->bindParam(1,$invoice);
        $getInvoice->execute();
        
        if($getInvoice->rowCount()>0){
            $result = $getInvoice->fetch();
            return $result;
            }
    }
    #GENERATE AVAILABLE STOCK
    function totalStock($id){
        $tStock = $this->dbConn->prepare("SELECT SUM(items_avail) AS total_stock FROM tbl_stock WHERE product_id=?");
        $tStock->bindParam(1,$id);
        $tStock->execute();
        
        if($tStock->rowCount()>0){
            $result = $tStock->fetch();
            return $result;
            }
    }

    function totalCart($id){
        $tCart = $this->dbConn->prepare("SELECT SUM(qty) AS total_cart FROM tbl_carts WHERE product_id=? AND status='Approved'");
        $tCart->bindParam(1,$id);
        $tCart->execute();
        
        if($tCart->rowCount()>0){
            $result = $tCart->fetch();
            return $result;
            }
    }

    // CART DATA
    function cartData($id){
        $cartD = $this->dbConn->prepare("SELECT * FROM tbl_products WHERE id=?");
        $cartD->bindParam(1,$id);
        $cartD->execute();
        
        if($cartD->rowCount()>0){
            $result = $cartD->fetch();
            return $result;
            }
    }

    #PRODUCT DROPDOWN
    function product_dropdown(){
        $getProduct = $this->dbConn->query("SELECT * FROM tbl_products GROUP BY category");
        
        if($getProduct->rowCount()>0){
            $result = $getProduct->fetchAll(PDO::FETCH_ASSOC);
            return $result;
            }
        
    }
    
    function ajax_product($productName){

        if(!empty($productName)){
            $getCategories = $this->dbConn->prepare("SELECT * FROM tbl_products WHERE category=?");
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
        $getCategories = $this->dbConn->prepare("DELETE FROM tbl_carts WHERE id=?");
            $getCategories->bindParam(1,$delete_key);
            $getCategories->execute();

            if($getCategories->rowCount()>0){
            return TRUE;
            }

    }
    #ADMIN SALES LIST
    function adminSalesView(){
        $viewSales = $this->dbConn->query("SELECT * FROM tbl_transactions ORDER BY id DESC");
        if($viewSales->rowCount()>0){
            $result = $viewSales->fetchAll(PDO::FETCH_ASSOC);
            return $result;
            }
    }
    #USER SALES LIST
    function userSalesView($saler_id,$today_date){
        $viewSales = $this->dbConn->prepare("SELECT * FROM tbl_transactions WHERE saler_id=? AND date='$today_date'");
        $viewSales->bindParam(1,$saler_id);
        $viewSales->execute();
        
        if($viewSales->rowCount()>0){
            $result = $viewSales->fetchAll();
            return $result;
            }
    }
    function userSalesViewAll($saler_id){
        $viewSales = $this->dbConn->prepare("SELECT * FROM tbl_transactions WHERE saler_id=? ORDER BY id DESC");
        $viewSales->bindParam(1,$saler_id);
        $viewSales->execute();
        
        if($viewSales->rowCount()>0){
            $result = $viewSales->fetchAll();
            return $result;
            }
    }
     // ORDER
    function order($order_no,$cashier_id,$name,$phone,$category,$amount,$bal,$size,$ddate){
        $addProduct = $this->dbConn->prepare("INSERT INTO order_tbl (order_no, cashier_id, name, phone, category, amount, balance, size, del_date) VALUES(?,?,?,?,?,?,?,?,?)");
        $addProduct->bindParam(1,$order_no);
        $addProduct->bindParam(2,$cashier_id);
        $addProduct->bindParam(3,$name);
        $addProduct->bindParam(4,$phone);
        $addProduct->bindParam(5,$category);
        $addProduct->bindParam(6,$amount);
        $addProduct->bindParam(7,$bal);
        $addProduct->bindParam(8,$size);
        $addProduct->bindParam(9,$ddate);
        $addProduct->execute();

        if ($addProduct->rowCount() > 0) {
            return TRUE;
        }

    }
    // VIEW ORDERS LIST
    function viewOrders($cashier_id){
        $orderD = $this->dbConn->prepare("SELECT * FROM order_tbl WHERE cashier_id=? ORDER BY id DESC");
        $orderD->bindParam(1,$cashier_id);
        $orderD->execute();
        
        if($orderD->rowCount()>0){
            $result = $orderD->fetchAll();
            return $result;
            }
    }
    // ORDER RECEIPT
    function orderReceipt($cashier_id){
        $orderD = $this->dbConn->prepare("SELECT * FROM order_tbl WHERE cashier_id=? ORDER BY id DESC LIMIT 1");
        $orderD->bindParam(1,$cashier_id);
        $orderD->execute();
        
        if($orderD->rowCount()>0){
            $result = $orderD->fetch();
            return $result;
            }
    }
    #UPDATE PRICE
    function updatePrice($product_id, $price){
        $updatePriceQuery = $this->dbConn->prepare("UPDATE tbl_products SET unit_price=? WHERE id=?");
        $updatePriceQuery->bindParam(1,$price);
        $updatePriceQuery->bindParam(2,$product_id);
        $updatePriceQuery->execute();
        
        if($updatePriceQuery->rowCount()>0){
            return true;
        }
    }

}


?>