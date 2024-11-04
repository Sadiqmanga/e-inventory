<?php
  require_once("../pi_includes/config.php");
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
    </style>
</head>

<!------ Include the above in your HEAD tag ---------->

<body>
  <?php include("../includes/header.php");?>

  <div class="col-md-8 ml-auto mr-auto">
  <div class="form-container">
    <div class="container">
        <table id="available_stock" class="table table-responsive table-striped" width="100%">
            <thead>
              <tr>
                <th class="th-sm">S/N</th>
                <th class="th-sm">Company</th>
                <th class="th-sm">Product</th>
                <th class="th-sm">Quantity
                </th>
              </tr>
            </thead>
            <tbody>
              <?php
                $i = 1;
                $vs = $conn->stock_list();
                if ($vs) {
                  foreach ($vs as $st_list){
                
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
  </div>
  <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
  <script src="../assets/js/plugin/datatables.min.js"></script>
  <script>
      $(document).ready(function() {
        $('#available_stock').DataTable({
          "pageLength": 10,
        });
      });
  </script>
</body>
</html>
