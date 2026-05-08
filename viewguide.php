<?php
$connection = new mysqli("localhost", "root", "", "finalproject");
if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);

$guide    = null;
$notFound = false;
$allGuides = [];

// ── Fetch a specific guide by ID ─────────────────────────────
if (isset($_GET["id"]) && $_GET["id"] !== "") {
    $id   = intval($_GET["id"]);
    $stmt = $connection->prepare("SELECT * FROM diy WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res  = $stmt->get_result();
    if ($res->num_rows > 0) {
        $guide = $res->fetch_assoc();
    } else {
        $notFound = true;
    }
    $stmt->close();
}

// ── Always load all guides for the browse list ───────────────
$res = $connection->query("SELECT id, problem FROM diy ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $allGuides[] = $row;
}

$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISO HELPPAGE | Troubleshooting Guides</title>
    <link rel="stylesheet" href="css/demo.css">
    <link rel="stylesheet" href="css/styleA.css">
    <style>
        .guide-list { list-style: none; display: flex; flex-direction: column; gap: 8px; margin-top: 20px; }
        .guide-list-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: var(--surface-2);
            border: 1.5px solid #d0e6f5;
            border-radius: var(--rs);
            color: var(--text);
            font-size: 0.88rem;
            font-weight: 500;
            transition: all var(--tr);
            text-decoration: none;
        }
        .guide-list-item a:hover {
            background: #e0f2fe;
            border-color: var(--c-teal);
            color: var(--c-teal);
            transform: translateX(3px);
        }
        .guide-id-badge {
            flex-shrink: 0;
            width: 32px; height: 32px;
            border-radius: 8px;
            background: rgba(124,58,237,0.10);
            color: var(--c-violet);
            font-size: 0.75rem;
            font-weight: 800;
            display: grid;
            place-items: center;
        }
        .guide-detail {
            background: #f5f3ff;
            border: 1.5px solid #c4b5fd;
            border-radius: var(--rs);
            padding: 20px 22px;
            margin-top: 20px;
        }
        .guide-detail h3 {
            font-size: 1rem;
            font-weight: 800;
            color: var(--c-violet);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .guide-steps {
            white-space: pre-wrap;
            font-size: 0.88rem;
            color: var(--text-2);
            line-height: 1.8;
            font-family: var(--font);
        }
        .guide-filter {
            width: 100%;
            padding: 9px 14px;
            font-size: 0.88rem;
            font-family: var(--font);
            color: var(--text);
            background: var(--surface-2);
            border: 1.5px solid #d0e6f5;
            border-radius: var(--rs);
            outline: none;
            margin-bottom: 4px;
            transition: border-color var(--tr), box-shadow var(--tr);
        }
        .guide-filter:focus {
            border-color: var(--c-violet);
            box-shadow: 0 0 0 3px rgba(124,58,237,0.10);
        }
        .empty-list {
            text-align: center;
            padding: 32px 16px;
            color: var(--muted);
            font-size: 0.88rem;
        }
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

        <!-- LEFT: Browse + Detail -->
        <div class="form-card">
            <div class="form-card-header">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><line x1="8" y1="9" x2="10" y2="9"/></svg>
                    Troubleshooting Guide Repository
                </h2>
            </div>
            <div class="form-card-body">

                <!-- Search by ID -->
                <form method="GET" style="display:flex;gap:10px;align-items:flex-end;">
                    <div class="form-group" style="flex:1;margin-bottom:0;">
                        <label for="id">Jump to Guide by ID</label>
                        <input type="number" id="id" name="id" min="1"
                               placeholder="Enter guide ID"
                               value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="flex-shrink:0;padding:11px 20px;">
                        &#128269; View
                    </button>
                </form>

                <?php if ($notFound): ?>
                    <div class="alert-error" style="margin-top:14px;">No guide found with that ID.</div>
                <?php endif; ?>

                <?php if ($guide): ?>
                    <!-- ── Guide Detail ── -->
                    <div class="guide-detail">
                        <h3>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Guide #<?php echo $guide['id']; ?> — <?php echo htmlspecialchars($guide['problem']); ?>
                        </h3>
                        <div class="guide-steps"><?php echo htmlspecialchars($guide['troubleshooting']); ?></div>
                    </div>
                    <a href="viewguide.php" class="btn btn-back" style="margin-top:14px;display:inline-flex;">
                        &#8592; Back to All Guides
                    </a>
                <?php endif; ?>

                <!-- ── Browse All Guides ── -->
                <?php if (!$guide): ?>
                    <div style="margin-top:22px;">
                        <p style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--muted);margin-bottom:10px;">
                            All Guides (<?php echo count($allGuides); ?>)
                        </p>
                        <?php if (count($allGuides) > 5): ?>
                            <input type="text" class="guide-filter" id="guideFilter"
                                   placeholder="&#128269;  Filter guides by keyword…"
                                   oninput="filterGuides(this.value)">
                        <?php endif; ?>

                        <?php if (count($allGuides) > 0): ?>
                            <ul class="guide-list" id="guideList">
                                <?php foreach ($allGuides as $g): ?>
                                    <li class="guide-list-item" data-title="<?php echo strtolower(htmlspecialchars($g['problem'])); ?>">
                                        <a href="viewguide.php?id=<?php echo $g['id']; ?>">
                                            <span class="guide-id-badge">#<?php echo $g['id']; ?></span>
                                            <?php echo htmlspecialchars($g['problem']); ?>
                                            <svg style="margin-left:auto;flex-shrink:0;opacity:0.4;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div id="noGuideMatch" class="empty-list" style="display:none;">
                                No guides match your search.
                            </div>
                        <?php else: ?>
                            <div class="empty-list">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 10px;display:block;opacity:0.3;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <p>No guides have been added yet.</p>
                                <a href="diy_corner.html" style="color:var(--c-violet);font-weight:600;">Add the first guide &#8594;</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <script>
    function filterGuides(val) {
        var q     = val.toLowerCase().trim();
        var items = document.querySelectorAll('#guideList .guide-list-item');
        var shown = 0;
        items.forEach(function(li) {
            var match = li.dataset.title.includes(q);
            li.style.display = match ? '' : 'none';
            if (match) shown++;
        });
        var noMatch = document.getElementById('noGuideMatch');
        if (noMatch) noMatch.style.display = shown === 0 ? 'block' : 'none';
    }
    </script>

</body>
</html>
