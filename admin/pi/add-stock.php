<?php
  require_once("../includes/config.php");
  session_start();
  if ($_SESSION['loggedin']==FALSE) {
          header("location:../");
        }
  $conn = new DB_Class();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>AMU GRV</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="../logo.ico" type="image/x-icon">
<link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link rel="stylesheet" href="../fontawesome/css/all.min.css">
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery-3.5.1.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/custom.js"></script>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../datatables/datatables.min.css">
<script src="../datatables/datatables.min.js"></script>
<script>
  $(document).ready(function () {
  $('#custList').DataTable();
  $('.dataTables_length').addClass('bs-select');
});
</script>
</head>

<!------ Include the above in your HEAD tag ---------->

<body>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-3">
    <div class="container-fluid">
        <a href="#" class="navbar-brand mr-3">AMU GRV (ADMIN)</a>
        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav">
                <a href="index.php" class="nav-item nav-link active">Dashboard</a>
            </div>
            <div class="navbar-nav ml-auto">
                <a href="signout.php" class="nav-item nav-link">LogOut</a>
            </div>
        </div>
    </div>    
</nav>
<div class="container">
  <div class="row">
      <div class="col-md-6 col-lg-4 col-xl-3"> </div>
          <div class="form-container">
              <button type="button" id="formButton" class="btn btn-primary mb-2 mr-sm-2">Add Stock</button>
              <?php
                if (isset($_POST['add'])) {
                  $prod_id = trim($_POST['category']);
                  $new_stock = trim($_POST['stock']);

                  if (!empty($prod_id) AND !empty($new_stock)) {
                    $newStock = $conn->addNewStock($prod_id,$new_stock);
                    if ($newStock) {
                      echo "<div class='alert alert-success text-center'>New Stock Added!</div>";
                    }
                    else{
                      echo "<div class='alert alert-warning text-center'><strong>Oops!</strong> an error occured</div>";
                    }
                  }
                  else{
                    echo "<div class='alert alert-danger text-center'>All required fields must be completed</div>";
                  }
                }
              ?>  
              <form id="form1" method="POST">
                <div class="form-inline" id="addYard">
                      <select name="product" id="product" class="form-control mb-3 mr-sm-2"  required>
                          <option value="">select product</option>
                          <?php 
                              foreach ($conn->product_dropdown() as $productInfo) {
                                echo "<option value='".$productInfo['category']."'>".$productInfo['category']."</option>";
                              }
                          ?>                
                      </select>                       
                      <select name="category" id="productName" class="form-control mb-3 mr-sm-2" required>
                              <!-- options are filled by javascript  -->
                      </select>
                  <input type="number" name="stock" class="form-control mb-3 mr-sm-2" id="nYard" placeholder="Stock In">
                  <button type="submit" id="submit" name="add" class="btn btn-success mb-3 mr-sm-2">Add</button>
                </div>
              </form> 
              <table id="custList" class="table" width="100%">
                  <thead>
                    <tr>
                      <th class="th-sm">S/N
                      </th>
                      <th class="th-sm">Category</th>
                      <th class="th-sm">Name</th>
                      <th class="th-sm">Available
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $i = 1;
                      $sl = $conn->stock_list();
                      if ($sl) {
                        foreach ($sl as $st_list){
                      
                          // stUCT LIST VARIABLES 
                            $pId = $st_list['id'];
                            $pName = $st_list['name'];
                            $pCategory = $st_list['category'];
                            // $qty = $st_list['total_stock'] + 5;
                            $_cart = $conn->totalCart($pId);
                            $_stock = $conn->totalStock($pId);

                            $qty = $_stock['total_stock'] - $_cart['total_cart'];

                            if ($qty > 0) {
                                echo "
                                <tr>
                                  <td>$i</td>
                                  <td>$pCategory</td>
                                  <td>$pName</td>
                                  <td>$qty</td>
                                </tr>
                              ";
                              $i++;
                            }
                          }
                      }
                    ?>
                  </tbody>
              </table>      
          </div>
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
                          $('#productName').append("<option value=''>select item</option>");
                          $.each(response, function(key,value) {
                              $('#productName').append("<option value='"+value['id']+"'>"+value['name'].trim()+"</option>");
                          });
                      }
                      else{
                          $('#productName').empty();
                          $('#productName').append("<option value=''>items not available</option>");
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
