<?php
function getAllContacts() {
    global $conn;
    return $conn->query("SELECT * FROM contacts ORDER BY surname ASC");
}

function getContactById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
