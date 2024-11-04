<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
	header("location: ../");
	exit;
}

include("../include/config.php");
$amount = "";
$search = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$search = true;
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];

	$a = date('jS M Y',strtotime($date1));										
	$b = date('jS M Y',strtotime($date2));										
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../include/metadata.php'); ?>
	<title>AMU GRV | Transaction Report from <?php echo $a." to ".$b; ?></title>
    <style>
        .text-custon{
            font-size: 20px; 
            font-weight:bold
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
							<a class="nav-link active" id="pills-one-tab-nobd" data-toggle="pill" href="#pills-one" 
							role="tab" aria-controls="pills-one" aria-selected="true">All Transactions</a>
						</li>
					</ul>
					<div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- List of payment by Cashiers  -->
						<div class="tab-pane fade show active" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
                            <h1>Transaction Report</h1>
							<div class="col-lg-12 ml-auto mr-auto">
								<div class="card">
									<div class="card-body">
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>From</label>
                                                        <input type="date" class="form-control" name="date1"  value="<?php echo $date1; ?>" required/>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>To</label>
                                                        <input type="date" class="form-control" name="date2" value="<?php echo $date2; ?>" required/>			 
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="submit" name="search" class="btn btn-block btn-custom mt-4" value="Search" >
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
									<hr>
									<?php if($search == true) : ?>	
									<?php 										
                                        $result = $mysqli->query(
                                            "SELECT a.id,a.saler_id, a.invoice_no, a.payment_mode, a.total_amount, a.discount, a.date_created, b.invoice_no, b.cname, c.fullname, c.role
                                                FROM tbl_transactions AS a
                                                JOIN tbl_inv_detail AS b
                                                ON a.invoice_no = b.invoice_no 
                                                JOIN tbl_users AS c
                                                ON b.user_id = c.id
                                                WHERE a.date BETWEEN '$date1' AND '$date2'"); 
                                    ?>
                                    <table id="report" class="table table-responsive table-stripped" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Invoice No.</th>
                                                <th>Amount</th>
                                                <th>Discount</th>
                                                <th>Income</th>
                                                <th>Mode</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i=1;  while ($row1 = $result->fetch_assoc()):?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $row1['invoice_no'];?></td>
                                                <td><?php $amount = $row1['total_amount']; echo "&#8358;".number_format((float) $amount);?></td>
                                                <td><?php $discount = $row1['discount']; echo "&#8358;".number_format((float) $discount,2);?></td>
                                                <td><?php $income = $amount - $discount; echo "&#8358;".number_format((float) $income,2);?></td>
                                                <td><?php echo $row1['payment_mode']; ?></td>
                                                <td>
                                                    <?php
                                                        $appTrnDate = $row1['date_created'];
                                                        echo date('d/m/Y',strtotime($appTrnDate));
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="form-button-action">
                                                        <a href="receipt.php?ref=<?php echo $row1['invoice_no']; ?>" class="btn btn-rounded" data-toggle="tooltip" data-placement="top" title="Print Invoice"><i class="icon-printer"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $i++; endwhile; ?>
                                            <tr>
                                                <td><?php echo $i++;?></td>
                                                <td class="text-right" style="font-size:23px; font-weight: bold;">Cash:</td>
                                                <td style="font-size:23px; font-weight: bold;">
                                                    <?php
                                                        $result3 = mysqli_query($mysqli, 
                                                        "SELECT SUM(a.total_amount) AS totalAmount, SUM(a.discount) AS totalDiscount
                                                            FROM tbl_transactions as a
                                                            JOIN tbl_inv_detail AS b
                                                            ON a.invoice_no=b.invoice_no
                                                            WHERE b.payment_mode='Cash' AND DATE BETWEEN '$date1' AND '$date2'"); 
                                                        $row3 = mysqli_fetch_assoc($result3); 
                                                        $totalAmount3 = $row3['totalAmount'];														
                                                        $totalDiscount3 = $row3['totalDiscount'];														
                                                        $totatIncome3 = $totalAmount3 - $totalDiscount3;														
                                                        echo "&#8358;".number_format($totatIncome3,2);																												
                                                    ?>
                                                </td>
                                                <td style="font-size:23px; font-weight: bold;">Transfer:</td>
                                                <td style="font-size:23px; font-weight: bold;">
                                                    <?php
                                                        $result4 = mysqli_query($mysqli, 
                                                        "SELECT SUM(a.total_amount) AS totalAmount, SUM(a.discount) AS totalDiscount
                                                            FROM tbl_transactions as a
                                                            JOIN tbl_inv_detail AS b
                                                            ON a.invoice_no=b.invoice_no
                                                            WHERE b.payment_mode='Transfer' AND DATE BETWEEN '$date1' AND '$date2'"); 
                                                        $row4 = mysqli_fetch_assoc($result4); 
                                                        $totalAmount4 = $row4['totalAmount'];														
                                                        $totalDiscount4 = $row4['totalDiscount'];														
                                                        $totatIncome4 = $totalAmount4 - $totalDiscount4;														
                                                        echo "&#8358;".number_format($totatIncome4,2);																												
                                                    ?>
                                                </td>
                                                <!--<td class="text-center" style="font-size:23px; font-weight: bold;">=</td>-->
                                                <td style="font-size:23px; font-weight: bold;">Total:</td>
                                                <td style="font-size:23px; font-weight: bold;">
                                                    <?php
                                                        $gTotalIncome = $totatIncome3 + $totatIncome4;														
                                                        echo "&#8358;".number_format($gTotalIncome,2);																												
                                                    ?>
                                                </td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="mt-3">
                                        <?php 										
                                            $amntPerSaler = $mysqli->query(
                                                "SELECT c.fullname, SUM(a.total_amount-a.discount) AS totalAmount 
                                                    FROM tbl_transactions as a 
                                                    JOIN tbl_inv_detail AS b 
                                                    ON a.invoice_no=b.invoice_no 
                                                    JOIN tbl_users AS c 
                                                    ON a.saler_id=c.id
                                                    WHERE a.date BETWEEN '$date1' AND '$date2'
                                                    GROUP BY 1"); 
                                            while ($apsrow = $amntPerSaler->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><h4><?php echo $apsrow["fullname"];?>: </h4></td>
                                            <td class="pl-2"><h4><?php $totalAmountPerSaler = $apsrow["totalAmount"]; echo "&#8358;".number_format($totalAmountPerSaler,2);?></h4></td>
                                        </tr>
                                        <?php endwhile;?>
                                        <tr>
                                            <td><h4>GRANT TOTAL:</h4></td>
                                            <td class="pl-2"><h4><?php echo "&#8358;".number_format($gTotalIncome,2);?></h4></td>
                                        </tr>
                                    </table>
                                    <?php else : ?>
                                    <?php endif; ?>
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
         $('#report').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    exportOptions:{ columns:[0,1,2,3,4,5,6]},
                    className: 'btn btn-sm'
                },
                {
                    extend: 'pdf',
                    exportOptions:{ columns:[0,1,2,3,4,5,6]},
                    className: 'btn btn-sm mx-1'

                },
                {
                    extend: 'print',
                    exportOptions:{ columns:[0,1,2,3,4,5,6]},
                    className: 'btn btn-sm'

                }
            ],
            "pageLength": 50,
        });
    </script>
</body>		
</html>
