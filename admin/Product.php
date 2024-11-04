<?php
  require_once("../includes/config.php");
  session_start();
  if ($_SESSION['loggedin']==FALSE) {
          header("location:../");
        }
  $conn = new DB_Func();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>AMU GRV | Manage Products</title>
  <?php include("../includes/metadata.php");?>

  <script>
    $(document).ready(function () {
    $('#custList').DataTable();
    $('.dataTables_length').addClass('bs-select');
  });
  </script>
</head>

<!------ Include the above in your HEAD tag ---------->

<body>
<?php include("../include/header.php");?>

<div class="container">
  <div class="row">
      <div class="col-md-6 col-lg-4 col-xl-3"> </div>
          <div class="form-container">
            <button type="button" id="formButton" class="btn btn-primary mb-2 mr-sm-2">Add Product</button>
            <?php
              if (isset($_POST['add'])) {
                
                $productName = trim($_POST['product']);
                $category = trim($_POST['category']);
                $unitPrice = trim($_POST['price']);

                if (!empty($productName) AND !empty($category) AND !empty($unitPrice)) {
                  $newProduct = $conn->addNewProduct($category,$productName,$unitPrice);
                  if ($newProduct) {
                    echo "<div class='alert alert-success text-center'>New Product Added!</div>";
                  }
                  else{
                    echo "<div class='alert alert-warning text-center'><strong>Oops!</strong> an error occured</div>";
                  }
                }
                else{
                  echo "<div class='alert alert-danger text-center'>All required fields must be completed</div>";
                }
              }

              if (isset($_POST['update'])) {
                $editId = $_POST['edit-id'];
                $editUnitPrice = trim($_POST['edit-price']);
                // echo $editUnitPrice.' '.$editId;
                if (!empty($editId) && !empty($editUnitPrice)) {
                  $updatedPrice = $conn->updatePrice($editId,$editUnitPrice);
                  if ($updatedPrice) {
                    echo "<script>alert('Price Updated Successfully!');</script>";
                  } else {
                    echo "<script>alert('Some error occurred! please try again...');</script>";
                  }
                }
              }
            ?>
            <form id="form1" method="POST">
                <div class="form-inline" id="addYard">
                  <input type="text" name="category" class="form-control mb-2 mr-sm-2" id="nYard" placeholder="Category" required>
                  <input type="text" name="product" class="form-control mb-2 mr-sm-2" id="name" placeholder="Product Name" required>
                  <input type="number" name="price" class="form-control mb-2 mr-sm-2" id="nard" placeholder="Unit Price" step="0.01" required>
                  <button type="submit" id="submit" name="add" class="btn btn-success mb-2 mr-sm-2">Add</button>
                </div>
            </form>
              
            <table id="custList" class="table" width="100%">
                <thead>
                  <tr>
                    <th class="th-sm">S/N
                    </th>
                    <th class="th-sm">Category</th>
                    <th class="th-sm">Name</th>
                    <th class="th-sm">Price
                    </th>
                    <th class="th-sm">Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $i = 1;
                    $pl = $conn->product_list();
                    if ($pl) {
                      foreach ($pl as $prod_list){
                    
                        // PRODUCT LIST VARIABLES 
                          $pId = $prod_list['id'];
                          $pName = $prod_list['name'];
                          $pCategory = $prod_list['category'];
                          $pPrice = number_format($prod_list['unit_price'],1);
                          $price = $prod_list['unit_price'];

                          echo "
                            <tr>
                              <td>$i</td>
                              <td>$pCategory</td>
                              <td>$pName</td>
                              <td>$pPrice</td>
                              <td>
                            <a class='edit' class='btn btn-primary' data-toggle='modal' data-target='#myModal$pId'>
                            Edit <i class='fas fa-edit'></i>
                          </a>

                          <div class='modal fade' id='myModal$pId'>
                            <div class='modal-dialog modal-dialog-centered'>
                              <div class='modal-content'>
                                <form method='POST'>
                                  <div class='modal-header'>
                                  <h4 class='modal-title'>Update Product Price</h4>
                                  <button type='button' class='close' data-dismiss='modal'>&times;</button>
                                  </div>
                                  
                                  <div class='modal-body'>
                                  
                                    <div class='form-group' id='addYard'>
                                    <input type='text' name='edit-id' class='form-control mb-2 col-md-12' value='$pId' hidden required>
                                    <input type='text' name='edit-category' class='form-control mb-2 col-md-12' value='$pCategory' disabled required>
                                    <input type='text' name='edit-product' class='form-control mb-2 col-md-12' value='$pName' disabled required>
                                    <input type='number' name='edit-price' class='form-control mb-2 col-md-12' value='$price' step='0.01' required>
                                    </div>
                                  
                                  </div>
                                  
                                  <div class='modal-footer'>
                                  <button type='submit' name='update' class='btn btn-success'>Update</button>
                                  <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                                  </div>
                                </form>	
                              </div>
                            </div>
                          </div>
                              </td>
                            </tr>
                          ";
                          $i++;
                        }
                    }
                  ?>
                </tbody>
            </table>      
        </div>
    </div>
</body>
</html>