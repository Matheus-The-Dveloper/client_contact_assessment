<?php
include 'models/db.php';

// Handle form submission (add contact)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $name = ucfirst(trim($_POST['name']));
    $surname = ucfirst(trim($_POST['surname']));
    $email = strtolower(trim($_POST['email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $check = $conn->prepare("SELECT id FROM contacts WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $insert = $conn->prepare("INSERT INTO contacts (name, surname, email) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $name, $surname, $email);
            if ($insert->execute()) {
                $contact_id = $insert->insert_id;
                header("Location: add_contact.php?id=$contact_id&saved=1");
                exit;
            } else {
                $error = "Failed to save contact.";
            }
        }
    }
}

// Load contact if editing
$contact = null;
$clients = [];
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM contacts WHERE id = $id");
    $contact = $res->fetch_assoc();

    // Load linked clients
    $clients = $conn->query("
        SELECT cli.id, cli.name, cli.code
        FROM clients cli
        INNER JOIN client_contact cc ON cc.client_id = cli.id
        WHERE cc.contact_id = $id
        ORDER BY cli.name ASC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/style.css">

    <title><?= isset($contact) ? "Edit Contact" : "Add Contact" ?></title>
    <style>
        .tab { margin-top: 20px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</head>

<body>
<?php include 'views/nav.php'; ?>

<h2><?= isset($contact) ? "Edit Contact" : "Add Contact" ?></h2>

<?php if (isset($_GET['saved'])): ?>
    <p style="color:green;">✅ Contact saved successfully!</p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p style="color:red;">❌ <?= $error ?></p>
<?php endif; ?>

<!-- FORM TO ADD OR EDIT CONTACT -->
<form method="post" onsubmit="return validateContactForm();">
    <div class="tab">
        <button type="button" onclick="showTab('general')">General</button>
        <button type="button" onclick="showTab('clients')">Client(s)</button>
    </div>

    <div id="general" class="tab-content active">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= $contact['name'] ?? '' ?>" required><br><br>

        <label>Surname:</label><br>
        <input type="text" name="surname" value="<?= $contact['surname'] ?? '' ?>" required><br><br>

        <?php if (!isset($contact)): ?>
            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>
            <button type="submit">Save Contact</button>
        <?php else: ?>
            <label>Email:</label><br>
            <input type="text" value="<?= $contact['email'] ?>" readonly><br><br>
        <?php endif; ?>
    </div>

    <div id="clients" class="tab-content">
        <?php if (isset($contact)): ?>
            <h3>Linked Clients</h3>
            <?php if ($clients->num_rows > 0): ?>
                <table border="1" cellpadding="5">
                    <tr><th>Client Name</th><th>Client Code</th><th></th></tr>
                    <?php while ($c = $clients->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= $c['code'] ?></td>
                            <td>
                                <button class="unlink-client-btn" data-contact-id="<?= $contact['id'] ?>" data-client-id="<?= $c['id'] ?>">
                                    Unlink
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No client(s) linked.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Please save the contact before linking clients.</p>
        <?php endif; ?>
    </div>
</form>

<!-- AJAX LINK CLIENT FORM (OUTSIDE MAIN FORM) -->
<?php if (isset($contact)): ?>
    <div style="margin-top: 20px;">
        <h4>Link a Client</h4>
        <?php
        $linked_ids = [];
        $get_ids = $conn->query("SELECT client_id FROM client_contact WHERE contact_id = {$contact['id']}");
        while ($r = $get_ids->fetch_assoc()) {
            $linked_ids[] = $r['client_id'];
        }

        $exclude = implode(',', $linked_ids ?: [0]);
        $unlinked = $conn->query("SELECT id, name FROM clients WHERE id NOT IN ($exclude) ORDER BY name ASC");
        ?>

        <?php if ($unlinked->num_rows > 0): ?>
            <form id="linkClientForm">
                <input type="hidden" name="contact_id" value="<?= $contact['id'] ?>">
                <select name="client_id" required>
                    <option value="">-- Select Client --</option>
                    <?php while ($c = $unlinked->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Link</button>
            </form>
            <div id="linkClientResult"></div>
        <?php else: ?>
            <p>All clients already linked.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script src="assets/scripts.js"></script>
</body>
</html>
