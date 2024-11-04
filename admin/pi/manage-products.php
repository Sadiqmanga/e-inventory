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
	header("location: ../../login.php");
	exit;
}

include("../../include/config.php");

// Defining dommy variables
$category =  $name = $unit_price = $product_err = "";

// Processing form data when form is submitted
if(isset($_POST['add-product'])){

    $sql = "SELECT * FROM pi_tbl_products WHERE category = ? AND name = ?";
    
    if($stmt = $mysqli->prepare($sql)){
        $stmt->bind_param("ss", $param_category, $param_name);

	    $param_unit_price  = trim($_POST["unit_price"]);
	    $param_name  = trim($_POST["name"]);
	    $param_category  = trim($_POST["category"]);

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
    $id = $_GET['delete'];
    $mysqli->query("DELETE FROM pi_tbl_products WHERE id=$id") or die($mysqli->connect_error);
    echo
        "<script>
            (function() {
                window.addEventListener('load', function() {
                    Swal.fire({
                        html: 'Product deleted successfully!',
                        icon: 'error',
                        showConfirmButton: false,
                        allowOutsideClick: true,
                        // delay: 4000,
                        footer:`<a class='btn btn-rounded btn-custom' href='manage-products.php'>O K</a>`
                    });
                }, false);
                // End of the IIFE()
            })();
        </script>";				
}

// Update statement
if (isset($_POST['update-product'])) {
	$id  = trim($_POST["id"]);
	$unit_price  = trim($_POST["unit_price"]);
	$status  = trim($_POST["status"]);

	if($mysqli->query("UPDATE pi_tbl_products SET unit_price='$unit_price', status='$status' WHERE id=$id")){
		echo
        "<script>
            (function() {
                window.addEventListener('load', function() {
                    Swal.fire({
                        html: 'Products updated successfully!',
                        icon: 'success',
                        showConfirmButton: false,
                        allowOutsideClick: true,
                        footer:`<a class='btn btn-round btn-custom' href='manage-products.php'>O K</a>`
                    });
                }, false);
                // End of the IIFE()
            })();
        </script>";
	}else{
		echo
		"<script>
			(function() {
				window.addEventListener('load', function() {
					Swal.fire({
						title: 'Oops! Something went wrong',
						icon: 'error',
						showConfirmButton: false,
						allowOutsideClick: true,
						footer:`<a class='btn btn-rounded btn-outline-info' onclick='Swal.close()' href='#'>O K</a>`
					});
				}, false);
				// End of the IIFE()
			})();
		</script>";
	}	

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../../pi_include/metadata.php'); ?>
	<title>List of Stock Products</title>
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
		<?php include('../../pi_include/header.php'); ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
				<ul class="nav nav-pills nav-black nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="pills-one-tab-nobd" data-toggle="pill" href="#pills-one" 
							role="tab" aria-controls="pills-one" aria-selected="true">All Products</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-two-tab-nobd" data-toggle="pill" href="#pills-two" 
							role="tab" aria-controls="pills-two" aria-selected="false">Add Product</a>
						</li>
					</ul>
					<div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- List of Shoes  -->
						<div class="tab-pane fade show active" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
                            <h1>List of Products</h1>
							<div class="col-lg-8 ml-auto mr-auto">
								<div class="card">
									<div class="card-body">
                                        <?php $result = $mysqli->query("SELECT * FROM pi_tbl_products ORDER BY id DESC") or die($mysqli->connect_error); ?>
                                        <table id="cars-table" class="display table table-responsive table-striped table-hover" >
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Company</th>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i=1;  while ($row = $result->fetch_assoc()):?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $row['category'];?></td>
                                                    <td><?php echo $row['name'];?></td>
                                                    <td><?php  $prc = $row['unit_price'];echo number_format((float) $prc)?></td>
                                                    <td>
                                                        <?php if($row['status'] == 'Active'): ?>
                                                        <span class="badge badge-success"><?php echo $row['status'];?></span>
                                                        <?php else: ?>
                                                        <span class="badge badge-danger"><?php echo $row['status'];?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <!-- Delete modal -->
                                                    <td>
                                                        <div class="form-button-action">
                                                            <!-- Edit Modal -->
                                                            <a href="#" class="btn btn-rounded" data-toggle="modal" data-target="#edit-modal<?php echo $row['id']; ?>" data-toggle="tooltip" data-placement="top" title="Edit Products"><i class="icon-note"></i></a>
                                                            <div class="pd-20 bg-red border-radius-4">
                                                                <div class="modal fade" id="edit-modal<?php echo $row['id'];?>" tabindex="-1" post="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" post="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-body text-center font-18">
                                                                            <h4 class="padding-top-30 weight-500"><strong><?php echo $row['category']."-".$row['name'];?></strong></h4>
                                                                                <div class="padding-bottom-30" style="max-width: 250px; margin: 0 auto;">
                                                                                    <form action="manage-products.php" method="post">
                                                                                        <div class="form-group">
                                                                                            <div class="form-group">
                                                                                                <label for="unit_price">Price</label>
                                                                                                <input type="number" class="form-control" name="unit_price" value="<?php echo $row['unit_price']; ?>">
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <select name="status" class="form-control">
                                                                                                    <option value="<?php echo $row['status']; ?>"><?php echo $row['status']; ?></option>
                                                                                                    <option value="Active">Activate</option>
                                                                                                    <option value="Inactive">Deactivate</option>
                                                                                                </select>
                                                                                            </div>
                                                                                            <input type="text" name="id" value="<?php echo $row['id']; ?>" hidden>
                                                                                        </div>
                                                                                        <button type="submit" name="update-product" class="btn btn-block btn-custom btn-rounded confirmation-btn" >Update Product</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Delete Modal  -->
                                                            <a href="#" class="btn btn-rounded" data-toggle="modal" data-target="#delete-modal<?php echo $row['id']; ?>" data-toggle="tooltip" data-placement="top" title="Delete Product"><i class="icon-trash text-danger"></i></a>
                                                            <div class="pd-5 bg-red border-radius-4">
                                                                <div class="modal fade" id="delete-modal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-body text-center font-18">
                                                                                <h4 class="padding-top-30 mb-30 weight-500">Are you sure <br>you want to <strong class="text-danger">delete</strong> this Product?</h4>
                                                                                <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                                                    <div class="col-6">
                                                                                        <button type="button" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                                                        <b>NO</b>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <a  href="manage-products.php?delete=<?php echo $row['id']; ?>"  class="btn btn-rounded btn-danger confirmation-btn" ><i class="fa fa-trash-alt"></i></a>
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
                        <!-- Add New Product tab -->
						<div class="tab-pane fade" id="pills-two" role="tabpanel" aria-labelledby="pills-two-tab">
							<form method="POST">
                                <h1>Add New Product</h1>
                                <div class="col-md-4 ml-auto mr-auto">
                                    <div class="form-group" <?php echo (!empty($product_err)) ? 'has-error' : ''; ?>>
                                        <label for="category">Product Company</label>
                                        <input type="text" class="form-control" name="category" value="<?php echo $category?>" placeholder="<?php if (empty($param_category)) {echo "Enter company name";}else{echo $param_category;}?>" required>	
                                    </div>
                                    <div class="form-group <?php echo (!empty($product_err)) ? 'has-error' : ''; ?>">
                                        <label for="name">Product code</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo $name?>" placeholder="<?php if (empty($param_name)) {echo "Enter product unique code";}else{echo $param_name;}?>" required>	
                                    </div>
                                    <div class="form-group">
                                        <label for="unit_price">Price</label>
                                        <input type="number" class="form-control" name="unit_price" value="<?php echo $unit_price?>" placeholder="<?php if (empty($param_unit_price)) {echo "Enter product price";}else{echo $param_unit_price;}?>" required>	
                                    </div>
                                    <div class="card-action mt-4 text-right">
                                        <!-- Confirmation modal -->													
                                        <a href="#" class="btn  btn-round btn-custom" id="saveUser" data-toggle="modal" data-target="#confirmation-modal">Add Product</a>
                                        <div class="pd-20 bg-red border-radius-4">
                                            <div class="modal fade" id="confirmation-modal" tabindex="-1" post="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" post="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body text-center font-18">
                                                            <h4 class="padding-top-30 mb-30 weight-500">Are you sure <br> you want continue?</h4>
                                                            <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                                <div class="col-6">
                                                                    <button type="button" class="btn btn-rounded btn-danger border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                                    <b>NO</b>
                                                                </div>
                                                                <div class="col-6">
                                                                    <button type="submit" name="add-product" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" ><i class="fa fa-check"></i></button>
                                                                    <b>YES</b>
                                                                </div>
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
    <?php include('../../pi_include/footer.php'); ?>
	<?php include('../../pi_include/scripts.php'); ?>
    <script>
         $('#cars-table').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    exportOptions:{ columns:[0,1,2,3,4]},
                    className: 'btn btn-xs'
                },
                {
                    extend: 'pdf',
                    exportOptions:{ columns:[0,1,2,3,4]},
                    className: 'btn btn-xs mx-1'

                },
                {
                    extend: 'print',
                    exportOptions:{ columns:[0,1,2,3,4]},
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
