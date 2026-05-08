<?php
// ── DB ──────────────────────────────────────────────────────
$connection = new mysqli("localhost", "root", "", "finalproject");
if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);

$message = "";
$ticket  = null;

// ── STEP 2: Confirmed delete ─────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_id"])) {
    $id   = intval($_POST["confirm_id"]);
    $stmt = $connection->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = $stmt->affected_rows > 0 ? "success" : "notfound";
    $stmt->close();
}

// ── STEP 1: Look up ticket to confirm ───────────────────────
elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id   = intval($_POST["id"]);
    $stmt = $connection->prepare("SELECT * FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res  = $stmt->get_result();
    if ($res->num_rows > 0) {
        $ticket  = $res->fetch_assoc();
        $message = "confirm";
    } else {
        $message = "notfound";
    }
    $stmt->close();
}

// ── Pre-fill from GET (coming from search results) ───────────
elseif (isset($_GET["id"]) && $_GET["id"] !== "") {
    $id   = intval($_GET["id"]);
    $stmt = $connection->prepare("SELECT * FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res  = $stmt->get_result();
    if ($res->num_rows > 0) {
        $ticket  = $res->fetch_assoc();
        $message = "confirm";
    }
    $stmt->close();
}

$connection->close();

function priorityBadge($p) {
    $map = [
        'Normal' => ['#059669','#d1fae5'],
        'High'   => ['#d97706','#fef3c7'],
        'Urgent' => ['#dc2626','#fee2e2'],
    ];
    $c = $map[$p] ?? ['#64748b','#f1f5f9'];
    return "<span style='display:inline-block;padding:2px 10px;border-radius:99px;font-size:0.75rem;font-weight:700;color:{$c[0]};background:{$c[1]};border:1px solid {$c[0]}33;'>$p</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISO HELPPAGE | Delete Ticket</title>
    <link rel="stylesheet" href="css/demo.css">
    <link rel="stylesheet" href="css/styleA.css">
    <style>
        .ticket-preview {
            background: #fff8f8;
            border: 1.5px solid #fca5a5;
            border-radius: var(--rs);
            padding: 18px 20px;
            margin: 18px 0;
        }
        .tp-row {
            display: flex;
            gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid #fee2e2;
            font-size: 0.88rem;
        }
        .tp-row:last-child { border-bottom: none; }
        .tp-label { font-weight: 700; color: #991b1b; min-width: 110px; flex-shrink: 0; }
        .tp-val   { color: var(--text-2); }
        .confirm-actions { display: flex; gap: 10px; margin-top: 4px; }
    </style>
</head>
<body class="page-shell">

    <div class="page-topbar">
        <div class="header-left">
            <div class="logo"><img src="Images/logo.svg" alt="MISO Help Page Logo"></div>
            <div class="brand-text">
                <span class="brand-name">MISO</span>
                <span class="brand-sub">IT Support Portal</span>
            </div>
        </div>
        <nav class="header-nav">
            <a href="index.html" class="nav-btn nav-btn--active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
                Home
            </a>
            <a href="javascript:history.back()" class="nav-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
        </nav>
    </div>

    <div class="page-body">

        <div class="form-card">
            <div class="form-card-header">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    Delete a Ticket
                </h2>
            </div>
            <div class="form-card-body">

                <?php if ($message === "success"): ?>
                    <div class="alert-success">&#10003; Ticket deleted successfully.</div>
                    <div class="result-box" style="margin-top:12px;">
                        <p>The ticket has been permanently removed from the system.</p>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:16px;">
                        <a href="search.php" class="btn btn-primary" style="flex:1;">&#128269; Search Tickets</a>
                        <a href="index.html" class="btn btn-back" style="flex:1;justify-content:center;">&#8592; Home</a>
                    </div>

                <?php elseif ($message === "confirm" && $ticket): ?>
                    <div class="alert-error" style="background:#fff8f8;">
                        &#9888;&nbsp; You are about to permanently delete this ticket. This cannot be undone.
                    </div>
                    <div class="ticket-preview">
                        <div class="tp-row"><span class="tp-label">Ticket ID</span><span class="tp-val"><strong>#<?php echo $ticket['id']; ?></strong></span></div>
                        <div class="tp-row"><span class="tp-label">Name</span><span class="tp-val"><?php echo htmlspecialchars($ticket['name']); ?></span></div>
                        <div class="tp-row"><span class="tp-label">Issue</span><span class="tp-val"><?php echo htmlspecialchars($ticket['issue']); ?></span></div>
                        <div class="tp-row"><span class="tp-label">Department</span><span class="tp-val"><?php echo htmlspecialchars($ticket['dept']); ?></span></div>
                        <div class="tp-row"><span class="tp-label">Priority</span><span class="tp-val"><?php echo priorityBadge($ticket['priority']); ?></span></div>
                        <div class="tp-row"><span class="tp-label">Email</span><span class="tp-val"><?php echo htmlspecialchars($ticket['email']); ?></span></div>
                    </div>
                    <div class="confirm-actions">
                        <form method="POST" style="flex:1;">
                            <input type="hidden" name="confirm_id" value="<?php echo $ticket['id']; ?>">
                            <button type="submit" class="btn btn-danger" style="width:100%;">
                                &#128465;&nbsp; Yes, Delete Permanently
                            </button>
                        </form>
                        <a href="index.html" class="btn btn-back" style="flex:1;justify-content:center;">
                            &#10005;&nbsp; Cancel
                        </a>
                    </div>

                <?php else: ?>
                    <?php if ($message === "notfound"): ?>
                        <div class="alert-error" style="margin-bottom:16px;">
                            &#10007; No ticket found with that ID. Please check and try again.
                        </div>
                    <?php endif; ?>

                    <p style="font-size:0.88rem;color:var(--muted);margin-bottom:18px;">
                        Enter the Ticket ID to look it up. You'll see the full ticket details before confirming deletion.
                    </p>
                    <form method="POST">
                        <div class="form-group">
                            <label for="id">Ticket ID <span style="color:#dc2626">*</span></label>
                            <input type="number" id="id" name="id" min="1"
                                   placeholder="Enter the ticket ID to delete" required>
                        </div>
                        <button type="submit" class="btn btn-danger" style="width:100%;">
                            &#128269;&nbsp; Find &amp; Review Ticket
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        </div>

    </div>

</body>
</html>
