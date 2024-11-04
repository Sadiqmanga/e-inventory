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
<title>AMU GRV | Pricing</title>
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

  <div class="col-md-6 ml-auto mr-auto">
  <div class="form-container">
    <div class="container">
        <table id="available_stock" class="table table-responsive table-striped" width="100%">
            <thead>
              <tr>
                <th class="th-sm">S/N</th>
                <th class="th-sm">Company</th>
                <th class="th-sm">Product</th>
                <th class="th-sm">Price</th>
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
                      $prc = $st_list['unit_price'];
                      $price = number_format($prc);
                      $_cart = $conn->totalCart($pId);
                      $_stock = $conn->totalStock($pId);

                      if ($prc > 0) {
                          echo 
                          "<tr>
                            <td>$i</td>
                            <td>$pCategory</td>
                            <td>$pName</td>
                            <td>&#8358;$price</td>
                          </tr>";
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
