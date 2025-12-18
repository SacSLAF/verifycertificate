<?php
session_start();
include '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM certificates_jcsc WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "JCSC Certificate deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting JCSC Certificate: " . $stmt->error;
    }
    
    $stmt->close();
}

header("Location: ../all-jcsc-certificates.php");
exit();
?>