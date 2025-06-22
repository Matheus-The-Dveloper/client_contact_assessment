<?php
include '../models/db.php';

$contact_id = intval($_POST['contact_id']);
$client_id = intval($_POST['client_id']);

// Prevent duplicate link
$stmt = $conn->prepare("SELECT * FROM client_contact WHERE contact_id = ? AND client_id = ?");
$stmt->bind_param("ii", $contact_id, $client_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    $link = $conn->prepare("INSERT INTO client_contact (client_id, contact_id) VALUES (?, ?)");
    $link->bind_param("ii", $client_id, $contact_id);
    $link->execute();
}

header("Location: ../add_contact.php?id=$contact_id");
