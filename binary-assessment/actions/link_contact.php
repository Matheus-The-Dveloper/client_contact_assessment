<?php
include '../models/db.php';

$client_id = intval($_POST['client_id']);
$contact_id = intval($_POST['contact_id']);

if (!$client_id || !$contact_id) {
    echo "❌ Missing data.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM client_contact WHERE client_id = ? AND contact_id = ?");
$stmt->bind_param("ii", $client_id, $contact_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $link = $conn->prepare("INSERT INTO client_contact (client_id, contact_id) VALUES (?, ?)");
    $link->bind_param("ii", $client_id, $contact_id);
    $link->execute();
    echo "✅ Contact linked successfully!";
} else {
    echo "⚠️ Already linked.";
}
