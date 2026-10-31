
<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit();
}

$orderId = $_SESSION['last_order_id'];

$stmt = $db->prepare("
    SELECT order_id, total_amount, created_at
    FROM orders
    WHERE order_id = ?
");

$stmt->bind_param("s", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: index.php");
    exit();
}

$ticketStmt = $db->prepare("
    SELECT
        m.title,
        s.cinema,
        s.screening_date,
        s.screening_time,
        ot.seat_label
    FROM order_tickets ot
    JOIN screenings s
        ON ot.screening_id = s.screening_id
    JOIN movies m
        ON s.movie_id = m.movie_id
    WHERE ot.order_id = ?
    ORDER BY s.screening_date, s.screening_time, ot.seat_label
");

$ticketStmt->bind_param("s", $orderId);
$ticketStmt->execute();
$tickets = $ticketStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$ticketStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed | RaZor Theatres</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="checkout-page">
    <section class="checkout-panel confirmation-panel">
        <div class="confirmation-icon">&#10003;</div>

        <h1>Booking Confirmed!</h1>

        <p>Thank you for your booking!</p>
        <p>Your booking has been successfully recorded.</p>

        <div class="booking-reference">
            <span>Booking Reference</span>
            <strong><?php echo htmlspecialchars($order['order_id']); ?></strong>
        </div>

        <p>
            Please save your booking reference to retrieve your tickets later.
        </p>

        <h2>Your Tickets</h2>

        <?php foreach ($tickets as $ticket) { ?>
            <div class="checkout-movie">
                <h3><?php echo htmlspecialchars($ticket['title']); ?></h3>

                <p><?php echo htmlspecialchars($ticket['cinema']); ?></p>

                <p>
                    <?php echo htmlspecialchars($ticket['screening_date']); ?>
                    at
                    <?php echo htmlspecialchars(substr($ticket['screening_time'], 0, 5)); ?>
                </p>

                <p>
                    Seat:
                    <strong><?php echo htmlspecialchars($ticket['seat_label']); ?></strong>
                </p>
            </div>
        <?php } ?>

        <div class="checkout-total-row grand-total">
            <strong>Total</strong>
            <strong>S$<?php echo number_format($order['total_amount'], 2); ?></strong>
        </div>

        <a href="index.php" class="checkout-button confirmation-home">
            Back to Home
        </a>
    </section>
</main>

</body>
</html>
