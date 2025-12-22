<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config.php";

// Check if the user is logged in and is admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Get certificate ID from URL
if (!isset($_GET['id'])) {
    header("location: all-certificates.php");
    exit;
}

$certificate_id = $_GET['id'];

// Fetch certificate details
$stmt = $conn->prepare("SELECT c.*, d.directorate_name, u.username as admin_verifier_name 
                       FROM certificates c 
                       LEFT JOIN directorates d ON c.directorate_id = d.id 
                       LEFT JOIN users u ON c.verified_by_admin = u.id 
                       WHERE c.id = ?");
$stmt->bind_param("i", $certificate_id);
$stmt->execute();
$result = $stmt->get_result();
$certificate = $result->fetch_assoc();
$stmt->close();

if (!$certificate) {
    header("location: all-certificates.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $verification_status = $_POST['verification_status'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $rejection_reason = $_POST['rejection_reason'] ?? '';
    $admin_notes = $_POST['admin_notes'] ?? '';

    // Get user IP address
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Get admin user ID - FIX: Check if user_id exists in session, otherwise use a default or get from users table
    if (isset($_SESSION['user_id'])) {
        $admin_id = $_SESSION['user_id'];
    } else {
        // If user_id is not in session, try to get it from the users table using username or email
        $admin_username = $_SESSION['username'] ?? 'admin'; // Adjust based on your session variables
        $user_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?  LIMIT 1");
        $user_stmt->bind_param("s", $admin_username);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user_data = $user_result->fetch_assoc();
        $admin_id = $user_data['id'] ?? 1; // Use default admin ID 1 if not found
        $user_stmt->close();
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // var_dump($verification_status);exit;
        // Update certificate verification status
        $update_stmt = $conn->prepare("UPDATE certificates 
                                     SET verification_status = ?, 
                                         is_active = ?, 
                                         rejection_reason = ?, 
                                         admin_notes = ?, 
                                         verified_by_admin = ?, 
                                         admin_verification_date = NOW() 
                                     WHERE id = ?");
        $update_stmt->bind_param(
            "sissii",
            $verification_status,
            $is_active,
            $rejection_reason,
            $admin_notes,
            $admin_id, // Use the resolved admin_id
            $certificate_id
        );
        $update_stmt->execute();
        $update_stmt->close();

        // Create verification log
        $action = "Certificate " . strtoupper($verification_status);
        $log_stmt = $conn->prepare("INSERT INTO verification_logs 
                                   (certificate_id, admin_id, action, ip_address, notes, created_at) 
                                   VALUES (?, ?, ?, ?, ?, NOW())");
        $log_stmt->bind_param(
            "iisss",
            $certificate_id,
            $admin_id, // Use the resolved admin_id
            $action,
            $user_ip,
            $admin_notes
        );
        $log_stmt->execute();
        $log_stmt->close();

        // Commit transaction
        $conn->commit();

        // Redirect with success message
        header("location: all-certificates.php?msg=verified");
        exit;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error = "Error updating certificate: " . $e->getMessage();
    }
}

// Create verification logs table if it doesn't exist
$create_log_table = "CREATE TABLE IF NOT EXISTS verification_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    certificate_id INT NOT NULL,
    admin_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_certificate_id (certificate_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_created_at (created_at)
)";
$conn->query($create_log_table);

// Fetch verification history
$log_stmt = $conn->prepare("SELECT vl.*, u.username 
                           FROM verification_logs vl 
                           LEFT JOIN users u ON vl.admin_id = u.id 
                           WHERE vl.certificate_id = ? 
                           ORDER BY vl.created_at DESC");
$log_stmt->bind_param("i", $certificate_id);
$log_stmt->execute();
$verification_logs = $log_stmt->get_result();
// $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include "template/head.php"; ?>
<style>
    .certificate-detail {
        color:#767676;
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
                                        <h4 class="card-title">Verify Certificate</h4>
                                        <div class="verification-status">
                                            <?php
                                            $status_badge = '';
                                            switch ($certificate['verification_status']) {
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
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Certificate Details -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title">Certificate Details</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><strong>Certificate ID:</strong></label>
                                                                <p class="certificate-detail"><?php echo htmlspecialchars($certificate['certificate_id'] ?? ''); ?></p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label><strong>Name:</strong></label>
                                                                <p class="certificate-detail"><?php echo htmlspecialchars($certificate['name'] ?? ''); ?></p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label><strong>Service No:</strong></label>
                                                                <p class="certificate-detail"><?php echo htmlspecialchars($certificate['service_no'] ?? ''); ?></p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label><strong>Rank:</strong></label>
                                                                <p class="certificate-detail"><?php echo htmlspecialchars($certificate['rank'] ?? ''); ?></p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label><strong>Directorate:</strong></label>
                                                                <p class="certificate-detail"><?php echo htmlspecialchars($certificate['directorate_name'] ?? ''); ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><strong>Date of Issue:</strong></label>
                                                                <p class="certificate-detail"><?php echo htmlspecialchars($certificate['date_of_issue'] ?? ''); ?></p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label><strong>NIC No:</strong></label>
                                                                <p class="certificate-detail"><?php echo htmlspecialchars($certificate['nic_no'] ?? ''); ?></p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label><strong>Passport No:</strong></label>
                                                                <p class="certificate-detail"><?php echo htmlspecialchars($certificate['passport_no'] ?? ''); ?></p>
                                                            </div>
                                                            <?php if (!empty($certificate['image'])): ?>
                                                                <div class="form-group">
                                                                    <label><strong>Photo:</strong></label><br>
                                                                    <img src="../certificate-photo/<?php echo htmlspecialchars($certificate['image']); ?>"
                                                                        alt="Certificate Photo" style="max-width: 200px; max-height: 200px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- Additional Details -->
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><strong>Experience:</strong></label>
                                                                <p class="certificate-detail"><?php echo nl2br(htmlspecialchars($certificate['experience'] ?? '')); ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><strong>Qualifications:</strong></label>
                                                                <p class="certificate-detail"><?php echo nl2br(htmlspecialchars($certificate['qualifications'] ?? '')); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Issuing Authority -->
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <h6>Issuing Authority Details</h6>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label><strong>Name:</strong></label>
                                                                    <p class="certificate-detail"><?php echo htmlspecialchars($certificate['issuing_authority_name'] ?? ''); ?></p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label><strong>Rank:</strong></label>
                                                                    <p class="certificate-detail"><?php echo htmlspecialchars($certificate['issuing_authority_rank'] ?? ''); ?></p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label><strong>Appointment:</strong></label>
                                                                    <p class="certificate-detail"><?php echo htmlspecialchars($certificate['issuing_authority_appointment'] ?? ''); ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-md-6">
                                                                    <label><strong>Email:</strong></label>
                                                                    <p class="certificate-detail"><?php echo htmlspecialchars($certificate['issuing_authority_email'] ?? ''); ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label><strong>Contact:</strong></label>
                                                                    <p class="certificate-detail"><?php echo htmlspecialchars($certificate['issuing_authority_contact'] ?? ''); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Verification Details -->
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <h6>Verification Details</h6>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label><strong>Verified By:</strong></label>
                                                                    <p class="certificate-detail"><?php echo htmlspecialchars($certificate['verified_by'] ?? ''); ?></p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label><strong>Verified Date:</strong></label>
                                                                    <p class="certificate-detail"><?php echo htmlspecialchars($certificate['verified_date'] ?? ''); ?></p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label><strong>Admin Verifier:</strong></label>
                                                                    <p class="certificate-detail"><?php echo htmlspecialchars($certificate['admin_verifier_name'] ?? 'Not verified yet'); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <!-- Verification Form -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title">Verification Action</h5>
                                                </div>
                                                <div class="card-body">
                                                    <form method="POST" action="">
                                                        <div class="form-group">
                                                            <label for="verification_status">Verification Status *</label>
                                                            <select class="form-control" id="verification_status" name="verification_status" required>
                                                                <option value="" selected disabled>Select a status</option>
                                                                <option value="pending" <?php echo $certificate['verification_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="approved" <?php echo $certificate['verification_status'] == 'approved' ? 'selected' : ''; ?>>Approve</option>
                                                                <option value="rejected" <?php echo $certificate['verification_status'] == 'rejected' ? 'selected' : ''; ?>>Reject</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                                                    <?php echo $certificate['is_active'] == 1 ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="is_active">
                                                                    Mark as Active
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group" id="rejection_reason_group" style="display: none;">
                                                            <label for="rejection_reason">Rejection Reason</label>
                                                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"
                                                                placeholder="Please provide reason for rejection..."><?php echo htmlspecialchars($certificate['rejection_reason']); ?></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="admin_notes">Admin Notes</label>
                                                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                                                placeholder="Any additional notes or comments..."><?php echo htmlspecialchars($certificate['admin_notes'] ?? ''); ?></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-sm btn-success btn-block">
                                                                <i class="fas fa-check-circle"></i> Update Verification
                                                            </button>
                                                            <a href="all-certificates.php" class="btn btn-sm btn-secondary btn-block">Cancel</a>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Verification History -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5 class="card-title">Verification History</h5>
                                                </div>
                                                <div class="card-body">
                                                    <?php if ($verification_logs->num_rows > 0): ?>
                                                        <div class="timeline">
                                                            <?php while ($log = $verification_logs->fetch_assoc()): ?>
                                                                <div class="timeline-item">
                                                                    <div class="timeline-marker"></div>
                                                                    <div class="timeline-content">
                                                                        <small class="text-muted">
                                                                            <?php echo date('M j, Y g:i A', strtotime($log['created_at'] ?? '')); ?>
                                                                        </small>
                                                                        <p class="mb-1">
                                                                            <strong><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></strong>
                                                                            <?php echo htmlspecialchars($log['action'] ?? ''); ?>
                                                                        </p>
                                                                        <small class="text-muted">
                                                                            IP: <?php echo htmlspecialchars($log['ip_address'] ?? ''); ?>
                                                                            <?php if (!empty($log['notes'])): ?>
                                                                                <br>Notes: <?php echo htmlspecialchars($log['notes']); ?>
                                                                            <?php endif; ?>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            <?php endwhile; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <p class="text-muted">No verification history found.</p>
                                                    <?php endif; ?>
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

            <?php
            // Close the statement after we're done using it
            if (isset($log_stmt)) {
                $log_stmt->close();
            }
            ?>
            <?php include 'template/foot.php'; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Show/hide rejection reason based on verification status
            function toggleRejectionReason() {
                if ($('#verification_status').val() === 'rejected') {
                    $('#rejection_reason_group').show();
                    $('#rejection_reason').prop('required', true);
                } else {
                    $('#rejection_reason_group').hide();
                    $('#rejection_reason').prop('required', false);
                }
            }

            // Initial state
            toggleRejectionReason();

            // Change event
            $('#verification_status').change(function() {
                toggleRejectionReason();
            });

            // Form validation
            $('form').submit(function() {
                if ($('#verification_status').val() === 'rejected' && $('#rejection_reason').val().trim() === '') {
                    alert('Please provide a rejection reason.');
                    return false;
                }
                return true;
            });
        });
    </script>

    <style>
        .timeline {
            position: relative;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 15px;
        }

        .timeline-marker {
            position: absolute;
            left: -20px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #007bff;
        }

        .timeline-content {
            padding-bottom: 10px;
            border-left: 2px solid #e9ecef;
            padding-left: 15px;
        }

        .timeline-item:last-child .timeline-content {
            border-left: none;
        }

        .badge {
            font-size: 0.9em;
            padding: 8px 12px;
        }
    </style>
</body>

</html>