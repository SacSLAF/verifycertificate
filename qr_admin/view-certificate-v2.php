<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

include("../config.php");
error_reporting(E_ALL);
$id = $_GET['id'] ?? '';

// Check if the id is not empty and matches the UUID pattern
if (!empty($id) && preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $id)) {
    // Get certificate data including type
    $user_data = getUserByCertificateId($id);

    if ($user_data) {
        // Store the data in variables
        $certificate_id = $user_data['certificate_id'] ?? '';
        $date_of_issue = $user_data['date_of_issue'] ?? '';
        $image = $user_data['image'] ?? '';
        $image_path = '../certificate-photo/' . $image;
        $name = $user_data['name'] ?? '';
        $service_no = $user_data['service_no'] ?? '';
        $rank = $user_data['rank'] ?? '';
        $passport_no = $user_data['passport_no'] ?? '';
        $nic_no = $user_data['nic_no'] ?? '';
        $date_of_enlistment = $user_data['date_of_enlistment'] ?? '';
        $date_of_retirement = $user_data['date_of_retirement'] ?? '';
        $total_service = $user_data['total_service'] ?? '';
        $experience = $user_data['experience'] ?? '';
        $qualifications = $user_data['qualifications'] ?? '';
        $course_name = $user_data['course_name'] ?? '';
        $course_duration = $user_data['course_duration'] ?? '';
        $course_description = $user_data['course_description'] ?? '';
        $issuing_authority_name = $user_data['issuing_authority_name'] ?? '';
        $issuing_authority_rank = $user_data['issuing_authority_rank'] ?? '';
        $issuing_authority_appointment = $user_data['issuing_authority_appointment'] ?? '';
        $issuing_authority_email = $user_data['issuing_authority_email'] ?? '';
        $issuing_authority_contact = $user_data['issuing_authority_contact'] ?? '';
        $verified_by = $user_data['verified_by'] ?? '';
        $verified_date = $user_data['verified_date'] ?? '';
        $certificate_uuid = $user_data['certificate_uuid'] ?? '';
        $certificate_directorate = $user_data['directorate_id'] ?? '';
        $certificate_type = $user_data['type'] ?? '1'; // Default to type 1
        $institute_id = $user_data['institute_id'] ?? '';
        $camp_id = $user_data['camp_id'] ?? '';
    } else {
        // If no user data found, display a message
        $error_message = 'No user data found. Please contact support.<br><br><table border="1">
                    <tr>
                        <td rowspan="2" style="padding:10px;">Contact details</td>
                        <td style="padding:10px;">General : +94112441044 Ext: 11009 / +94772229006</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;">Private : +94714283280</td>
                    </tr>
                </table>';
    }
} else {
    // If id is invalid or empty, display a message
    $error_message = 'No user data found. Please contact support.<br><br><table border="1">
                    <tr>
                        <td rowspan="2" style="padding:10px;">Contact details</td>
                        <td style="padding:10px;">General : +94112441044 Ext: 11009 / +94772229006</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;">Private : +94714283280</td>
                    </tr>
                </table>';
}

// Fetch organization name based on certificate type
$organization_name = "SRI LANKA AIR FORCE";
if ($certificate_type == '3') {
    // Type 3: Get directorate name
    if (isset($certificate_directorate) && !empty($certificate_directorate)) {
        $stmt = $conn->prepare("SELECT `directorate_name` FROM directorates WHERE id = ?");
        $stmt->bind_param("i", $certificate_directorate);
        $stmt->execute();
        $stmt->bind_result($db_org_name);
        if ($stmt->fetch()) {
            $organization_name = "" . strtoupper($db_org_name) . " <br> SRI LANKA AIR FORCE";
        }
        $stmt->close();
    }
} else {
    if (isset($institute_id) && !empty($institute_id)) {
        $stmt = $conn->prepare("SELECT name FROM training_institutes WHERE id = ?");
        $stmt->bind_param("i", $institute_id);
        $stmt->execute();
        $stmt->bind_result($db_org_name);
        if ($stmt->fetch()) {
            $organization_name = "" . strtoupper($db_org_name) . " <br> SRI LANKA AIR FORCE";
        } else {
            // Fallback to camp name
            if (isset($camp_id) && !empty($camp_id)) {
                $stmt = $conn->prepare("SELECT name FROM camps WHERE id = ?");
                $stmt->bind_param("i", $camp_id);
                $stmt->execute();
                $stmt->bind_result($db_org_name);
                if ($stmt->fetch()) {
                    $organization_name = "" . strtoupper($db_org_name) . " <br> SRI LANKA AIR FORCE";
                }
            }
        }
        $stmt->close();
    }
}

function getUserByCertificateId($id)
{
    global $conn;
    // Prepare the query
    $stmt = $conn->prepare("SELECT * FROM certificates WHERE certificate_uuid = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch user data
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/main.css">
    <style>
        /* Type 3 (Leaving Service) specific styles */
        .certificate-type-3 .certificate {
            background: #f9f9f9;
            padding: 30px;
            border: 2px solid #333;
            border-radius: 10px;
        }
        
        .certificate-type-3 .header {
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .certificate-type-3 .logo {
            max-height: 80px;
        }
        
        .certificate-type-3 .student-image {
            max-width: 150px;
            border: 3px solid #333;
            border-radius: 5px;
            margin: 20px auto;
        }
        
        .certificate-type-3 .custom-table {
            margin-bottom: 20px;
        }
        
        .certificate-type-3 .table-head {
            background-color: #343a40;
            color: white;
        }
        
        /* Type 1/2 (Training) specific styles - SIMPLIFIED */
        .certificate-type-12 .certificate {
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .certificate-type-12 .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .certificate-type-12 .logo {
            max-height: 60px;
        }
        
        .certificate-type-12 .student-image {
            max-width: 100px;
            border: 2px solid #ddd;
            border-radius: 5px;
            margin: 10px auto;
        }
        
        .certificate-type-12 .certificate-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .certificate-type-12 .course-details {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .certificate-type-12 .simple-table {
            font-size: 0.9rem;
        }
        
        .certificate-type-12 .simple-table th {
            background-color: #6c757d;
            color: white;
            padding: 8px;
        }
        
        /* Common styles */
        .corner-info {
            font-size: 0.85rem;
        }
        
        .qr-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .badge-type {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 100;
        }
    </style>
</head>

<body class="certificate-type-<?php echo $certificate_type; ?>">
    <div class="container mt-3">
        <!-- Certificate Type Badge -->
        <div class="badge-type">
            <span class="badge badge-<?php echo ($certificate_type == '3') ? 'danger' : 'primary'; ?>">
                <?php echo ($certificate_type == '3') ? 'Leaving Service Certificate' : 'Training Certificate'; ?>
            </span>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-<?php echo ($certificate_type == '3') ? '10' : '8'; ?>">
                <?php if (isset($error_message)) : ?>
                    <div class="alert alert-danger" style="text-align:-webkit-center;">
                        <?php echo $error_message; ?>
                    </div>
                <?php else : ?>
                    
                    <?php if ($certificate_type == '3') : ?>
                        <!-- TYPE 3: LEAVING SERVICE CERTIFICATE (Full Detailed View) -->
                        <div class="certificate">
                            <div class="corner-info text-left">
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p class="mb-0"><strong>Certificate ID: </strong></p>
                                            </td>
                                            <td>
                                                <p class="mb-0"><strong><?php echo $certificate_id; ?></strong></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="mb-0"><strong>Date of Issue: </strong></p>
                                            </td>
                                            <td>
                                                <p class="mb-0"><strong><?php echo $date_of_issue; ?></strong></p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="header">
                                <img src="../img/logo.png" alt="Company Logo" class="logo">
                                <h4 class="mt-3 tt subtopic">CERTIFICATE VERIFICATION</h4>
                                <h4 class="tt subtopic"><?php echo $organization_name; ?></h4>
                            </div>
                            <img src="<?php echo $image_path; ?>" alt="Image" class="student-image">
                            <div class="details text-left mt-5">
                                <div class="personal-info">
                                    <table class="table table-bordered custom-table">
                                        <thead>
                                            <tr>
                                                <th class="tt table-head" colspan="2">
                                                    <h5 class="subtopic">PERSONAL INFORMATION</h5>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;">Full Name</th>
                                                <td><?php echo $name; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Service No</th>
                                                <td><?php echo $service_no; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Rank</th>
                                                <td><?php echo $rank; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Passport No</th>
                                                <td><?php echo $passport_no; ?></td>
                                            </tr>
                                            <tr>
                                                <th>NIC No</th>
                                                <td><?php echo $nic_no; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Date of Enlistment</th>
                                                <td><?php echo $date_of_enlistment; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Date of Retirement</th>
                                                <td><?php echo $date_of_retirement; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Total Service (Years)</th>
                                                <td><?php echo $total_service; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php if (!empty($experience)) : ?>
                                <table class="table table-bordered custom-table">
                                    <thead>
                                        <tr>
                                            <th class="tt table-head" colspan="2">
                                                <h5 class="subtopic">EXPERIENCE, KNOWLEDGE & SKILLS</h5>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $experience_items = explode(',', $experience);
                                        foreach ($experience_items as $item) {
                                            if (!empty(trim($item))) {
                                                echo "<tr><td>" . htmlspecialchars(trim($item)) . "</td></tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php endif; ?>
                                
                                <?php if (!empty($qualifications)) : ?>
                                <table class="table table-bordered custom-table">
                                    <thead>
                                        <tr>
                                            <th class="tt table-head" colspan="2">
                                                <h5 class="subtopic">QUALIFICATIONS, AWARDS & ACHIEVEMENTS</h5>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $qualification_items = explode(',', $qualifications);
                                        foreach ($qualification_items as $item) {
                                            if (!empty(trim($item))) {
                                                echo "<tr><td>" . htmlspecialchars(trim($item)) . "</td></tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php endif; ?>
                                
                                <div class="issuing-authority">
                                    <table class="table table-bordered custom-table">
                                        <thead>
                                            <tr>
                                                <th class="tt table-head" colspan="2">
                                                    <h5 class="subtopic">ISSUING AUTHORITY</h5>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;">Name</th>
                                                <td><?php echo $issuing_authority_name; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Rank</th>
                                                <td><?php echo $issuing_authority_rank; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Appointment</th>
                                                <td><?php echo $issuing_authority_appointment; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Contact No</th>
                                                <td><?php echo $issuing_authority_contact; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td><?php echo $issuing_authority_email; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="footer mt-5" style="display:none;">
                                <p>Verified by: <strong><?php echo $verified_by; ?></strong></p>
                                <p>Date: <?php echo $verified_date; ?></p>
                            </div>
                        </div>
                        
                    <?php else : ?>
                        <!-- TYPE 1/2: TRAINING CERTIFICATE (Simplified View) -->
                        <div class="certificate">
                            <div class="certificate-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Certificate ID:</strong> <?php echo $certificate_id; ?></p>
                                        <p class="mb-1"><strong>Issue Date:</strong> <?php echo $date_of_issue; ?></p>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <p class="mb-1"><strong>Verified by:</strong> <?php echo $verified_by; ?></p>
                                        <p class="mb-1"><strong>Verified Date:</strong> <?php echo $verified_date; ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="header text-center">
                                <img src="../img/logo.png" alt="Company Logo" class="logo mb-2">
                                <h4 class="mb-2">CERTIFICATE OF COMPLETION</h4>
                                <h5><?php echo $organization_name; ?></h5>
                            </div>
                            
                            <div class="text-center my-4">
                                <img src="<?php echo $image_path; ?>" alt="Photograph" class="student-image">
                                <h3 class="mt-3"><?php echo $name; ?></h3>
                                <h5><?php echo $rank; ?> / <?php echo $service_no; ?></h5>
                            </div>
                            
                            <div class="course-details">
                                <h4 class="text-center mb-3"><?php echo $course_name; ?></h4>
                                <p class="text-center"><strong>Duration:</strong> <?php echo $course_duration; ?></p>
                                <?php if (!empty($course_description)) : ?>
                                <p class="text-center"><strong>Description:</strong> <?php echo $course_description; ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-4">
                                <table class="table table-bordered simple-table">
                                    <thead>
                                        <tr>
                                            <th colspan="2">ISSUING AUTHORITY DETAILS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%;">Name</th>
                                            <td><?php echo $issuing_authority_name; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Rank</th>
                                            <td><?php echo $issuing_authority_rank; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Appointment</th>
                                            <td><?php echo $issuing_authority_appointment; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Contact</th>
                                            <td><?php echo $issuing_authority_contact; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?php echo $issuing_authority_email; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="text-center mt-4">
                                <p><em>This certificate is issued in recognition of successful completion of the course.</em></p>
                                <p class="mt-3"><strong>Certificate Validated:</strong> <?php echo $verified_date; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                <?php endif; ?>
            </div>
        </div>
        
        <!-- QR Code Section (Common for both types) -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="qr-section">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5>Certificate Verification QR Code</h5>
                            <p class="mb-2 text-muted">Scan to verify authenticity</p>
                            <div class="qr-code">
                                <img id="qrImage" src="fetch-qr.php?data=<?php echo 'https://airforce.lk/verificationlog/certificate.php?certificate_id=' . $certificate_uuid; ?>" alt="QR Code">
                            </div>
                        </div>
                        <div>
                            <button onclick="downloadQR()" class="btn btn-primary">
                                <i class="fas fa-download"></i> Download QR Code
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Verification URL:</strong> https://airforce.lk/verificationlog/certificate.php?certificate_id=<?php echo $certificate_uuid; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="row justify-content-center mt-3">
            <div class="col-md-8 text-center">
                <button class="btn btn-secondary" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i> Go Back
                </button>
            </div>
        </div>
    </div>

    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "all-certificates.php";
            }
        }

        function downloadQR() {
            var qrImage = document.getElementById('qrImage');
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');

            // Ensure the image is loaded before drawing
            qrImage.onload = function() {
                // Set canvas dimensions to the image dimensions
                canvas.width = qrImage.naturalWidth;
                canvas.height = qrImage.naturalHeight;

                // Draw the image onto the canvas
                context.drawImage(qrImage, 0, 0);

                // Convert the canvas to a data URL (base64 encoded)
                var dataURL = canvas.toDataURL('image/png');

                // Create a download link and trigger it
                var downloadLink = document.createElement('a');
                downloadLink.href = dataURL;
                downloadLink.download = 'certificate-qr-code.png';
                downloadLink.click();
            };

            // In case the image is already loaded
            if (qrImage.complete) {
                qrImage.onload();
            }
        }
    </script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>