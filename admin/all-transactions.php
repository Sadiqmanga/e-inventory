<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
	header("location: ../");
	exit;
}

include("../include/config.php");


// Personal info variables
$reference = $route = $amount = $available_tickets = $success = "";
$reference_err = "";
$today =  date("Y-m-d");

    // Processing form data when form is submitted
    if(isset($_POST["submit"])){

        // Prepare a select statement
        $sql = "SELECT * FROM tbl_payments WHERE reference = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_reference);
            $stmt->bind_param("s", $param_route);
            $stmt->bind_param("s", $param_amount);
            
            
            // Set parameters
            $param_route = strtoupper(trim($_POST['route']));
            $param_amount  = trim($_POST["amount"]);
            $param_reference  = trim($_POST["reference"]);

            // Caching temporary data for submission error (this is optional)
            $route = strtoupper(trim($_POST["route"]));
            $amount  = trim($_POST["amount"]);
            $reference  = trim($_POST["reference"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    
                    $reference_err =
                    "<script>
                        (function() {
                            window.addEventListener('load', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    html: 'The reference: <strong>$reference</strong> <br>is used before.',
                                    showConfirmButton: false,
                                    allowOutsideClick: true,
                                    footer:`<a class='btn btn-outline-info' onclick='Swal.close()' href='#'>O K</a>`
                                });
                            }, false);
                            // End of the IIFE()
                        })();
                    </script>
                    ";
                } else{
                    $route = strtoupper(trim($_POST["route"]));
                    $amount  = trim($_POST["amount"]);
                    $reference  = trim($_POST["reference"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
            
        // Close statement
        $stmt->close();

        
        // Check input errors before inserting in database
        if(empty($reference_err)){
            
            $result1 = mysqli_query($mysqli, "SELECT SUM(quantity) AS total FROM tbl_tickets WHERE route='$route' AND today_date='$today'");
            $row1 = mysqli_fetch_assoc($result1); 
            $available_tickets = $row1['total'];
    
            $sql2="SELECT count('1') FROM tbl_payments WHERE route='$route' AND date='$today'";
            $result2=mysqli_query($mysqli,$sql2);
            $sold_ticket = mysqli_fetch_array($result2);
            $sold_tickets = $sold_ticket[0];

            // Prepare an insert statement
            if($sold_tickets >= $available_tickets || $available_tickets == 0){
                echo
                "<script>
                    (function() {
                        window.addEventListener('load', function() {
                            Swal.fire({
                                title: 'Alert!',
                                html: 'No available ticket for $route',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                footer:`<a class='btn btn-sm btn-outline-info' href='index.php'>O K</a>`
                            });
                        }, false);
                    })();
                </script>
                ";
            }else{
                $sql = "INSERT INTO tbl_payments (reference, route, amount) VALUES (?, ?, ?)";
                if($stmt = $mysqli->prepare($sql)){
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("sss", $param_reference, $param_route, $param_amount);
                    
                    // Set parameters
                    $random_string = substr(str_shuffle(str_repeat("QWERTYUIOPLKJHGFDSAZXCVBNM", mt_rand(1,4))), 1, 4)."".substr(str_shuffle(str_repeat("1234567890", mt_rand(1,5))), 1, 5);
                    $param_reference = $random_string;
                    $param_route = $route;
                    $param_amount = $amount;
                    // Attempt to execute the prepared statement
                    if($stmt->execute()){
                        $sold_tickets_plus_one = $sold_tickets + 1;
						// USE THIS TO TRIGGER THE PRINT MODAL
						$success = TRUE;

                    //     echo
                    //         "<script>
                    //             (function() {
                    //                 window.addEventListener('load', function() {
                    //                     Swal.fire({
                    //                         title: 'Ticket paid successful!',
                    //                         html: 'Ticket No.: <strong>$random_string</strong> <h3>$route</h3>Available Tickets: $available_tickets <br> Sold Tickets: $sold_tickets_plus_one',
                    //                         icon: 'success',
                    //                         showConfirmButton: false,
                    //                         allowOutsideClick: false,
                    //                         footer:`<a class='btn btn-sm btn-outline-info' href='index.php'>O K</a>`
                    //                     });
                    //                 }, false);
                    //             })();
                    //         </script>
                    //         ";
                    } else{
                        echo
                            "<script>
                                (function() {
                                    window.addEventListener('load', function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops!',
                                            text: 'Something went wrong. Please try again',
                                            showConfirmButton: false,
                                            allowOutsideClick: true,
                                            footer:`<a class='btn btn-sm btn-outline-info' onclick='Swal.close()' href='#'>O K</a>`
                                        });
                                    }, false);
                                })();
                            </script>
                            ";
                    }
                }
            }
            // Close statement
            // $stmt->close();
        }
        
        // Close connection
        // $mysqli->close();
    }

    //  delete statement
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $mysqli->query("DELETE FROM tbl_carts WHERE id=$id") or die($mysqli->connect_error);
        echo
            "<script>
                (function() {
                    window.addEventListener('load', function() {
                        Swal.fire({
                            html: 'product deleted successfully!',
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
    <?php include('../include/metadata.php'); ?>
	<title>AMU GRV | All Transactions</title>
    <style>
		.form-control:disabled, .form-control[readonly] {
			background: #ffffff !important;
			border-color: #ffffff !important;
		}
        #invoice {
            width: 8.4cm;
            height: 5.2cm;
            padding: 0.5cm
        }
        .barcode {
            text-align: center;
        }
		.invoice-no {
            letter-spacing: 8px;
            margin-bottom: 5px!important;
        }
        .last {
            color: white;
            background-color: black;
            font-family: "Segoe Script";
            width: 200px;
            font-size: 14px;
            margin-left: auto;
            margin-right: auto;
        }
		.modal-dialog {
			max-width: 350px;
		}
    </style>
</head>
<body>
	<div class="wrapper horizontal-layout-3">
		<?php include('../include/header.php'); ?>

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
                                    <?php $result = $mysqli->query("SELECT * FROM tbl_carts ORDER BY id DESC") or die($mysqli->connect_error); ?>
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
												<td><?php $dte = $row['cart_date']; echo date('D, jS M Y',strtotime($dte));?></td>
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
    <?php include('../include/footer.php'); ?>
	<?php include('../include/scripts.php'); ?>
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
