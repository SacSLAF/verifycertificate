<?php
session_start();
include '../../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_GET['action'] ?? 'add';
    
    // Generate UUID if not exists
    function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    if ($action == 'add') {
        // Add new certificate
        $certificate_id = $_POST['certificate_id'];
        $date_of_issue = $_POST['date_of_issue'];
        $recipient_name = $_POST['recipient_name'];
        $course_name = $_POST['course_name'];
        $course_dates = $_POST['course_dates'];
        $commanding_officer_name = $_POST['commanding_officer_name'];
        $commanding_officer_rank = $_POST['commanding_officer_rank'];
        $director_general_name = $_POST['director_general_name'];
        $director_general_rank = $_POST['director_general_rank'];
        $verified_by = $_POST['verified_by'];
        $verified_date = $_POST['verified_date'];
        $certificate_uuid = generateUUID();
        
        $sql = "INSERT INTO certificates_jcsc (certificate_id, certificate_uuid, date_of_issue, recipient_name, course_name, course_dates, commanding_officer_name, commanding_officer_rank, director_general_name, director_general_rank, verified_by, verified_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $certificate_id, $certificate_uuid, $date_of_issue, $recipient_name, $course_name, $course_dates, $commanding_officer_name, $commanding_officer_rank, $director_general_name, $director_general_rank, $verified_by, $verified_date);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "JCSC Certificate added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding JCSC Certificate: " . $stmt->error;
        }
        
        $stmt->close();
        header("Location: ../all-jcsc-certificates.php");
        exit();
        
    } elseif ($action == 'edit') {
        // Update existing certificate
        $id = $_POST['id'];
        $certificate_id = $_POST['certificate_id'];
        $date_of_issue = $_POST['date_of_issue'];
        $recipient_name = $_POST['recipient_name'];
        $course_name = $_POST['course_name'];
        $course_dates = $_POST['course_dates'];
        $commanding_officer_name = $_POST['commanding_officer_name'];
        $commanding_officer_rank = $_POST['commanding_officer_rank'];
        $director_general_name = $_POST['director_general_name'];
        $director_general_rank = $_POST['director_general_rank'];
        $verified_by = $_POST['verified_by'];
        $verified_date = $_POST['verified_date'];
        
        $sql = "UPDATE certificates_jcsc SET certificate_id=?, date_of_issue=?, recipient_name=?, course_name=?, course_dates=?, commanding_officer_name=?, commanding_officer_rank=?, director_general_name=?, director_general_rank=?, verified_by=?, verified_date=? WHERE id=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssi", $certificate_id, $date_of_issue, $recipient_name, $course_name, $course_dates, $commanding_officer_name, $commanding_officer_rank, $director_general_name, $director_general_rank, $verified_by, $verified_date, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "JCSC Certificate updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating JCSC Certificate: " . $stmt->error;
        }
        
        $stmt->close();
        header("Location: ../all-jcsc-certificates.php");
        exit();
    }
}

$conn->close();
?>