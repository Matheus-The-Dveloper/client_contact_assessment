<?php
include '../models/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = intval($_POST['client_id']);
    $contact_id = intval($_POST['contact_id']);

    if (!$client_id || !$contact_id) {
        http_response_code(400);
        echo "Missing data";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM client_contact WHERE client_id = ? AND contact_id = ?");
    $stmt->bind_param("ii", $client_id, $contact_id);
    if ($stmt->execute()) {
        echo "Unlinked successfully";
    } else {
        http_response_code(500);
        echo "Failed to unlink";
    }
} else {
    http_response_code(405);
    echo "Method not allowed";
}
