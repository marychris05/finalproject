<?php
$connection = new mysqli("localhost", "root", "", "finalproject");
if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);

$ticket_id = null;
$error     = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $connection->real_escape_string($_POST["fname"] . " " . $_POST["lname"]);
    $email    = $connection->real_escape_string($_POST["email"]);
    $issue    = $connection->real_escape_string($_POST["issue"]);
    $dept     = $connection->real_escape_string($_POST["dept"]);
    $phone    = $connection->real_escape_string($_POST["phone_no"]);
    $priority = $connection->real_escape_string($_POST["priority"]);
    $desc     = $connection->real_escape_string($_POST["desc"]);

    $sql = "INSERT INTO tickets (name, email, issue, dept, phone_no, priority, description)
            VALUES ('$name', '$email', '$issue', '$dept', '$phone', '$priority', '$desc')";

    if ($connection->query($sql) === TRUE) {
        $ticket_id = $connection->insert_id;
    } else {
        $error = $connection->error;
    }
}
$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISO HELPPAGE | Ticket Created</title>
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
                <h2>&#128228; Ticket Submission</h2>
            </div>
            <div class="form-card-body">

                <?php if ($ticket_id): ?>
                    <div class="alert-success">&#10003; Ticket successfully submitted</div>
                    <div class="result-box">
                        <p><strong>Ticket ID:</strong> <?php echo $ticket_id; ?></p>
                        <p style="margin-top:6px;">Save this ID — you'll need it to search, update, or delete your ticket.</p>
                    </div>
                    <a href="index.html" class="btn btn-primary" style="width:100%; margin-top:8px;">&#8592; Back to Home</a>
                <?php else: ?>
                    <div class="alert-error">&#10007; Error occurred. Please try again.</div>
                    <a href="send_ticket.html" class="btn btn-primary" style="width:100%; margin-top:8px;">&#8592; Try Again</a>
                <?php endif; ?>

            </div>
        </div>

    </div>

</body>
</html>


