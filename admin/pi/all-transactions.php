<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
	header("location: ../../");
	exit;
}

include("../../include/config.php");


// Personal info variables
$reference = $route = $amount = $available_tickets = $success = "";
$reference_err = "";
$today =  date("Y-m-d");


    //  delete statement
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $mysqli->query("DELETE FROM pi_tbl_carts WHERE id=$id") or die($mysqli->connect_error);
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
                            footer:`<a class='btn btn-rounded btn-outline-info' href='all-transactions.php'>O K</a>`
                        });
                    }, false);
                    // End of the IIFE()
                })();
            </script>";				
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../../pi_include/metadata.php'); ?>
	<title>AMU GRV | All Transactions</title>
</head>
<body>
	<div class="wrapper horizontal-layout-3">
		<?php include('../../pi_include/header.php'); ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- List of Users  -->
						<div class="tab-pane fade show active" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
							<div class="col-lg-12">
								<h1>List of All Transactions</h1>
								<div class="card">
									<div class="card-body">
                                    <?php $result = $mysqli->query("SELECT * FROM pi_tbl_carts ORDER BY id DESC") or die($mysqli->connect_error); ?>
									<div class="table-responsive">
									<table id="transactions-table" class="display table table-striped table-hover" >
										<thead>
											<tr>
												<th>S/N</th>
												<th>Invoice</th>
												<th>Company</th>
												<th>Product</th>
												<th>Pric</th>
												<th>Qty</th>
												<th>Amount</th>
												<th>Status</th>
												<th>Date</th>
                                                <?php if($_SESSION["role"] == "Developer"):?>
                                                <th>Action</th>
                                                <?php else:?>
                                                <?php endif;?>
											</tr>
										</thead>
										<tbody>
											<?php $i=1;  while ($row = $result->fetch_assoc()):?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $row['invoice_no'];?></td>
												<td><?php echo $row['product_name'];?></td>
												<td><?php echo $row['product_cat'];?></td>
												<td><?php  $price = $row['price']; echo "&#8358;".number_format((float) $price);?></td>
												<td><?php echo $row['qty'];?></td>
												<td><?php  $amount = $row['amount']; echo "&#8358;".number_format((float) $amount);?></td>
												<td>
                                                    <?php if($row['status'] == 'Approved'): ?>
                                                    <span class="badge badge-success"><?php echo $row['status'];?></span>
                                                    <?php else: ?>
                                                    <span class="badge badge-danger"><?php echo $row['status'];?></span>
                                                    <?php endif; ?>
                                                </td>
												<td><?php $dte = $row['cart_date']; echo date('d/m/Y',strtotime($dte));?></td>
                                                <?php if($_SESSION["role"] == "Developer"):?>
                                                <td>
                                                    <div class="form-button-action">
                                                        <a href="#" class="btn btn-rounded" data-toggle="modal" data-target="#delete-modal<?php echo $row['id']; ?>" data-toggle="tooltip" data-placement="top" title="Delete Product"><i class="icon-trash text-danger"></i></a>
                                                        <div class="pd-5 bg-red border-radius-4">
                                                            <div class="modal fade" id="delete-modal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-body text-center font-18">
                                                                            <h4 class="padding-top-30 weight-500"><?php echo $row['product_name']."-".$row['product_cat']."<br>"."&#8358;".number_format((float) $row['amount']);?></h4>
                                                                            <h4 class="padding-top-30 mb-30 weight-500">Are you sure <br>you want to <strong class="text-danger">delete</strong> this product?</h4>
                                                                            <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                                                <div class="col-6">
                                                                                    <button type="button" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                                                    <b>NO</b>
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <a  href="all-transactions.php?delete=<?php echo $row['id']; ?>"  class="btn btn-rounded btn-danger confirmation-btn" ><i class="fa fa-trash-alt"></i></a>
                                                                                    <b>YES</b>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <?php else:?>
                                                <?php endif;?>
											</tr>
											<?php $i++; endwhile; ?>
										</tbody>
									</table>
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
    <?php include('../../pi_include/footer.php'); ?>
	<?php include('../../pi_include/scripts.php'); ?>
    <script>
         $('#transactions-table').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    exportOptions:{ columns:[0,1,2,3,4,5,6,7]},
                    className: 'btn btn-xs'
                },
                {
                    extend: 'pdf',
                    exportOptions:{ columns:[0,1,2,3,4,5,6,7]},
                    className: 'btn btn-xs mx-1'

                },
                {
                    extend: 'print',
                    exportOptions:{ columns:[0,1,2,3,4,5,6,7]},
                    className: 'btn btn-xs'
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
