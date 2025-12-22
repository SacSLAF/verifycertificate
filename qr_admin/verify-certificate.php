<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config.php";

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Get certificate ID and action from URL
$certificate_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'verify'; // 'verify' or 'dt_approve'

if (!$certificate_id) {
    header("location: all-certificates.php");
    exit;
}

// Fetch certificate details
$stmt = $conn->prepare("SELECT c.*, d.directorate_name, u.username as admin_verifier_name,
                        dt_user.username as dt_approver_name
                       FROM certificates c 
                       LEFT JOIN directorates d ON c.directorate_id = d.id 
                       LEFT JOIN users u ON c.verified_by_admin = u.id 
                       LEFT JOIN users dt_user ON c.dt_approved_by = dt_user.id 
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

$cert_type = $certificate['type'] ?? 1;
$user_type = $_SESSION['user_type'] ?? '';
$directorate_id = $_SESSION['directorate'] ?? 0;
$is_admin = ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'super_admin');

// Check permissions based on action
if ($action == 'dt_approve') {
    // Only Directorate of Training (directorate_id = 8) can approve at DT level
    if ($directorate_id != 8 && !$is_admin) {
        header("location: all-certificates.php?msg=no_dt_permission");
        exit;
    }
    // Check if certificate is type 1 (only type 1 needs DT approval)
    if ($cert_type != 1) {
        header("location: all-certificates.php?msg=not_type1");
        exit;
    }
    // Check if certificate is already verified
    if ($certificate['verification_status'] != 'approved') {
        header("location: all-certificates.php?msg=not_verified");
        exit;
    }
    // Check if already DT approved
    if ($certificate['dt_approved'] == 1) {
        header("location: all-certificates.php?msg=already_dt_approved");
        exit;
    }
} else {
    // Regular verification - check if user can verify
    // Institute Admin, DT Admin, or System Admin can verify
    if (!in_array($user_type, ['1', '3']) && !$is_admin) {
        header("location: all-certificates.php?msg=no_permission");
        exit;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;

    if (!$user_id) {
        // Try to get user ID from username
        $admin_username = $_SESSION['username'] ?? 'admin';
        $user_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $user_stmt->bind_param("s", $admin_username);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user_data = $user_result->fetch_assoc();
        $user_id = $user_data['id'] ?? 1;
        $user_stmt->close();
    }

    if (!$user_id) {
        $error = "User not authenticated properly.";
    } else {
        $conn->begin_transaction();

        try {
            if ($action == 'dt_approve') {
                // DT Approval process
                $dt_approved = $_POST['verification_status']; // Use same field name for consistency
                $rejection_reason = $_POST['rejection_reason'] ?? '';
                $admin_notes = $_POST['admin_notes'] ?? '';
                $is_active = isset($_POST['is_active']) ? 1 : 0;

                if ($dt_approved == 'approved') {
                    // Approve at DT level
                    $sql = "UPDATE certificates 
                           SET dt_approved = 1,
                               dt_approved_by = ?,
                               dt_approval_date = NOW(),
                               dt_rejection_reason = NULL,
                               is_active = ?,
                               admin_notes = CONCAT(IFNULL(admin_notes, ''), ' | DT Notes: ', ?)
                           WHERE id = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iisi", $user_id, $is_active, $admin_notes, $certificate_id);

                    $log_action = "DT Approval: APPROVED";
                } else {
                    // Reject at DT level
                    $sql = "UPDATE certificates 
                           SET dt_approved = 0,
                               dt_approved_by = ?,
                               dt_approval_date = NOW(),
                               dt_rejection_reason = ?,
                               verification_status = 'rejected',
                               is_active = 0,
                               admin_notes = CONCAT(IFNULL(admin_notes, ''), ' | DT Rejection: ', ?)
                           WHERE id = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("issi", $user_id, $rejection_reason, $admin_notes, $certificate_id);

                    $log_action = "DT Approval: REJECTED";
                }
            } else {
                // Normal verification process
                $verification_status = $_POST['verification_status'];
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $rejection_reason = $_POST['rejection_reason'] ?? '';
                $admin_notes = $_POST['admin_notes'] ?? '';

                // For type 1, cannot activate until DT approval
                if ($cert_type == 1 && $verification_status == 'approved') {
                    $is_active = 0; // Type 1 stays inactive until DT approval
                }

                // Reset DT approval if rejecting a type 1 certificate
                if ($cert_type == 1 && $verification_status == 'rejected') {
                    $sql = "UPDATE certificates 
                           SET verification_status = ?, 
                               is_active = ?, 
                               rejection_reason = ?, 
                               admin_notes = ?, 
                               verified_by_admin = ?, 
                               admin_verification_date = NOW(),
                               dt_approved = 0,
                               dt_approved_by = NULL,
                               dt_approval_date = NULL,
                               dt_rejection_reason = NULL
                           WHERE id = ?";
                } else {
                    $sql = "UPDATE certificates 
                           SET verification_status = ?, 
                               is_active = ?, 
                               rejection_reason = ?, 
                               admin_notes = ?, 
                               verified_by_admin = ?, 
                               admin_verification_date = NOW()
                           WHERE id = ?";
                }

                $stmt = $conn->prepare($sql);
                if ($cert_type == 1 && $verification_status == 'rejected') {
                    $stmt->bind_param(
                        "sissii",
                        $verification_status,
                        $is_active,
                        $rejection_reason,
                        $admin_notes,
                        $user_id,
                        $certificate_id
                    );
                } else {
                    $stmt->bind_param(
                        "sissii",
                        $verification_status,
                        $is_active,
                        $rejection_reason,
                        $admin_notes,
                        $user_id,
                        $certificate_id
                    );
                }

                $log_action = "Verification: " . strtoupper($verification_status);
            }

            $stmt->execute();
            $stmt->close();

            // Create verification log
            $log_sql = "INSERT INTO verification_logs 
                       (certificate_id, admin_id, action, ip_address, notes, created_at) 
                       VALUES (?, ?, ?, ?, ?, NOW())";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param(
                "iisss",
                $certificate_id,
                $user_id,
                $log_action,
                $user_ip,
                $admin_notes
            );
            $log_stmt->execute();
            $log_stmt->close();

            $conn->commit();
            header("location: all-certificates.php?msg=" . ($action == 'dt_approve' ? 'dt_verified' : 'verified'));
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error: " . $e->getMessage();
        }
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
?>

<!DOCTYPE html>
<html lang="en">
<?php include "template/head.php"; ?>
<style>
    .certificate-detail {
        color: #767676;
    }

    .verification-step {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        border-left: 4px solid #ddd;
    }

    .step-institute {
        border-left-color: #17a2b8;
        background-color: #f8f9fa;
    }

    .step-dt {
        border-left-color: #007bff;
        background-color: #f8f9fa;
    }

    .step-active {
        background-color: #e7f3ff;
    }

    .step-completed {
        background-color: #d4edda;
    }

    .step-current {
        background-color: #fff3cd;
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
                                        <h4 class="card-title">
                                            <?php
                                            if ($action == 'dt_approve') {
                                                echo 'DT Approval - Directorate of Training';
                                            } else {
                                                echo 'Certificate Verification';
                                            }
                                            ?>
                                            <small class="text-muted">(Type <?php echo $cert_type; ?>)</small>
                                        </h4>
                                        <div class="verification-status">
                                            <?php
                                            $status_badge = '';
                                            if ($action == 'dt_approve') {
                                                // DT Approval status
                                                if ($certificate['dt_approved'] == 1) {
                                                    $status_badge = '<span class="badge badge-success">DT Approved</span>';
                                                } else {
                                                    $status_badge = '<span class="badge badge-info">Pending DT Approval</span>';
                                                }
                                            } else {
                                                // Normal verification status
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

                                    <!-- Verification Steps for Type 1 -->
                                    <?php if ($cert_type == 1): ?>
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <h6>Two-Step Verification Process</h6>

                                                <!-- Step 1: Institute Verification -->
                                                <div class="verification-step step-institute 
                                                <?php echo $certificate['verification_status'] == 'approved' ? 'step-completed' : ($action == 'verify' ? 'step-current' : ''); ?>">
                                                    <h6><i class="fas fa-university"></i> Step 1: Institute Verification</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1">
                                                                <strong>Status:</strong>
                                                                <?php
                                                                switch ($certificate['verification_status']) {
                                                                    case 'approved':
                                                                        echo '<span class="badge badge-success">Approved</span>';
                                                                        break;
                                                                    case 'rejected':
                                                                        echo '<span class="badge badge-danger">Rejected</span>';
                                                                        break;
                                                                    default:
                                                                        echo '<span class="badge badge-warning">Pending</span>';
                                                                }
                                                                ?>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php if ($certificate['verified_by_admin']): ?>
                                                                <p class="mb-1"><strong>Verified By:</strong> <?php echo htmlspecialchars($certificate['admin_verifier_name'] ?? 'Unknown'); ?></p>
                                                                <p class="mb-1"><strong>Date:</strong> <?php echo date('M d, Y', strtotime($certificate['admin_verification_date'])); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Step 2: DT Approval -->
                                                <div class="verification-step step-dt 
                                                <?php echo $certificate['dt_approved'] == 1 ? 'step-completed' : ($action == 'dt_approve' ? 'step-current' : ''); ?>">
                                                    <h6><i class="fas fa-shield-alt"></i> Step 2: DT Approval (Directorate of Training)</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1">
                                                                <strong>Status:</strong>
                                                                <?php
                                                                if ($certificate['dt_approved'] == 1) {
                                                                    echo '<span class="badge badge-success">Approved</span>';
                                                                } elseif ($certificate['verification_status'] == 'approved') {
                                                                    echo '<span class="badge badge-info">Awaiting DT Approval</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-secondary">Pending Institute</span>';
                                                                }
                                                                ?>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php if ($certificate['dt_approved_by']): ?>
                                                                <p class="mb-1"><strong>Approved By:</strong> <?php echo htmlspecialchars($certificate['dt_approver_name'] ?? 'Unknown'); ?></p>
                                                                <p class="mb-1"><strong>Date:</strong> <?php echo date('M d, Y', strtotime($certificate['dt_approval_date'])); ?></p>
                                                            <?php elseif ($certificate['verification_status'] == 'approved'): ?>
                                                                <p class="mb-1"><span class="badge badge-warning">Ready for DT Approval</span></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Certificate Details -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title">Certificate Details</h5>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Keep all your existing certificate details -->
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
                                                            <!-- Continue from where we left off in the previous file -->

                                                            <div class="form-group">
                                                                <label><strong>Certificate Type:</strong></label>
                                                                <p class="certificate-detail">
                                                                    <?php
                                                                    if ($cert_type == 1) {
                                                                        echo 'Training Certificate (Requires DT Approval)';
                                                                    } elseif ($cert_type == 2) {
                                                                        echo 'Training Certificate';
                                                                    } else {
                                                                        echo 'Leaving Service Certificate';
                                                                    }
                                                                    ?>
                                                                </p>
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

                                                    <!-- Course Details for Type 1 -->
                                                    <?php if ($cert_type == 1): ?>
                                                        <div class="row mt-3">
                                                            <div class="col-md-12">
                                                                <h6>Course Details</h6>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label><strong>Course Name:</strong></label>
                                                                            <p class="certificate-detail"><?php echo htmlspecialchars($certificate['course_name'] ?? ''); ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label><strong>Course Duration:</strong></label>
                                                                            <p class="certificate-detail"><?php echo htmlspecialchars($certificate['course_duration'] ?? ''); ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label><strong>Course Description:</strong></label>
                                                                            <p class="certificate-detail"><?php echo htmlspecialchars($certificate['course_description'] ?? ''); ?></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

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
                                                            <?php if ($cert_type == 1): ?>
                                                                <div class="row mt-2">
                                                                    <div class="col-md-6">
                                                                        <label><strong>DT Approved By:</strong></label>
                                                                        <p class="certificate-detail"><?php echo htmlspecialchars($certificate['dt_approver_name'] ?? 'Not DT approved yet'); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label><strong>DT Approval Date:</strong></label>
                                                                        <p class="certificate-detail"><?php echo htmlspecialchars($certificate['dt_approval_date'] ?? ''); ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <!-- Verification/Approval Form -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title">
                                                        <?php
                                                        if ($action == 'dt_approve') {
                                                            echo 'DT Approval Action';
                                                        } else {
                                                            echo 'Verification Action';
                                                        }
                                                        ?>
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <form method="POST" action="">
                                                        <div class="form-group">
                                                            <label for="verification_status">
                                                                <?php
                                                                if ($action == 'dt_approve') {
                                                                    echo 'DT Approval Status *';
                                                                } else {
                                                                    echo 'Verification Status *';
                                                                }
                                                                ?>
                                                            </label>
                                                            <select class="form-control" id="verification_status" name="verification_status" required>
                                                                <option value="" selected disabled>Select a status</option>
                                                                <?php if ($action == 'dt_approve'): ?>
                                                                    <option value="approved" <?php echo ($certificate['dt_approved'] == 1) ? 'selected' : ''; ?>>Approve</option>
                                                                    <option value="rejected" <?php echo ($certificate['verification_status'] == 'rejected') ? 'selected' : ''; ?>>Reject</option>
                                                                <?php else: ?>
                                                                    <option value="pending" <?php echo $certificate['verification_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                    <option value="approved" <?php echo $certificate['verification_status'] == 'approved' ? 'selected' : ''; ?>>Approve</option>
                                                                    <option value="rejected" <?php echo $certificate['verification_status'] == 'rejected' ? 'selected' : ''; ?>>Reject</option>
                                                                <?php endif; ?>
                                                            </select>
                                                        </div>

                                                        <!-- Active Checkbox - Different logic for DT approval -->
                                                        <?php if ($action == 'dt_approve'): ?>
                                                            <div class="form-group">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                                                        <?php echo ($certificate['is_active'] == 1) ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="is_active">
                                                                        Activate Certificate (Make Public)
                                                                    </label>
                                                                    <small class="form-text text-muted">Certificate will be publicly accessible after DT approval</small>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($cert_type != 1): ?>
                                                            <!-- For non-type 1 certificates, show active checkbox during normal verification -->
                                                            <div class="form-group">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                                                        <?php echo $certificate['is_active'] == 1 ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="is_active">
                                                                        Mark as Active
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <!-- For type 1 during normal verification, show info message -->
                                                            <div class="alert alert-info">
                                                                <small><i class="fas fa-info-circle"></i> Type 1 certificates require DT approval before they can be activated.</small>
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- Rejection Reason -->
                                                        <div class="form-group" id="rejection_reason_group" style="display: none;">
                                                            <label for="rejection_reason">
                                                                <?php
                                                                if ($action == 'dt_approve') {
                                                                    echo 'DT Rejection Reason *';
                                                                } else {
                                                                    echo 'Rejection Reason *';
                                                                }
                                                                ?>
                                                            </label>
                                                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"
                                                                placeholder="Please provide reason for rejection...">
                        <?php
                        if ($action == 'dt_approve') {
                            echo htmlspecialchars($certificate['dt_rejection_reason'] ?? '');
                        } else {
                            echo htmlspecialchars($certificate['rejection_reason'] ?? '');
                        }
                        ?>
                    </textarea>
                                                        </div>

                                                        <!-- Notes -->
                                                        <div class="form-group">
                                                            <label for="admin_notes">
                                                                <?php
                                                                if ($action == 'dt_approve') {
                                                                    echo 'DT Approval Notes';
                                                                } else {
                                                                    echo 'Admin Notes';
                                                                }
                                                                ?>
                                                            </label>
                                                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                                                placeholder="Any additional notes or comments...">
                        <?php echo htmlspecialchars($certificate['admin_notes'] ?? ''); ?>
                    </textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-sm 
                        <?php
                        if ($action == 'dt_approve') {
                            echo 'btn-primary';
                        } else {
                            echo 'btn-success';
                        }
                        ?> btn-block">
                                                                <i class="fas fa-check-circle"></i>
                                                                <?php
                                                                if ($action == 'dt_approve') {
                                                                    echo 'Submit DT Approval';
                                                                } else {
                                                                    echo 'Update Verification';
                                                                }
                                                                ?>
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
                var selectedStatus = $('#verification_status').val();
                var isRejected = (selectedStatus === 'rejected');

                if (isRejected) {
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
                var selectedStatus = $('#verification_status').val();
                var rejectionReason = $('#rejection_reason').val().trim();

                if (selectedStatus === 'rejected' && rejectionReason === '') {
                    alert('Please provide a rejection reason.');
                    return false;
                }

                // For DT approval, warn if rejecting
                <?php if ($action == 'dt_approve'): ?>
                    if (selectedStatus === 'rejected') {
                        if (!confirm('Are you sure you want to reject this DT approval? This will mark the certificate as rejected.')) {
                            return false;
                        }
                    }
                <?php endif; ?>

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