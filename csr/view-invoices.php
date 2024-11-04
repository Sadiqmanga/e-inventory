<?php
    require_once("../includes/config.php");
    session_start();
      if ($_SESSION['loggedin']==FALSE) {
              header("location:../");
            }
      $conn = new DB_Func();
      $saler_id = $_SESSION['id'];

      require_once"../include/config.php";
      $ref_err = $invoice_no = "";

      $today_date = date('Y-m-d');

      if (isset($_POST["view-invoice"])) {
        $invoice_no = $_POST["ref"];
        $result1 = $mysqli->query("SELECT COUNT(invoice_no) AS invoice FROM tbl_carts WHERE invoice_no='$invoice_no'") or die($mysqli->connect_error); 
        $row1 = $result1->fetch_assoc();
        $invoices = $row1["invoice"];
        if ($invoices > 0) {
          header("location: receipt.php?ref=$invoice_no");
        }else {
          $ref_err = "Invoice number does not exist";
        }
      }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>AMU GRV | Approved Invoices</title>
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
        .text-custom{
            color: #0c3133;
        }
    </style>
</head>

<!------ Include the above in your HEAD tag ---------->

<body>
  <?php include('../includes/header.php'); ?>

  <div class="container">
      <div class="form-container">
        <form action="" method='POST'>
            <div class='row col-md-8'>
              <div class="form-group mr-2 <?php echo (!empty($ref_err)) ? 'has-error' : ''; ?>">
                <input type='text' name='ref' class='form-control' value="<?php echo $invoice_no; ?>" placeholder='Invoice Reference Number' required>
                <span class="help-block text-danger"><?php echo $ref_err; ?></span>
              </div>
              <div class="form-group">
                <button type='submit' name='view-invoice' class='btn btn-block btn-outline-secondary'><i class="icon-printer"></i> Print</button>
              </div>
          </div>
        </form>
        <table id="view_invoices" class="table table-responsive">
            <thead>
              <tr>
                <th class="th-sm">S/N</th>
                <th class="th-sm">Invoice No.</th>
                <th class="th-sm">Pay. Mode</th>
                <th class="th-sm">Amount</th>
                <th class="th-sm">Discount</th>
                <th class="th-sm">Total</th>
                <th class="th-sm">Date</th>
                <th class="th-sm">Acton</th>
              </tr>
            </thead>
            <tbody>
              <?php
                    $userSales = $conn->userSalesView($saler_id,$today_date);
                    $i = 1;
                    if ($userSales) {
                      foreach ($userSales as $sales){
                      // PRODUCT LIST VARIABLES 
                        $invoiceNo = $sales['invoice_no'];
                        $paymwnt_mode = $sales['payment_mode'];
                        $amount = $sales['total_amount'];
                        $disc = $sales['discount'];
                        $total = $amount - $disc;
                        $date = strtotime($sales['date_created']);
                        $x = date('j/m/y',$date);

                        echo "
                          <tr>
                            <td>$i</td>
                            <td>$invoiceNo</td>
                            <td>$paymwnt_mode</td>
                            <td>".number_format($amount,2)."</td>
                            <td>".number_format($disc,2)."</td>
                            <td>&#8358;".number_format($total,2)."</td>
                            <td>$x</td>
                            <td>
                                <div class='form-button-action'>
                                    <a href='receipt.php?ref=$invoiceNo' class='btn btn-custom text-info' data-toggle='tooltip' data-placement='top' title='Print Invoice'><i class='icon-printer'></i></a>
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
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/jquery-3.5.1.min.js"></script>
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../datatables/datatables.min.js"></script>
  <script src="../js/custom.js"></script>
  <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
  <script src="../assets/js/plugin/datatables.min.js"></script>
  <script>
      $(document).ready(function() {
        $('#view_invoices').DataTable({
          "pageLength": 10,
        });
      });
  </script>
</body>
</html>
