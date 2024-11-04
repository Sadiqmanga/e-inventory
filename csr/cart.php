<?php

  require_once("../includes/config.php");
  session_start();
    if ($_SESSION['loggedin']==FALSE) {
          header("location:../");
        }
  $conn = new DB_Func();
  // USER ID
  $userSession = $_SESSION['id'];
  $userInfo = $conn->casherName($userSession);
  $userID = $userInfo['id'];
  // INVOICE NUMBER
  $ivc = $_SESSION['new_invoice'];
  
  if (isset($_POST['send-invoice'])) {
    require_once"../include/config.php";
   
    $sql1 = "SELECT * FROM tbl_inv_detail WHERE invoice_no = ?";
    
    if($stmt = $mysqli->prepare($sql1)){
      $stmt->bind_param("s", $param_invoice_no);

	    $param_invoice_no  = $ivc;

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // store result
            $stmt->store_result();
            
            if($stmt->num_rows == 1){
                echo $trnError = 
                "<script>
                    (function() {
                        window.addEventListener('load', function() {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Oops!',
                                html: 'The invoice: <strong>$param_invoice_no</strong> <br> is already waiting for approval',
                                showConfirmButton: false,
                                allowOutsideClick: true,
                                // delay: 4000,
                                footer:`<a class='btn btn-rounded btn-custom' onclick='Swal.close()' href='index.php'>O K</a>`
                            });
                        }, false);
                        // End of the IIFE()
                    })();
                </script>
                ";
            } else{
              $invoice_number = $ivc;
              $payment_mode = $_POST['payOption'];
              $cname = strtoupper(trim($_POST['cname']));
              $cphone = strtoupper(trim($_POST['cphone']));
              $caddress = strtoupper(trim($_POST['caddress']));
              $user_id = strtoupper(trim($userID));          
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // TRANSACTION
    if (empty($trnError)) {
      $sql2 = "INSERT INTO tbl_inv_detail(user_id,invoice_no,payment_mode,cname,cphone,caddress) VALUES(?,?,?,?,?,?)";
      $stmt = $mysqli->prepare($sql2);
      $stmt->bind_param("ssssss", $user_id, $invoice_number, $payment_mode, $cname, $cphone, $caddress);
      if($stmt->execute()){
          echo 
          "<script>
          (function() {
              window.addEventListener('load', function() {
                  Swal.fire({
                      icon: 'success',
                      html: 'Invoice sent successfully',
                      showConfirmButton: false,
                      allowOutsideClick: false,
                      // delay: 4000,
                      footer:`<a class='btn btn-rounded btn-custom' onclick='Swal.close()' href='index.php'>O K</a>`
                  });
              }, false);
              // End of the IIFE()
          })();
      </script>
      ";
        }
      }
      // else{
      //     die("Error: ".mysqli_error($mysqli));
      // }
    // }
    // else{
    //   echo "<div class='alert alert-warning text-warning text-center'><strong>select Payment mode</strong></div>";
    // }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>AMU GRV</title>
  <?php include("../includes/metadata.php");?>
  <script>
    $(document).ready(function () {
    $('#custList').DataTable();
    $('.dataTables_length').addClass('bs-select');
  });
  </script>
    <style>
        a{
            color: white;
        }
        a:hover{
            color: gray;
        }
        .btn-custom{
            background-color: #0c3133;
            color: white;
        }
        .btn-custom:hover{
            background-color: #352e8f;
            color: white;
        }
    </style>
</head>

<body>
  <?php include('../includes/header.php'); ?>

  <div class="container">
    <!-- <div class="row"> -->
      <div class="col-md-10 mr-auto ml-auto">
          <div class="form-container">
            <button type="button" id="formButton" class="btn btn-custom mb-2 mr-sm-2">Add Sales</button>
            <?php
              // REMOVE ITEM FROM CART
              if (isset($_GET['id'])) {
                $x = explode(".", $_GET['id']);
                $deleteid = end($x);

                $del = $conn->removeCart($deleteid);
                if ($del) {
                  // header("location:cart.php");
                }
              }
              // ADD ITEM INTO CART FUNCTION
              if (isset($_POST['add'])) {
                $proID = trim($_POST['category']);
                
                // FETCH DATA TO CART
                $cartInfo = $conn->cartData($proID);
                $crtName = $cartInfo['name'];
                $crtCat = $cartInfo['category'];
                $crtPrice = $cartInfo['unit_price'];
                $crtQty = $cartQty = trim($_POST['quantity']);
                $qtt = $cartQty;
                $crtAmount = $qtt*$crtPrice;
                // DETERMINE AVAILABLE STOCK
                $_cart = $conn->totalCart($proID);
                $_stock = $conn->totalStock($proID);
                $ttStock = $_stock['total_stock'];
                $ttCart = $_cart['total_cart'];
                $remStock = $ttStock - $ttCart;
                $crtStatus = 'Pending';

                if ($remStock >= $crtQty) {
                  $newItem = $conn->insertToCart($proID,$ivc,$crtName,$crtCat,$crtPrice,$crtQty,$crtAmount,$crtStatus);
                  if ($newItem) {
                    // header("location:cart.php");
                  }
                }
                else{
                  echo "<div class='alert alert-warning text-center'>Only <strong class='text-danger'>$remStock</strong> $crtCat-$crtName are available</div>";
                }

              // SEND INVOICE FUNCTION
              }
            ?>
            <form id="form1" method="POST">
              <div class="row">
                <div class="col-md-4">
                  <select name="product" id="product" class="form-control mb-2"  required>
                      <option value="">Select Company</option>
                      <?php 
                          foreach ($conn->product_dropdown() as $productInfo) {
                            echo "<option value='".$productInfo['category']."'>".$productInfo['category']."</option>";
                          }
                      ?>                
                  </select>                       
                </div>
                <div class="col-md-3">
                  <select name="category" id="productName" class="form-control mb-2" required>
                          <!-- options are filled by javascript  -->
                  </select>     
                </div>
                <div class="col-md-2">
                  <input type="number" name="quantity" class="form-control mb-2" placeholder="Qty">
                </div>
                <div class="col-md-3">
                  <button type="submit" name="add" id="submit" class="btn btn-custom mb-2 float-right">Add to Invoice</button>
                </div>
              </div>
            </form>
            <!--Table to dispay cart items  -->
            <table id="custList" class="table table-responsive" width="100%">
              <thead>
                <?php
                   // PURCHASED TOTAL AMOUNT
                   $sumMoney = $conn->totalAmount($ivc);
                   $totalMoney = $sumMoney['allMoney'];
                  if ($totalMoney > 0) {
                    echo "
                        <tr>
                          <th></th>
                          <th >Product Desc.
                          </th>
                          <th >Price
                          </th>
                          <th >Qty
                          </th>
                          <th >Amount
                          </th>
                          <th >Action
                          </th>
                        </tr>
                    ";
                  }
                  else{
                    echo "<div class='alert alert-info text-info text-center'><strong>Cart is empty!</strong></div>";
                  }
                ?>
              </thead>
              <tbody>
                <?php
                  // // PURCHASED TOTAL AMOUNT
                  // $sumMoney = $conn->totalAmount($ivc);
                  // $totalMoney = $sumMoney['allMoney'];

                  $ivcFresh = $conn->invoiceInfo($ivc);
                  if ($ivcFresh) {
                    foreach ($ivcFresh as $invoiceDatum){
                      // stUCT LIST VARIABLES 
                      $pID = md5(1000).".".$invoiceDatum['id'];
                      $pName = $invoiceDatum['product_name'];
                      $pCat = $invoiceDatum['product_cat'];
                      $ppp = number_format($invoiceDatum['price'],1);
                      $qTy = $invoiceDatum['qty'];
                      $money = number_format($invoiceDatum['amount'],1);
                          echo "
                          <tr>
                            <td></td>
                            <td>$pName-$pCat</td>
                            <td>$ppp</td>
                            <td>$qTy</td>
                            <td>$money</td>
                            <td>
                            <a href='cart.php?id=$pID' class='btn btn-outline-danger text-danger btn-sm btn-block' title='Delete' data-toggle='tooltip'><i class='fas fa-cancel'></i>Remove</a>
                            </td>
                          </tr>
                        ";
                    }
                  }
                  // TOTAL PURCHASE AMOUNT
                  if ($totalMoney > 0) {
                    $formatTotalMoney = number_format($totalMoney,1);
                    echo "
                      <tr>
                        <td></td>
                        <td><strong>Total</strong></td>
                        <td><strong>-</strong></td>
                        <td><strong>-</strong></td>
                        <td><strong>$formatTotalMoney</strong></td>
                      </tr>
                  ";
                  }
                ?>
                  
              </tbody>
            </table>
            <?php
              if ($totalMoney > 0) {
                echo 
                    "<form method='POST'>
                      <div class='row'>
                        <div class='col-md-4'>
                          <input type='text' name='cname' class='form-control mb-2 mr-sm-2' placeholder='Customer Name' required>
                        </div>
                        <div class='col-md-4'>
                          <input type='number' name='cphone' class='form-control mb-2 mr-sm-2' placeholder='Customer Phone' required>
                        </div>
                        <div class='col-md-4'>
                          <select name='payOption' id='payOption' class='form-control mb-2 mr-sm-2'  required>
                            <option value=''>Select payment mode</option>               
                            <option value='Cash'>Cash</option>               
                            <option value='Transfer'>Transfer</option>               
                          </select>      
                        </div>
                        <div class='col-md-8'>
                        <input type='text' name='caddress' class='form-control mb-2 mr-sm-2' placeholder='Customer Address' required>
                        </div>
                        <div class='col-md-4'>
                          <button type='submit' name='send-invoice' class='btn btn-block btn-success mb-2 mr-sm-2'>Send</button>
                        </div>
                      </div>
                    </form>
                    ";
              }
            ?>
        </div>
      </div>
    <!-- </div> -->
  </div>
    <script>
      $(document).ready(function () {

          $('#product').on('change',function(e){
              var productCategory = "id="+$(this).val();

              $.ajax({
                  type: 'POST',
                  url: '../includes/loadAjax.php',
                  data: productCategory,
                  dataType:'json',
                  cache:false,
                  success: function (response) {
                      console.log(response);

                      if(response!="empty"){
            
                          $('#productName').empty();
                          $('#productName').append("<option value=''>Select item</option>");
                          $.each(response, function(key,value) {
                              $('#productName').append("<option value='"+value['id']+"'>"+value['name'].trim()+"</option>");
                          });
                      }
                      else{
                          $('#productName').empty();
                          $('#productName').append("<option value=''>Items not available</option>");
                      }
                  },
                  error:function(request,error){
                      console.log(error);
                  }
                  
              });

          }); 

      });
    </script>
</body>
