<?php

	session_start();

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
		header("location: ../../login.php");
		exit;
	}
	
	include("../../include/config.php");
	$id = $_SESSION["id"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php include('../../pi_include/metadata.php'); ?>
	<title>Dashboard</title>
</head>
<body>

	<div class="wrapper horizontal-layout-3">
		<?php include('../../pi_include/header.php'); ?>

		<div class="main-panel">
			<div class="bg-secondary pt-4 pb-5">
				<div class="container text-white py-2">
					<div class="d-flex align-items-center">
						<div class="mr-3">
							<h2 class="mb-3">AMU PRIME INTERNATIONAL <br>ADMIN DASHBOARD</h2>
						</div>
						<div class="ml-auto badge mr-3">
							<?php echo DATE('l, jS M Y'); ?>
						</div>
						<div class="ml-auto">
							<h4 class="mb-3"><?php echo $_SESSION["role"]; ?><br><?php echo $_SESSION["fullname"]; ?></h4>
						</div>
					</div>
				</div>
			</div>
			<div class="container mt--5">
				<div class="page-inner mt--3">
					<div class="row">
						<div class="col-6 col-sm-4 col-lg-3">
							<div class="card">
								<div class="card-body text-secondary2 p-3 text-center">
									<div class="h1 m-0">
									<?php
										$result1 = mysqli_query($mysqli, "SELECT SUM(total_amount) AS grandTotal, date FROM pi_tbl_transactions WHERE date=CURDATE()"); 
										$row1 = mysqli_fetch_assoc($result1); 
										$grandTotal = $row1['grandTotal'];														
										echo "&#8358;".number_format($grandTotal,2);
									?>
									</div>
									<div class="my-1">Total Sales</div>
								</div>
							</div>
						</div>
						<div class="col-6 col-sm-4 col-lg-3">
							<div class="card">
								<div class="card-body text-secondary2 p-3 text-center">
									<div class="h1 m-0">
										<?php
											$result2 = mysqli_query($mysqli, "SELECT SUM(discount) AS totalDiscount, date FROM pi_tbl_transactions WHERE date=CURDATE()"); 
											$row2 = mysqli_fetch_assoc($result2); 
											$totalDiscount = $row2['totalDiscount'];														
											echo "&#8358;".number_format($totalDiscount,2);
										?>
									</div>
									<div class="my-1">Total Discount</div>
								</div>
							</div>
						</div>
						<div class="col-6 col-sm-4 col-lg-3">
							<div class="card">
								<div class="card-body text-secondary2 p-3 text-center">
									<div class="">
										<h1><?php
											$cashAtHand = $grandTotal - $totalDiscount;														
											echo "&#8358;".number_format($cashAtHand,2);
										?></h1>
									</div>
									<div class="my-1">Total Income</div>
								</div>
							</div>
						</div>
						<div class="col-6 col-sm-4 col-lg-3">
							<div class="card">
								<div class="card-body text-secondary2 p-3 text-center">
									<div class="h1 m-0">
										<?php
											$sql3="SELECT count('1') FROM pi_tbl_transactions WHERE DATE(date_created)=CURDATE()";
											$result3=mysqli_query($mysqli,$sql3);
											$row3=mysqli_fetch_array($result3);
											echo $row3[0];
										?>
									</div>
									<!-- <i class="fas fa-users fa-3x"></i> -->
									<div class="my-1">Total Invoices</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-6 col-sm-4 col-lg-3">
							<div class="card">
								<div class="card-body text-secondary2 p-3 text-center">
									<div class="h1 m-0">
									<?php
										$result4 = mysqli_query($mysqli, 
											"SELECT SUM(total_amount) AS total, SUM(discount) AS totalDiscount, a.date
												FROM pi_tbl_transactions AS a
												JOIN pi_tbl_inv_detail AS b
												ON a.invoice_no=b.invoice_no
												WHERE b.payment_mode='Cash' AND a.date=CURDATE();"); 
											$row4 = mysqli_fetch_assoc($result4); 
											$total1 = $row4['total'] ?? NULL;
											$discount1 = $row4['totalDiscount'] ?? NULL;		
											$cash = $total1 - $discount1;											
											echo "&#8358;".number_format($cash,2);
									?>
									</div>
									<div class="my-1">Total Cash</div>
								</div>
							</div>
						</div>
						<div class="col-6 col-sm-4 col-lg-3">
							<div class="card">
								<div class="card-body text-secondary2 p-3 text-center">
									<div class="h1 m-0">
										<?php
											$result5 = mysqli_query($mysqli, 
											"SELECT SUM(total_amount) AS total, SUM(discount) AS totalDiscount, a.date
												FROM pi_tbl_transactions AS a
												JOIN pi_tbl_inv_detail AS b
												ON a.invoice_no=b.invoice_no
												WHERE b.payment_mode='Transfer' AND a.date=CURDATE()
												;"); 
											$row6 = mysqli_fetch_assoc($result5); 
											$total2 = $row6['total'] ?? NULL;
											$discount2 = $row6['totalDiscount'] ?? NULL;		
											$transfer = $total2 - $discount2;														
											echo "&#8358;".number_format($transfer,2);
										?>
									</div>
									<div class="my-1">Total Transfer</div>
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
	
</body>	
</html>
