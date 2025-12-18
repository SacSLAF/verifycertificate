<?php
session_start(); // Start the session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../config.php';

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Get user type from session
// var_dump($_SESSION); exit;s
$user_type = $_SESSION['user_type'] ?? '1'; // Default to type 1 if not set
// var_dump($user_type); exit;
$action = '';
// Check if editing existing certificate
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Step 2: Construct the SQL query
    $sql = "SELECT * FROM `certificates` WHERE `id` = $id";
    $result = mysqli_query($conn, $sql);
    $row = $result->fetch_assoc();
    // Get certificate type from database if editing
    $certificate_type = $row['type'] ?? $user_type;
} else {
    $certificate_type = $user_type;
}
// var_dump($certificate_type); exit;
$action = isset($_GET['action']) ? $_GET['action'] : 'add';

$buttonText = 'Submit'; // Default button text
if ($action === 'edit') {
    $buttonText = 'Update';
}

// Assuming $row['image'] contains the image filename from the database
$imagePath = '../certificate-photo/'; // Set the correct path to your images directory
$existingImage = ($action == 'edit' && !empty($row['image'])) ? $imagePath . $row['image'] : '';

// Page title based on certificate type
$pageTitle = ($certificate_type == '3') ? 'Leaving Service Certificate' : 'Training Certificate';

?>
<!DOCTYPE html>
<html lang="en">

<?php include "template/head.php"; ?>

<style>
    /* Form styling */
    .form-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #007bff;
    }
    
    .form-section h5 {
        color: #495057;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #dee2e6;
    }
    
    /* Required field indicator */
    .required::after {
        content: " *";
        color: #dc3545;
    }
    
    /* Toggle for form type */
    .form-type-toggle {
        background: #e9ecef;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    /* Hide sections based on certificate type */
    .type-3-only {
        display: <?= ($certificate_type == '3') ? 'block' : 'none' ?>;
    }
    
    .type-12-only {
        display: <?= ($certificate_type != '3') ? 'block' : 'none' ?>;
    }
</style>

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
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="card-title"><?php echo $buttonText; ?> <?php echo $pageTitle; ?></div>
                                        <div class="badge badge-<?php echo ($certificate_type == '3') ? 'danger' : 'primary'; ?>">
                                            <?php echo ($certificate_type == '3') ? 'Leaving Service' : 'Training Certificate'; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <form action="control/certificate_process.php?action=<?php echo $action; ?>" method="POST" enctype="multipart/form-data" id="certificateForm">
                                                <!-- Hidden fields -->
                                                <input type="hidden" name="camp" value="<?= $_SESSION['camp'] ?? ''; ?>">
                                                <input type="hidden" name="institute" value="<?= $_SESSION['institute'] ?? ''; ?>">
                                                <input type="hidden" name="directorate" value="<?= $_SESSION['directorate'] ?? ''; ?>">
                                                <input type="hidden" name="certificate_type" value="<?= $certificate_type; ?>">
                                                <?php if ($action == 'edit') { ?>
                                                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                                                <?php } ?>
                                                
                                                <!-- Common Fields for ALL certificate types -->
                                                <div class="form-section">
                                                    <h5><i class="fas fa-id-card"></i> Basic Information</h5>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="certificate_id" class="required">Certificate ID</label>
                                                                <input type="text" class="form-control" id="certificate_id" name="certificate_id" 
                                                                       value="<?php echo ($action == 'edit') ? $row['certificate_id'] : ''; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="date_of_issue" class="required">Date of Issue</label>
                                                                <input type="date" class="form-control" id="date_of_issue" name="date_of_issue" 
                                                                       value="<?php echo ($action == 'edit') ? $row['date_of_issue'] : date('Y-m-d'); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="image">Photograph</label>
                                                                <?php if (!empty($existingImage)): ?>
                                                                    <div class="mb-2">
                                                                        <img src="<?php echo htmlspecialchars($existingImage, ENT_QUOTES, 'UTF-8'); ?>" 
                                                                             alt="Existing Image" style="max-width: 100px; max-height: 100px; border-radius: 4px;">
                                                                    </div>
                                                                <?php endif; ?>
                                                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" 
                                                                       <?php echo ($action == 'add') ? 'required' : ''; ?>>
                                                                <small class="form-text text-muted">Max size: 2MB. Formats: JPG, PNG</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Personnel Information -->
                                                <div class="form-section">
                                                    <h5><i class="fas fa-user"></i> Personnel Information</h5>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="name" class="required">Full Name</label>
                                                                <input type="text" class="form-control" id="name" name="name" 
                                                                       value="<?php echo ($action == 'edit') ? $row['name'] : ''; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="service_no" class="required">Service No</label>
                                                                <input type="text" class="form-control" id="service_no" name="service_no" 
                                                                       value="<?php echo ($action == 'edit') ? $row['service_no'] : ''; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="rank" class="required">Rank</label>
                                                                <select class="form-control" id="rank" name="rank" required>
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
                                                    </div>
                                                    
                                                    <!-- For Training Certificates (Type 1 & 2) -->
                                                    <div class="type-12-only">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="course_name" class="required">Course Name</label>
                                                                    <input type="text" class="form-control" id="course_name" name="course_name" 
                                                                           value="<?php echo ($action == 'edit') ? $row['course_name'] : ''; ?>"
                                                                           <?php echo ($certificate_type != '3') ? 'required' : ''; ?>>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="course_duration" class="required">Course Duration</label>
                                                                    <input type="text" class="form-control" id="course_duration" name="course_duration" 
                                                                           placeholder="e.g., 3 months, 6 weeks, etc."
                                                                           value="<?php echo ($action == 'edit') ? $row['course_duration'] : ''; ?>"
                                                                           <?php echo ($certificate_type != '3') ? 'required' : ''; ?>>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="course_description">Course Description</label>
                                                                    <textarea class="form-control" id="course_description" name="course_description" 
                                                                              rows="3"><?php echo ($action == 'edit') ? $row['course_description'] : ''; ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- For Leaving Service Certificates (Type 3) -->
                                                    <div class="type-3-only">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="passport_no">Passport No</label>
                                                                    <input type="text" class="form-control" id="passport_no" name="passport_no" 
                                                                           value="<?php echo ($action == 'edit') ? $row['passport_no'] : ''; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="nic_no">NIC No</label>
                                                                    <input type="text" class="form-control" id="nic_no" name="nic_no" 
                                                                           value="<?php echo ($action == 'edit') ? $row['nic_no'] : ''; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="total_service">Total Service</label>
                                                                    <input type="text" class="form-control" id="total_service" name="total_service" 
                                                                           placeholder="e.g., 20 years, 6 months"
                                                                           value="<?php echo ($action == 'edit') ? $row['total_service'] : ''; ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="date_of_enlistment">Date of Enlistment</label>
                                                                    <input type="date" class="form-control" id="date_of_enlistment" name="date_of_enlistment" 
                                                                           value="<?php echo ($action == 'edit') ? $row['date_of_enlistment'] : ''; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="date_of_retirement">Date of Retirement</label>
                                                                    <input type="date" class="form-control" id="date_of_retirement" name="date_of_retirement" 
                                                                           value="<?php echo ($action == 'edit') ? $row['date_of_retirement'] : ''; ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Experience & Qualifications (Type 3 Only) -->
                                                <div class="form-section type-3-only">
                                                    <h5><i class="fas fa-briefcase"></i> Experience & Qualifications</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="experience">Experience, Knowledge & Skills</label>
                                                                <textarea class="form-control" id="experience" name="experience" rows="4"><?php echo ($action == 'edit') ? $row['experience'] : ''; ?></textarea>
                                                                <small class="form-text text-muted">Enter each experience item separated by commas or new lines.</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="qualifications">Qualifications, Awards & Achievements</label>
                                                                <textarea class="form-control" id="qualifications" name="qualifications" rows="4"><?php echo ($action == 'edit') ? $row['qualifications'] : ''; ?></textarea>
                                                                <small class="form-text text-muted">Enter each qualification item separated by commas or new lines.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Issuing Authority (Common for all) -->
                                                <div class="form-section">
                                                    <h5><i class="fas fa-stamp"></i> Issuing Authority</h5>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="issuing_authority_name" class="required">Name</label>
                                                                <input type="text" class="form-control" id="issuing_authority_name" name="issuing_authority_name" 
                                                                       value="<?php echo ($action == 'edit') ? $row['issuing_authority_name'] : ''; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="issuing_authority_rank">Rank</label>
                                                                <input type="text" class="form-control" id="issuing_authority_rank" name="issuing_authority_rank" 
                                                                       value="<?php echo ($action == 'edit') ? $row['issuing_authority_rank'] : ''; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="issuing_authority_appointment">Appointment</label>
                                                                <input type="text" class="form-control" id="issuing_authority_appointment" name="issuing_authority_appointment" 
                                                                       value="<?php echo ($action == 'edit') ? $row['issuing_authority_appointment'] : ''; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="issuing_authority_email">Email</label>
                                                                <input type="email" class="form-control" id="issuing_authority_email" name="issuing_authority_email" 
                                                                       value="<?php echo ($action == 'edit') ? $row['issuing_authority_email'] : ''; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="issuing_authority_contact">Contact No</label>
                                                                <input type="text" class="form-control" id="issuing_authority_contact" name="issuing_authority_contact" 
                                                                       value="<?php echo ($action == 'edit') ? $row['issuing_authority_contact'] : ''; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Verification (Common for all) -->
                                                <div class="form-section">
                                                    <h5><i class="fas fa-check-circle"></i> Verification</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="verified_by" class="required">Verified By</label>
                                                                <input type="text" class="form-control" id="verified_by" name="verified_by" 
                                                                       value="<?php echo ($action == 'edit') ? $row['verified_by'] : ''; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="verified_date" class="required">Verification Date</label>
                                                                <input type="date" class="form-control" id="verified_date" name="verified_date" 
                                                                       value="<?php echo ($action == 'edit') ? $row['verified_date'] : date('Y-m-d'); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Submit Buttons -->
                                                <div class="row mt-4">
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <button type="submit" class="btn btn-primary btn-lg">
                                                                    <i class="fas fa-save"></i> <?php echo $buttonText; ?> Certificate
                                                                </button>
                                                                <button type="reset" class="btn btn-secondary btn-lg">
                                                                    <i class="fas fa-redo"></i> Reset Form
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <a href="all-certificates.php" class="btn btn-light btn-lg">
                                                                    <i class="fas fa-times"></i> Cancel
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            
                                            <!-- Back Button -->
                                            <div class="row mt-4">
                                                <div class="col-md-12 text-center">
                                                    <button class="btn btn-outline-secondary" onclick="goBack()">
                                                        <i class="fas fa-arrow-left"></i> Go Back
                                                    </button>
                                                </div>
                                            </div>
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
            // Initialize DataTable if needed
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
            
            // Set today's date for date fields if empty
            if ($('#date_of_issue').val() === '') {
                $('#date_of_issue').val(new Date().toISOString().split('T')[0]);
            }
            if ($('#verified_date').val() === '') {
                $('#verified_date').val(new Date().toISOString().split('T')[0]);
            }
            
            // Form validation
            $('#certificateForm').submit(function(e) {
                let isValid = true;
                let errorMessage = '';
                
                // Common validation
                if (!$('#certificate_id').val()) {
                    isValid = false;
                    errorMessage = 'Certificate ID is required';
                } else if (!$('#name').val()) {
                    isValid = false;
                    errorMessage = 'Name is required';
                } else if (!$('#service_no').val()) {
                    isValid = false;
                    errorMessage = 'Service No is required';
                } else if (!$('#rank').val()) {
                    isValid = false;
                    errorMessage = 'Rank is required';
                } else if (!$('#issuing_authority_name').val()) {
                    isValid = false;
                    errorMessage = 'Issuing Authority Name is required';
                } else if (!$('#verified_by').val()) {
                    isValid = false;
                    errorMessage = 'Verified By is required';
                }
                
                // Type-specific validation
                const certType = '<?php echo $certificate_type; ?>';
                
                if (certType !== '3') {
                    // Training certificate validation
                    if (!$('#course_name').val()) {
                        isValid = false;
                        errorMessage = 'Course Name is required for training certificates';
                    } else if (!$('#course_duration').val()) {
                        isValid = false;
                        errorMessage = 'Course Duration is required for training certificates';
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Error: ' + errorMessage);
                    return false;
                }
                
                return true;
            });
        });

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "all-certificates.php";
            }
        }
    </script>
</body>
</html>