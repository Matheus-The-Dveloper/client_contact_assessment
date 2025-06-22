<?php
include 'models/db.php';

$result = $conn->query("
    SELECT c.*, 
    (SELECT COUNT(*) FROM client_contact cc WHERE cc.client_id = c.id) AS contact_count
    FROM clients c ORDER BY c.name ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clients</title>
    <link rel="stylesheet" href="assets/style.css">

</head>
<body>
    <?php include 'views/nav.php'; ?>

    <h2>Client List</h2>
    <a href="add_client.php">Add New Client</a><br><br>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Name</th>
                <th>Client Code</th>
                <th>No. of Linked Contacts</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['code'] ?></td>
                    <td align="center"><?= $row['contact_count'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No client(s) found.</p>
    <?php endif; ?>
</body>
</html>
