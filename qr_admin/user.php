<?php
session_start(); // Start the session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// var_dump($_SESSION["institute"]);exit;

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ) {
    header("location: index.php");
    exit;
}

include "../config.php";
include "control/helper.php";
?>


<!DOCTYPE html>
<html lang="en">

<?php include "template/head.php"; ?>

<body>
    <div class="wrapper">
        <?php
        include 'template/sidebar.php';
        ?>

        <div class="main-panel">
            <?php
            include 'template/main-header.php';
            //include 'template/main-content.php';
            ?>


            <div class="container">
                <div class="page-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">User</h4>

                                </div>
                                <div class="card-body">
                                    <p class="demo">
                                    <div class="d-flex" style="justify-content: flex-start;align-items:center;">
                                        <div class="avatar avatar-xxl">
                                            <img src="assets/img/profile.png" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                        <div class="mx-5">
                                            <h3><span class="profile-username"><?php echo $_SESSION["username"]; ?></span></h3>
                                            <h4><span class="profile-username">Directorate : <?php echo getDirectorateName($conn, $_SESSION["directorate"]); ?></span></h4>
                                            <h4><span class="profile-username">Camp : <?php echo getCampName($conn, $_SESSION["camp"]); ?></span></h4>
                                            <h4><span class="profile-username">Training Institute : <?php echo getInstituteName($conn, $_SESSION["institute"]); ?></span></h4>
                                            <h5><span class="profile-username">Role : <?php echo $_SESSION["role"]; ?></span></h5>
                                        </div>
                                    </div>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            <?php
            include 'template/foot.php';
            ?>
        </div>
    </div>
</body>

</html>