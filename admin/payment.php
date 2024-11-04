<?php
// PHP7 specific, fails fast, this file only
declare(strict_types=1);
// this file and all included/required files
  error_reporting(-1);
  ini_set('display_errors', 'true');
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
$srole = $_SESSION["role"];
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
	exit;
}

include("../include/config.php");

$fullname = $item = $school_name = $groupno = $visit = "";


if(isset($_POST["allocate"])){
	//a callback to use mysqli connection to escape strings
	$mysqli_escape = function($str) use ($mysqli) {
		return mysqli_real_escape_string($mysqli, $str);
	};

	$item = array_map($mysqli_escape, $_POST['item']);
	$quantity = array_map($mysqli_escape, $_POST['quantity']);
	$cashier = $_POST['id'];

	for($i=0;$i<count($item);$i++) {
		if($item==""){
			echo
			"<script>
				(function() {
					window.addEventListener('load', function() {
						Swal.fire({
							title: 'No item selected!',
							icon: 'error',
							showConfirmButton: false,
							allowOutsideClick: false,
							// delay: 4000,
							footer:`<a class='btn btn-sm btn-outline-info' href='#'>Back</a>`
						});
					}, false);
					// End of the IIFE()
				})();
			</script>";
		}else{
            // Validate Allocation
                $sql = 'INSERT INTO tbl_transactions (item, quantity, pmode, cashier, status) VALUES (\''.$item[$i].'\', \''.$quantity[$i].'\', \''.$pmode.'\', \''.$cashier.'\', \''.$status.'\')';
                if($mysqli->query($sql)){
                    echo
                    "<script>
                        (function() {
                            window.addEventListener('load', function() {
                                Swal.fire({
                                    html: 'All supervision allocated successfully!',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    // delay: 4000,
                                    footer:`<a class='btn btn-round btn-custom' href='allocate.php?ref=$id'>OK</a>`
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
                                    html: 'visits already allocated',
                                    icon: 'warning',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    // delay: 4000,
                                    footer:`<a class='btn btn-sm btn-custom' href='allocate.php?ref=$id'>Back</a>`
                                });
                            }, false);
                            // End of the IIFE()
                        })();
                    </script>";
                }
            
        }
	}

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../include/metadata.php'); ?>
	<title>Payment</title>
</head>
<body>
	<div class="wrapper horizontal-layout-3">
		<?php include('../include/header.php'); ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
                    <h1>Payment</h1>
                    <form method="POST">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input name="fullname" type="text" class="form-control" value="<?php echo $_SESSION["fullname"]; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control" value="<?php echo $_SESSION["role"]; ?>" readonly>
                                    </div>
                                    <input type="text" name="id" class="form-control" value="<?php echo $_SESSION["id"]; ?>" hidden>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="cname" class="form-control" placeholder="Customer Name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="number" name="cphone" class="form-control" placeholder="Customer Phone Number">
                                    </div>
                                    <input type="text" name="id" class="form-control" value="<?php echo $_SESSION["id"]; ?>" hidden>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group-addon">
                                            <a href="javascript:void(0)" class="btn btn-custom btn-round addMore" name="add"><span class="" aria-hidden="true"></span> Add Item</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="border-color: #0c3133">
                        </div>
                        <!-- First constant input field -->
                        <div class="form-group fieldGroup">
                            <div class="input-group">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="item">Item Name</label>
                                        <input list="item" type="text" name="item[]" class="form-control" placeholder="-- Select item --" value="<?php echo $item; ?>" required/>
                                        <datalist id="item">
                                            <?php 
                                                $result1 = $mysqli->query("SELECT * FROM tbl_items WHERE status='Active'") or die($mysqli->connect_error);
                                                while ($row1 = $result1->fetch_assoc()){
                                                $item1 = $row1['item'];
                                            ?>                
                                            <option value='<?php echo $item1;?>'>
                                            <?php } ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" name="quantity[]" class="form-control" placeholder="quantity" required/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pmode">Payment Mode</label>
                                        <select class="form-control" name="pmode" id="pmode">
                                            <option value="">-Select-</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Transfer">Transfer</option>
                                            <option value="Bank">Bank</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Copy of input fields group -->
                        <div class="form-group fieldGroupCopy" style="display: none;">
                            <div class="input-group">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input list="item" type="text" name="item[]" class="form-control" placeholder="-- Select item --" value="<?php echo $item; ?>" required/>
                                        <datalist id="item">
                                            <?php 
                                                $result2 = $mysqli->query("SELECT * FROM tbl_items WHERE status='Active'") or die($mysqli->connect_error);
                                                while ($row2 = $result2->fetch_assoc()){
                                                $item2 = $row2['item'];
                                            ?>                
                                            <option value='<?php echo $item2;?>'>
                                            <?php } ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                    <input type="number" name="quantity[]" class="form-control" placeholder="quantity" required/>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <a href="javascript:void(0)" class="btn btn-xs btn-rounded btn-outline-danger remove">
                                        <i class='icon-trash fa-2x'></i>
                                    </a>
                                </div>
                            </div>
                             <!-- Jquery script to add the total price of books items -->
                             <script>
                                // we used jQuery 'keyup' to trigger the computation as the user type
                                $('.price').keyup(function () {
                                
                                    // initialize the sum (total price) to zero
                                    var sum = 0;
                                    
                                    // we use jQuery each() to loop through all the textbox with 'price' class
                                    // and compute the sum for each loop
                                    $('.price').each(function() {
                                        sum += Number($(this).val());
                                    });
                                    
                                    // set the computed value to 'totalPrice' textbox
                                    $('#totalPrice').val(sum);
                                
                                });
                            </script>
                        </div>
                        <!-- Input group that output grand total -->
                        <div class="col-md-4 ml-auto mr-auto">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon3"><b>T O T A L (&#8358;)</b></span>
                                    </div>
                                    <input type='text' name="total" id='totalPrice' value="0.00" class="form-group text-xl" disabled />
                                </div>
                            </div>
                        </div>
                        <hr style="border-color: #0c3133">
                        <div class="card-action mt-4 text-right">
                            <a href="#" class="btn  btn-round btn-custom" id="saveUser" data-toggle="modal" data-target="#confirmation-modal">Submit</a>
                            <div class="pd-20 bg-red border-radius-4">
                                <div class="modal fade" id="confirmation-modal" tabindex="-1" post="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" post="document">
                                        <div class="modal-content">
                                            <div class="modal-body text-center font-18">
                                                <h4 class="padding-top-30 mb-30 weight-500">Are you sure <br>you want to submit?</h4>
                                                <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-rounded btn-danger border-radius-100 confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                                        <b>NO</b>
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="submit" name="allocate" class="btn btn-rounded btn-custom border-radius-100 confirmation-btn" ><i class="fa fa-check"></i></button>
                                                        <b>YES</b>
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

    <?php include('../include/footer.php'); ?>
	<?php include('../include/scripts.php'); ?>
    <script>
        $(document).ready(function(){
            //items add limit
            var maxGroup = 5;
            $(".sn").html(function(n){
                return + n+1;
            });
            //add more fields group
            $(".addMore").click(function(){
                if($('body').find('.fieldGroup').length < maxGroup){
                    // var i = $('.fieldGroup').length + 1;
                    $(".sn").html(function(n){
                        return + n+1;
                    });
                    var fieldHTML = '<div class="form-group fieldGroup">'+$(".fieldGroupCopy").html()+'</div>';
                    $('body').find('.fieldGroup:last').after(fieldHTML);
                }else{
                    alert('Maximum of '+maxGroup+' allocation remain for <?php echo $fullname; ?>.');
                }
            });
            //remove fields group
            $("body").on("click",".remove",function(){
                $(this).parents(".fieldGroup").remove();
            });
        });
	</script>
</body>
</html>
