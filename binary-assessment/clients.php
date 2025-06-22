<?php
include 'models/db.php';
include 'models/client.php';
include 'views/nav.php';

// Fetch all clients using the model function
$clients = getAllClients();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client List</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<h2>All Clients</h2>
<a href="add_client.php">+ Add New Client</a><br><br>

<?php if ($clients && $clients->num_rows > 0): ?>
    <table border="1" cellpadding="5">
        <tr><th>Name</th><th>Code</th><th>Actions</th></tr>
        <?php while ($c = $clients->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= $c['code'] ?></td>
                <td><a href="add_client.php?id=<?= $c['id'] ?>">Edit</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No clients found.</p>
<?php endif; ?>

</body>
</html>
