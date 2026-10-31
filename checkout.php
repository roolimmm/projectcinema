
<?php
session_start();
require_once "db_connect.php";

$ticketPrice = 15.00;
$error = "";
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: cart.php");
    exit();
}

$totalTickets = 0;

foreach ($cart as $item) {
    $totalTickets += count($item['seats']);
}

$totalPrice = $totalTickets * $ticketPrice;
$name = "";
$email = "";
$phone = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');

    if ($name === '' || strlen($name) > 100) {
        $error = "Please enter a valid name.";
    } elseif (!preg_match('/^[\w.-]+@(?:\w+\.){1,3}\w{2,3}$/', $email) || strlen($email) > 255) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match('/^\+[1-9][0-9]{1,14}$/', $phone)) {
        $error = "Please enter a valid phone number with country code (e.g. +6591234567).";
    } else {
        try {
            $db->begin_transaction();

            $selectedTickets = [];
            $seenSeats = [];

            $seatStmt = $db->prepare("
                SELECT screening_seat_id
                FROM screening_seats
                WHERE screening_id = ?
                  AND seat_label = ?
                  AND status = 'available'
                FOR UPDATE
            ");

            foreach ($cart as $item) {
                $screeningId = (int) $item['screening_id'];

                foreach ($item['seats'] as $seatLabel) {
                    $seatLabel = trim($seatLabel);
                    $key = $screeningId . ':' . $seatLabel;

                    if (isset($seenSeats[$key])) {
                        throw new Exception("Duplicate seat detected.");
                    }

                    $seenSeats[$key] = true;

                    $seatStmt->bind_param("is", $screeningId, $seatLabel);
                    $seatStmt->execute();

                    $seat = $seatStmt->get_result()->fetch_assoc();

                    if (!$seat) {
                        throw new Exception(
                            "Seat " . $seatLabel .
                            " is no longer available. Please review your cart."
                        );
                    }

                    $selectedTickets[] = [
                        'screening_id' => $screeningId,
                        'seat_label' => $seatLabel,
                        'screening_seat_id' => (int) $seat['screening_seat_id']
                    ];
                }
            }

            $seatStmt->close();

            if (count($selectedTickets) !== $totalTickets) {
                throw new Exception("Invalid cart information.");
            }

            $orderId = bin2hex(random_bytes(16));

            $orderStmt = $db->prepare("
                INSERT INTO orders (order_id, total_amount)
                VALUES (?, ?)
            ");

            $orderStmt->bind_param("sd", $orderId, $totalPrice);
            $orderStmt->execute();
            $orderStmt->close();

            $ticketStmt = $db->prepare("
                INSERT INTO order_tickets
                    (order_id, screening_id, seat_label, ticket_price)
                VALUES (?, ?, ?, ?)
            ");

            $updateStmt = $db->prepare("
                UPDATE screening_seats
                SET status = 'unavailable'
                WHERE screening_seat_id = ?
                  AND status = 'available'
            ");

            foreach ($selectedTickets as $ticket) {
                $screeningId = $ticket['screening_id'];
                $seatLabel = $ticket['seat_label'];
                $seatId = $ticket['screening_seat_id'];

                $ticketStmt->bind_param(
                    "sisd",
                    $orderId,
                    $screeningId,
                    $seatLabel,
                    $ticketPrice
                );
                $ticketStmt->execute();

                $updateStmt->bind_param("i", $seatId);
                $updateStmt->execute();

                if ($updateStmt->affected_rows !== 1) {
                    throw new Exception(
                        "A selected seat is no longer available."
                    );
                }
            }

            $ticketStmt->close();
            $updateStmt->close();

            $db->commit();

            unset($_SESSION['cart']);

            $_SESSION['last_order_id'] = $orderId;

            header("Location: confirmation.php");
            exit();

        } catch (Throwable $e) {
            $db->rollback();
            error_log($e->getMessage());

            $error = $e instanceof mysqli_sql_exception
                ? "Unable to complete booking. Please try again."
                : $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | RaZor Theatres</title>
    <link rel="stylesheet" href="style.css">
    link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<header class="site-header">
    <a href="index.php" class="logo">
        <h1>RaZor Theatres</h1>
    </a>
    <nav>
        <a href="index.php">Home</a>
        <a href="movies.php">Movies</a>
        <a href="theatres.php">Theatres</a>
        <a href="promotions.php">Promotions</a>
        <a href="booking.php">My Bookings</a>
        <a href="cart.php"
            class="nav-cart active"
            aria-label="Cart">
            <i class="fa-solid fa-cart-shopping"></i>
        </a>
    </nav>
</header>

<main class="checkout-page">
    <a href="cart.php" class="checkout-back">&larr; Back to Cart</a>

    <h1>Checkout</h1>
    <p class="checkout-subtitle">
        Review your tickets and confirm your booking.
    </p>

    <?php if ($error !== "") { ?>
        <div class="checkout-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <div class="checkout-layout">
        <section class="checkout-panel">
            <h2>Customer Details</h2>

            <form method="POST" id="checkout-form">
                <label for="customer_name">Full Name</label>
                <input
                    type="text"
                    id="customer_name"
                    name="customer_name"
                    maxlength="100"
                    value="<?php echo htmlspecialchars($name); ?>"
                    required
                >

                <label for="customer_email">Email Address</label>
                <input
                    type="email"
                    id="customer_email"
                    name="customer_email"
                    maxlength="255"
                    value="<?php echo htmlspecialchars($email); ?>"
                    required
                >

                <label for="customer_phone">Phone Number</label>
                <input
                    type="tel"
                    id="customer_phone"
                    name="customer_phone"
                    maxlength="16"
                    placeholder="e.g. +6591234567"
                    value="<?php echo htmlspecialchars($phone); ?>"
                    required
                >

                <p class="checkout-note">
                    No online payment will be collected.
                </p>

                <button type="submit" class="checkout-button">
                    Confirm Booking
                </button>
            </form>
        </section>

        <aside class="checkout-panel">
            <h2>Order Summary</h2>

            <?php foreach ($cart as $item) { ?>
                <div class="checkout-movie">
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>

                    <p>
                        Cinema:
                        <?php echo htmlspecialchars($item['cinema']); ?>
                    </p>

                    <p>
                        Date:
                        <?php echo htmlspecialchars($item['date']); ?>
                    </p>

                    <p>
                        Time:
                        <?php echo htmlspecialchars(substr($item['time'], 0, 5)); ?>
                    </p>

                    <p>
                        Seats:
                        <?php echo htmlspecialchars(implode(', ', $item['seats'])); ?>
                    </p>
                </div>
            <?php } ?>

            <div class="checkout-total-row">
                <span>Tickets</span>
                <span><?php echo $totalTickets; ?></span>
            </div>

            <div class="checkout-total-row">
                <span>Price per ticket</span>
                <span>S$<?php echo number_format($ticketPrice, 2); ?></span>
            </div>

            <div class="checkout-total-row grand-total">
                <strong>Total</strong>
                <strong>S$<?php echo number_format($totalPrice, 2); ?></strong>
            </div>
        </aside>
    </div>
</main>
<script src="script.js"></script>
</body>
</html>
