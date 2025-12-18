<?php
include "../../config.php";

if (!isset($_GET['camp_id']) || empty($_GET['camp_id'])) {
    echo json_encode([]);
    exit;
}

$campId = (int) $_GET['camp_id'];

$stmt = $conn->prepare("
    SELECT id, name 
    FROM training_institutes
    WHERE related_camp = ? ORDER BY name ASC
");
$stmt->bind_param("i", $campId);
$stmt->execute();

$result = $stmt->get_result();
$institutes = [];

while ($row = $result->fetch_assoc()) {
    $institutes[] = $row;
}

$stmt->close();

header('Content-Type: application/json');
echo json_encode($institutes);
