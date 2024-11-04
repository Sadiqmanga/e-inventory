<?php
    require_once("../includes/config.php");
    session_start();
    if ($_SESSION['loggedin']==FALSE) {
          header("location:../");
        }
    $conn = new DB_Func();
    $userSession = $_SESSION['id'];
    $userInfo = $conn->casherName($userSession);
    #####################################################################
    $new_ivc = $userInfo['id'].date("Ymdhis");
    if (isset($_POST['cart'])) {
         $_SESSION['cahier'] = $userInfo['fullname'];
         $_SESSION['new_invoice'] = $new_ivc;
         if (isset($_SESSION['cahier']) AND isset($_SESSION['new_invoice'])) {
             header("location:cart.php");
         }
         else{
            echo "<i class='text-danger'><strong>Opps!</strong> An error occured</i>";
         }
     }
     elseif (isset($_POST['order'])) {
         $_SESSION['cahier'] = $userInfo['fullname'];
         $_SESSION['new_invoice'] = $new_ivc;
         if (isset($_SESSION['cahier']) AND isset($_SESSION['new_invoice'])) {
             header("location:order.php");
         }
         else{
            echo "<i class='text-danger'><strong>Opps!</strong> An error occured</i>";
         }
     }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>AMU GRV</title>
    <?php include("../includes/metadata.php");?>
    <style>
        a{
            color: white;
        }
        a:hover{
            color: gray;
        }
        .text-custom{
            color: #0c3133;
        }
        .type1{
            border-color: #0c3133;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    
    <?php include('../includes/header.php'); ?>
    
    <div class="container">
    <div class="row">
        <div class="col-md-10 mx-auto"> 
            <div class="form-container">
            <h3 class="text-custom">Sales Dashboard<br>AMU Global Resource Ventures <br><span style="font-size: smaller;"><?php echo date('l, jS M Y')?></span></h3>
            <div class="form-inline ">
                <a href="available-stock.php">
                <div class="zoom type1 py-3 px-4 mt-5">
                    <h5 class="text-custom">Available Stock</h5>
                    <i class="icon-chart fa-8x text-custom"></i>
                    </a>   
                </div>
                <div class="zoom mt-5">
                    <form method="POST">
                        <button  type="submit" name="cart" class="type1">
                            <h5 class="text-custom">Sales</h5>
                    <i class="icon-basket fa-8x text-custom"></i>
                        </button>
                    </form> 
                </div> 
                <a href="view-invoices.php">
                <div class="zoom type1 py-3 px-4 mt-5">
                    <h5 class="text-custom">Invoices</h5>
                    <i class="icon-docs fa-8x text-custom"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<?php include('../includes/footer.php'); ?>



