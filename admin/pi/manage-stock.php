<?php

require_once("../../pi_includes/config.php");
  session_start();
  if ($_SESSION['loggedin']==FALSE) {
          header("location:../../");
        }
  $conn = new DB_Class();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../../pi_include/metadata.php'); ?>
<script src="../../assets/pdo/js/jquery-3.5.1.min.js"></script>
<script src="../../assets/pdo/js/custom.js"></script>
<link rel="stylesheet" href="../../assets/pdo/css/style.css">


	<title>List of Stock Items</title>
    <style>
        .tb-fs {
            font-size: 50px;
            padding-top: 15px;
            padding-bottom: 15px;
        }
    </style>
     <script>
    $(document).ready(function () {
    $('#custList').DataTable();
    $('.dataTables_length').addClass('bs-select');
  });
  </script>
</head>
<body>
	<div class="wrapper horizontal-layout-3">
		<?php // include('../include/header.php'); ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
				    <ul class="nav nav-pills nav-black nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
						<li class="nav-item">
							<a class="btn btn-round btn-custom" href="index.php"><i class="icon-home"></i> Home</a>
						</li>
                        <li class="nav-item">
							<a class="nav-link active" id="pills-one-tab-nobd" data-toggle="pill" href="#pills-one" 
							role="tab" aria-controls="pills-one" aria-selected="true">Manage Stock</a>
						</li>
					</ul>
					<div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- List of Available Stock  -->
						<div class="tab-pane fade show active" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
                            <h1>List of Available Stock Items</h1>
							<div class="col-lg-8 ml-auto mr-auto">
								<div class="card">
									<div class="card-body">
                                        <?php
                                            if (isset($_POST['add'])) {
                                            $prod = trim($_POST['product']);
                                            $prod_id = trim($_POST['category']);
                                            $new_stock = trim($_POST['stock']);

                                            if (!empty($prod_id) AND !empty($new_stock)) {
                                                $newStock = $conn->addNewStock($prod_id,$new_stock);
                                                if ($newStock) {
                                                // echo "<div class='alert alert-success text-center'>New Stock Added!</div>";
                                                    echo 
                                                        "<script>
                                                            (function() {
                                                                window.addEventListener('load', function() {
                                                                    Swal.fire({
                                                                        icon: 'success',
                                                                        html: 'Stock added successfully.',
                                                                        showConfirmButton: false,
                                                                        allowOutsideClick: true,
                                                                        // delay: 4000,
                                                                        footer:`<a class='btn btn-rounded btn-outline-info' onclick='Swal.close()' href='#'>O K</a>`
                                                                    });
                                                                }, false);
                                                                // End of the IIFE()
                                                            })();
                                                        </script>
                                                        ";
                                                }
                                                else{
                                                echo "<div class='alert alert-warning text-center'><strong>Oops!</strong> an error occured</div>";
                                                }
                                            }
                                            else{
                                                echo "<div class='alert alert-danger text-center'>All required fields must be completed</div>";
                                            }
                                            }
                                        ?>  
                                        <button type="button" id="formButton" class="btn btn-custom btn-sm btn-outline-secondary mb-2 mr-sm-2">Add Stock</button>
                                        <form id="form1" method="POST">
                                            <div class="form-inline" id="addYard">
                                                <select name="product" id="product" class="form-control mb-2 mr-sm-2"  required>
                                                    <option value="">Select company</option>
                                                    <!-- <option value="SLAI O'LAI STRAWBERRY (BOX)">Selected</option> -->
                                                    <?php 
                                                        foreach ($conn->product_dropdown() as $productInfo) {
                                                            echo "<option value='".$productInfo['category']."'>".$productInfo['category']."</option>";
                                                        }
                                                    ?>                
                                                </select>                       
                                                <select name="category" id="productName" class="form-control mb-2 mr-sm-2" required>
                                                        <!-- options are filled by javascript  -->
                                                </select>
                                            <input type="number" name="stock" class="form-control mb-2 mr-sm-2" placeholder="Stock In" required>
                                            <button type="submit" id="submit" name="add" class="btn btn-round btn-custom mr-sm-2">Add</button>
                                            </div>
                                        </form> 
                                        <table id="stock-table" class="table table-responsive" width="100%">
                                            <thead>
                                                <tr>
                                                <th class="th-sm">S/N</th>
                                                <th class="th-sm">Company</th>
                                                <th class="th-sm">Product</th>
                                                <th class="th-sm">Available Stock
                                                </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                $sl = $conn->stock_list();
                                                if ($sl) {
                                                    foreach ($sl as $st_list){
                                                
                                                        // stUCT LIST VARIABLES 
                                                        $pId = $st_list['id'];
                                                        $pName = $st_list['name'];
                                                        $pCategory = $st_list['category'];
                                                        $_cart = $conn->totalCart($pId);
                                                        $_stock = $conn->totalStock($pId);

                                                        $qty = $_stock['total_stock'] - $_cart['total_cart'];

                                                        if ($qty > 0) {
                                                            echo 
                                                            "<tr>
                                                                <td>$i</td>
                                                                <td>$pCategory</td>
                                                                <td>$pName</td>
                                                                <td>$qty</td>
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
						</div>
                    </div>					
                </div>
            </div>
        </div>
    </div>
	<?php include('../../pi_include/scripts.php'); ?>
    <script>
      $(document).ready(function () {
          $('#product').on('change',function(e){
              var productCategory = "id="+$(this).val();
              console.log(productCategory);
              $.ajax({
                  type: 'POST',
                  url: '../../pi_includes/loadAjax.php',
                  data: productCategory,
                  dataType:'json',
                  cache:false,
                  success: function (response) {
                      console.log(response);
                      if(response!="empty"){
                          $('#productName').empty();
                          $('#productName').append("<option value=''>Select product</option>");
                          $.each(response, function(key,value) {
                              $('#productName').append("<option value='"+value['id']+"'>"+value['name'].trim()+"</option>");
                          });
                      }
                      else{
                          $('#productName').empty();
                          $('#productName').append("<option value=''>Select product</option>");
                      }
                  },
                  error:function(request,error){
                      console.log(error);
                  }
              });
          }); 
      });
    </script>
    <script>
         $('#stock-table').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    exportOptions:{ columns:[0,1,2,3]},
                    className: 'btn btn-xs'
                },
                {
                    extend: 'pdf',
                    exportOptions:{ columns:[0,1,2,3]},
                    className: 'btn btn-xs mx-1'

                },
                {
                    extend: 'print',
                    exportOptions:{ columns:[0,1,2,3]},
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
