<?php
// ── DB ──────────────────────────────────────────────────────
$connection = new mysqli("localhost", "root", "", "finalproject");
if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);

$message = "";
$ticket  = null;

// ── SAVE UPDATE ─────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    // Validation
    $errors = [];
    $name   = trim($_POST["name"]        ?? "");
    $email  = trim($_POST["email"]       ?? "");
    $issue  = trim($_POST["issue"]       ?? "");
    $dept   = trim($_POST["dept"]        ?? "");
    $phone  = trim($_POST["phone_no"]    ?? "");
    $prio   = trim($_POST["priority"]    ?? "");
    $desc   = trim($_POST["description"] ?? "");

    if ($name  === "") $errors[] = "Full name is required.";
    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "A valid email address is required.";
    if ($issue === "") $errors[] = "Issue title is required.";
    if ($dept  === "") $errors[] = "Department is required.";
    if ($phone === "") $errors[] = "Contact number is required.";

    if (empty($errors)) {
        $stmt = $connection->prepare(
            "UPDATE tickets SET name=?, email=?, issue=?, dept=?, phone_no=?, priority=?, description=? WHERE id=?"
        );
        $stmt->bind_param("sssssssi", $name, $email, $issue, $dept, $phone, $prio, $desc, $id);
        $message = $stmt->execute() ? "success" : "error";
        $stmt->close();
    } else {
        $message = "validation";
        // Re-populate ticket for re-display
        $ticket = [
            'id'=>$id,'name'=>$name,'email'=>$email,'issue'=>$issue,
            'dept'=>$dept,'phone_no'=>$phone,'priority'=>$prio,'description'=>$desc
        ];
    }
}

// ── LOAD TICKET BY ID ────────────────────────────────────────
if ($message === "" && isset($_GET["id"]) && $_GET["id"] !== "") {
    $id   = intval($_GET["id"]);
    $stmt = $connection->prepare("SELECT * FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res  = $stmt->get_result();
    if ($res->num_rows > 0) $ticket = $res->fetch_assoc();
    $stmt->close();
}
$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISO HELPPAGE | Update Ticket</title>
    <link rel="stylesheet" href="css/demo.css">
    <link rel="stylesheet" href="css/styleA.css">
    <style>
        /* Save button disabled state */
        #saveBtn:disabled {
            opacity: 0.45; cursor: not-allowed;
            transform: none !important; box-shadow: none !important;
        }
        .changed-indicator {
            display:none; font-size:0.75rem; color:#d97706;
            font-weight:600; margin-left:8px;
        }
        .field-error {
            font-size:0.75rem; color:var(--c-red);
            margin-top:4px; display:block;
        }
        .validation-list {
            padding:12px 16px 12px 32px; margin-bottom:16px;
            background:#fef2f2; border:1.5px solid #fca5a5;
            border-radius:var(--rs); color:#b91c1c; font-size:0.88rem;
        }
        .validation-list li { margin-bottom:4px; }
    </style>
</head>
<body class="page-shell">

    <!-- ── ANIMATED GEOMETRIC BACKGROUND ── -->
    <div class="bg-canvas" aria-hidden="true">
        <svg class="bg-svg" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs><pattern id="grid" width="80" height="80" patternUnits="userSpaceOnUse"><path d="M 80 0 L 0 0 0 80" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="0.8"/></pattern></defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
            <line x1="-5%" y1="30%" x2="40%" y2="0%"    stroke="rgba(255,255,255,0.10)" stroke-width="1.2"/>
            <line x1="0%"  y1="70%" x2="55%" y2="10%"   stroke="rgba(255,255,255,0.07)" stroke-width="0.9"/>
            <line x1="60%" y1="0%"  x2="105%" y2="50%"  stroke="rgba(255,255,255,0.10)" stroke-width="1.2"/>
            <line x1="45%" y1="100%" x2="105%" y2="30%" stroke="rgba(255,255,255,0.07)" stroke-width="0.9"/>
            <line x1="20%" y1="100%" x2="80%" y2="0%"   stroke="rgba(255,255,255,0.05)" stroke-width="0.7"/>
            <circle cx="18%" cy="22%" r="3"   fill="rgba(255,255,255,0.18)"/>
            <circle cx="40%" cy="8%"  r="2"   fill="rgba(255,255,255,0.14)"/>
            <circle cx="72%" cy="15%" r="3.5" fill="rgba(255,255,255,0.16)"/>
            <circle cx="88%" cy="42%" r="2.5" fill="rgba(255,255,255,0.13)"/>
            <circle cx="65%" cy="78%" r="3"   fill="rgba(255,255,255,0.15)"/>
            <circle cx="28%" cy="85%" r="2"   fill="rgba(255,255,255,0.12)"/>
            <circle cx="8%"  cy="60%" r="2.5" fill="rgba(255,255,255,0.14)"/>
            <line x1="18%" y1="22%" x2="40%" y2="8%"   stroke="rgba(255,255,255,0.12)" stroke-width="0.8" stroke-dasharray="4 6"/>
            <line x1="40%" y1="8%"  x2="72%" y2="15%"  stroke="rgba(255,255,255,0.10)" stroke-width="0.8" stroke-dasharray="4 6"/>
            <line x1="72%" y1="15%" x2="88%" y2="42%"  stroke="rgba(255,255,255,0.12)" stroke-width="0.8" stroke-dasharray="4 6"/>
            <line x1="88%" y1="42%" x2="65%" y2="78%"  stroke="rgba(255,255,255,0.10)" stroke-width="0.8" stroke-dasharray="4 6"/>
            <line x1="65%" y1="78%" x2="28%" y2="85%"  stroke="rgba(255,255,255,0.12)" stroke-width="0.8" stroke-dasharray="4 6"/>
            <line x1="28%" y1="85%" x2="8%"  y2="60%"  stroke="rgba(255,255,255,0.10)" stroke-width="0.8" stroke-dasharray="4 6"/>
            <line x1="8%"  y1="60%" x2="18%" y2="22%"  stroke="rgba(255,255,255,0.12)" stroke-width="0.8" stroke-dasharray="4 6"/>
        </svg>
    </div>

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

    </nav>
</div>

<div class="page-body">

    <!-- Glowing crimson tab + form card wrapper -->
    <div class="page-card-wrap">
        <div class="page-card-tab page-card-tab--crimson" aria-hidden="true"></div>
    <div class="form-card form-card--crimson">
        <div class="form-card-header">
            <h2>&#9998; Update a Ticket
                <span class="changed-indicator" id="changedBadge">&#9679; Unsaved changes</span>
            </h2>
        </div>
        <div class="form-card-body">

        <?php if ($message === "success"): ?>
            <!-- ── SUCCESS ── -->
            <div class="alert-success">&#10003; Ticket successfully submitted</div>
            <div class="result-box">
                <p>Your ticket has been saved. You can search for it anytime using your Ticket ID.</p>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <a href="search.php" class="btn btn-primary" style="flex:1;">&#128269; Search Tickets</a>
                <a href="index.html" class="btn btn-back" style="flex:1;justify-content:center;">&#8592; Home</a>
            </div>

        <?php elseif ($message === "error"): ?>
            <div class="alert-error">&#10007; Error occurred. Please try again.</div>

        <?php elseif ($ticket): ?>
            <!-- ── EDIT FORM ── -->
            <?php if ($message === "validation" && !empty($errors)): ?>
                <ul class="validation-list">
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" id="updateForm">
                <input type="hidden" name="id" value="<?php echo $ticket['id']; ?>">

                <!-- Store originals for change detection -->
                <input type="hidden" id="orig_name"        value="<?php echo htmlspecialchars($ticket['name']); ?>">
                <input type="hidden" id="orig_email"       value="<?php echo htmlspecialchars($ticket['email']); ?>">
                <input type="hidden" id="orig_issue"       value="<?php echo htmlspecialchars($ticket['issue']); ?>">
                <input type="hidden" id="orig_dept"        value="<?php echo htmlspecialchars($ticket['dept']); ?>">
                <input type="hidden" id="orig_phone"       value="<?php echo htmlspecialchars($ticket['phone_no']); ?>">
                <input type="hidden" id="orig_priority"    value="<?php echo htmlspecialchars($ticket['priority']); ?>">
                <input type="hidden" id="orig_description" value="<?php echo htmlspecialchars($ticket['description']); ?>">

                <div class="form-group">
                    <label>Full Name <span style="color:#dc2626">*</span></label>
                    <input type="text" name="name" id="f_name"
                           value="<?php echo htmlspecialchars($ticket['name']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span style="color:#dc2626">*</span></label>
                        <input type="email" name="email" id="f_email"
                               value="<?php echo htmlspecialchars($ticket['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Number <span style="color:#dc2626">*</span></label>
                        <input type="text" name="phone_no" id="f_phone"
                               value="<?php echo htmlspecialchars($ticket['phone_no']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Issue <span style="color:#dc2626">*</span></label>
                    <input type="text" name="issue" id="f_issue"
                           value="<?php echo htmlspecialchars($ticket['issue']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Department <span style="color:#dc2626">*</span></label>
                        <select name="dept" id="f_dept">
                            <?php
                            $depts = [
                                "1"=>"College of Education","2"=>"College of Agriculture",
                                "3"=>"College of Forestry","4"=>"College of Hospitality and Tourism",
                                "5"=>"College of Arts and Science","6"=>"Registrar's Office",
                                "7"=>"Administrative Office","8"=>"Cashier's / Accounting Office",
                                "9"=>"College of Technology and Engineering"
                            ];
                            foreach ($depts as $v => $label):
                                $sel = ($ticket['dept'] == $v || $ticket['dept'] == $label) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $v; ?>" <?php echo $sel; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" id="f_priority">
                            <?php foreach (['Normal','High','Urgent'] as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo $ticket['priority']==$p?'selected':''; ?>>
                                    <?php echo $p; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="f_description"><?php echo htmlspecialchars($ticket['description']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" id="saveBtn" style="width:100%;" disabled>
                    &#10003; Save Changes
                </button>
                <p style="font-size:0.75rem;color:var(--muted);text-align:center;margin-top:8px;">
                    Save button activates when you make a change.
                </p>
            </form>

        <?php else: ?>
            <!-- ── FIND TICKET ── -->
            <?php if (isset($_GET["id"]) && $_GET["id"] !== ""): ?>
                <div class="alert-error" style="margin-bottom:18px;">
                    &#10007; No ticket found with ID <strong>#<?php echo intval($_GET['id']); ?></strong>.
                </div>
            <?php endif; ?>

            <form method="GET">
                <div class="form-group">
                    <label for="id">Ticket ID <span style="color:#dc2626">*</span></label>
                    <input type="number" id="id" name="id" min="1"
                           placeholder="Enter ticket ID to edit" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">&#128269; Find Ticket</button>
            </form>
        <?php endif; ?>

        </div>
    </div><!-- /.form-card -->
    </div><!-- /.page-card-wrap -->


</div>

<script>
// ── Change detection: enable Save only when something changed ──
(function() {
    var fields = ['f_name','f_email','f_issue','f_dept','f_phone','f_priority','f_description'];
    var origIds = ['orig_name','orig_email','orig_issue','orig_dept','orig_phone','orig_priority','orig_description'];
    var saveBtn = document.getElementById('saveBtn');
    var badge   = document.getElementById('changedBadge');
    if (!saveBtn) return;

    function checkChanges() {
        var changed = false;
        for (var i = 0; i < fields.length; i++) {
            var f = document.getElementById(fields[i]);
            var o = document.getElementById(origIds[i]);
            if (f && o && f.value !== o.value) { changed = true; break; }
        }
        saveBtn.disabled = !changed;
        if (badge) badge.style.display = changed ? 'inline' : 'none';
    }

    fields.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', checkChanges);
        if (el) el.addEventListener('change', checkChanges);
    });
})();
</script>
</body>
</html>
