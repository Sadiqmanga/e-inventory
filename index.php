<?php

    // session_start();
    // ob_start();
    include("include/config.php");


    // Define variables and initialize with empty values
	$fullname = $email = $phone = $role = $password = $status = "";
	$email_err = $password_err = "";
 
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
	
		// Check if email is empty
		if(empty(trim($_POST["email"]))){   
			$email_err = "Please enter your email.";
		} else{
			$email = trim($_POST["email"]);
		}
		
		// Check if password is empty
		if(empty(trim($_POST["password"]))){
			$password_err = "Please enter your password.";
		} else{
			$password = trim($_POST["password"]);
		}
		
		// Validate credentials
		if(empty($email_err) && empty($password_err)){

				// Prepare a select statement
				$sql = "SELECT id, fullname, email, phone, role, password, status FROM tbl_users WHERE email = ? AND status = 'Active'";
				$stmt1 = $mysqli->prepare($sql);           
				$stmt1->bind_param("s", $param_email);            
					
				// Set parameters
				$param_fullname = $fullname;              
				$param_email = $email;
				$param_phone = $phone;            
				$param_role = $role;
				$param_status = $status;

				$stmt1->execute();
				$stmt1->store_result();
							
			if($stmt1->num_rows == 1){

				// Attempt to execute the prepared statement
				if($stmt1->execute()){
					// Store result
					$stmt1->store_result();
					
					// Check if email exists, if yes then verify password
					if($stmt1->num_rows == 1){                    
						// Bind result variables
						$stmt1->bind_result($id, $fullname, $email, $phone, $role, $hashed_password, $status);
						if($stmt1->fetch()){
							if(password_verify($password, $hashed_password)){
								// Password is correct, so start a new session
								session_start();
								
								// Store data in session variables
								$_SESSION["loggedin"] = true;
								$_SESSION["id"] = $id;
								$_SESSION["fullname"] = $fullname;
								$_SESSION["email"] = $email;
								$_SESSION["phone"] = $phone;  
								$_SESSION["role"] = $role;
								$_SESSION["status"] = $status;
			
								if($_SESSION["role"]=="Developer" || $_SESSION["role"]=="Admin" || $_SESSION["role"]=="Assistant Admin" || $_SESSION["role"]=="Stocker"){
									header('location: admin/index.php');
								}elseif($_SESSION["role"]=="Sales"){
									header('location: csr/index.php');
								}elseif($_SESSION["role"]=="Sales"){
									header('location: csr/index.php');
								}elseif($_SESSION["role"]=="Prime Sales"){
									header('location: pi/index.php');
								}else{
									header('location: index.php');
								}

							} else{
								// Display an error message if password is not valid
								$password_err = "Incorrect Password!";
							}
						}
					} 
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
			} else{
				// Display an error message if email doesn't exist
				$_SESSION['email_err'] = "<div class='text-danger' id='card'><h4><i class='fa fa-times-circle'></i> This email is not recognized.</h4></div>";  
				$email_err = " ";
			}
		}
		// Close connection
		$mysqli->close();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="assets/img/icon.ico" type="image/x-icon"/>

	<!-- Fonts and icons -->
	<script src="assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['assets/css/fonts.min.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/atlantis2.css">
	<link rel="stylesheet" href="assets/css/custom.css">
	<style>
		.quick-actions.quick-actions-info .quick-actions-header {
			background: #1a2035;
		}
		.quick-actions.quick-actions-info:after {
			border-bottom-color: #1a2035 !important;
		}
	</style>
</head>
<body class="login">
	<div class="wrapper wrapper-login">
		<div class="container container-login animated fadeIn">
			<h2 class="text-center">AMU <br> GLOBAL RESOURCE VENTURES</h2>
			<h4 class="text-center">Admin Login</h4>
			<?php if (isset($_SESSION['email_err'])){ ?>     
				<div class="alert alert-danger text-center">
					<?= $_SESSION['email_err']; ?>
				</div>
			<?php } unset($_SESSION['email_err']); ?>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">                            
				<div class="login-form">
					<div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
						<label for="email" class="placeholder"><b>Email</b></label>
						<input name="email" type="text" class="form-control" id="email" value="<?php echo $email; ?>" placeholder="Enter email">
					</div>
					<div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
						<label for="password" class="placeholder"><b>Password</b></label>
						<a href="#" class="link float-right">Forget Password?</a>
						<div class="position-relative">
							<input id="password" name="password" type="password" class="form-control" required>
							<div class="show-password">
								<i class="fa fa-eye"></i>
							</div>
						</div>
						<span class="help-block text-danger mt-5"><?php echo $password_err; ?></span>                                                                                                                               
					</div>
					<div class="form-group form-action-d-flex mb-3">
						<button type="submit" class="btn btn-custom btn-block mt-3 mt-sm-0 fw-bold">Login</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<script src="assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>
	<script src="assets/js/atlantis2.min.js"></script>

</body>
</html>