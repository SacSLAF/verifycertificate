<?php
include("config.php");
error_reporting(E_ALL);
$id = $_GET['certificate_id'] ?? '';

// Check if the id is not empty and matches the UUID pattern
if (!empty($id) && preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $id)) {
    // Proceed with querying the database
    $user_data = getJCSCByCertificateId($id);

    if ($user_data) {
        // Store the data in variables
        $certificate_id = $user_data['certificate_id'] ?? '';
        $date_of_issue = $user_data['date_of_issue'] ?? '';
        $recipient_name = $user_data['recipient_name'] ?? '';
        $course_name = $user_data['course_name'] ?? '';
        $course_dates = $user_data['course_dates'] ?? '';
        $commanding_officer_name = $user_data['commanding_officer_name'] ?? '';
        $commanding_officer_rank = $user_data['commanding_officer_rank'] ?? '';
        $director_general_name = $user_data['director_general_name'] ?? '';
        $director_general_rank = $user_data['director_general_rank'] ?? '';
        $verified_by = $user_data['verified_by'] ?? '';
        $verified_date = $user_data['verified_date'] ?? '';
    } else {
        // If no user data found, display a message
        $error_message = 'No JCSC certificate data found. Please contact support.<br><br><table border="1">
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
    $error_message = 'No JCSC certificate data found. Please contact support.<br><br><table border="1">
                    <tr>
                        <td rowspan="2" style="padding:10px;">Contact details</td>
                        <td style="padding:10px;">General : +94112441044 Ext: 11009 / +94772229006</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;">Private : +94714283280</td>
                    </tr>
                </table>';

}

// Function to query the database for JCSC certificates
function getJCSCByCertificateId($id)
{
    global $conn;
    // Prepare the query
    $stmt = $conn->prepare("SELECT * FROM certificates_jcsc WHERE certificate_uuid = ?");
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

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>JCSC Certificate Verification</title>
        <!-- Bootstrap 5 CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style/jc-certificate.css">
    </head>
    <body>
        <div class="container mt-3">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <?php if (isset($error_message)) : ?>
                        <div class="alert alert-danger" style="text-align:-webkit-center;">
                            <?php echo $error_message; ?>
                        </div>
                    <?php else : ?>
                        <div class="certificate-paper d-flex align-items-center justify-content-center">
                            <div class="certificate card p-5 text-center shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-2 text-start">
                                        <img src="img/slaf-logo.png" alt="Left logo" class="logo-left" />
                                    </div>
                                    <div class="col-8">
                                        <h5 class="issuer">Junior Command and Staff College</h5>
                                        <h5 class="title">Sri Lanka Air Force Academy, China Bay</h5>
                                        <p class="subtitle">This is to certify that</p>
                                    </div>
                                    <div class="col-2 text-end">
                                        <img src="img/jcsc-logo.png" alt="Right logo" class="logo-right" />
                                    </div>
                                </div>

                                <span class="recipient"><?php echo $recipient_name; ?></span>
                                <span>...................................................................................................................</span>

                                <p class="lead description">
                                    <em>Has successfully completed</em><br>
                                    <strong><?php echo $course_name; ?></strong><br>
                                    <em>(<?php echo $course_dates; ?>)</em>
                                </p>

                                <p class="issue-details">
                                    <span class="label">Date of Issue</span> <span class="value">: <?php echo $date_of_issue; ?></span><br>
                                    <span class="label">Place of Issue</span> <span class="value">: Sri Lanka Air Force Academy China Bay</span>
                                </p>

                                <div class="row signatures">
                                    <div class="col-4">
                                        <img src="img/red-seal.png" alt="Left seal" class="red-seal" />
                                    </div>
                                    <div class="col-4">
                                        <img src="img/sign1.png" alt="Left signature" class="signature-left" />
                                        <div class="sig-title">
                                            (<?php echo $commanding_officer_name; ?>)<br>
                                            <?php echo $commanding_officer_rank; ?><br>
                                            COMMANDING OFFICER
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <img src="img/sign2.png" alt="Right signature" class="signature-right" />
                                        <div class="sig-title">
                                            (<?php echo $director_general_name; ?>)<br>
                                            <?php echo $director_general_rank; ?><br>
                                            DIRECTOR GENERAL TRAINING
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2 text-muted" style="font-size: 0.5rem;">
                                    Authenticity of the certificate can be checked with online directory
                                    <a href="https://www.airforce.lk" target="_blank" rel="noopener noreferrer"><em>www.airforce.lk</em></a>            
                                </div>
                                
                                <!-- Verification details (hidden by default) -->
                                <div class="verification-details mt-4" style="display:none; font-size: 0.7rem;">
                                    <p>Verified by: <strong><?php echo $verified_by; ?></strong></p>
                                    <p>Date: <?php echo $verified_date; ?></p>
                                    <p>Certificate ID: <?php echo $certificate_id; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>