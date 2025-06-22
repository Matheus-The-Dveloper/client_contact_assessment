<?php
include_once 'models/db.php';
$clients = $conn->query("SELECT * FROM clients ORDER BY name ASC");
?>

<h2>All Clients</h2>
<a href="add_client.php">+ Add New Client</a><br><br>
<table border="1" cellpadding="5">
    <tr><th>Name</th><th>Code</th><th>Action</th></tr>
    <?php while ($c = $clients->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= $c['code'] ?></td>
            <td><a href="add_client.php?id=<?= $c['id'] ?>">Edit</a></td>
        </tr>
    <?php endwhile; ?>
</table>
