<?php
include 'models/db.php';

// Handle form submission (only if adding new client)
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['id'])) {
    $name = strtoupper(trim($_POST['name']));
    $alpha = substr(preg_replace('/[^A-Z]/', '', $name), 0, 3);
    $alpha = str_pad($alpha, 3, 'A');

    $stmt = $conn->prepare("SELECT code FROM clients WHERE code LIKE CONCAT(?, '%') ORDER BY code DESC LIMIT 1");
    $stmt->bind_param("s", $alpha);
    $stmt->execute();
    $stmt->bind_result($last_code);
    $stmt->fetch();
    $stmt->close();

    $next_num = $last_code ? intval(substr($last_code, 3)) + 1 : 1;
    $final_code = $alpha . str_pad($next_num, 3, '0', STR_PAD_LEFT);

    $insert = $conn->prepare("INSERT INTO clients (name, code) VALUES (?, ?)");
    $insert->bind_param("ss", $name, $final_code);
    if ($insert->execute()) {
        $client_id = $insert->insert_id;
        header("Location: add_client.php?id=$client_id&saved=1");
        exit;
    } else {
        $error = "Failed to save client.";
    }
}

// Load existing client and linked contacts
$client = null;
$contacts = [];
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM clients WHERE id = $id");
    $client = $res->fetch_assoc();

    $contacts = $conn->query("
        SELECT con.id, CONCAT(con.surname, ' ', con.name) AS full_name, con.email
        FROM contacts con
        INNER JOIN client_contact cc ON cc.contact_id = con.id
        WHERE cc.client_id = $id
        ORDER BY con.surname ASC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/style.css">

    <title><?= isset($client) ? "Edit Client" : "Add Client" ?></title>
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

<h2><?= isset($client) ? "Edit Client" : "Add Client" ?></h2>
<?php if (isset($_GET['saved'])): ?>
    <p style="color:green;">Client saved successfully!</p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

<!-- MAIN FORM for creating the client -->
<form method="post" onsubmit="return validateClientForm();">
    <div class="tab">
        <button type="button" onclick="showTab('general')">General</button>
        <button type="button" onclick="showTab('contacts')">Contact(s)</button>
    </div>

    <div id="general" class="tab-content active">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= $client['name'] ?? '' ?>" required><br><br>

        <?php if (isset($client)): ?>
            <label>Client Code:</label><br>
            <input type="text" value="<?= $client['code'] ?>" readonly><br><br>
        <?php else: ?>
            <button type="submit">Save Client</button>
        <?php endif; ?>
    </div>

    <div id="contacts" class="tab-content">
        <?php if (isset($client)): ?>
            <h3>Linked Contacts</h3>
            <?php if ($contacts->num_rows > 0): ?>
                <table border="1" cellpadding="5">
                    <tr><th>Full Name</th><th>Email</th><th></th></tr>
                    <?php while ($c = $contacts->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['full_name']) ?></td>
                            <td><?= $c['email'] ?></td>
                            <td>
                                <button class="unlink-btn" data-client-id="<?= $client['id'] ?>" data-contact-id="<?= $c['id'] ?>">
                                Unlink
                                </button>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No contacts linked yet.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Please save the client before linking contacts.</p>
        <?php endif; ?>
    </div>
</form>

<!-- AJAX CONTACT LINK FORM: OUTSIDE MAIN FORM -->
<?php if (isset($client)): ?>
    <div style="margin-top: 20px;">
        <h4>Link a Contact</h4>
        <?php
        $linked_ids = [];
        $get_ids = $conn->query("SELECT contact_id FROM client_contact WHERE client_id = {$client['id']}");
        while ($r = $get_ids->fetch_assoc()) {
            $linked_ids[] = $r['contact_id'];
        }

        $exclude = implode(',', $linked_ids ?: [0]);
        $unlinked = $conn->query("
            SELECT id, CONCAT(surname, ' ', name) AS full_name 
            FROM contacts 
            WHERE id NOT IN ($exclude) 
            ORDER BY surname ASC
        ");

        if ($unlinked->num_rows > 0):
        ?>
            <form id="linkContactForm">
                <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
                <select name="contact_id" required>
                    <option value="">-- Select Contact --</option>
                    <?php while ($c = $unlinked->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Link</button>
            </form>
            <div id="linkResult"></div>
        <?php else: ?>
            <p>All contacts are already linked.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script src="assets/scripts.js"></script>
</body>
</html>
