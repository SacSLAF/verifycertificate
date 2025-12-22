<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../config.php';
include 'control/helper.php';

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Get the user role from the session
$role = $_SESSION['role'];
$directorate_id = $_SESSION['directorate'];
$camp = $_SESSION['camp'];
$institute = $_SESSION['institute'];
$is_admin = ($role == 'admin' || $role == 'super_admin') ? true : false;
$user_type = $_SESSION['user_type'];

// Prepare the query based on the role
if ($role == 'super_admin') {
    // Super admin sees all certificates
    $query = "SELECT id, certificate_id, name, service_no, rank, date_of_issue, issuing_authority_name, directorate_id FROM certificates";
} else {
    if ($user_type == '1' || $user_type == '2') {
        $query = "SELECT id, certificate_id, name, service_no, rank, date_of_issue, issuing_authority_name FROM certificates WHERE institute_id  = ?";
    } else {
        $query = "SELECT id, certificate_id, name, service_no, rank, date_of_issue, issuing_authority_name FROM certificates WHERE directorate_id = ?";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include "template/head.php";


?>

<body>
    <div class="wrapper">
        <?php include 'template/sidebar.php'; ?>

        <div class="main-panel">
            <?php
            include 'template/main-header.php';
            ?>

            <div class="container">
                <div class="page-inner">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <?php
                                        if ($role == 'admin' || $role == 'super_admin') {
                                            echo "All Certificates";
                                        } else {
                                            // Initialize variable
                                            $show_name = "Unknown";

                                            // Check USER type, not certificate type
                                            if ($user_type == '3') {
                                                // For user type 3, get directorate name
                                                $query = "SELECT directorate_name FROM directorates WHERE id = ?";
                                                $stmt = mysqli_prepare($conn, $query);
                                                if ($stmt) {
                                                    mysqli_stmt_bind_param($stmt, "i", $directorate_id);
                                                    mysqli_stmt_execute($stmt);
                                                    mysqli_stmt_bind_result($stmt, $directorate_name);
                                                    if (mysqli_stmt_fetch($stmt)) {
                                                        $show_name = $directorate_name;
                                                    }
                                                    mysqli_stmt_close($stmt);
                                                }
                                                echo $show_name . " Certificates";
                                            } else if ($user_type == '1' || $user_type == '2') {
                                                // For user type 1 or 2, get institute name
                                                $query = "SELECT `name` FROM training_institutes WHERE id = ?";
                                                $stmt = mysqli_prepare($conn, $query);
                                                if ($stmt) {
                                                    mysqli_stmt_bind_param($stmt, "i", $institute);
                                                    mysqli_stmt_execute($stmt);
                                                    mysqli_stmt_bind_result($stmt, $institute_name);
                                                    if (mysqli_stmt_fetch($stmt)) {
                                                        $show_name = $institute_name;
                                                    }
                                                    mysqli_stmt_close($stmt);
                                                }
                                                echo $show_name . " Certificates";
                                            } else {
                                                echo "My Certificates";
                                            }
                                        }
                                        ?>
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="multi-filter-select" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Certif. ID</th>
                                                    <th>SVC No</th>
                                                    <th>Rank</th>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Certif. ID</th>
                                                    <th>SVC No</th>
                                                    <th>Rank</th>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                if (($role == 'super_admin')) {
                                                    $query = "SELECT `id`, `certificate_id`, `name`, `service_no`, `rank`,`type`,`date_of_issue`, `issuing_authority_name`,`certificate_uuid`,`directorate_id`, `verification_status`,is_active FROM certificates";
                                                    $stmt = mysqli_prepare($conn, $query);
                                                } else {
                                                    if ($user_type == '1' || $user_type == '2') {
                                                        $query = "SELECT `id`, `certificate_id`, `name`, `service_no`, `rank`,`type`, `date_of_issue`, `issuing_authority_name`,`certificate_uuid`,`directorate_id`, `verification_status`,is_active FROM certificates WHERE institute_id = ?";
                                                        $stmt = mysqli_prepare($conn, $query);
                                                        mysqli_stmt_bind_param($stmt, "i", $institute);
                                                    } else {
                                                        $query = "SELECT `id`, `certificate_id`, `name`, `service_no`, `rank`,`type`, `date_of_issue`, `issuing_authority_name`,`certificate_uuid`,`directorate_id`, `verification_status`,is_active FROM certificates WHERE directorate_id = ?";
                                                        $stmt = mysqli_prepare($conn, $query);
                                                        mysqli_stmt_bind_param($stmt, "i", $directorate_id);
                                                    }
                                                }

                                                // Execute the query
                                                mysqli_stmt_execute($stmt);

                                                // Get the result
                                                $result = mysqli_stmt_get_result($stmt);

                                                // Fetch and display the data
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['certificate_id']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['service_no']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['rank']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                    // Verification status with badge
                                                    $status_badge = '';
                                                    $is_active_badge = '';
                                                    switch ($row['verification_status']) {
                                                        case 'approved':
                                                            $status_badge = '<span class="badge badge-success">Approved</span>';
                                                            break;
                                                        case 'rejected':
                                                            $status_badge = '<span class="badge badge-danger">Rejected</span>';
                                                            break;
                                                        default:
                                                            $status_badge = '<span class="badge badge-warning">Pending</span>';
                                                    }

                                                    // Active status badge
                                                    if ($row['is_active'] == 1) {
                                                        $is_active_badge = '<span class="badge badge-info badge-sm">Active</span>';
                                                    } else {
                                                        $is_active_badge = '<span class="badge badge-secondary badge-sm">Inactive</span>';
                                                    }

                                                    echo "<td>" . $status_badge . " " . $is_active_badge . "</td>";

                                                    // Action buttons
                                                    echo '<td style="white-space: nowrap;">';

                                                    // View button - only show for approved and active certificates for non-admin users
                                                    if (!$is_admin && $row['verification_status'] == 'approved' && $row['is_active'] == 1) {
                                                        echo '<a href="view-certificate-v2.php?id=' . htmlspecialchars($row['certificate_uuid']) . '" class="btn btn-secondary btn-sm" title="View">';
                                                        echo '<i class="fas fa-eye"></i>';
                                                        echo '</a> &nbsp;';
                                                    } elseif (!$is_admin) {
                                                        echo '<button class="btn btn-secondary btn-sm" disabled title="View (Only available for approved and active certificates)">';
                                                        echo '<i class="fas fa-eye"></i>';
                                                        echo '</button> &nbsp;';
                                                    } else {
                                                        // Admin can always view
                                                        echo '<a href="view-certificate-v2.php?id=' . htmlspecialchars($row['certificate_uuid']) . '" class="btn btn-secondary btn-sm" title="View">';
                                                        echo '<i class="fas fa-eye"></i>';
                                                        echo '</a> &nbsp;';
                                                    }

                                                    // Verify button - ONLY FOR ADMIN USERS for pending certificates
                                                    if ($is_admin && $row['verification_status'] == 'pending') {
                                                        echo '<a href="verify-certificate.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-success btn-sm" title="Verify Certificate">';
                                                        echo '<i class="fas fa-check-circle"></i> Verify';
                                                        echo '</a> &nbsp;';
                                                    } elseif ($is_admin && $row['verification_status'] == 'approved') {
                                                        echo '<button class="btn btn-success btn-sm" disabled title="Already Verified">';
                                                        echo '<i class="fas fa-check-circle"></i> Verified';
                                                        echo '</button> &nbsp;';
                                                    } elseif ($is_admin && $row['verification_status'] == 'rejected') {
                                                        echo '<a href="verify-certificate.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-warning btn-sm" title="Review Rejected Certificate">';
                                                        echo '<i class="fas fa-redo"></i> Review';
                                                        echo '</a> &nbsp;';
                                                    }

                                                    // Edit button - only for regular users from same directorate (completely hidden for admin)
                                                    if (!$is_admin && $row['directorate_id'] == $directorate_id) {
                                                        echo '<a href="make-certificate.php?id=' . htmlspecialchars($row['id']) . '&action=edit" class="btn btn-info btn-sm" title="Edit">';
                                                        echo '<i class="fas fa-edit"></i>';
                                                        echo '</a> &nbsp;';
                                                    } elseif (!$is_admin) {
                                                        // Only show disabled button for non-admin users without permission
                                                        echo '<button class="btn btn-info btn-sm" disabled title="No Permission">';
                                                        echo '<i class="fas fa-edit"></i>';
                                                        echo '</button> &nbsp;';
                                                    }

                                                    // Delete button - only for same directorate users
                                                    if (!$is_admin && $row['directorate_id'] == $directorate_id) {
                                                        echo '<a href="#" onclick="confirmDelete(\'' . htmlspecialchars($row['id']) . '\'); return false;" class="btn btn-warning btn-sm" title="Delete">';
                                                        echo '<i class="fas fa-trash"></i>';
                                                        echo '</a>';
                                                    } elseif ($is_admin) {
                                                        echo '<a href="#" onclick="confirmDelete(\'' . htmlspecialchars($row['id']) . '\'); return false;" class="btn btn-warning btn-sm" title="Delete">';
                                                        echo '<i class="fas fa-trash"></i>';
                                                        echo '</a>';
                                                    } else {
                                                        echo '<button class="btn btn-warning btn-sm" disabled title="No Permission">';
                                                        echo '<i class="fas fa-trash"></i>';
                                                        echo '</button>';
                                                    }

                                                    echo '</td>';

                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'template/foot.php'; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#multi-filter-select").DataTable({
                pageLength: 5,
                initComplete: function() {
                    this.api()
                        .columns()
                        .every(function() {
                            var column = this;
                            var select = $('<select class="form-select"><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on("change", function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column
                                        .search(val ? "^" + val + "$" : "", true, false)
                                        .draw();
                                });

                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    select.append('<option value="' + d + '">' + d + "</option>");
                                });
                        });
                },
            });
        });
    </script>
</body>

</html>