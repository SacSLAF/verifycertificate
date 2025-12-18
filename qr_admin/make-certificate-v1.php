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

// Check if user has permission to access this page
if ($_SESSION['directorate'] == 12) {
    // Admin users shouldn't create certificates directly
    header("location: all-certificates.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Step 2: Construct the SQL query
    $sql = "SELECT c.*, d.directorate_name 
            FROM `certificates` c 
            LEFT JOIN `directorates` d ON c.directorate_id = d.id 
            WHERE c.`id` = $id";
    $result = mysqli_query($conn, $sql);
    $row = $result->fetch_assoc();
    
    // Check if user has permission to edit this certificate
    if ($_SESSION['directorate'] != 12 && $row['directorate_id'] != $_SESSION['directorate']) {
        header("location: all-certificates.php");
        exit;
    }
}

$action = isset($_GET['action']) ? $_GET['action'] : 'add';

$buttonText = 'Submit'; // Default button text
if ($action === 'edit') {
    $buttonText = 'Update';
}

// Assuming $row['image'] contains the image filename from the database
$imagePath = '../certificate-photo/'; // Set the correct path to your images directory
$existingImage = ($action == 'edit' && !empty($row['image'])) ? $imagePath . $row['image'] : '';

// Get verification status for display
$verification_status = ($action == 'edit') ? $row['verification_status'] : 'pending';
$rejection_reason = ($action == 'edit') ? $row['rejection_reason'] : '';
$admin_notes = ($action == 'edit') ? $row['admin_notes'] : '';

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
                                    <div class="card-title"><?php echo $buttonText; ?> Certificate Details</div>
                                    <?php if ($action == 'edit'): ?>
                                        <div class="verification-status">
                                            <?php 
                                            $status_badge = '';
                                            switch($verification_status) {
                                                case 'approved':
                                                    $status_badge = '<span class="badge badge-success">Approved</span>';
                                                    break;
                                                case 'rejected':
                                                    $status_badge = '<span class="badge badge-danger">Rejected</span>';
                                                    break;
                                                default:
                                                    $status_badge = '<span class="badge badge-warning">Pending Verification</span>';
                                            }
                                            echo $status_badge;
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <form action="control/process-v1.php?action=<?php echo $action; ?>" method="POST" enctype="multipart/form-data">

                                            <!-- Certificate Basic Information -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="certificate_id">Certificate ID:</label>
                                                        <input type="text" class="form-control" id="certificate_id" name="certificate_id" value="<?php echo ($action == 'edit') ? $row['certificate_id'] : ''; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="date_of_issue">Date of Issue:</label>
                                                        <input type="date" class="form-control" id="date_of_issue" name="date_of_issue" value="<?php echo ($action == 'edit') ? $row['date_of_issue'] : ''; ?>" required>
                                                    </div>
                                                </div>
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

                                            <!-- Personal Information -->
                                            <h5 class="section-title">Personal Information</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">Name:</label>
                                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo ($action == 'edit') ? $row['name'] : ''; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="service_no">Service No:</label>
                                                        <input type="text" class="form-control" id="service_no" name="service_no" value="<?php echo ($action == 'edit') ? $row['service_no'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
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
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="passport_no">Passport No:</label>
                                                        <input type="text" class="form-control" id="passport_no" name="passport_no" value="<?php echo ($action == 'edit') ? $row['passport_no'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nic_no">NIC No:</label>
                                                        <input type="text" class="form-control" id="nic_no" name="nic_no" value="<?php echo ($action == 'edit') ? $row['nic_no'] : ''; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="total_service">Total Service:</label>
                                                        <input type="text" class="form-control" id="total_service" name="total_service" value="<?php echo ($action == 'edit') ? $row['total_service'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="date_of_enlistment">Date of Enlistment:</label>
                                                        <input type="date" class="form-control" id="date_of_enlistment" name="date_of_enlistment" value="<?php echo ($action == 'edit') ? $row['date_of_enlistment'] : ''; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="date_of_retirement">Date of Retirement:</label>
                                                        <input type="date" class="form-control" id="date_of_retirement" name="date_of_retirement" value="<?php echo ($action == 'edit') ? $row['date_of_retirement'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Experience and Qualifications -->
                                            <h5 class="section-title">Professional Details</h5>
                                            <div class="form-group">
                                                <label for="experience">Experience, Knowledge & Skills:</label>
                                                <textarea class="form-control" id="experience" name="experience" rows="5"><?php echo htmlspecialchars($action == 'edit' ? $row['experience'] : '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                <small class="form-text text-muted">Enter each experience item separated by commas.</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="qualifications">Qualifications, Awards & Achievements:</label>
                                                <textarea class="form-control" id="qualifications" name="qualifications" rows="5"><?php echo htmlspecialchars($action == 'edit' ? $row['qualifications'] : '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                <small class="form-text text-muted">Enter each qualification item separated by commas.</small>
                                            </div>

                                            <!-- Issuing Authority Information -->
                                            <h5 class="section-title">Issuing Authority Details</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="issuing_authority_name">Issuing Authority Name:</label>
                                                        <input type="text" class="form-control" id="issuing_authority_name" name="issuing_authority_name" value="<?php echo ($action == 'edit') ? $row['issuing_authority_name'] : ''; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="issuing_authority_rank">Issuing Authority Rank:</label>
                                                        <input type="text" class="form-control" id="issuing_authority_rank" name="issuing_authority_rank" value="<?php echo ($action == 'edit') ? $row['issuing_authority_rank'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="issuing_authority_appointment">Issuing Authority Appointment:</label>
                                                        <input type="text" class="form-control" id="issuing_authority_appointment" name="issuing_authority_appointment" value="<?php echo ($action == 'edit') ? $row['issuing_authority_appointment'] : ''; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="issuing_authority_email">Issuing Authority Email:</label>
                                                        <input type="email" class="form-control" id="issuing_authority_email" name="issuing_authority_email" value="<?php echo ($action == 'edit') ? $row['issuing_authority_email'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="issuing_authority_contact">Issuing Authority Contact:</label>
                                                        <input type="text" class="form-control" id="issuing_authority_contact" name="issuing_authority_contact" value="<?php echo ($action == 'edit') ? $row['issuing_authority_contact'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Verification Details -->
                                            <h5 class="section-title">Verification Details</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="verified_by">Verified By:</label>
                                                        <input type="text" class="form-control" id="verified_by" name="verified_by" value="<?php echo ($action == 'edit') ? $row['verified_by'] : ''; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="verified_date">Verified Date:</label>
                                                        <input type="date" class="form-control" id="verified_date" name="verified_date" value="<?php echo ($action == 'edit') ? $row['verified_date'] : ''; ?>" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Admin Verification Section (Visible only for admin users and when editing) -->
                                            <?php if ($_SESSION['directorate'] == 12 && $action == 'edit'): ?>
                                                <h5 class="section-title">Admin Verification</h5>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="verification_status">Verification Status:</label>
                                                            <select class="form-control" id="verification_status" name="verification_status">
                                                                <option value="pending" <?php echo ($verification_status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="approved" <?php echo ($verification_status == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                                                <option value="rejected" <?php echo ($verification_status == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="is_active">Active Status:</label>
                                                            <select class="form-control" id="is_active" name="is_active">
                                                                <option value="0" <?php echo ($row['is_active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                                                <option value="1" <?php echo ($row['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="rejection_reason">Rejection Reason (if rejected):</label>
                                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"><?php echo htmlspecialchars($rejection_reason, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label for="admin_notes">Admin Notes:</label>
                                                    <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"><?php echo htmlspecialchars($admin_notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($action == 'edit') { ?>
                                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <?php } ?>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary"><?php echo $buttonText; ?></button>
                                                <a href="all-certificates.php" class="btn btn-secondary">Cancel</a>
                                            </div>
                                        </form>

                                        <?php if ($action == 'edit' && $verification_status == 'rejected' && !empty($rejection_reason)): ?>
                                            <div class="alert alert-danger mt-3">
                                                <strong>Rejection Reason:</strong> <?php echo htmlspecialchars($rejection_reason, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($action == 'edit' && !empty($admin_notes)): ?>
                                            <div class="alert alert-info mt-3">
                                                <strong>Admin Notes:</strong> <?php echo htmlspecialchars($admin_notes, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        <?php endif; ?>
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
            // Show/hide rejection reason based on verification status
            $('#verification_status').change(function() {
                if ($(this).val() === 'rejected') {
                    $('#rejection_reason').closest('.form-group').show();
                } else {
                    $('#rejection_reason').closest('.form-group').hide();
                }
            });

            // Trigger change on page load
            $('#verification_status').trigger('change');
        });

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "all-certificates.php";
            }
        }
    </script>

    <style>
        .section-title {
            margin-top: 20px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            color: #495057;
            font-weight: 600;
        }
        .verification-status {
            position: absolute;
            right: 20px;
            top: 20px;
        }
        .badge {
            font-size: 0.9em;
            padding: 8px 12px;
        }
    </style>
</body>
</html>