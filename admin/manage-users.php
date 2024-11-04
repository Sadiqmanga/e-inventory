<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
	header("location: ../login.php");
	exit;
}

include("../include/config.php");

// Personal info variables
$fullname = $email = $phone = $role = $password = $confirm_password = "";
$status = "Active";
$email_err = $password_err = $confirm_password_err = $image_err = "";

// Processing form data when form is submitted
if(isset($_POST['add-user'])){
 
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter staff email.";
    } else{
        // Prepare a select statement
        $sql = "SELECT * FROM tbl_users WHERE email = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_fullname);
			$stmt->bind_param("s", $param_phone);			
            $stmt->bind_param("s", $param_role);
			$stmt->bind_param("s", $param_password);
            $stmt->bind_param("s", $param_email);
			
            // Set parameters
            $param_fullname = trim($_POST['fullname']);
			$param_phone  = trim($_POST["phone"]);
            $param_role  = trim($_POST["role"]);        
            $param_password  = trim($_POST["password"]);
            $param_email = trim($_POST["email"]);
			
            // Caching temporary data for submission error (this is optional)
            $fullname = trim($_POST["fullname"]);
            $phone  = trim($_POST["phone"]);
            $role  = trim($_POST["role"]);	
            $password  = trim($_POST["password"]);
            $email = trim($_POST["email"]);

			// Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
					echo $email_err = 
                    "<script>
                        (function() {
                            window.addEventListener('load', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    html: 'The email: <strong>$email</strong> <br> is already used by another user.',
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
                } else{
					$fullname = trim($_POST["fullname"]);
					$phone  = trim($_POST["phone"]);
					$role  = trim($_POST["role"]);
					$password  = trim($_POST["password"]);
					$email = trim($_POST["email"]);
					$status = "Active";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
	}

    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO tbl_users (
		fullname,
		  phone,
		   email,
		    role,
			password, status) VALUES (?, ?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
			$stmt2 = $stmt->bind_param("ssssss", 
            $param_fullname,
            $param_phone,
            $param_email,
            $param_role,
            $param_password, 
            $param_status);
            
            // Set parameters
            $param_fullname = $fullname;
			$param_phone = $phone;
            $param_email = $email;
			$param_role = $role;
			$param_status = $status;
			$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
				echo
                "<script>
                    (function() {
                        window.addEventListener('load', function() {
                            Swal.fire({
                                icon: 'success',
								html: 'Registration is successful!<br><br><strong>$role</strong> added.',                                
								showConfirmButton: false,
								allowOutsideClick: false,
								footer:`<a class='btn btn-round btn-custom' href='manage-users.php'>O K</a>`                            });
                        }, false);
                    })();
                </script>";
				
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        // $stmt->close();
    }
    
    // Close connection
    // $mysqli->close();
}

// Update statement
if (isset($_POST['update-user'])) {
	$id  = trim($_POST["id"]);
	$role  = trim($_POST["role"]);
	$status  = trim($_POST["status"]);

	if($mysqli->query("UPDATE tbl_users SET role='$role', status='$status' WHERE id=$id")){
		echo
        "<script>
            (function() {
                window.addEventListener('load', function() {
                    Swal.fire({
                        html: 'User Updated Successfully!',
                        icon: 'success',
                        showConfirmButton: false,
                        allowOutsideClick: true,
                        footer:`<a class='btn btn-round btn-custom' href='manage-users.php'>O K</a>`
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

//  delete statement
 if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $mysqli->query("DELETE FROM tbl_users WHERE id=$id") or die($mysqli->connect_error);
    echo
        "<script>
            (function() {
                window.addEventListener('load', function() {
                    Swal.fire({
                        html: 'User Deleted Successfully!',
                        icon: 'error',
                        showConfirmButton: false,
                        allowOutsideClick: true,
                        // delay: 4000,
                        footer:`<a class='btn btn-rounded btn-outline-info' href='manage-users.php'>O K</a>`
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
	<title>Manage Users</title>
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
							role="tab" aria-controls="pills-one" aria-selected="true">All Users</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-two-tab-nobd" data-toggle="pill" href="#pills-two" 
							role="tab" aria-controls="pills-two" aria-selected="false">Add New User</a>
						</li>
					</ul>
					<div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- List of Users  -->
						<div class="tab-pane fade show active" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
							<div class="col-lg-12">
								<h1>List of Users</h1>
								<div class="card">
									<div class="card-body">
                                    <?php $result = $mysqli->query("SELECT * FROM tbl_users WHERE email!='developer@gmail.com' AND email!='developer2@gmail.com'") or die($mysqli->connect_error); ?>
									<div class="table-responsive">
									<table id="multi-filter-select" class="display table table-striped table-hover" >
										<thead>
											<tr>
												<th>S/N</th>
												<th>Name</th>
												<th>Email</th>
												<th>Phone</th>
												<th>Role</th>
												<th>Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php $i=1;  while ($row = $result->fetch_assoc()):?>
											<tr>
												<td><?php echo $i; ?></td>
												<td>
                                                    <?php 
                                                        $sn = explode(" ",$row['fullname']);
                                                        echo implode("&nbsp;",$sn);
                                                    ?>
                                                </td>
												<td><?php echo $row['email'];?></td>
												<td><?php echo $row['phone'];?></td>
												<td><?php echo $row['role'];?></td>
                                                <td>
                                                    <?php if($row['status'] == 'Active'): ?>
                                                    <span class="badge badge-success"><?php echo $row['status'];?></span>
                                                    <?php else: ?>
                                                    <span class="badge badge-danger"><?php echo $row['status'];?></span>
                                                    <?php endif; ?>
                                                </td>
												<td>
													<div class="form-button-action">
                                                        <a href="#" class="btn btn-rounded" data-toggle="modal" data-target="#edit-modal<?php echo $row['id']; ?>" data-toggle="tooltip" data-placement="top" title="Edit User"><i class="fas fa-edit"></i></a>
                                                        <div class="pd-20 bg-red border-radius-4">
                                                            <div class="modal fade" id="edit-modal<?php echo $row['id'];?>" tabindex="-1" post="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" post="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-body text-center font-18">
                                                                            <h4 class="padding-top-30 mb-30 weight-500"><strong><?php echo $row['fullname'];?></strong></h4>
                                                                            <div class="padding-bottom-30" style="max-width: 200px; margin: 0 auto;">
                                                                                <form action="manage-users.php" method="post">
                                                                                    <div class="form-group">
                                                                                        <div class="form-group">
                                                                                            <select class="form-control" name="role">
                                                                                            <option value="<?php echo $row['role'];?>"><?php echo $row['role'];?></option>
                                                                                            <option value="Assistant Admin">Assistant Admin</option>
                                                                                            <option value="Stocker">Stocker</option>
                                                                                            <option value="Sales">Sales</option>
                                                                                            <option value="Prime Sales">Prime Sales</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="form-group">
                                                                                            <select class="form-control" name="status">
                                                                                            <option value="<?php echo $row['status'];?>"><?php echo $row['status'];?></option>
                                                                                            <option value="Active">Activate</option>
                                                                                            <option value="Inactive">Deactivate</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <input type="text" name="id" value="<?php echo $row['id']; ?>" hidden>
                                                                                    </div>
																					<button type="submit" name="update-user" class="btn btn-block btn-custom btn-rounded confirmation-btn" >Update User</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Delete Modal -->															
                                                        <a href="#" class="btn btn-rounded" data-toggle="modal" data-target="#delete-modal<?php echo $row['id']; ?>" data-toggle="tooltip" data-placement="top" title="Delete User"><i class="fas fa-trash-alt text-danger"></i></a>
                                                        <div class="pd-5 bg-red border-radius-4">
                                                            <div class="modal fade" id="delete-modal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-body text-center font-18">
                                                                            <h4 class="padding-top-30 mb-30 weight-500">Are you sure <br>you want to <strong class="text-danger">delete</strong> this user?</h4>
                                                                            <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                                                <div class="col-6">
                                                                                    <button type="button" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                                                    <b>NO</b>
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <a  href="manage-users.php?delete=<?php echo $row['id']; ?>"  class="btn btn-rounded btn-danger confirmation-btn" ><i class="fa fa-trash-alt"></i></a>
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
						</div>
                        <!-- Add New User Form -->
						<div class="tab-pane fade" id="pills-two" role="tabpanel" aria-labelledby="pills-two-tab">
							<form method="POST">
                                <h1>Add New User</h1>
                                <div class="col-md-10 ml-auto mr-auto">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fullname">Full Name:</label>
                                                <input name="fullname" type="text" class="form-control" id="fullname" value="<?php echo $fullname; ?>" placeholder="Enter Full Name" required>
                                                <div class="invalid-feedback">Please provide username.</div>
                                            </div>
                                            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                                                <label for="email">Email:</label>
                                                <input name="email" type="email" class="form-control" id="email" value="<?php echo $email; ?>" placeholder="Enter Email" required>
                                                <span class="help-block text-danger"><?php echo $email_err; ?></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Phone No:</label>
                                                <input  name="phone" type="number" class="form-control" id="phone" value="<?php echo $phone; ?>" maxlength="11" placeholder="Enter Phone No" required>
                                                <div class="invalid-feedback">Please provide user phone number.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">																									
                                            <div class="form-group">
                                                <label for="role">User Role:</label>
                                                <select name="role" class="form-control" id="role" required>
                                                    <option value="<?php echo $role; ?>"><?php if ($role == "") {echo "--- Select User Role ---";}else{echo $role;} ?></option>
                                                    <option value="Developer">Developer</option>
                                                    <option value="Admin">Admin</option>
                                                    <option value="Assistant Admin">Assistant Admin</option>
                                                    <option value="Stocker">Stocker</option>
                                                    <option value="Sales">Sales</option>
                                                    <option value="Prime Sales">Prime Sales</option>
                                                </select>
                                            </div>
                                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                                <label for="password">Password:</label>
                                                <input name="password" type="password" class="form-control" id="password" value="<?php echo $password; ?>" placeholder="Enter Password" required>
                                                <div class="invalid-feedback">Please provide user password.</div>
                                                <span class="help-block text-danger"><?php echo $password_err; ?></span>
                                            </div>
                                            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                                                <label for="confirm_password">Confirm Password:</label>
                                                <input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="Confirm Password" required>
                                                <div class="invalid-feedback">Please confirm user password.</div>
                                                <span class="help-block text-danger"><?php echo $confirm_password_err; ?></span>
                                            </div>
                                            <div class="card-action mt-4 text-right">
                                                <!-- Confirmation modal -->													
                                                <a href="#" class="btn  btn-round btn-custom" id="saveUser" data-toggle="modal" data-target="#confirmation-modal">Register User</a>
                                                <div class="pd-20 bg-red border-radius-4">
                                                    <div class="modal fade" id="confirmation-modal" tabindex="-1" post="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" post="document">
                                                            <div class="modal-content">
                                                                <div class="modal-body text-center font-18">
                                                                    <h4 class="padding-top-30 mb-30 weight-500">Are you sure you <br>want to add new<strong> User</strong>?</h4>
                                                                    <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                                        <div class="col-6">
                                                                            <button type="button" class="btn btn-rounded btn-danger border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                                            <b>NO</b>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <button type="submit" name="add-user" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" ><i class="fa fa-check"></i></button>
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
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>					
                </div>
            </div>
        </div>
    </div>
    <?php include('../include/footer.php'); ?>
	<?php include('../include/scripts.php'); ?>
</body>		
</html>
