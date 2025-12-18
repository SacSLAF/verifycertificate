<?php
session_start(); // Start the session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../config.php';
$action = '';
// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}


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

$action = isset($_GET['action']) ? $_GET['action'] : 'add';

$buttonText = 'Submit'; // Default button text
if ($action === 'edit') {
    $buttonText = 'Update';
}

// Assuming $row['image'] contains the image filename from the database
$imagePath = '../certificate-photo/'; // Set the correct path to your images directory
$existingImage = ($action == 'edit' && !empty($row['image'])) ? $imagePath . $row['image'] : '';

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
                                    <div class="card-title"><?php echo $buttonText; ?> Certificate Details</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <form action="control/certificate_process.php?action=<?php echo $action; ?>" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="camp" value="<?= $_SESSION['camp'];?>">
                                            <input type="hidden" name="institute" value="<?= $_SESSION['institute'];?>">
                                            <input type="hidden" name="directorate" value="<?= $_SESSION['directorate'];?>">
                                            <div class="form-group">
                                                <label for="certificate_id">Certificate ID:</label>
                                                <input type="text" class="form-control" id="certificate_id" name="certificate_id" value="<?php echo ($action == 'edit') ? $row['certificate_id'] : ''; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="date_of_issue">Date of Issue:</label>
                                                <input type="date" class="form-control" id="date_of_issue" name="date_of_issue" value="<?php echo ($action == 'edit') ? $row['date_of_issue'] : ''; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="image">Image:</label>
                                                <?php if (!empty($existingImage)): ?>
                                                    <div>
                                                        <img src="<?php echo htmlspecialchars($existingImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Existing Image" style="max-width: 200px; max-height: 200px; margin-bottom: 10px;">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" <?php echo ($action == 'add') ? 'required' : ''; ?>>
                                            </div>

                                            <div class="form-group">
                                                <label for="name">Name:</label>
                                                <input type="text" class="form-control" id="name" name="name" value="<?php echo ($action == 'edit') ? $row['name'] : ''; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="service_no">Service No:</label>
                                                <input type="text" class="form-control" id="service_no" name="service_no" value="<?php echo ($action == 'edit') ? $row['service_no'] : ''; ?>">
                                            </div>
                                            <!-- <div class="form-group">
                                                <label for="rank">Rank:</label>
                                                <input type="text" class="form-control" id="rank" name="rank" value="<?php echo ($action == 'edit') ? $row['rank'] : ''; ?>">
                                            </div> -->
                                            <div class="form-group">
                                                <label for="rank">Rank:</label>
                                                <select class="form-control" id="rank" name="rank">
                                                    <option value="" selected disabled>Select Rank</option>
                                                    <?php
                                                    $airforce = array("Air Chief Marshal", "Air Marshal", "Air Vice Marshal", "Air Commodore", "Group Captain", "Wing Commander", "Squadron Leader", "Flight Lieutenant", "Flying Officer", "Pilot Officer", "Master Warrant Officer", "Warrant Officer", "Flight Sergeant", "Sergeant", "Corporal", "Leading Aircraftsman", "Aircraftsman");
                                                    $selected_rank = ($action == 'edit') ? $row['rank'] : '';
                                                    foreach ($airforce as $rank) {
                                                        $selected = ($rank == $selected_rank) ? 'selected' : '';
                                                        echo "<option value=\"$rank\" $selected>$rank</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="passport_no">Passport No:</label>
                                                <input type="text" class="form-control" id="passport_no" name="passport_no" value="<?php echo ($action == 'edit') ? $row['passport_no'] : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="nic_no">NIC No:</label>
                                                <input type="text" class="form-control" id="nic_no" name="nic_no" value="<?php echo ($action == 'edit') ? $row['nic_no'] : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="date_of_enlistment">Date of Enlistment:</label>
                                                <input type="date" class="form-control" id="date_of_enlistment" name="date_of_enlistment" value="<?php echo ($action == 'edit') ? $row['date_of_enlistment'] : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="date_of_retirement">Date of Retirement:</label>
                                                <input type="date" class="form-control" id="date_of_retirement" name="date_of_retirement" value="<?php echo ($action == 'edit') ? $row['date_of_retirement'] : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="total_service">Total Service:</label>
                                                <input type="text" class="form-control" id="total_service" name="total_service" value="<?php echo ($action == 'edit') ? $row['total_service'] : ''; ?>">
                                            </div>

                                            <div class="form-group">
                                                <label for="experience">Experience, Knowledge & Skills:</label>
                                                <textarea class="form-control" id="experience" name="experience" rows="5">
        <?php echo htmlspecialchars($action == 'edit' ? $row['experience'] : '', ENT_QUOTES, 'UTF-8'); ?>
    </textarea>
                                                <small class="form-text text-muted">Enter each experience item separated by commas.</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="qualifications">Qualifications, Awards & Achievements:</label>
                                                <textarea class="form-control" id="qualifications" name="qualifications" rows="5">
        <?php echo htmlspecialchars($action == 'edit' ? $row['qualifications'] : '', ENT_QUOTES, 'UTF-8'); ?>
    </textarea>
                                                <small class="form-text text-muted">Enter each knowledge item separated by commas.</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="issuing_authority_name">Issuing Authority Name:</label>
                                                <input type="text" class="form-control" id="issuing_authority_name" name="issuing_authority_name" value="<?php echo ($action == 'edit') ? $row['issuing_authority_name'] : ''; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="issuing_authority_rank">Issuing Authority Rank:</label>
                                                <input type="text" class="form-control" id="issuing_authority_rank" name="issuing_authority_rank" value="<?php echo ($action == 'edit') ? $row['issuing_authority_rank'] : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="issuing_authority_appointment">Issuing Authority Appointment:</label>
                                                <input type="text" class="form-control" id="issuing_authority_appointment" name="issuing_authority_appointment" value="<?php echo ($action == 'edit') ? $row['issuing_authority_appointment'] : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="issuing_authority_email">Issuing Authority Email:</label>
                                                <input type="email" class="form-control" id="issuing_authority_email" name="issuing_authority_email" value="<?php echo ($action == 'edit') ? $row['issuing_authority_email'] : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="issuing_authority_contact">Issuing Authority Contact:</label>
                                                <input type="text" class="form-control" id="issuing_authority_contact" name="issuing_authority_contact" value="<?php echo ($action == 'edit') ? $row['issuing_authority_contact'] : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="verified_by">Verified By:</label>
                                                <input type="text" class="form-control" id="verified_by" name="verified_by" value="<?php echo ($action == 'edit') ? $row['verified_by'] : ''; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="verified_date">Verified Date:</label>
                                                <input type="date" class="form-control" id="verified_date" name="verified_date" value="<?php echo ($action == 'edit') ? $row['verified_date'] : ''; ?>" required>
                                            </div>
                                            <?php if ($action == 'edit') { ?><input type="hidden" name="id" value="<?php echo $id; ?>"><?php } ?>
                                            <button type="submit" class="btn btn-primary"><?php echo $buttonText; ?></button>
                                        </form>
                                        <div class="row">
                                            <div class="mt-5 mb-5 text-center"><button class="btn btn-secondary" onclick="goBack()">Go Back</button></div>
                                        </div>
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

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "all-certificates.php"; // Redirects to a fallback URL
            }
        }
    </script>
</body>

</html>