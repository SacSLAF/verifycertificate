<?php
session_start();
require_once "../../config.php";
$directorate = $_SESSION['directorate'];


if (isset($_GET['action'])) {
    $action = $_GET['action'];
}
// var_dump($_POST);
//         var_dump($_FILES);
//         exit();
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


    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        // $certificate_id = $_POST['certificate_id'] ?? '';
        // $date_of_issue = $_POST['date_of_issue'] ?? '';
        // $name = $_POST['name'] ?? '';
        // $service_no = $_POST['service_no'] ?? '';
        // $rank = $_POST['rank'] ?? '';
        // $passport_no = $_POST['passport_no'] ?? '';
        // $nic_no = $_POST['nic_no'] ?? '';
        // $date_of_enlistment = $_POST['date_of_enlistment'] ?? '';
        // $date_of_retirement = $_POST['date_of_retirement'] ?? '';
        // $total_service = $_POST['total_service'] ?? '';
        // $experience_input = $_POST['experience'] ?? '';
        // $qualifications_input = $_POST['qualifications'] ?? '';
        // $issuing_authority_name = $_POST['issuing_authority_name'] ?? '';
        // $issuing_authority_rank = $_POST['issuing_authority_rank'] ?? '';
        // $issuing_authority_appointment = $_POST['issuing_authority_appointment'] ?? '';
        // $issuing_authority_email = $_POST['issuing_authority_email'] ?? '';
        // $issuing_authority_contact = $_POST['issuing_authority_contact'] ?? '';
        // $verified_by = $_POST['verified_by'] ?? '';
        // $verified_date = $_POST['verified_date'] ?? '';


        // Helper: clean plain text (no HTML)
        function clean_text(string $input): string
        {
            return trim(strip_tags($input));
        }

        // Helper: safe output (when you echo later, not during storage)
        function safe_html(string $input): string
        {
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }

        // Certificate details
        $certificate_id = clean_text($_POST['certificate_id'] ?? '');

        // Dates (validate format: Y-m-d)
        function validate_date(?string $date): string
        {
            if ($date && DateTime::createFromFormat('Y-m-d', $date) !== false) {
                return $date;
            }
            return '';
        }
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
        $total_service = isset($_POST['total_service']) && is_numeric($_POST['total_service'])
            ? (int)$_POST['total_service']
            : 0;
        $camp = isset($_POST['camp']) && is_numeric($_POST['camp']) ? (int)$_POST['camp']: 0;
        $institute = isset($_POST['institute']) && is_numeric($_POST['institute']) ? (int)$_POST['institute']: 0;
        $directorate = isset($_POST['directorate']) && is_numeric($_POST['directorate']) ? (int)$_POST['directorate']: 0;


        // Longer text inputs (allow safe HTML characters but no tags)
        $experience_input     = htmlspecialchars($_POST['experience'] ?? '', ENT_QUOTES, 'UTF-8');
        $qualifications_input = htmlspecialchars($_POST['qualifications'] ?? '', ENT_QUOTES, 'UTF-8');

        // Issuing authority
        $issuing_authority_name       = clean_text($_POST['issuing_authority_name'] ?? '');
        $issuing_authority_rank       = clean_text($_POST['issuing_authority_rank'] ?? '');
        $issuing_authority_appointment = clean_text($_POST['issuing_authority_appointment'] ?? '');

        $issuing_authority_email = filter_var($_POST['issuing_authority_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';
        $issuing_authority_contact = preg_replace('/\D/', '', $_POST['issuing_authority_contact'] ?? ''); // keep digits only

        $verified_by = clean_text($_POST['verified_by'] ?? '');


        $certificate_uuid = generate_uuid();


        $experience = explode(',', $experience_input);
        $qualifications = explode(',', $qualifications_input);

        // Convert arrays to comma-separated strings
        $experience_str = implode(',', $experience);
        $qualifications_str = implode(',', $qualifications);



        // Handle image upload
        $image_path = upload_image($_FILES["image"]);

        // Prepare SQL statement for insertion
        $query = "INSERT INTO certificates 
                  (certificate_id, date_of_issue, image, name, service_no, rank, passport_no, nic_no, 
                  date_of_enlistment, date_of_retirement, total_service, experience, qualifications,issuing_authority_name, issuing_authority_rank, issuing_authority_appointment, 
                  issuing_authority_email, issuing_authority_contact, verified_by, verified_date, certificate_uuid,directorate_id,camp_id,institute_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $query)) {
            // Bind parameters
            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssssssssssssssiii",
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
                $certificate_uuid,
                $directorate,
                $camp_id,
                $institute_id
            );

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // echo "Certificate details inserted successfully.";
                header("Location: ../dashboard.php?msg=2");
            } else {
                // echo "Error inserting certificate details: " . mysqli_error($conn);
                header("Location: ../dashboard.php?msg=1");
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
        header("Location: ../make_certificate.php");
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
