function validateClientForm() {
    const name = document.querySelector('input[name="name"]').value.trim();
    if (name === "") {
        alert("Please enter a client name.");
        return false;
    }
    return true;
}

document.addEventListener("DOMContentLoaded", function () {
    // LINK CONTACT TO CLIENT (from client page)
    const linkForm = document.getElementById("linkContactForm");
    if (linkForm) {
        console.log("✅ linkContactForm found");

        linkForm.addEventListener("submit", function (e) {
            e.preventDefault();
            console.log("🚀 Submitting link form via AJAX");

            const formData = new FormData(linkForm);
            const xhr = new XMLHttpRequest();

            xhr.open("POST", "actions/link_contact.php", true);
            xhr.onload = function () {
                console.log("📡 Server responded:", xhr.status, xhr.responseText);
                if (xhr.status === 200) {
                    document.getElementById("linkResult").innerHTML = xhr.responseText;
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert("Something went wrong.");
                }
            };
            xhr.onerror = function () {
                console.error("❌ AJAX request failed.");
            };
            xhr.send(formData);
        });
    } else {
        console.warn("⚠️ linkContactForm not found");
    }

    // UNLINK CONTACT FROM CLIENT
    document.querySelectorAll(".unlink-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const clientId = this.getAttribute("data-client-id");
            const contactId = this.getAttribute("data-contact-id");
            const row = this.closest("tr");

            if (!confirm("Are you sure you want to unlink this contact?")) return;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "actions/unlink_contact.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    if (row) row.remove();
                    console.log("✅ Contact unlinked successfully!");
                } else {
                    alert("❌ Failed to unlink contact.");
                }
            };
            xhr.send(`client_id=${clientId}&contact_id=${contactId}`);
        });
    });

    // UNLINK CLIENT FROM CONTACT
    document.querySelectorAll(".unlink-client-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const clientId = this.getAttribute("data-client-id");
            const contactId = this.getAttribute("data-contact-id");
            const row = this.closest("tr");

            if (!confirm("Are you sure you want to unlink this client?")) return;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "actions/unlink_client.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    if (row) row.remove();
                    console.log("✅ Client unlinked from contact!");
                } else {
                    alert("❌ Failed to unlink client.");
                }
            };
            xhr.send(`client_id=${clientId}&contact_id=${contactId}`);
        });
    });

    // LINK CLIENT TO CONTACT (from contact page)
    const linkClientForm = document.getElementById("linkClientForm");
    if (linkClientForm) {
        linkClientForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(linkClientForm);

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "actions/link_client.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("linkClientResult").innerHTML = xhr.responseText;
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert("❌ Failed to link client.");
                }
            };
            xhr.send(formData);
        });
    }
});
