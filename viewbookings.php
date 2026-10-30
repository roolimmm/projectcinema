<?php
require_once "db_connect.php";

$orderId = "";
$error = "";
$order = null;
$ticketsByScreening = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedOrderId = $_POST['order_id'] ?? '';
    $orderId = is_string($submittedOrderId)
        ? strtolower(trim($submittedOrderId))
        : '';

    if (!is_string($submittedOrderId)
        || !preg_match('/\A[a-f0-9]{32}\z/i', $orderId)) {
        $error = "Enter the 32-character order ID from your confirmation.";
    } else {
        $orderQuery = "
            SELECT order_id, total_amount, created_at
            FROM orders
            WHERE order_id = ?
        ";
        try {
            $orderStmt = $db->prepare($orderQuery);

            if (!$orderStmt) {
                error_log("Unable to prepare order lookup: " . $db->error);
                http_response_code(500);
                $error = "Booking lookup is temporarily unavailable. Please try again later.";
            } else {
                $orderStmt->bind_param("s", $orderId);
                $orderStmt->execute();
                $order = $orderStmt->get_result()->fetch_assoc();
                $orderStmt->close();
            }
        } catch (mysqli_sql_exception $exception) {
            error_log("Order lookup failed: " . $exception->getMessage());
            http_response_code(500);
            $error = "Booking lookup is temporarily unavailable. Please try again later.";
        }

        if ($order) {
            $ticketQuery = "
                SELECT
                    s.screening_id,
                    s.cinema,
                    s.screening_date,
                    s.screening_time,
                    m.title,
                    ot.seat_label,
                    ot.ticket_price
                FROM order_tickets ot
                JOIN screenings s ON s.screening_id = ot.screening_id
                JOIN movies m ON m.movie_id = s.movie_id
                WHERE ot.order_id = ?
                ORDER BY s.screening_date, s.screening_time, s.cinema, ot.seat_label
            ";

            try {
                $ticketStmt = $db->prepare($ticketQuery);

                if (!$ticketStmt) {
                    error_log("Unable to prepare booking details lookup: " . $db->error);
                    http_response_code(500);
                    $error = "Booking details are temporarily unavailable. Please try again later.";
                    $order = null;
                } else {
                    $ticketStmt->bind_param("s", $orderId);
                    $ticketStmt->execute();
                    $ticketResult = $ticketStmt->get_result();

                    while ($ticket = $ticketResult->fetch_assoc()) {
                        $screeningKey = (string) $ticket['screening_id'];

                        if (!isset($ticketsByScreening[$screeningKey])) {
                            $ticketsByScreening[$screeningKey] = [
                                'title' => $ticket['title'],
                                'cinema' => $ticket['cinema'],
                                'date' => $ticket['screening_date'],
                                'time' => $ticket['screening_time'],
                                'seats' => [],
                                'total' => 0
                            ];
                        }

                        $ticketsByScreening[$screeningKey]['seats'][] = $ticket['seat_label'];
                        $ticketsByScreening[$screeningKey]['total'] += (float) $ticket['ticket_price'];
                    }

                    $ticketStmt->close();
                }
            } catch (mysqli_sql_exception $exception) {
                error_log("Booking details lookup failed: " . $exception->getMessage());
                http_response_code(500);
                $error = "Booking details are temporarily unavailable. Please try again later.";
                $order = null;
            }
        } elseif ($error === "") {
            $error = "We couldn't find a booking with that order ID. Check the ID and try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | RaZor Theatres</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <header class="site-header">
        <a href="index.php" class="logo">
            <h1>RaZor Theatres</h1>
        </a>

        <nav aria-label="Main navigation">
            <a href="index.php">Home</a>
            <a href="movies.php">Movies</a>
            <a href="theatres.php">Theatres</a>
            <a href="promotions.php">Promotions</a>
            <a href="viewbookings.php" aria-current="page">My Bookings</a>
            <a href="cart.php" class="nav-cart" aria-label="View shopping cart">
                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
            </a>
        </nav>
    </header>

    <main class="booking-lookup-page">
        <section class="page-banner">
            <div class="page-banner-content">
                <p class="hero-label">YOUR NEXT MOVIE NIGHT</p>
                <h2>My Bookings</h2>
                <p>Enter the order ID from your booking confirmation to check your tickets.</p>
            </div>
        </section>

        <section class="booking-lookup-content" aria-labelledby="booking-lookup-heading">
            <div class="booking-lookup-card">
                <h3 id="booking-lookup-heading">Find your booking</h3>
                <p class="booking-lookup-help">
                    Your order ID is a 32-character code included in your order confirmation.
                </p>

                <?php if ($error !== "") { ?>
                    <p class="booking-lookup-error" role="alert">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php } ?>

                <form method="POST" action="viewbookings.php" class="booking-lookup-form">
                    <label for="order-id">Order ID</label>
                    <input
                        type="text"
                        id="order-id"
                        name="order_id"
                        value="<?php echo htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8'); ?>"
                        minlength="32"
                        maxlength="32"
                        pattern="[A-Fa-f0-9]{32}"
                        spellcheck="false"
                        autocomplete="off"
                        required
                        aria-describedby="order-id-hint"
                    >
                    <span id="order-id-hint">Use the full ID from your confirmation.</span>
                    <button type="submit" class="booking-lookup-button">Find Booking</button>
                </form>
            </div>

            <?php if ($order) { ?>
                <section class="booking-result" aria-labelledby="booking-result-heading">
                    <div class="booking-result-header">
                        <div>
                            <p class="card-kicker">Booking found</p>
                            <h3 id="booking-result-heading">Your tickets</h3>
                        </div>
                        <p class="booking-order-reference">
                            Order ID
                            <strong><?php echo htmlspecialchars(strtoupper($order['order_id']), ENT_QUOTES, 'UTF-8'); ?></strong>
                        </p>
                    </div>

                    <p class="booking-order-date">
                        Booked <?php echo htmlspecialchars(date("j M Y, g:i A", strtotime($order['created_at'])), ENT_QUOTES, 'UTF-8'); ?>
                    </p>

                    <?php if (empty($ticketsByScreening)) { ?>
                        <p class="booking-lookup-error" role="status">
                            This order exists, but its ticket details are not available. Please contact the cinema.
                        </p>
                    <?php } else { ?>
                        <div class="booking-ticket-list">
                            <?php foreach ($ticketsByScreening as $screening) { ?>
                                <article class="booking-ticket-card">
                                    <h4><?php echo htmlspecialchars($screening['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p><strong>Theatre:</strong> <?php echo htmlspecialchars($screening['cinema'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p>
                                        <strong>Showtime:</strong>
                                        <?php echo htmlspecialchars(date("j M Y", strtotime($screening['date'])), ENT_QUOTES, 'UTF-8'); ?>
                                        at
                                        <?php echo htmlspecialchars(date("g:i A", strtotime($screening['time'])), ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <p><strong>Seats:</strong> <?php echo htmlspecialchars(implode(', ', $screening['seats']), ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Tickets:</strong> <?php echo count($screening['seats']); ?></p>
                                    <p class="booking-ticket-total">
                                        <strong>Subtotal:</strong> S$<?php echo number_format($screening['total'], 2); ?>
                                    </p>
                                </article>
                            <?php } ?>
                        </div>

                        <p class="booking-total">
                            Order total
                            <strong>S$<?php echo number_format((float) $order['total_amount'], 2); ?></strong>
                        </p>
                    <?php } ?>
                </section>
            <?php } ?>
        </section>
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 RaZor Theatres. All Rights Reserved.</p>
    </footer>
</body>
</html>
