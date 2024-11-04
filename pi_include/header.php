
<div class="main-header no-box-shadow" style="background-color: #0c3133;">
    <div class="nav-top">
        <div class="container d-flex flex-row">
            <button class="navbar-toggler sidenav-toggler2 ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon">
                    <i class="icon-menu text-white"></i>
                </span>
            </button>
            <button class="topbar-toggler more"><i class="icon-options-vertical text-white"></i></button>
            
            <!-- Logo Header -->
            <a href="index.php" class="logo d-flex align-items-center">
                <img src="../../assets/img/icon.jpg" height="70px" width="150px" alt="navbar brand" class="navbar-brand text-white">
            </a>
            <!-- End Logo Header -->

            <!-- Navbar Header -->
            <nav class="navbar navbar-header-left navbar-expand-lg p-0">
                <ul class="navbar-nav page-navigation pl-md-3">
                    <h3 class="title-menu d-flex d-lg-none"> 
                        Menu 
                        <div class="close-menu"> <i class="flaticon-cross text-white"></i></div>
                    </h3>
                    <li class="nav-item active">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu
                        </a>
                        <div class="dropdown-menu animated" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="index.php">Dashboard</a>
                            <?php if($_SESSION["role"] == "Developer" || $_SESSION["role"] == "Admin"):?>
                                <a class="dropdown-item" href="all-transactions.php">All Transactions</a>
                                <a class="dropdown-item" href="manage-products.php">Manage Products</a>
                                <a class="dropdown-item" href="manage-stock.php">Manage Stock</a>
                                <a class="dropdown-item" href="manage-invoices.php">Approve Invoices</a>
                                <a class="dropdown-item" href="reports.php">Reports</a>
                                <a class="dropdown-item" href="../index.php">AMU GRV</a>
                                <?php elseif($_SESSION["role"] == "Assistant Admin"): ?>
                                    <a class="dropdown-item" href="manage-invoices.php">Approve Invoices</a>
                                    <a class="dropdown-item" href="../index.php">AMU GRV</a>
                                    <?php elseif($_SESSION["role"] == "Stocker"): ?>
                                        <a class="dropdown-item" href="manage-products.php">Manage Products</a>
                                        <a class="dropdown-item" href="manage-stock.php">Manage Stock</a>
                                        <a class="dropdown-item" href="../index.php">AMU GRV</a>
                            <?php endif;?>
                        </div>
                    </li>
                </ul>
            </nav>
            <nav class="navbar navbar-header navbar-expand-lg p-0">
                <div class="container-fluid p-0" style="background-color: #0c3133;">
                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item dropdown hidden-caret">
                            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                                <i class="fas fa-bars"></i>
                            </a>
                            <div class="dropdown-menu quick-actions quick-actions-info">
                                <div class="quick-actions-header">
                                    <span class="title mb-1">Quick Actions</span>
                                    <span class="subtitle op-8">Shortcuts</span>
                                </div>
                                <div class="quick-actions-scroll scrollbar-outer">
                                    <div class="quick-actions-items">
                                        <div class="row m-0">
                                            <a class="col-6 col-md-6 p-0" href="change-password.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item rounded-circle text-danger">
                                                        <i class="fas fa-lock"></i>
                                                    </div>
                                                    <span class="text-secondary2">Change&nbsp;Password</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-6 p-0 mr-auto ml-auto" href="../../pi_include/logout.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item rounded-circle text-danger">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </div>
                                                    <span class="text-secondary2">Log Out</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>
    </div>
</div>