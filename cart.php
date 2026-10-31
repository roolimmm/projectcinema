<?php
session_start();
require_once "db_connect.php";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$error = "";
$ticketPrice = 15.00;
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['add_to_cart'])) {
    $screeningId = filter_input(INPUT_POST, 'screening_id', FILTER_VALIDATE_INT);
    $selectedSeats = isset($_POST['selected_seats']) ? explode(',', $_POST['selected_seats']) : [];
    $selectedSeats = array_values(array_unique(array_map('trim', $selectedSeats)));
    if (!$screeningId ||
        empty($selectedSeats) ||
        in_array('', $selectedSeats, true)) {
        $error = "Please select at least one valid seat.";
    } else {
        $query = "
            SELECT
                s.screening_id,
                s.cinema,
                s.screening_date,
                s.screening_time,
                m.title
            FROM screenings s
            JOIN movies m
                ON s.movie_id = m.movie_id
            WHERE s.screening_id = ?
        ";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $screeningId);
        $stmt->execute();
        $screening = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$screening) {
            $error = "The selected screening could not be found.";
        } else {
            $seatQuery = "
                SELECT seat_label
                FROM screening_seats
                WHERE screening_id = ?
                  AND status = 'available'
            ";
            $seatStmt = $db->prepare($seatQuery);
            $seatStmt->bind_param("i", $screeningId);
            $seatStmt->execute();
            $seatResult = $seatStmt->get_result();
            $availableSeats = [];
            while ($seat = $seatResult->fetch_assoc()) {
                $availableSeats[] = $seat['seat_label'];
            }
            $seatStmt->close();
            foreach ($selectedSeats as $seat) {
                if (!in_array($seat, $availableSeats, true)) {
                    $error = "Seat " . $seat . " is unavailable or does not exist.";
                    break;
                }
            }
            if ($error === "") {
                $existingIndex = null;
                foreach ($_SESSION['cart'] as $index => $item) {
                    if ($item['screening_id'] == $screeningId) {
                        $existingIndex = $index;
                        break;
                    }
                }
                if ($existingIndex !== null) {
                    $_SESSION['cart'][$existingIndex]['seats'] =
                        array_values(
                            array_unique(
                                array_merge(
                                    $_SESSION['cart'][$existingIndex]['seats'],
                                    $selectedSeats
                                )
                            )
                        );
                } else {
                    $_SESSION['cart'][] = [
                        'screening_id' => $screeningId,
                        'title' => $screening['title'],
                        'cinema' => $screening['cinema'],
                        'date' => $screening['screening_date'],
                        'time' => $screening['screening_time'],
                        'seats' => $selectedSeats
                    ];
                }
                header("Location: cart.php");
                exit();
            }
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['remove_index'])
    && ctype_digit((string) $_POST['remove_index'])) {
    $index = (int) $_POST['remove_index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header("Location: cart.php");
    exit();
}
$cart = $_SESSION['cart'];
$totalTickets = 0;
foreach ($cart as $item) {
    $totalTickets += count($item['seats']);
}
$totalPrice = $totalTickets * $ticketPrice;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Cart | RaZor Theatres</title>
    <link rel="stylesheet" href="style.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >
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
            <a href="viewbookings.php">My Bookings</a>
            <a href="cart.php"
               class="nav-cart active"
               aria-label="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
        </nav>
    </header>

    <main class="cart-page">
        <div class="cart-page-header">
            <h2>Cart</h2>
            <p>
                Review your selected movie tickets
                before proceeding to checkout.
            </p>
        </div>

        <?php if ($error !== "") { ?>
            <div class="checkout-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>
        <?php if (!empty($cart)) { ?>
            <div class="cart-layout">

                <section class="cart-items">
                    <h3>Your Tickets</h3>
                    <?php foreach ($cart as $index => $item) { ?>
                        <article class="cart-item">
                            <div class="cart-item-details">
                                <h3>
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>
                                <p>
                                    <strong>Cinema:</strong>
                                    <?php echo htmlspecialchars($item['cinema']); ?>
                                </p>
                                <p>
                                    <strong>Date:</strong>
                                    <?php echo htmlspecialchars($item['date']); ?>
                                </p>
                                <p>
                                    <strong>Time:</strong>
                                    <?php echo htmlspecialchars(substr($item['time'], 0, 5)); ?>
                                </p>
                                <p>
                                    <strong>Seats:</strong>
                                    <?php echo htmlspecialchars(implode(', ', $item['seats'])); ?>
                                </p>
                                <p>
                                    <strong>Tickets:</strong>
                                    <?php echo count($item['seats']); ?>
                                </p>
                            </div>

                            <form method="POST"
                                  action="cart.php"
                                  class="cart-remove-form">
                                <input
                                    type="hidden"
                                    name="remove_index"
                                    value="<?php echo $index; ?>"
                                >
                                <button type="submit" class="cart-remove-button" title="Remove ticket" aria-label="Remove ticket">
                                    &times;
                                </button>
                            </form>
                        </article>
                    <?php } ?>
                </section>

                <aside class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="cart-summary-row">
                        <span>Total Tickets</span>
                        <span>
                            <?php echo $totalTickets; ?>
                        </span>
                    </div>
                    <div class="cart-summary-row">
                        <span>Price per Ticket</span>
                        <span>
                            S$<?php echo number_format($ticketPrice, 2); ?>
                        </span>
                    </div>
                    <div class="cart-summary-row cart-summary-total">
                        <strong>Total</strong>
                        <strong>
                            S$<?php echo number_format($totalPrice, 2); ?>
                        </strong>
                    </div>

                    <a href="checkout.php"
                       class="checkout-button cart-checkout-button">
                        Proceed to Checkout
                    </a>
                    <a href="movies.php"
                       class="cart-continue-link">
                        Continue Browsing Movies
                    </a>
                </aside>
            </div>
        <?php } else { ?>

            <div class="cart-empty">
                <i class="fa-solid fa-cart-shopping"></i>
                <h3>Your cart is empty</h3>
                <p>
                    You haven't selected any movie tickets yet.
                </p>
                <a href="movies.php"
                   class="checkout-button cart-browse-button">
                    Browse Movies
                </a>
            </div>
        <?php } ?>
    </main>

    <footer class="site-footer">
        <p>
            &copy; 2026 RaZor Theatres.
            All Rights Reserved.
        </p>
    </footer>
</body>
</html>
