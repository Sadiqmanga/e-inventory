<?php
// PHP7 specific, fails fast, this file only 
declare(strict_types=1); 
// this file and all included/required files
  error_reporting(-1); 
  ini_set('display_errors', 'true');
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
	header("location: ../login.php");
	exit;
} 

include("../include/config.php");
$ref  = $_GET['ref'];
$_SESSION['ref'] =  $ref;
$invoice_ref = $_SESSION['ref'];

$result1 = $mysqli->query(
    "SELECT a.invoice_no, SUM(amount) AS naTotal, a.product_cat, a.product_name, a.status, b.user_id, b.payment_mode, b.cname,b.caddress, b.date_created, c.id, c.fullname
        FROM tbl_carts AS a
        JOIN tbl_inv_detail AS b
        ON a.invoice_no = b.invoice_no
        JOIN tbl_users AS c
        ON b.user_id = c.id
        WHERE a.invoice_no=$invoice_ref;") or die($mysqli->connect_error); 
        $row1 = $result1->fetch_assoc();
        $productName = $row1["product_name"]."-".$row1["product_cat"];
        $salesId = $row1["user_id"];

    // Update statement
    if (isset($_POST['update-transaction'])) {
        $id  = trim($_POST["id"]);
        $qty  = trim($_POST["qty"]);
        $product_id  = trim($_POST["product_id"]);
        $invoice_no  = trim($_POST["invoice_no"]);
        $price  = trim($_POST["price"]);

        $qry2 = "SELECT SUM(items_avail) AS total_stock FROM tbl_stock WHERE product_id=$product_id";
        $result2 = $mysqli->query($qry2) or die($mysqli->connect_error); 
        $row2 = $result2->fetch_assoc();
        $row2TotalStock = $row2["total_stock"];

        $qry3 = "SELECT SUM(qty) AS total_cart FROM tbl_carts WHERE product_id=$product_id AND invoice_no!=$invoice_ref";
        $result3 = $mysqli->query($qry3) or die($mysqli->connect_error); 
        $row3 = $result3->fetch_assoc();
        $row3TotalSold = $row3["total_cart"];

        $availableStock = $row2TotalStock - $row3TotalSold;
        // $compare = $row2TotalStock - $availableStock;
        $updatedAmount = ((int)$qty * (int)$price);

        if ($qty <= $availableStock) {
            // $qry4 = "UPDATE tbl_carts SET qty=$qty, amount='$updatedAmount' WHERE product_id=$product_id AND invoice_no='$invoice_no'";
            if ($mysqli->query("UPDATE tbl_carts SET price=$price, qty=$qty, amount=$updatedAmount WHERE id=$id")) {
                header("location:edit-invoice.php?ref=$invoice_no");
            }else {
                echo "Error";
            }
        }else{
            //   echo "<div class='alert alert-warning text-center'>Only <strong class='text-danger'>$remStock</strong> $crtCat-$crtName are available</div>";
            echo
            "<script>
                (function() {
                    window.addEventListener('load', function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops!',
                            html: 'Only $availableStock $productName are available',
                            showConfirmButton: false,
                            allowOutsideClick: true,
                            footer:`<a class='btn btn-round btn-outline-secondary' href='edit-invoice.php?ref=$invoice_no'>O K</a>`
                        });
                    }, false);
                    // End of the IIFE()
                })();
            </script>";
        }
    }

    //  delete statement
    if (isset($_GET['delete'])) {
        // $x = explode("=", $_GET['delete']);
        // $ivcNo = current($x);
        // $id = end($x);
        $redirectUrl = "edit-invoice.php?ref=".$ivcNO;

        $mysqli->query("DELETE FROM tbl_carts WHERE id='$id'") or die($mysqli->connect_error);
        echo
            "<script>
                (function() {
                    window.addEventListener('load', function() {
                        Swal.fire({
                            html: 'Transaction deleted successfully!',
                            icon: 'error',
                            showConfirmButton: false,
                            allowOutsideClick: true,
                            // delay: 4000,
                            footer:`<a class='btn btn-rounded btn-outline-secondary' href='$redirectUrl'>O K</a>`
                        });
                    }, false);
                    // End of the IIFE()
                })();
            </script>";				
    }

    if (isset($_POST['approve-transaction'])) {

        $sql1 = "SELECT * FROM tbl_transactions WHERE invoice_no = ?";
        
        if($stmt = $mysqli->prepare($sql1)){
            $stmt->bind_param("s", $param_invoice_no);

            $param_invoice_no  = $invoice_ref;

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
                                    html: 'The invoice: <strong>$param_invoice_no</strong> <br> has already been approved',
                                    showConfirmButton: false,
                                    allowOutsideClick: true,
                                    // delay: 4000,
                                    footer:`<a class='btn btn-rounded btn-custom' onclick='Swal.close()' href='manage-invoices.php'>O K</a>`
                                });
                            }, false);
                            // End of the IIFE()
                        })();
                    </script>
                    ";
                } else{
                    $saler_id = trim($_POST['saler_id']);
                    $user_id = $_SESSION["id"];
                    $invoice_no = strtoupper(trim($_POST['invoice_no']));
                    $payment_mode = strtoupper(trim($_POST['payment_mode']));
                    $total_amount = trim($_POST['total_amount']);
                    $disc = trim($_POST['discount']);
                    if (empty($disc)) {
                        $discount = 0;
                    }else{
                        $discount = ($disc/100) * $total_amount;
                    }         
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        if (empty($trnError)) {
            $sql5 = "INSERT INTO tbl_transactions(saler_id,user_id,invoice_no,payment_mode,total_amount,discount) VALUES(?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql5);
            $stmt->bind_param("ssssss", $saler_id, $user_id, $invoice_no, $payment_mode, $total_amount,$discount);
            if($stmt->execute()) {
                $mysqli->query("UPDATE tbl_carts SET status='Approved' WHERE invoice_no=$invoice_no");
                echo 
                    "<script>
                        (function() {
                            window.addEventListener('load', function() {
                                Swal.fire({
                                    icon: 'success',
                                    html: 'Invoice approved successfully!',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    // delay: 4000,
                                    footer:`<a class='btn btn-rounded btn-outline-secondary' onclick='Swal.close()' href='manage-invoices.php'>O K</a>`
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
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../include/metadata.php'); ?>
	<title>AMU GRV | Edit Invoice</title>
    <style>
        .tb-fs {
            font-size: 50px;
            padding-top: 15px;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>
	<div class="wrapper horizontal-layout-3">
		<?php include('../include/header.php'); ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
				<ul class="nav nav-pills nav-black nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                    <li class="nav-item">
                        <a class="btn btn-round btn-custom" href="manage-invoices.php"><i class="icon-home"></i> Back</a>
                    </li>		
                    <li class="nav-item">
							<a class="nav-link active" id="pills-one-tab-nobd" data-toggle="pill" href="#pills-one" 
							role="tab" aria-controls="pills-one" aria-selected="true">Pending Invoice</a>
						</li>
					</ul>
					<div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- List of Pending  -->
						<div class="tab-pane fade show active" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
                            <!-- <h1>List of Pending</h1> -->
							<div class="col-lg-9 ml-auto mr-auto">
								<div class="card">
									<div class="card-body">
                                        <?php
                                            $result = $mysqli->query(
                                                "SELECT a.id, a.product_id, a.invoice_no, a.product_name, a.product_cat, a.price, a.qty, a.amount, a.status, b.payment_mode, b.date_created 
                                                FROM tbl_carts AS a
                                                JOIN tbl_inv_detail AS b
                                                ON a.invoice_no = b.invoice_no
                                                WHERE a.invoice_no=$invoice_ref;") or die($mysqli->connect_error); ?>
                                        <table class="table table-responsive table-hover" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Company</th>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Qty</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i=1;  while ($row = $result->fetch_assoc()):?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <!-- <td><?php echo $row['invoice_no'];?></td> -->
                                                    <td><?php echo $row['product_name'];?></td>
                                                    <td><?php echo $row['product_cat'];?></td>
                                                    <td><?php $rprice = $row['price']; echo "&#8358;".number_format((float) $rprice);?></td>
                                                    <td><?php echo $row['qty'];?></td>
                                                    <td><?php $ramount = $row['amount']; echo "&#8358;".number_format((float) $ramount);?></td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <!-- Update Modal -->
                                                            <a href="#" class="btn btn-rounded" data-toggle="modal" data-target="#approve-modal<?php echo $row['id']; ?>" data-toggle="tooltip" data-placement="top" title="Update Transaction"><i class="fas fa-edit"></i></a>
                                                            <div class="pd-20 bg-red border-radius-4">
                                                                <div class="modal fade" id="approve-modal<?php echo $row['id'];?>" tabindex="-1" post="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" post="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-body text-center font-18">
                                                                            <h4 class="padding-top-30 weight-500"><?php echo $row['qty']." ".$row['product_name']."-".$row['product_cat'];?></h4>
                                                                            <h2 class="weight-500"><?php $mamount = $row['amount']; echo "&#8358;".number_format((float) $mamount); ?></h2>
                                                                                <div class="padding-bottom-30" style="max-width: 250px; margin: 0 auto;">
                                                                                    <form action="edit-invoice.php?ref=<?php echo $row['invoice_no']; ?>" method="post">
                                                                                        <div class="form-group">
                                                                                            <div class="form-group">
                                                                                                <label for="qty">Price</label>
                                                                                                <input type="number" class="form-control" name="price" value="<?php echo $row['price'];?>">
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label for="qty">Quantity</label>
                                                                                                <input type="number" class="form-control" name="qty" value="<?php echo $row['qty'];?>">
                                                                                            </div>
                                                                                            <input type="text" name="id" value="<?php echo $row['id']; ?>" hidden>
                                                                                            <input type="text" name="product_id" value="<?php echo $row['product_id']; ?>" hidden>
                                                                                            <input type="text" name="invoice_no" value="<?php echo $row['invoice_no']; ?>" hidden>
                                                                                        </div>
                                                                                        <button type="submit" name="update-transaction" class="btn btn-block btn-custom btn-rounded confirmation-btn" >Update Transaction</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Delete Modal  -->
                                                            <!-- <a href="#" class="btn btn-rounded" data-toggle="modal" data-target="<?php echo $id = $row['id']; ?>" data-toggle="tooltip" data-placement="top" title="Delete Product"><i class="fas fa-trash-alt text-danger"></i></a>
                                                            <div class="pd-5 bg-red border-radius-4">
                                                                <div class="modal fade" id="delete-modal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-body text-center font-18">
                                                                            <h5 class="padding-top-30 weight-500"><?php echo $row['product_name']."-".$row['product_cat'];?></h5>
                                                                            <h1 class="weight-500"><?php $mamount = $row['amount']; echo "&#8358;".number_format((float) $mamount); ?></h1>
                                                                                <h4 class="padding-top-30 mb-30 weight-500">Are you sure <br>you want to <strong class="text-danger">delete</strong> this transaction?</h4>
                                                                                <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                                                    <div class="col-6">
                                                                                        <button type="button" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                                                        <b>NO</b>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <a  href="edit-invoice.php?ref=<?php echo $row['invoice_no'];?>_delete=<?php echo $row['id'];?>"  class="btn btn-rounded btn-danger confirmation-btn" ><i class="fa fa-trash-alt"></i></a>
                                                                                        <b>YES</b>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> -->
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php $i++; endwhile; ?>
                                            </tbody>
                                        </table>
									</div>
                                    <form action="edit-invoice.php?ref=<?php echo $row1['invoice_no']; ?>" method="POST" enctype="multipart/form-data">
                                        <div class="row mx-3">
                                            <div class="col-md-8">
                                                <!-- <div class="form-group"> -->
                                                    <h2>Total: <?php $row1Total = $row1['naTotal']; echo "&#8358;".number_format((float) $row1Total);?>
                                                        <?php if($row1['status'] == 'Approved'): ?>
                                                        <span class="badge badge-success"><?php echo $row1['status'];?></span>
                                                        <?php else: ?>
                                                        <span class="badge badge-danger">Status: <?php echo $row1['status'];?></span>
                                                        <?php endif; ?>
                                                    </h2>
                                                    <h4>
                                                        <?php echo $row1["cname"];?> | <span class="badge badge-info">Mode: <?php echo $row1['payment_mode'];?></span><br>
                                                        <span class="text-small"><?php echo $row1["caddress"];?><br></span>
                                                        <?php 
                                                            $row1Date = $row1['date_created'];
                                                            echo date('D, jS F Y | h:i A',strtotime($row1Date));
                                                        ?><br>
                                                        Sales: <?php echo $row1["fullname"];?><br>
                                                    </h4>
                                                <!-- </div> -->
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount">Discount (%)</label>
                                                    <input type="number" class="form-control" name="saler_id" value="<?php echo $row1['user_id'];?>" hidden>
                                                    <input type="number" class="form-control" name="invoice_no" value="<?php echo $row1['invoice_no'];?>" hidden>
                                                    <input type="text" class="form-control" name="payment_mode" value="<?php echo $row1['payment_mode'];?>" hidden>
                                                    <input type="number" class="form-control" name="total_amount" value="<?php echo $row1['naTotal'];?>" hidden>
                                                    <input type="number" class="form-control" name="discount" placeholder="Enter discount in %">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-action text-right">
                                            <!-- Confirmation modal -->													
                                            <a href="#" class="btn  btn-round btn-custom" id="saveUser" data-toggle="modal" data-target="#confirmation-modal">A p p r o v e</a>
                                            <div class="bg-red border-radius-4">
                                                <div class="modal fade" id="confirmation-modal" tabindex="-1" post="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" post="document">
                                                        <div class="modal-content">
                                                            <div class="modal-body text-center font-18">
                                                                <h4 class="padding-top-30 mb-30 weight-500">Are you sure you <br>want to <strong>APPROVE</strong> this invoice?</h4>
                                                                <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                                    <div class="col-6">
                                                                        <button type="button" class="btn btn-rounded btn-danger border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                                        <b>NO</b>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <button type="submit" name="approve-transaction" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" ><i class="fa fa-check"></i></button>
                                                                        <b>YES</b>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                           							
                                        </div>
                                    </form>
								</div>
							</div>
						</div>
                    </div>					
                </div>
            </div>
        </div>
    </div>
	<?php include('../include/scripts.php'); ?>
</body>		
</html>
