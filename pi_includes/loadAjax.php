<?php
    require_once("config.php");
    $conn = new DB_Class();

    if(isset($_POST['id'])){
        $productId = $_POST['id'];
        $ajax_output = $conn->ajax_product($productId);
        if ($ajax_output) {
            echo json_encode($ajax_output);
        }
        else{
            echo json_encode("empty");
        }
    }
    
?>