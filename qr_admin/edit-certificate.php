<?php
session_start(); // Start the session
include '../config.php';

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['directorate'])) {
    header("location: index.php");
    exit;
}

include "../config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Step 2: Construct the SQL query
    $sql = "SELECT * FROM `certificates` WHERE `id` = $id";
    $result = mysqli_query($conn, $sql);
    $row = $result->fetch_assoc();
    // echo $row['content']; exit;
    // Step 4: Close the connection
    mysqli_close($conn);
}
if (isset($_GET['action'])) {
    $action = $_GET['action'];
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
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Forms</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home">
                                <a href="#">
                                    <i class="icon-home"></i>
                                </a>
                            </li>
                            <li class="separator">
                                <i class="icon-arrow-right"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Forms</a>
                            </li>
                            <li class="separator">
                                <i class="icon-arrow-right"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Basic Form</a>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Edit Certificate Details</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <form action="control/certificate_process.php?action=edit" method="post" enctype="multipart/form-data">
                                            <div class="col-md-12 col-lg-12">
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="certificateid"
                                                        class="col-md-3 col-form-label">Certificate ID</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="text"
                                                            class="form-control input-full"
                                                            id="certificateid" value="<?php echo ($action == 'edit') ? $row['certificate_id'] : ''; ?>" />
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">Date of issued</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="text"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">Name</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="text"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">Service No</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="text"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">Rank</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="text"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleFormControlFile1">Profile Image</label>
                                                    <input
                                                        type="file"
                                                        class="form-control-file"
                                                        id="exampleFormControlFile1" />
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">Passport No</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="text"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">NIC No</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="text"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">Date of enlistement</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="date"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">Date of retirement</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="date"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label
                                                        for="inlineinput"
                                                        class="col-md-3 col-form-label">Total Service</label>
                                                    <div class="col-md-9 p-0">
                                                        <input
                                                            type="text"
                                                            class="form-control input-full"
                                                            id="inlineinput"
                                                            placeholder="Enter Input" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="experience">Experience, Knowledge & Skills:</label>
                                                    <textarea class="form-control" id="experience" name="experience" rows="5"></textarea>
                                                    <small class="form-text text-muted">Enter each experience item separated by commas.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="knowledge">qualifications,Awards & Achievements:</label>
                                                    <textarea class="form-control" id="qualifications" name="qualifications" rows="5"></textarea>
                                                    <small class="form-text text-muted">Enter each knowledge item separated by commas.</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="issuing_authority_name">Issuing Authority Name:</label>
                                                    <input type="text" class="form-control" id="issuing_authority_name" name="issuing_authority_name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="issuing_authority_rank">Issuing Authority Rank:</label>
                                                    <input type="text" class="form-control" id="issuing_authority_rank" name="issuing_authority_rank">
                                                </div>
                                                <div class="form-group">
                                                    <label for="issuing_authority_appointment">Issuing Authority Appointment:</label>
                                                    <input type="text" class="form-control" id="issuing_authority_appointment" name="issuing_authority_appointment">
                                                </div>
                                                <div class="form-group">
                                                    <label for="issuing_authority_email">Issuing Authority Email:</label>
                                                    <input type="email" class="form-control" id="issuing_authority_email" name="issuing_authority_email">
                                                </div>
                                                <div class="form-group">
                                                    <label for="issuing_authority_contact">Issuing Authority Contact:</label>
                                                    <input type="text" class="form-control" id="issuing_authority_contact" name="issuing_authority_contact">
                                                </div>
                                                <div class="form-group">
                                                    <label for="verified_by">Verified By:</label>
                                                    <input type="text" class="form-control" id="verified_by" name="verified_by" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="verified_date">Verified Date:</label>
                                                    <input type="date" class="form-control" id="verified_date" name="verified_date" required>
                                                </div>


                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-action">
                                    <button class="btn btn-success">Update</button>
                                    <button class="btn btn-danger">Cancel</button>
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