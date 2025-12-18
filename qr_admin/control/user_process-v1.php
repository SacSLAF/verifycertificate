<?php
session_start();
require_once "../../config.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}


if ($action == 'add') {
    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $directorate = $_POST['directorate'] ?? '';
        $created_at = date('Y-m-d H:i:s');
        $role = 'user';
        $email = $_POST['email'] ?? '';

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement for insertion
        // $query = "INSERT INTO users (username, password,email, directorate, created_at, role) VALUES (?, ?,?, ?, ?, ?)";
        $query = "INSERT INTO users (username, password,email, directorate, created_at, role) VALUES (?, ?,?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $query)) {
            // Bind parameters
            mysqli_stmt_bind_param(
                $stmt,
                "sssiss",
                $username,
                $hashed_password,
                $email,
                $directorate,
                $created_at,
                $role
            );

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // echo "Certificate details inserted successfully.";
                header("Location: ../create-users-v1.php?msg=4");
            } else {
                // echo "Error inserting certificate details: " . mysqli_error($conn);
                header("Location: ../create-users-v1.php?msg=6");
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }

        // Close connection
        mysqli_close($conn);
    } else {
        // Redirect back to form if accessed directly
        header("Location: ../create-users-v1.php?msg=6");
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
// Toggle user status
if ($action == 'toggle_status') {
    $user_id = $_POST['user_id'];
    $current_status = $_POST['current_status'];
    $new_status = $current_status == 1 ? 0 : 1;

    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $user_id);

    if ($stmt->execute()) {
        header("location: ../create-users-v1.php?msg=success");
    } else {
        header("location: ../create-users-v1.php?msg=error");
    }
    exit;
}

// Delete user
if ($action == 'delete') {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("location: ../create-users-v1.php?msg=success");
    } else {
        header("location: ../create-users-v1.php?msg=error");
    }
    exit;
}
