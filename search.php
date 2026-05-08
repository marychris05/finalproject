<?php
// ── DB ──────────────────────────────────────────────────────
$connection = new mysqli("localhost", "root", "", "finalproject");
if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);

$tickets  = [];
$searched = false;
$error    = "";

// ── SEARCH ──────────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "GET" &&
    (isset($_GET["q"]) || isset($_GET["search_id"]))) {

    $searched = true;
    $q        = trim($_GET["q"]         ?? "");
    $sid      = trim($_GET["search_id"] ?? "");

    if ($sid !== "") {
        // Exact match by ID
        $id   = intval($sid);
        $stmt = $connection->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } elseif ($q !== "") {
        // Partial match by name
        $like = "%" . $q . "%";
        $stmt = $connection->prepare("SELECT * FROM tickets WHERE name LIKE ?");
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $error = "Please enter a Ticket ID or a name to search.";
    }
}
$connection->close();

// ── Priority badge helper ────────────────────────────────────
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
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISO HELPPAGE | Search Ticket</title>
    <link rel="stylesheet" href="css/demo.css">
    <link rel="stylesheet" href="css/styleA.css">
    <style>
        .search-tabs { display:flex; gap:8px; margin-bottom:20px; }
        .tab-btn {
            flex:1; padding:9px 14px; font-size:0.85rem; font-weight:600;
            border:1.5px solid #d0e6f5; border-radius:var(--rs);
            background:var(--surface-2); color:var(--text-2); cursor:pointer;
            transition:all var(--tr); font-family:var(--font);
        }
        .tab-btn.active, .tab-btn:hover {
            background:var(--c-teal); color:#fff; border-color:var(--c-teal);
        }
        .search-panel { display:none; }
        .search-panel.active { display:block; }

        .results-table { width:100%; border-collapse:collapse; margin-top:24px; font-size:0.88rem; }
        .results-table th {
            background:var(--surface-2); color:var(--text-2); font-size:0.72rem;
            font-weight:700; text-transform:uppercase; letter-spacing:0.5px;
            padding:10px 14px; text-align:left; border-bottom:2px solid #d0e6f5;
        }
        .results-table td {
            padding:12px 14px; border-bottom:1px solid #d0e6f5;
            color:var(--text); vertical-align:top;
        }
        .results-table tr:last-child td { border-bottom:none; }
        .results-table tr:hover td { background:var(--surface-2); }
        .results-table .detail-row td { background:#f8fafc; }

        .action-btns { display:flex; gap:6px; flex-wrap:wrap; }
        .btn-sm {
            padding:6px 12px; font-size:0.78rem; font-weight:700;
            border-radius:6px; cursor:pointer;
            font-family:var(--font); display:inline-flex; align-items:center; gap:5px;
            transition:all var(--tr); text-decoration:none;
        }
        .btn-sm-edit { background:#fff7ed; color:#d97706; border:1.5px solid #fcd34d; }
        .btn-sm-edit:hover { background:#d97706; color:#fff; border-color:#d97706; }
        .btn-sm-del  { background:#fef2f2; color:#dc2626; border:1.5px solid #fca5a5; }
        .btn-sm-del:hover  { background:#dc2626; color:#fff; border-color:#dc2626; }

        .no-results { text-align:center; padding:40px 20px; color:var(--muted); }
        .no-results svg { margin:0 auto 12px; display:block; opacity:0.35; }
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

    <!-- Glowing amber tab + form card wrapper -->
    <div class="page-card-wrap">
        <div class="page-card-tab page-card-tab--amber" aria-hidden="true"></div>
        <div class="form-card form-card--amber">
        <div class="form-card-header">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Search a Ticket
            </h2>
        </div>
        <div class="form-card-body">

            <!-- Tab switcher -->
            <div class="search-tabs">
                <button class="tab-btn <?php echo (!isset($_GET['q']) || isset($_GET['search_id'])) ? 'active' : ''; ?>"
                        onclick="switchTab('id-panel','name-panel',this)" type="button">
                    Search by ID
                </button>
                <button class="tab-btn <?php echo (isset($_GET['q']) && !isset($_GET['search_id'])) ? 'active' : ''; ?>"
                        onclick="switchTab('name-panel','id-panel',this)" type="button">
                    Search by Name
                </button>
            </div>

            <!-- ID search -->
            <div id="id-panel" class="search-panel <?php echo (!isset($_GET['q']) || isset($_GET['search_id'])) ? 'active' : ''; ?>">
                <form method="GET">
                    <div class="form-group">
                        <label for="search_id">Ticket ID</label>
                        <input type="number" id="search_id" name="search_id" min="1"
                               placeholder="Enter exact ticket ID"
                               value="<?php echo htmlspecialchars($_GET['search_id'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        &#128269; Search by ID
                    </button>
                </form>
            </div>

            <!-- Name search -->
            <div id="name-panel" class="search-panel <?php echo (isset($_GET['q']) && !isset($_GET['search_id'])) ? 'active' : ''; ?>">
                <form method="GET">
                    <div class="form-group">
                        <label for="q">First or Last Name</label>
                        <input type="text" id="q" name="q"
                               placeholder="e.g. Juan or Dela Cruz"
                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        &#128269; Search by Name
                    </button>
                </form>
            </div>

            <!-- Validation error -->
            <?php if ($error): ?>
                <div class="alert-error" style="margin-top:16px;"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Results -->
            <?php if ($searched && !$error): ?>
                <?php if (count($tickets) > 0): ?>
                    <p style="margin-top:20px;font-size:0.85rem;color:var(--muted);">
                        <?php echo count($tickets); ?> result<?php echo count($tickets) > 1 ? 's' : ''; ?> found
                    </p>
                    <div style="overflow-x:auto;">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Issue</th>
                                <th>Priority</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($tickets as $t): ?>
                            <tr>
                                <td><strong>#<?php echo $t['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($t['name']); ?></td>
                                <td><?php echo htmlspecialchars($t['issue']); ?></td>
                                <td><?php echo priorityBadge($t['priority']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="update.php?id=<?php echo $t['id']; ?>" class="btn-sm btn-sm-edit">
                                            &#9998; Update
                                        </a>
                                        <a href="delete.php?id=<?php echo $t['id']; ?>" class="btn-sm btn-sm-del">
                                            &#128465; Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="detail-row">
                                <td colspan="5" style="padding:6px 14px 14px;font-size:0.82rem;color:var(--text-2);">
                                    <strong>Email:</strong> <?php echo htmlspecialchars($t['email']); ?> &nbsp;&bull;&nbsp;
                                    <strong>Dept:</strong> <?php echo htmlspecialchars($t['dept']); ?> &nbsp;&bull;&nbsp;
                                    <strong>Contact:</strong> <?php echo htmlspecialchars($t['phone_no']); ?><br>
                                    <?php if (!empty($t['description'])): ?>
                                    <strong>Description:</strong> <?php echo htmlspecialchars($t['description']); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <p style="font-weight:600;color:var(--text-2);">No tickets found</p>
                        <p style="font-size:0.82rem;margin-top:4px;">Try a different ID or name.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div><!-- /.form-card -->
    </div><!-- /.page-card-wrap -->

    <!-- RIGHT: Info Panel removed -->
</div>

<script>
function switchTab(show, hide, btn) {
    document.getElementById(show).classList.add('active');
    document.getElementById(hide).classList.remove('active');
    document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
}
</script>
</body>
</html>
