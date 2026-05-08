<?php
// ── DB ──────────────────────────────────────────────────────
$connection = new mysqli("localhost", "root", "", "finalproject");
if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    !empty(trim($_POST['a'] ?? '')) &&
    !empty(trim($_POST['b'] ?? ''))) {

    $problem         = $connection->real_escape_string(trim($_POST['a']));
    $troubleshooting = $connection->real_escape_string(trim($_POST['b']));
    $sql             = "INSERT INTO diy (problem, troubleshooting) VALUES ('$problem', '$troubleshooting')";
    $success         = $connection->query($sql) === true;
} else {
    // Nothing was posted — redirect back to the form
    header("Location: diy_corner.html");
    exit;
}

$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISO HELPPAGE | Guide Submitted</title>
    <link rel="stylesheet" href="css/demo.css">
    <link rel="stylesheet" href="css/styleA.css">
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

        <!-- LEFT: Result -->
        <div class="form-card">
            <div class="form-card-header">
                <h2>&#9998; Guide Submission</h2>
            </div>
            <div class="form-card-body">

                <?php if ($success): ?>
                    <div class="alert-success">&#10003; Guide successfully submitted</div>
                <?php else: ?>
                    <div class="alert-error">&#10007; Error occurred. Please try again.</div>
                <?php endif; ?>

                <div class="progress-wrap">
                    <p class="progress-label-top">Saving to database&hellip;</p>
                    <div class="progress-track">
                        <div class="progress-bar" id="myBar">
                            <span id="label">10%</span>
                        </div>
                    </div>
                </div>

                <a href="index.html" class="btn btn-primary" style="width:100%; margin-top:16px;">&#8592; Back to Home</a>
                <a href="diy_corner.html" class="btn btn-back" style="width:100%; margin-top:10px; justify-content:center;">&#43; Add Another Guide</a>

            </div>
        </div>

    </div>

    <script>
        var bar = document.getElementById("myBar");
        var lbl = document.getElementById("label");
        var w   = 10;
        var id  = setInterval(function() {
            if (w >= 100) { clearInterval(id); lbl.textContent = "Done!"; }
            else { w++; bar.style.width = w + "%"; lbl.textContent = w + "%"; }
        }, 12);
    </script>

</body>
</html>


