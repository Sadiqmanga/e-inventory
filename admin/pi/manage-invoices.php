<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
	header("location: ../../login.php");
	exit;
}else{
    if ($_SESSION["role"] == "Developer" || $_SESSION["role"] == "Admin" || $_SESSION["role"] == "Assistant Admin") {
    }else {
        header("location: index.php");
    }
}

include("../../include/config.php");

// Defining dommy variables
$category =  $name = $unit_price = $product_err = "";
$today = date('Y-m-d');

// Processing form data when form is submitted
if(isset($_POST['add-product'])){

    $sql = "SELECT * FROM pi_tbl_products WHERE name = ?";
    
    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $param_category);
        $stmt->bind_param("s", $param_unit_price);
        $stmt->bind_param("s", $param_name);

	    $param_category  = trim($_POST["category"]);
	    $param_unit_price  = trim($_POST["unit_price"]);
	    $param_name  = trim($_POST["name"]);

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // store result
            $stmt->store_result();
            
            if($stmt->num_rows == 1){
                echo $product_err = 
                "<script>
                    (function() {
                        window.addEventListener('load', function() {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Oops!',
                                html: 'The product: <strong>$param_category-$param_name</strong> <br> is already added',
                                showConfirmButton: false,
                                allowOutsideClick: true,
                                // delay: 4000,
                                footer:`<a class='btn btn-rounded btn-custom' onclick='Swal.close()' href='#'>O K</a>`
                            });
                        }, false);
                        // End of the IIFE()
                    })();
                </script>
                ";
            } else{
                $category = strtoupper(trim($_POST['category']));
                $name = strtoupper(trim($_POST['name']));
                $unit_price = trim($_POST['unit_price']);
                $status = "Active";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    if (empty($product_err)) {
        $sql2 = "INSERT INTO pi_tbl_products(category,name,unit_price,status) VALUES(?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql2);
        $stmt->bind_param("ssis", $category, $name, $unit_price, $status);
        if($stmt->execute()) {
            echo 
                "<script>
                    (function() {
                        window.addEventListener('load', function() {
                            Swal.fire({
                                icon: 'success',
                                html: 'Product added successfully!',
                                showConfirmButton: false,
                                allowOutsideClick: true,
                                // delay: 4000,
                                footer:`<a class='btn btn-rounded btn-custom' onclick='Swal.close()' href='manage-products.php'>O K</a>`
                            });
                        }, false);
                        // End of the IIFE()
                    })();
                </script>
                ";
        }else{
            die("Error: ".mysqli_error($mysqli));
        }
    }
}

 //  delete statement
 if (isset($_GET['delete'])) {
    $invoice_no = $_GET['delete'];
    $mysqli->query("DELETE FROM pi_tbl_carts WHERE invoice_no=$invoice_no");
    $mysqli->query("DELETE FROM pi_tbl_inv_detail WHERE invoice_no=$invoice_no");
    $mysqli->query("DELETE FROM pi_tbl_transactions WHERE invoice_no=$invoice_no");
    echo
        "<script>
            (function() {
                window.addEventListener('load', function() {
                    Swal.fire({
                        html: 'Transaction declined successfully',
                        icon: 'error',
                        showConfirmButton: false,
                        allowOutsideClick: true,
                        // delay: 4000,
                        footer:`<a class='btn btn-rounded btn-outline-info' href='manage-invoices.php'>O K</a>`
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
	<title>AMU GRV | Manage Invoices</title>
    <style>
        .tb-fs {
            font-size: 50px;
            padding-top: 15px;
            padding-bottom: 15px;
        }
        .badge1{
            height: 25px;
            padding-top: 6px;
            margin-top:10px;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
	<div class="wrapper horizontal-layout-3">
		<?php include('../../pi_include/header.php'); ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
				    <ul class="nav nav-pills nav-black nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="pills-one-tab-nobd" data-toggle="pill" href="#pills-one" 
							role="tab" aria-controls="pills-one" aria-selected="true">Pending Invoices</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-two-tab-nobd" data-toggle="pill" href="#pills-two" 
							role="tab" aria-controls="pills-two" aria-selected="false">Approved Invoices</a>
						</li>
					</ul>
					<div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- List of Pending  -->
						<div class="tab-pane fade show active" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
                            <h1>List of pending invices</h1>
							<div class="col-lg-10 ml-auto mr-auto">
								<div class="card">
									<div class="card-body">
                                        <?php
                                            $result = $mysqli->query(
                                                "SELECT a.id, a.invoice_no, SUM(amount) AS naTotal, a.status, b.payment_mode, b.cname,b.caddress,b.date_created 
                                                FROM pi_tbl_carts AS a
                                                JOIN pi_tbl_inv_detail AS b
                                                ON a.invoice_no = b.invoice_no 
                                                WHERE a.status='Pending'
                                                GROUP BY 2
                                                ORDER BY a.id DESC") or die($mysqli->connect_error);
                                        ?>
                                        <table class="display table table-responsive table-striped table-hover" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Invoice No.</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i=1;  while ($row = $result->fetch_assoc()):?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $row['invoice_no'];?></td>
                                                    <td><?php  $naTotal = $row['naTotal']; echo "&#8358;".number_format($naTotal);?></td>
                                                    <td>
                                                        <?php if($row['status'] == 'Approved'): ?>
                                                        <span class="badge badge-success"><?php echo $row['status'];?></span>
                                                        <?php else: ?>
                                                        <span class="badge badge-danger"><?php echo $row['status'];?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                         $datesd = $row['date_created'];
                                                         echo date('d/m/Y',strtotime($datesd));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <!-- View Invoice Link -->
                                                            <a href="edit-invoice.php?ref=<?php echo $row['invoice_no']; ?>" class="btn btn-rounded" data-toggle="tooltip" data-placement="top" title="View Invoice"><i class="icon-eye"></i></a>
                                                            <!-- Delete Modal  -->
                                                            <a href="#" class="badge badge-danger badge1" data-toggle="modal" data-target="#delete-modal<?php echo $row['invoice_no']; ?>" data-toggle="tooltip" data-placement="top" title="Decline Transaction"><i class="icon-trash"></i> Decline</a>
                                                            <div class="pd-5 bg-red border-radius-4">
                                                                <div class="modal fade" id="delete-modal<?php echo $row['invoice_no']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-body text-center font-18">
                                                                                <h4 class="padding-top-30 weight-500"><?php echo $row['cname']."<br>"."&#8358;".number_format((float) $naTotal);?></h4>
                                                                                <h4 class="padding-top-30 mb-30 weight-500">Are you sure <br>you want to <strong class="text-danger">decline</strong> this invoice?</h4>
                                                                                <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                                                    <div class="col-6">
                                                                                        <button type="button" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                                                        <b>NO</b>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <a  href="manage-invoices.php?delete=<?php echo $row['invoice_no']; ?>"  class="btn btn-rounded btn-danger confirmation-btn" ><i class="fa fa-trash-alt"></i></a>
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
                                                </tr>
                                                <?php $i++; endwhile; ?>
                                            </tbody>
                                        </table>
									</div>
								</div>
							</div>
						</div>
                        <!-- Lisst of approved invoices -->
						<div class="tab-pane fade" id="pills-two" role="tabpanel" aria-labelledby="pills-two-tab">
                            <h1>List of Approved Invoices</h1>
							<div class="col-lg-10 ml-auto mr-auto">
								<div class="card">
									<div class="card-body">
                                        <?php
                                            
                                            $result6 = $mysqli->query(
                                                "SELECT c.id, a.status,c.invoice_no,c.total_amount AS aTotal,c.date,b.date_created
                                                    FROM pi_tbl_carts AS a
                                                    JOIN pi_tbl_inv_detail AS b
                                                    ON a.invoice_no = b.invoice_no 
                                                    JOIN pi_tbl_transactions AS c
                                                    ON b.invoice_no=c.invoice_no
                                                    WHERE a.status='Approved' AND c.date='$today'
                                                    GROUP BY 3
                                                    ORDER BY c.id DESC
                                                    ") or die($mysqli->connect_error); 
                                        ?>
                                        <table class="display table table-responsive table-striped table-hover" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Invoice No.</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i=1;  while ($row6 = $result6->fetch_assoc()):?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $row6['invoice_no'];?></td>
                                                    <td><?php  $aTotal = $row6['aTotal']; echo "&#8358;".number_format((float) $aTotal,2);?></td>
                                                    <td>
                                                        <?php if($row6['status'] == 'Approved'): ?>
                                                        <span class="badge badge-success"><?php echo $row6['status'];?></span>
                                                        <?php else: ?>
                                                        <span class="badge badge-danger"><?php echo $row6['status'];?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            $datesd = $row6['date_created'];
                                                            echo date('D, jS M Y',strtotime($datesd));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <!-- View Invoice Link -->
                                                            <a href="receipt.php?ref=<?php echo $row6['invoice_no']; ?>" class="btn btn-rounded" data-toggle="tooltip" data-placement="top" title="Print Invoice"><i class="icon-printer"></i></a>
                                                        </div>
                                                    </td>
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
    <?php include('../../pi_include/footer.php'); ?>
	<?php include('../../pi_include/scripts.php'); ?>
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
