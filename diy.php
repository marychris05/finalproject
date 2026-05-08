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


