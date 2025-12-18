<?php
session_start();
require_once "../../config.php";

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../index.php");
    exit;
}

// Get action
$action = $_GET['action'] ?? 'add';

// Get certificate type from form
$certificate_type = $_POST['certificate_type'] ?? '1';

if ($action == 'add') {
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
    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../certificate-photo/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }
    $certificate_uuid = generate_uuid();
    // Prepare data based on certificate type
    if ($certificate_type == '3') {
        // Leaving Service Certificate
        $sql = "INSERT INTO certificates (
            certificate_id, date_of_issue, `image`, `name`, service_no, `rank`, 
            passport_no, nic_no, date_of_enlistment, date_of_retirement, 
            total_service, experience, qualifications, issuing_authority_name, 
            issuing_authority_rank, issuing_authority_appointment, 
            issuing_authority_email, issuing_authority_contact, verified_by, 
            verified_date,certificate_uuid, camp_id, institute_id, directorate_id, `type`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssssssssssssssiiis",
            $_POST['certificate_id'],
            $_POST['date_of_issue'],
            $image,
            $_POST['name'],
            $_POST['service_no'],
            $_POST['rank'],
            $_POST['passport_no'],
            $_POST['nic_no'],
            $_POST['date_of_enlistment'],
            $_POST['date_of_retirement'],
            $_POST['total_service'],
            $_POST['experience'],
            $_POST['qualifications'],
            $_POST['issuing_authority_name'],
            $_POST['issuing_authority_rank'],
            $_POST['issuing_authority_appointment'],
            $_POST['issuing_authority_email'],
            $_POST['issuing_authority_contact'],
            $_POST['verified_by'],
            $_POST['verified_date'],
            $certificate_uuid,
            $_POST['camp'],
            $_POST['institute'],
            $_POST['directorate'],
            $certificate_type
        );
    } else {
        // Training Certificate
        $directorate_id = NULL;
        $sql = "INSERT INTO certificates (
            certificate_id, date_of_issue, `image`, `name`, service_no, `rank`, 
            course_name, course_duration, course_description,
            issuing_authority_name, issuing_authority_rank, 
            issuing_authority_appointment, issuing_authority_email, 
            issuing_authority_contact, verified_by, verified_date, certificate_uuid, 
            camp_id, institute_id, directorate_id, `type`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssssssssssiiis",
            $_POST['certificate_id'],
            $_POST['date_of_issue'],
            $image,
            $_POST['name'],
            $_POST['service_no'],
            $_POST['rank'],
            $_POST['course_name'],
            $_POST['course_duration'],
            $_POST['course_description'],
            $_POST['issuing_authority_name'],
            $_POST['issuing_authority_rank'],
            $_POST['issuing_authority_appointment'],
            $_POST['issuing_authority_email'],
            $_POST['issuing_authority_contact'],
            $_POST['verified_by'],
            $_POST['verified_date'],
            $certificate_uuid,
            $_POST['camp'],
            $_POST['institute'],
            $directorate_id,
            $certificate_type
        );
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Certificate created successfully!";
        header("location: ../all-certificates.php?msg=2");
    } else {
        $_SESSION['error'] = "Error creating certificate: " . $conn->error;
        header("location: ../all-certificates.php?msg=1 ");
    }

    $stmt->close();
} elseif ($action == 'edit') {
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

    $experience = explode(',', $experience_input);
    $qualifications = explode(',', $qualifications_input);

    $experience_str = implode(',', $experience);
    $qualifications_str = implode(',', $qualifications);

    // Prepare the SQL statement for updating the existing record
    $query = "UPDATE certificates 
              SET certificate_id = ?, date_of_issue = ?, image = ?, name = ?, service_no = ?, rank = ?, passport_no = ?, nic_no = ?, 
                  date_of_enlistment = ?, date_of_retirement = ?, total_service = ?, experience = ?, qualifications = ?, 
                  issuing_authority_name = ?, issuing_authority_rank = ?, issuing_authority_appointment = ?, 
                  issuing_authority_email = ?, issuing_authority_contact = ?, verified_by = ?, verified_date = ? 
              WHERE id = ?";

    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind parameters (make sure the number matches the placeholders)
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssssssssssssi", // number of 's' must match the columns you are updating
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
            $experience_str,
            $qualifications_str,
            $issuing_authority_name,
            $issuing_authority_rank,
            $issuing_authority_appointment,
            $issuing_authority_email,
            $issuing_authority_contact,
            $verified_by,
            $verified_date,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../dashboard.php?msg=updated");
        } else {
            header("Location: ../dashboard.php?msg=failed");
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }

    // Close connection
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
