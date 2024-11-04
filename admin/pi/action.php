<?php
if (isset($_POST['approve-transaction'])) {
    $user_id = $_SESSION["id"];
    $invoice_no = strtoupper(trim($_POST['invoice_no']));
    $payment_mode = strtoupper(trim($_POST['payment_mode']));
    $total_amount = trim($_POST['total_amount']);
    $disc = trim($_POST['discount']);
    $discount = ($disc/100) * $total_amount;

    $sql5 = "INSERT INTO pi_tbl_transactions(user_id,invoice_no,payment_mode,total_amount,discount) VALUES(?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql5);
    $stmt->bind_param("ssss", $user_id, $invoice_no, $payment_mode, $total_amount,$discount);
    if($stmt->execute()) {
        header("location:edit-invoice.php?$invoice_no");
        echo 
            "<script>
                (function() {
                    window.addEventListener('load', function() {
                        Swal.fire({
                            icon: 'success',
                            html: 'Invoice approved successfully!',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            // delay: 4000,
                            footer:`<a class='btn btn-rounded btn-outline-secondary' onclick='Swal.close()' href='manage-invoices.php'>O K</a>`
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
?>