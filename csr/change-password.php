<?php
    require_once("../includes/config.php");
    session_start();
      if ($_SESSION['loggedin']==FALSE) {
              header("location:../");
            }
      $conn = new DB_Func();
      $userSession = $_SESSION['id'];
      $userInfo = $conn->casherName($userSession);
      $saler_id = $userInfo['id'];
      // $userSales = $conn->userSalesView($saler_id);
?>
<?php

// session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
	header("location: ../login.php");
	exit;
}

require_once"../include/config.php";

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter your new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm your new password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE tbl_users SET password = ? WHERE id = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("si", $param_password, $param_id);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){

                echo
					"<script>
						(function() {
							window.addEventListener('load', function() {
								Swal.fire({
									icon: 'success',
									html: 'Password updated successfully!',
									showConfirmButton: false,
									allowOutsideClick: true,
									// delay: 4000,
									footer:`<a class='btn btn-round btn-custom' href='index.php'>O K</a>`
								});
							}, false);
							// End of the IIFE()
						})();
					</script>
					";

            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>AMU GRV | Change Password</title>
    <?php include("../includes/metadata.php");?>  
    <style>
        a{
            color: white;
        }
        a:hover{
            color: gray;
        }
        .text-custom{
            color: #0c3133;
        }
        .btn-custom{
            background-color: #0c3133;
            color: white;
        }
        .btn-custom:hover{
            background-color: #352e8f;
            color: white;
        }
    </style>
</head>

<!------ Include the above in your HEAD tag ---------->

<body>
  <?php include('../includes/header.php'); ?>

  <div class="container">
    <div class="col-md-12">
        <h2 class="mt-5">Change Password</h2>
        <form method="POST">
            <div class="col-md-4 ml-auto mr-auto">	
                <div class="form-group">
                    <div class="position-relative <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                        <label for="new_password">New Password</label>
                        <input name="new_password" type="password" class="form-control" value="<?php echo $new_password; ?>">
                        <span class="help-block text-danger mt-5"><?php echo $new_password_err; ?></span>                                                                                                                               
                    </div>
                </div>
                <div class="form-group">
                    <div class="position-relative <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                        <label for="confirm_password">Comfirm Password</label>
                        <input name="confirm_password" type="password" class="form-control" value="<?php echo $confirm_password; ?>">
                        <span class="help-block text-danger mt-5"><?php echo $confirm_password_err; ?></span>                                                                                                                               
                    </div>
                </div>
                <div class="card-action mt-4 text-right">
                    <!-- Confirmation modal -->													
                    <a href="#" class="btn  btn-round btn-custom" id="saveUser" data-toggle="modal" data-target="#confirmation-modal">Change Password</a>
                    <div class="pd-20 bg-red border-radius-4">
                        <div class="modal fade" id="confirmation-modal" tabindex="-1" post="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" post="document">
                                <div class="modal-content">
                                    <div class="modal-body text-center font-18">
                                        <h4 class="padding-top-30 mb-30 weight-500">Are you sure <br>you want change your password?</h4>
                                        <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                            <div class="col-6">
                                                <button type="button" class="btn btn-rounded btn-danger border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                <b>NO</b>
                                            </div>
                                            <div class="col-6">
                                                <button type="submit" name="change-password" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" ><i class="fa fa-check"></i></button>
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
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/jquery-3.5.1.min.js"></script>
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../datatables/datatables.min.js"></script>
  <script src="../js/custom.js"></script>
  <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
  <script src="../assets/js/plugin/datatables.min.js"></script>
</body>
</html>