<?php
session_start(); // Start the session
include '../config.php';

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

// Prepare the query based on the role
if ($role == 'admin') {
    // Super admin sees all certificates
    $query = "SELECT id, certificate_id, name, service_no, rank, date_of_issue, issuing_authority_name, directorate FROM certificates";
} else {
    // Regular users see only their own directorate's certificates
    $query = "SELECT id, certificate_id, name, service_no, rank, date_of_issue, issuing_authority_name FROM certificates WHERE directorate = ?";
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
                                    <h4 class="card-title"> <?php
                                                            if ($role == 'admin') {
                                                                echo "All Directorates Certificates";
                                                            } else {
                                                                // Fetch the directorate name for regular users
                                                                $directorate_name = "Unknown Directorate";
                                                                $stmt = mysqli_prepare($conn, "SELECT directorate_name FROM directorates WHERE id = ?");
                                                                mysqli_stmt_bind_param($stmt, "i", $directorate_id);
                                                                mysqli_stmt_execute($stmt);
                                                                mysqli_stmt_bind_result($stmt, $directorate_name);
                                                                mysqli_stmt_fetch($stmt);
                                                                mysqli_stmt_close($stmt);

                                                                echo $directorate_name . " Certificates";
                                                            }
                                                            ?></h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="multi-filter-select" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Certif. ID</th>
                                                    <th>Name</th>
                                                    <th>SVC No</th>
                                                    <th>Rank</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Certif. ID</th>
                                                    <th>Name</th>
                                                    <th>SVC No</th>
                                                    <th>Rank</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                if (($role == 'admin')) {
                                                    $query = "SELECT id, certificate_id, name, service_no, rank, date_of_issue, issuing_authority_name,certificate_uuid FROM certificates";
                                                    $stmt = mysqli_prepare($conn, $query);
                                                } else {
                                                    $query = "SELECT id, certificate_id, name, service_no, rank, date_of_issue, issuing_authority_name,certificate_uuid FROM certificates WHERE directorate_id = ?";
                                                    $stmt = mysqli_prepare($conn, $query);
                                                    mysqli_stmt_bind_param($stmt, "i", $directorate_id);
                                                }

                                                // Execute the query
                                                mysqli_stmt_execute($stmt);

                                                // Get the result
                                                $result = mysqli_stmt_get_result($stmt);

                                                // Fetch and display the data
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['certificate_id']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['service_no']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['rank']) . "</td>";

                                                    // echo "<td>" . htmlspecialchars($row['issuing_authority_name']) . "</td>";
                                                    echo '<td style="text-wrap:nowrap;">';
                                                    echo '<a href="view-certificate.php?id=' . htmlspecialchars($row['certificate_uuid']) . '" class="btn btn-secondary btn-sm">View</a> &nbsp;';
                                                    echo '<a href="make-certificate.php?id=' . htmlspecialchars($row['id']) . '&action=edit" class="btn btn-sm btn-info">Edit</a> &nbsp;';

                                                    // Inside your PHP script where you generate the delete link
                                                    echo '<a href="#" onclick="confirmDelete(\'' . htmlspecialchars($row['id']) . '\'); return false;" class="btn btn-warning btn-sm">Delete</a>';


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