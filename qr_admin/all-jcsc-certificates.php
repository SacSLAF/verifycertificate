<?php
session_start();
include '../config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Fetch all JCSC certificates
$sql = "SELECT * FROM certificates_jcsc ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">JCSC Certificates</div>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if (isset($_SESSION['success_message'])) {
                                        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                                        unset($_SESSION['success_message']);
                                    }
                                    if (isset($_SESSION['error_message'])) {
                                        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                                        unset($_SESSION['error_message']);
                                    }
                                    ?>
                                    <div class="table-responsive">
                                        <table id="multi-filter-select" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Certificate ID</th>
                                                    <th>Recipient Name</th>
                                                    <th>Course Name</th>
                                                    <th>Date of Issue</th>
                                                    <th>Verified By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($result && $result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td>" . $row['certificate_id'] . "</td>";
                                                        echo "<td>" . $row['recipient_name'] . "</td>";
                                                        echo "<td>" . $row['course_name'] . "</td>";
                                                        echo "<td>" . $row['date_of_issue'] . "</td>";
                                                        echo "<td>" . $row['verified_by'] . "</td>";
                                                        echo "<td>";
                                                        echo "<a href='../jcsc-certificate.php?certificate_id=" . $row['certificate_uuid'] . "' target='_blank' class='btn btn-info btn-sm'>View</a> ";
                                                        echo "<a href='jcsc-certificate-form.php?action=edit&id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                                                        echo "<a href='control/delete_jcsc_certificate.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this certificate?\");'>Delete</a>";
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='6'>No JCSC certificates found.</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <a href="jcsc-certificate-form.php" class="btn btn-primary">Add New JCSC Certificate</a>
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
                pageLength: 10,
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