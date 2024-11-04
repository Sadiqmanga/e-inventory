<?php
  require_once("../includes/config.php");
  session_start();
    if ($_SESSION['loggedin']==FALSE) {
          header("location:../");
        }
  ob_start();
  $conn = new DB_Class();
  // USER ID
  $userSession = $_SESSION['id'];
  $userInfo = $conn->casherName($userSession);
  // INVOICE NUMBER
  $ivc_no = $_SESSION['new_invoice'];

    unset($_SESSION['new_invoice']);
    $new_ivc = $userInfo['id'].date("Ymdhis");
    $_SESSION['new_invoice'] = $new_ivc;
    if (isset($_SESSION['new_invoice'])) {
        header("location:cart.php");
    }
?>