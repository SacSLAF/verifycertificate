<?php
session_start();
require_once "../../config.php";
$directorate = $_SESSION['directorate'];

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

// Function to handle image upload
function upload_image($file)
{
    if (!isset($file['name']) || $file['name'] == '') {
        return ''; // No new file uploaded, return empty string
    }

    $target_dir = "../../certificate-photo/";
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        return ''; // Return empty string if upload fails
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return basename($file["name"]);
        } else {
            echo "Sorry, there was an error uploading your file.";
            return ''; // Return empty string if upload fails
        }
    }
}

if ($action == 'add') {
    // var_dump($_POST); exit;
    function generate_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Helper: clean plain text (no HTML)
        function clean_text(string $input): string
        {
            return trim(strip_tags($input));
        }

        // Dates (validate format: Y-m-d)
        function validate_date(?string $date): string
        {
            if ($date && DateTime::createFromFormat('Y-m-d', $date) !== false) {
                return $date;
            }
            return '';
        }

        // Certificate details
        $certificate_id = clean_text($_POST['certificate_id'] ?? '');

        // Dates
        $date_of_issue     = validate_date($_POST['date_of_issue'] ?? '');
        $date_of_enlistment = validate_date($_POST['date_of_enlistment'] ?? '');
        $date_of_retirement = validate_date($_POST['date_of_retirement'] ?? '');
        $verified_date      = validate_date($_POST['verified_date'] ?? '');

        // Text fields
        $name        = clean_text($_POST['name'] ?? '');
        $service_no  = clean_text($_POST['service_no'] ?? '');
        $rank        = clean_text($_POST['rank'] ?? '');
        $passport_no = clean_text($_POST['passport_no'] ?? '');
        $nic_no      = clean_text($_POST['nic_no'] ?? '');

        // Numbers
        $total_service = clean_text($_POST['total_service'] ?? '');

        // Longer text inputs
        $experience_input     = htmlspecialchars($_POST['experience'] ?? '', ENT_QUOTES, 'UTF-8');
        $qualifications_input = htmlspecialchars($_POST['qualifications'] ?? '', ENT_QUOTES, 'UTF-8');

        // Issuing authority
        $issuing_authority_name       = clean_text($_POST['issuing_authority_name'] ?? '');
        $issuing_authority_rank       = clean_text($_POST['issuing_authority_rank'] ?? '');
        $issuing_authority_appointment = clean_text($_POST['issuing_authority_appointment'] ?? '');
        $issuing_authority_email = filter_var($_POST['issuing_authority_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';
        $issuing_authority_contact = clean_text($_POST['issuing_authority_contact'] ?? '');
        $verified_by = clean_text($_POST['verified_by'] ?? '');

        $certificate_uuid = generate_uuid();

        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
            $image_path = upload_image($_FILES["image"]);
        }

        // If no image was uploaded, set a default value or handle the error
        if (empty($image_path)) {
            // You can either set a default image name or return an error
            // For now, let's set a default empty value
            $image_path = 'default.jpg'; // Make sure this file exists in your certificate-photo folder
            // Or you can return an error:
            // header("Location: ../make-certificate.php?error=image_required");
            // exit();
        }

        // Debug: Check what we're inserting
        error_log("Inserting certificate with image: " . $image_path);

        // Prepare SQL statement for insertion - FIXED to match your form data
        $query = "INSERT INTO certificates 
                  (certificate_id, date_of_issue, image, name, service_no, `rank`, 
                  passport_no, nic_no, date_of_enlistment, date_of_retirement, total_service, 
                  experience, qualifications, issuing_authority_name, issuing_authority_rank, 
                  issuing_authority_appointment, issuing_authority_email, issuing_authority_contact, 
                  verified_by, verified_date, certificate_uuid, directorate_id, verification_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

        if ($stmt = mysqli_prepare($conn, $query)) {
            // Bind parameters
            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssssssssssssssi",
                $certificate_id,
                $date_of_issue,
                $image_path,
                $name,
                $service_no,
                $rank,
                $passport_no,
                $nic_no,
                $date_of_enlistment,
                $date_of_retirement,
                $total_service,
                $experience_input,
                $qualifications_input,
                $issuing_authority_name,
                $issuing_authority_rank,
                $issuing_authority_appointment,
                $issuing_authority_email,
                $issuing_authority_contact,
                $verified_by,
                $verified_date,
                $certificate_uuid,
                $directorate
            );

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                header("Location: ../dashboard.php?msg=2");
            } else {
                error_log("MySQL Error: " . mysqli_error($conn));
                header("Location: ../make-certificate.php?error=db_error");
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            error_log("Prepare Error: " . mysqli_error($conn));
            header("Location: ../make-certificate.php?error=prepare_error");
        }

        // Close connection
        mysqli_close($conn);
    } else {
        // Redirect back to form if accessed directly
        header("Location: ../make-certificate.php");
        exit();
    }
}

if ($action == 'edit') {
    $id = $conn->real_escape_string($_POST['id']);

    // Retrieve existing image path from database
    $query = "SELECT image FROM certificates WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $existing_image);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    // Handle new image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $image_path = upload_image($_FILES['image']);
    }

    // If no new image is uploaded, retain the existing image
    if ($image_path == '') {
        $image_path = $existing_image;
    }

    // Retrieve form data
    $certificate_id = $_POST['certificate_id'] ?? '';
    $date_of_issue = $_POST['date_of_issue'] ?? '';
    $name = $_POST['name'] ?? '';
    $service_no = $_POST['service_no'] ?? '';
    $rank = $_POST['rank'] ?? '';
    $passport_no = $_POST['passport_no'] ?? '';
    $nic_no = $_POST['nic_no'] ?? '';
    $date_of_enlistment = $_POST['date_of_enlistment'] ?? '';
    $date_of_retirement = $_POST['date_of_retirement'] ?? '';
    $total_service = $_POST['total_service'] ?? '';
    $experience_input = $_POST['experience'] ?? '';
    $qualifications_input = $_POST['qualifications'] ?? '';
    $issuing_authority_name = $_POST['issuing_authority_name'] ?? '';
    $issuing_authority_rank = $_POST['issuing_authority_rank'] ?? '';
    $issuing_authority_appointment = $_POST['issuing_authority_appointment'] ?? '';
    $issuing_authority_email = $_POST['issuing_authority_email'] ?? '';
    $issuing_authority_contact = $_POST['issuing_authority_contact'] ?? '';
    $verified_by = $_POST['verified_by'] ?? '';
    $verified_date = $_POST['verified_date'] ?? '';

    // Handle admin verification fields if user is admin
    $verification_status = $_POST['verification_status'] ?? 'pending';
    $is_active = $_POST['is_active'] ?? 0;
    $rejection_reason = $_POST['rejection_reason'] ?? '';
    $admin_notes = $_POST['admin_notes'] ?? '';

    // Check if user is admin (directorate_id = 12)
    $is_admin = ($_SESSION['directorate'] == 12);

    if ($is_admin) {
        // Admin update - include verification fields
        $query = "UPDATE certificates 
                  SET certificate_id = ?, date_of_issue = ?, image = ?, name = ?, service_no = ?, `rank` = ?, 
                      passport_no = ?, nic_no = ?, date_of_enlistment = ?, date_of_retirement = ?, 
                      total_service = ?, experience = ?, qualifications = ?, 
                      issuing_authority_name = ?, issuing_authority_rank = ?, issuing_authority_appointment = ?, 
                      issuing_authority_email = ?, issuing_authority_contact = ?, verified_by = ?, verified_date = ?,
                      verification_status = ?, is_active = ?, rejection_reason = ?, admin_notes = ?,
                      verified_by_admin = ?, admin_verification_date = NOW()
                  WHERE id = ?";

        if ($stmt = mysqli_prepare($conn, $query)) {
            // Use session user ID or default to 1 if not set
            $verified_by_admin = $_SESSION['user_id'] ?? 1;

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssissssssssssissii",
                $certificate_id,
                $date_of_issue,
                $image_path,
                $name,
                $service_no,
                $rank,
                $passport_no,
                $nic_no,
                $date_of_enlistment,
                $date_of_retirement,
                $total_service,
                $experience_input,
                $qualifications_input,
                $issuing_authority_name,
                $issuing_authority_rank,
                $issuing_authority_appointment,
                $issuing_authority_email,
                $issuing_authority_contact,
                $verified_by,
                $verified_date,
                $verification_status,
                $is_active,
                $rejection_reason,
                $admin_notes,
                $verified_by_admin,
                $id
            );
        }
    } else {
        // Regular user update - basic fields only
        $query = "UPDATE certificates 
                  SET certificate_id = ?, date_of_issue = ?, image = ?, name = ?, service_no = ?, `rank` = ?, 
                      passport_no = ?, nic_no = ?, date_of_enlistment = ?, date_of_retirement = ?, 
                      total_service = ?, experience = ?, qualifications = ?, 
                      issuing_authority_name = ?, issuing_authority_rank = ?, issuing_authority_appointment = ?, 
                      issuing_authority_email = ?, issuing_authority_contact = ?, verified_by = ?, verified_date = ?
                  WHERE id = ?";

        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssisssssssssi",
                $certificate_id,
                $date_of_issue,
                $image_path,
                $name,
                $service_no,
                $rank,
                $passport_no,
                $nic_no,
                $date_of_enlistment,
                $date_of_retirement,
                $total_service,
                $experience_input,
                $qualifications_input,
                $issuing_authority_name,
                $issuing_authority_rank,
                $issuing_authority_appointment,
                $issuing_authority_email,
                $issuing_authority_contact,
                $verified_by,
                $verified_date,
                $id
            );
        }
    }

    if ($stmt && mysqli_stmt_execute($stmt)) {
        header("Location: ../all-certificates.php?msg=updated");
    } else {
        echo "Error: " . mysqli_error($conn);
        header("Location: ../all-certificates.php?msg=failed");
    }

    if ($stmt) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}

if ($action == 'delete') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    $sql = "DELETE FROM `certificates` WHERE `id` = '$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("location: ../all-certificates.php?msg=5");
    } else {
        header("location: ../all-certificates.php?msg=1");
    }
}
