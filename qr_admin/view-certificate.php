<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['directorate'])) {
    header("location: index.php");
    exit;
}

include("../config.php");
error_reporting(E_ALL);
$id = $_GET['id'] ?? '';
// var_dump($id);exit;
// Check if the id is not empty and matches the UUID pattern
if (!empty($id) && preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $id)) {
    // Proceed with querying the database
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
        $issuing_authority_name = $user_data['issuing_authority_name'] ?? '';
        $issuing_authority_rank = $user_data['issuing_authority_rank'] ?? '';
        $issuing_authority_appointment = $user_data['issuing_authority_appointment'] ?? '';
        $issuing_authority_email = $user_data['issuing_authority_email'] ?? '';
        $issuing_authority_contact = $user_data['issuing_authority_contact'] ?? '';
        $verified_by = $user_data['verified_by'] ?? '';
        $verified_date = $user_data['verified_date'] ?? '';
        $certificate_uuid = $user_data['certificate_uuid'] ?? '';
        $certificate_directorate = $user_data['directorate_id'] ?? '';
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

// Fetch directorate name from database
$directorate_name = "SRI LANKA AIR FORCE"; // Default fallback

// Get directorate_id from certificate data - FIXED: Use $certificate_directorate instead of $row['directorate_id']
if (isset($certificate_directorate) && !empty($certificate_directorate)) {
    $stmt = $conn->prepare("SELECT directorate_name FROM directorates WHERE id = ?");
    $stmt->bind_param("i", $certificate_directorate);
    $stmt->execute();
    $stmt->bind_result($db_directorate_name);
    if ($stmt->fetch()) {
        $directorate_name = "" . strtoupper($db_directorate_name) . " <br> SRI LANKA AIR FORCE";
    }
    $stmt->close();
}
function getUserByCertificateId($id)
{
    global $conn;
    // Prepare the query
    $stmt = $conn->prepare("SELECT * FROM certificates WHERE certificate_uuid = ?");
    $stmt->bind_param("s", $id); // Use 's' for string
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

</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (isset($error_message)) : ?>
                    <div class="alert alert-danger" style="text-align:-webkit-center;">
                        <?php echo $error_message; ?>
                    </div>
                <?php else : ?>
                    <div class="certificate text-center">
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
                            <h4 class="tt subtopic"><?php echo $directorate_name; ?></h4>
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
                                    // Displaying experience as table rows
                                    if (!empty($experience)) {
                                        $experience_items = explode(',', $experience);
                                        foreach ($experience_items as $item) {
                                            echo "<tr><td>" . htmlspecialchars($item) . "</td></tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
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
                                    // Displaying qualifications as table rows
                                    if (!empty($qualifications)) {
                                        $qualification_items = explode(',', $qualifications);
                                        foreach ($qualification_items as $item) {
                                            echo "<tr><td>" . htmlspecialchars($item) . "</td></tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="">
                    <!-- <div class="form-group">
                        <label for="uuid">UUID:</label>
                        <input type="text" class="form-control" id="uuid" name="uuid" value="<?php echo $certificate_uuid; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="url">URL:</label>
                        <input type="text" class="form-control" id="url" name="url" value="https://airforce.lk/verificationlog/certificate.php?certificate_id=<?php echo $certificate_uuid; ?>" disabled>
                    </div> -->
                    <div class="d-flex">
                        <div class="qr-code" id="printableArea">
                            <img id="qrImage" src="fetch-qr.php?data=<?php echo 'https://airforce.lk/verificationlog/certificate.php?certificate_id=' . $certificate_uuid; ?>" alt="QR Code">
                        </div>
                        <div class="print-btn-container mx-5">
                            <input type="button" onclick="downloadQR()" class="btn btn-warning" value="Download QR!" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="mt-5 mb-5 text-center"><button class="btn btn-secondary" onclick="goBack()">Go Back</button></div>
    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "all-certificates.php"; // Redirects to a fallback URL
            }
        }

        // function printDiv(divId) {
        //     var printContents = document.getElementById(divId).innerHTML;
        //     var originalContents = document.body.innerHTML;

        //     document.body.innerHTML = printContents;
        //     window.print();
        //     document.body.innerHTML = originalContents;
        // }

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
                downloadLink.download = 'qr-code.png'; // Set filename
                downloadLink.click(); // Trigger download
            };

            // In case the image is already loaded
            if (qrImage.complete) {
                qrImage.onload(); // Trigger manually if already loaded
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>