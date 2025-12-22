<?php
function getCampsOptions(mysqli $conn, $selectedCampId = null)
{
    $options = '';

    $stmt = $conn->prepare("SELECT id, name FROM camps ORDER BY name ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $selected = ($selectedCampId == $row['id']) ? 'selected' : '';
        $options .= '<option value="' . htmlspecialchars($row['id']) . '" ' . $selected . '>'
                  . htmlspecialchars($row['name']) .
                  '</option>';
    }

    $stmt->close();

    return $options;
}

function getDirectorateOptions(mysqli $conn, $selectedId = null)
{
    $options = '';

    $stmt = $conn->prepare("SELECT id, `directorate_name` FROM directorates ORDER BY directorate_name");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $selected = ($selectedId == $row['id']) ? 'selected' : '';
        $options .= '<option value="' . htmlspecialchars($row['id']) . '" ' . $selected . '>'
                  . htmlspecialchars($row['directorate_name']) .
                  '</option>';
    }

    $stmt->close();
    return $options;
}

function getDirectorateName(mysqli $conn, $directorateId)
{
    $stmt = $conn->prepare("SELECT directorate_name FROM directorates WHERE id = ?");
    $stmt->bind_param("i", $directorateId);
    $stmt->execute();
    $stmt->bind_result($directorateName);
    $stmt->fetch();
    $stmt->close();

    return $directorateName;
}

function getCampName(mysqli $conn, $campId)
{
    $stmt = $conn->prepare("SELECT name FROM camps WHERE id = ?");
    $stmt->bind_param("i", $campId);
    $stmt->execute();
    $stmt->bind_result($campName);
    $stmt->fetch();
    $stmt->close();

    return $campName;
}

function getInstituteName(mysqli $conn, $instituteId)
{
    $stmt = $conn->prepare("SELECT name FROM training_institutes WHERE id = ?");
    $stmt->bind_param("i", $instituteId);
    $stmt->execute();
    $stmt->bind_result($instituteName);
    $stmt->fetch();
    $stmt->close();

    return $instituteName;
}

function getVerificationBadge($status) {
    switch ($status) {
        case 'approved':
            return '<span class="badge badge-success">Approved</span>';
        case 'rejected':
            return '<span class="badge badge-danger">Rejected</span>';
        default:
            return '<span class="badge badge-warning">Pending</span>';
    }
}