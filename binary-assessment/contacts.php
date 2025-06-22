<?php
include 'models/db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/style.css">

    <title>Contact List</title>
</head>
<body>
    <?php include 'views/nav.php'; ?>

    <h2>Contact List</h2>
    <a href="add_contact.php">Add New Contact</a><br><br>

    <?php
    $query = "
        SELECT c.*, 
        (SELECT COUNT(*) FROM client_contact cc WHERE cc.contact_id = c.id) AS client_count
        FROM contacts c
        ORDER BY surname ASC, name ASC
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0):
    ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>Email Address</th>
                <th>No. of Linked Clients</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['surname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td align="center"><?= $row['client_count'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No contact(s) found.</p>
    <?php endif; ?>
</body>
</html>
