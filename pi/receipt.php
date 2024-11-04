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

$invoice_ref = $_GET["ref"];

if (empty($invoice_ref)) {
	header("location: view-invoices.php");
}

$result1 = $mysqli->query(
    "SELECT a.invoice_no, a.product_cat, a.product_name, b.user_id, b.payment_mode, b.cname, b.cphone, b.caddress, c.total_amount, c.discount,  a.status, d.fullname, c.date_created 
        FROM pi_tbl_carts AS a
        JOIN pi_tbl_inv_detail AS b
        ON a.invoice_no = b.invoice_no
        JOIN pi_tbl_transactions AS c
        ON b.invoice_no = c.invoice_no
        JOIN tbl_users AS d
        ON b.user_id = d.id
        WHERE c.invoice_no=$invoice_ref;") or die($mysqli->connect_error); 
        $row1 = $result1->fetch_assoc();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../include/metadata.php'); ?>
	<title>Generated Invoice for <?php echo $row1['cname'];?></title>
    <style>
        .tb-fs {
            font-size: 50px;
            padding-top: 15px;
            padding-bottom: 15px;
        }
        @media print{
            .no-print, .no-print *
            {
                display: none!important;
            }
        }
        .font-custom1{
            font-size: 20px;
        }
        table,th,td{
            font-size: 16px;
            padding: 7px;
        }
    </style>
</head>
<body>
	<div class="wrapper horizontal-layout-3">
		<?php // include('../include/header.php'); ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-10 col-xl-9">
							<div class="row align-items-center no-print">
								<div class="col">
									<h6 class="page-pretitle">
										Payments
									</h6>
									<h4 class="page-title">Invoice #<?php echo $invoice_ref;?></h4>
								</div>
								<div class="col-auto">
									<a href="index.php" class="btn btn-outline-secondary">
										Back
									</a>
									<a href="#" onclick="window.print()" class="btn btn-custom ml-2">
										Print
									</a>
								</div>
							</div>
							<div class="page-divider"></div>
							<div class="row">
								<div class="col-md-12">
									<div class="card card-invoice">
										<div class="card-header">
											<div class="invoice-header">
												<h3 class="invoice-title">
													Invoice/Cash Receipt
												</h3>
												<div class="invoice-logo">
													<img src="../assets/img/icon.jpg" alt="company logo">
												</div>
											</div>
											<div class="invoice-desc">
												<strong class="font-custom1">AMU GLOBAL RESOURCE VENTURES 
                                                    <br>Partner of Prime International Ltd.
                                                </strong><br/>
                                               <strong>Tel: 08036015175 | 09038433575</strong> 
											</div>
										</div>
										<div class="card-body">
											<div class="separator-solid"></div>
											<div class="row">
												<div class="col-3 info-invoice">
													<h5 class="sub">Date</h5>
													<p>
                                                        <?php 
                                                            $datesd = $row1['date_created'];
                                                            echo date('D, jS F Y',strtotime($datesd));
                                                        ?>
                                                    </p>
												</div>
												<div class="col-4 info-invoice">
													<h5 class="sub">Invoice To</h5>
													<p><?php echo $row1['cname'];?><br><?php echo $row1['cphone'];?></p>
												</div>
												<div class="col-5 info-invoice">
													<h5 class="sub">Address</h5>
													<p>
                                                        <?php echo $row1['caddress'];?> 
													</p>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="invoice-detail">
														<div class="invoice-top">
															<h3 class="title"><strong class="font-custom1">Order summary</strong></h3>
														</div>
														<div class="invoice-item">
                                                            <?php
                                                                $result2 = $mysqli->query(
                                                                    "SELECT a.id, a.product_id, a.invoice_no, a.product_name, a.product_cat, a.price, a.qty, a.amount, a.status, b.payment_mode, b.date_created 
                                                                    FROM pi_tbl_carts AS a
                                                                    JOIN pi_tbl_inv_detail AS b
                                                                    ON a.invoice_no = b.invoice_no
                                                                    WHERE a.invoice_no=$invoice_ref;") or die($mysqli->connect_error); 
                                                            ?>
                                                            <table class=""  width="100%">
                                                                <thead style="border-bottom: 1px solid #0c3133;">
                                                                    <tr style="border-bottom: 1px solid #0c3133;">
                                                                        <th class="text-center">S/N</th>
                                                                        <th class="text-center">Product</th>
                                                                        <th class="text-center">Gram</th>
                                                                        <th class="text-center">Price</th>
                                                                        <th class="text-center">Qty</th>
                                                                        <th class="text-right">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $i=1;  while ($row2 = $result2->fetch_assoc()):?>
                                                                    <tr style="border-bottom: 1px solid #0c3133;">
                                                                        <td  class="text-center"><?php echo $i; ?></td>
                                                                        <td class="text-center"><?php echo $row2['product_name'];?></td>
                                                                        <td class="text-center"><?php echo $row2['product_cat'];?></td>
                                                                        <td class="text-center"><?php $rprice = $row2['price']; echo "&#8358;".number_format((float) $rprice);?></td>
                                                                        <td class="text-center"><?php echo $row2['qty'];?></td>
                                                                        <td class="text-right"><?php $ramount = $row2['amount']; echo "&#8358;".number_format((float) $ramount);?></td>
                                                                    </tr>
                                                                    <?php $i++; endwhile; ?>
                                                                    <tr style="border-bottom: 1px solid #0c3133;">
                                                                        <td colspan="4"></td>
                                                                        <td class="text-right"><strong>Subtotal</strong></td>
                                                                        <td class="text-right"><?php $row1SubTotal = $row1['total_amount']; echo "&#8358;".number_format((float) $row1SubTotal,2);?></td>
                                                                    </tr>
                                                                    <tr style="border-bottom: 1px solid #0c3133;">
                                                                        <td colspan="4"></td>
                                                                        <td class="text-right"><strong>Discount</strong></td>
                                                                        <td class="text-right"><?php $row1Discount = $row1['discount']; echo "&#8358;".number_format((float) $row1Discount,2);?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
														</div>
													</div>	
													<div class="separator-solid  mb-3"></div>
												</div>	
											</div>
										</div>
										<div class="card-footer">
											<div class="row">
												<div class="col-6 col-6 mb-3 mb-md-0 transfer-to">
													<h5 class="sub">Sales : <?php echo $row1['fullname'];?></h5>
													<div class="account-transfer">
                                                        <div><span>Payment Mode:</span><span><?php echo $row1['payment_mode'];?></span></div>
														<div><span>Status:</span><span class="badge badge-info"><?php echo $row1['status'];?></span></div>
														<div><span>Reference:</span><span><?php echo $invoice_ref;?> <i class="icon-check"></i><strong>Paid</strong></span></div>
													</div>
												</div>
												<div class="col-6 col-6 transfer-total">
													<h5 class="sub">Total Amount</h5>
													<div class="price text-dark" style="font-size: 35px;"><?php $grandTotal=$row1SubTotal-$row1Discount; echo "&#8358;".number_format((float) $grandTotal,2);?></div>
													<span>Discount Included</span>
												</div>
											</div>
											<div class="separator-solid"></div>
											<p class="mb-0">
                                                <span class="text-danger">No refund of money after payment.</span> 
                                                | We appreciate doing business with you.<br>
                                                Thank you for your patronage. <span class="float-right text-small">Printed on: <?php echo date('jS M Y | h:i a');?></span><br><hr>
                                                <span class="float-right text-small">&copy;<?php echo date("Y");?> | Powered by <a href="https://sitsng.com">https://sitsng.com</a></span>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
    <?php // include('../include/footer.php'); ?>
	<?php include('../include/scripts.php'); ?>
    <script>
         $('#cars-table').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    exportOptions:{ columns:[0,1,2,3,4]},
                    className: 'btn btn-sm'
                },
                {
                    extend: 'pdf',
                    exportOptions:{ columns:[0,1,2,3,4]},
                    className: 'btn btn-sm mx-1'

                },
                {
                    extend: 'print',
                    exportOptions:{ columns:[0,1,2,3,4]},
                    className: 'btn btn-sm'

                }
            ],
            "pageLength": 20,
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select class="form-control"><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                            );

                        column
                        .search( val ? '^'+val+'$' : '', true, false )
                        .draw();
                    } );

                    column.data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                } );
            }
        });
    </script>
</body>		
</html>
