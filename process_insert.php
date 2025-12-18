<?php
// Include your database connection configuration
include("config.php");

// Function to generate UUID
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
// Function to handle image upload
function upload_image($file)
{
    $target_dir = "certificate-photo/";
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
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return basename($file["name"]);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    return '';
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    $certificate_uuid = generate_uuid();


    $experience = explode(',', $experience_input);
    $qualifications = explode(',', $qualifications_input);

    // Convert arrays to comma-separated strings
    $experience_str = implode(',', $experience);
    $qualifications_str = implode(',', $qualifications);

    // $experience = array_map('trim', $experience);
    // $knowledge = array_map('trim', $knowledge);
    // $skills = array_map('trim', $skills);


    // Handle image upload
    $image_path = upload_image($_FILES["image"]);

    // Prepare SQL statement for insertion
    $query = "INSERT INTO certificates 
              (certificate_id, date_of_issue, image, name, service_no, rank, passport_no, nic_no, 
              date_of_enlistment, date_of_retirement, total_service, experience, qualifications,issuing_authority_name, issuing_authority_rank, issuing_authority_appointment, 
              issuing_authority_email, issuing_authority_contact, verified_by, verified_date, certificate_uuid) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind parameters
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssssssssssssss",
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
            $certificate_uuid
        );

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // echo "Certificate details inserted successfully.";
            header("Location: index.php?msg=success");
        } else {
            // echo "Error inserting certificate details: " . mysqli_error($conn);
            header("Location: index.php?msg=failed");
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
    header("Location: insert_certificate.php");
    exit();
}
