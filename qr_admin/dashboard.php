<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}
// var_dump($_SESSION["institute"]);exit;
include "../config.php";
include "control/helper.php";

$camp = $_SESSION['camp'];
$institute = $_SESSION['institute'];
$directorate_id = $_SESSION['directorate'];
$user_type = $_SESSION['user_type'];
$count = 0;
// var_dump($_SESSION['role']);exit;

if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'user') {
    if ($user_type == '1' || $user_type == '2') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM certificates WHERE institute_id = ?");
        $stmt->bind_param("i", $_SESSION['institute']);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM certificates WHERE directorate_id = ?");
        $stmt->bind_param("i", $_SESSION['directorate']);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        // Get directorate name
        $stmt = $conn->prepare("SELECT directorate_name FROM directorates WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['directorate']);
        $stmt->execute();
        $stmt->bind_result($directorate_name);
        $stmt->fetch();
        $stmt->close();
    }
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM certificates");
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
}


// if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'super_admin') {
//     $pending_count = 0;
//     $approved_count = 0;
//     $rejected_count = 0;

//     $stmt = $conn->prepare("SELECT verification_status, COUNT(*) FROM certificates GROUP BY verification_status");
//     $stmt->execute();
//     $stmt->bind_result($status, $status_count);
//     while ($stmt->fetch()) {
//         switch($status) {
//             case 'pending': $pending_count = $status_count; break;
//             case 'approved': $approved_count = $status_count; break;
//             case 'rejected': $rejected_count = $status_count; break;
//         }
//     }
//     $stmt->close();
// }
if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'super_admin') {
    $pending_count = 0;
    $approved_count = 0;
    $rejected_count = 0;

    // Build base query
    $sql = "SELECT 
            SUM(CASE WHEN verification_status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN verification_status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN verification_status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM certificates";

    $params = [];
    $param_types = "";

    // Add WHERE clause for regular admins
    if ($_SESSION['role'] == 'admin') {
        $user_type = $_SESSION['user_type'] ?? null;

        if ($user_type == '3' && isset($_SESSION['directorate']) && $_SESSION['directorate'] > 0) {
            $sql .= " WHERE directorate_id = ?";
            $params[] = $_SESSION['directorate'];
            $param_types = "i";
        } else if (($user_type == '1' || $user_type == '2') && isset($_SESSION['institute']) && $_SESSION['institute'] > 0) {
            $sql .= " WHERE institute_id = ?";
            $params[] = $_SESSION['institute'];
            $param_types = "i";
        } else if (isset($_SESSION['directorate']) && $_SESSION['directorate'] > 0) {
            $sql .= " WHERE directorate_id = ?";
            $params[] = $_SESSION['directorate'];
            $param_types = "i";
        } else if (isset($_SESSION['institute']) && $_SESSION['institute'] > 0) {
            $sql .= " WHERE institute_id = ?";
            $params[] = $_SESSION['institute'];
            $param_types = "i";
        }
    }

    // Execute query
    if ($stmt = $conn->prepare($sql)) {
        if (!empty($params)) {
            $stmt->bind_param($param_types, ...$params);
        }

        $stmt->execute();
        $stmt->bind_result($pending_count, $approved_count, $rejected_count);
        $stmt->fetch();
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include "template/head.php"; ?>

<body>
    <div class="wrapper">
        <?php include 'template/sidebar.php'; ?>
        <div class="main-panel">
            <?php include 'template/main-header.php'; ?>
            <div class="container">
                <div class="page-inner">
                    <h3 class="fw-bold mb-3">Certificates info</h3>

                    <?php if ($_SESSION['role'] == 'super_admin'): ?>
                        <!-- Admin Dashboard -->
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <div class="card card-stats card-info card-round">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="icon-big text-center">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                            <div class="col-9 col-stats">
                                                <div class="numbers">
                                                    <p class="card-category">Role</p>
                                                    <h4 class="card-title">Super Admin</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="card card-stats card-primary card-round">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="icon-big text-center">
                                                    <i class="fas fa-file-certificate"></i>
                                                </div>
                                            </div>
                                            <div class="col-9 col-stats">
                                                <div class="numbers">
                                                    <p class="card-category">Total Certificates</p>
                                                    <h4 class="card-title"><?php echo $count; ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="card card-stats card-warning card-round">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="icon-big text-center">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                            </div>
                                            <div class="col-9 col-stats">
                                                <div class="numbers">
                                                    <p class="card-category">Pending Verification</p>
                                                    <h4 class="card-title"><?php echo $pending_count; ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="card card-stats card-success card-round">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="icon-big text-center">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                            <div class="col-9 col-stats">
                                                <div class="numbers">
                                                    <p class="card-category">Approved</p>
                                                    <h4 class="card-title"><?php echo $approved_count; ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Regular User Dashboard -->
                        <div class="row">
                            <div class="col-sm-6 col-md-8">
                                <div class="card card-stats card-info card-round">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-5">
                                                <div class="icon-big text-center">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                            <div class="col-7 col-stats">
                                                <div class="numbers">
                                                    <!-- <p class="card-category">Directorate / Training Establishment</p> -->
                                                    <h4 class="card-title"><?php echo getDirectorateName($conn, $directorate_id); ?></h4>
                                                    <h4 class="card-title"><?php echo getCampName($conn, $camp); ?></h4>
                                                    <h6><?php echo getInstituteName($conn, $institute); ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="card card-stats card-primary card-round">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-5">
                                                <div class="icon-big text-center">
                                                    <i class="fas fa-user-check"></i>
                                                </div>
                                            </div>
                                            <div class="col-7 col-stats">
                                                <div class="numbers">
                                                    <p class="card-category">Issued certificates</p>
                                                    <h4 class="card-title"><?php echo $count; ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php include 'template/foot.php'; ?>
        </div>
    </div>
</body>

</html>