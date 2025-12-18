<?php
include("config.php");

$id = $_GET['certificate_id'] ?? '';

$query = "SELECT * FROM certificates WHERE certificate_uuid = '$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);


// Store the data in variables
$certificate_id = $row['certificate_id'] ?? '';
$date_of_issue = $row['date_of_issue'] ?? '';
$image = $row['image'] ?? '';
$image_path = 'certificate-photo/' . $image;
$name = $row['name'] ?? '';
$service_no = $row['service_no'] ?? '';
$rank = $row['rank'] ?? '';
$passport_no = $row['passport_no'] ?? '';
$nic_no = $row['nic_no'] ?? '';
$date_of_enlistment = $row['date_of_enlistment'] ?? '';
$date_of_retirement = $row['date_of_retirement'] ?? '';
$total_service = $row['total_service'] ?? '';
$experience = $row['experience'] ?? '';
$qualifications = $row['qualifications'] ?? '';
$issuing_authority_name = $row['issuing_authority_name'] ?? '';
$issuing_authority_rank = $row['issuing_authority_rank'] ?? '';
$issuing_authority_appointment = $row['issuing_authority_appointment'] ?? '';
$issuing_authority_email = $row['issuing_authority_email'] ?? '';
$issuing_authority_contact = $row['issuing_authority_contact'] ?? '';;
$verified_by = $row['verified_by'] ?? '';
$verified_date = $row['verified_date'] ?? '';
$certificate_uuid = $row['certificate_uuid'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/main.css">
</head>

<body>

    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
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
                        <!-- <p><strong>Certificate ID: </strong><?php echo $certificate_id; ?></p> -->
                        <!-- <p><strong>Date of Issue: </strong><?php echo $date_of_issue; ?></p> -->
                    </div>
                    <div class="header">
                        <img src="img/logo.png" alt="Company Logo" class="logo">
                        <h4 class="mt-3 tt subtopic">CERTIFICATE VERIFICATION</h4>
                        <h4 class="tt subtopic">DIRECTORATE OF LOGISTICS - SRI LANKA AIR FORCE</h4>
                    </div>
                    <img src="<?php echo $image_path; ?>" alt="Student Image" class="student-image">
                    <div class="details text-left mt-5">
                        <!-- <h4></h4> -->
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
                                // Displaying qualifications, awards, and achievements as table rows
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
                                // Displaying qualifications, awards, and achievements as table rows
                                if (!empty($qualifications)) {
                                    $experience_items = explode(',', $experience);
                                    foreach ($experience_items as $item) {
                                        echo "<tr><td>" . htmlspecialchars($item) . "</td></tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- <h4 class="mt-5">Experience, Knowledge and Skills</h4>
                        <div class="experience">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Experience Details</th>
                                        <td>
                                            <ul>
                                                <?php
                                                // Displaying experience as a list
                                                if (!empty($experience)) {
                                                    $experience_items = explode(',', $experience);
                                                    foreach ($experience_items as $item) {
                                                        echo "<li>" . htmlspecialchars($item) . "</li>";
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> -->

                        <!-- <h4 class="mt-5">Knowledge</h4>
                        <div class="knowledge">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Knowledge Details</th>                           
                                        <td>
                                            <ul>
                                                <?php
                                                // Displaying experience as a list
                                                if (!empty($knowledge)) {
                                                    $knowledge_items = explode(',', $knowledge);
                                                    foreach ($knowledge_items as $item) {
                                                        echo "<li>" . htmlspecialchars($item) . "</li>";
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h4 class="mt-5">Skills</h4>
                        <div class="skills">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Skills Details</th>
                                        <td>
                                            <ul>
                                                <?php
                                                // Displaying experience as a list
                                                if (!empty($skills)) {
                                                    $skills_items = explode(',', $skills);
                                                    foreach ($skills_items as $item) {
                                                        echo "<li>" . htmlspecialchars($item) . "</li>";
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> -->
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
                <!-- <div class="mt-3 text-center mb-3"><a href="index.php"><button class="btn btn-primary">Home</button></a></div> -->
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>