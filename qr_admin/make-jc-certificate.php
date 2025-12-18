<?php
session_start();
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
    $sql = "SELECT * FROM `certificates_jcsc` WHERE `id` = $id";
    $result = mysqli_query($conn, $sql);
    $row = $result->fetch_assoc();
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

<?php include "template/head.php"; ?>

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
                                    <div class="card-title"><?php echo $buttonText; ?> JCSC Certificate Details</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <form action="control/jcsc_certificate_process.php?action=<?php echo $action; ?>" method="POST" enctype="multipart/form-data">

                                            <div class="form-group">
                                                <label for="certificate_id">Certificate ID:</label>
                                                <input type="text" class="form-control" id="certificate_id" name="certificate_id" value="<?php echo ($action == 'edit') ? $row['certificate_id'] : ''; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="date_of_issue">Date of Issue:</label>
                                                <input type="date" class="form-control" id="date_of_issue" name="date_of_issue" value="<?php echo ($action == 'edit') ? $row['date_of_issue'] : ''; ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="recipient_name">Recipient Name:</label>
                                                <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="<?php echo ($action == 'edit') ? $row['recipient_name'] : ''; ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="course_name">Course Name:</label>
                                                <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo ($action == 'edit') ? $row['course_name'] : 'No.79 Junior Command and Staff Course'; ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="course_dates">Course Dates:</label>
                                                <input type="text" class="form-control" id="course_dates" name="course_dates" value="<?php echo ($action == 'edit') ? $row['course_dates'] : '2nd May 2025 to 11th August 2025'; ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="commanding_officer_name">Commanding Officer Name:</label>
                                                <input type="text" class="form-control" id="commanding_officer_name" name="commanding_officer_name" value="<?php echo ($action == 'edit') ? $row['commanding_officer_name'] : 'SGHT Silva'; ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="commanding_officer_rank">Commanding Officer Rank:</label>
                                                <input type="text" class="form-control" id="commanding_officer_rank" name="commanding_officer_rank" value="<?php echo ($action == 'edit') ? $row['commanding_officer_rank'] : 'Wing Commander'; ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="director_general_name">Director General Name:</label>
                                                <input type="text" class="form-control" id="director_general_name" name="director_general_name" value="<?php echo ($action == 'edit') ? $row['director_general_name'] : 'NHDN Dias'; ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="director_general_rank">Director General Rank:</label>
                                                <input type="text" class="form-control" id="director_general_rank" name="director_general_rank" value="<?php echo ($action == 'edit') ? $row['director_general_rank'] : 'Air Vice Marshal'; ?>" required>
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
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "all-jcsc-certificates.php"; // Redirects to a fallback URL
            }
        }
    </script>
</body>

</html>