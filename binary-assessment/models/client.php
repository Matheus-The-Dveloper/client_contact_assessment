<?php
function getAllClients() {
    global $conn;
    return $conn->query("SELECT * FROM clients ORDER BY name ASC");
}

function getClientById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
