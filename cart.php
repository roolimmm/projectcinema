
<?php
session_start();
require_once "db_connect.php";

// Initialise the shopping cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$error = "";
$ticketPrice = 15.00; // Temporary ticket price

// =====================================
// ADD SELECTED SEATS TO CART
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['add_to_cart'])) {

    $screeningId = filter_input(
        INPUT_POST,
        'screening_id',
        FILTER_VALIDATE_INT
    );

    $selectedSeats = isset($_POST['selected_seats'])
        ? explode(',', $_POST['selected_seats'])
        : [];

    $selectedSeats = array_values(array_unique(
        array_map('trim', $selectedSeats)
    ));

    if (!$screeningId || empty($selectedSeats)
        || in_array('', $selectedSeats, true)) {

        $error = "Please select at least one valid seat.";

    } else {

        // Retrieve screening and movie information
        $query = "
            SELECT
                s.screening_id,
                s.cinema,
                s.screening_date,
                s.screening_time,
                m.title
            FROM screenings s
            JOIN movies m ON s.movie_id = m.movie_id
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

            // Retrieve available seats for this screening
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

            // Validate selected seats
            foreach ($selectedSeats as $seat) {
                if (!in_array($seat, $availableSeats, true)) {
                    $error = "Seat " . $seat
                        . " is unavailable or does not exist.";
                    break;
                }
            }

            if ($error === "") {

                // Check if the screening already exists in the cart
                $existingIndex = null;

                foreach ($_SESSION['cart'] as $index => $item) {
                    if ($item['screening_id'] == $screeningId) {
                        $existingIndex = $index;
                        break;
                    }
                }

                if ($existingIndex !== null) {

                    // Add seats to the existing screening
                    $_SESSION['cart'][$existingIndex]['seats'] =
                        array_values(array_unique(array_merge(
                            $_SESSION['cart'][$existingIndex]['seats'],
                            $selectedSeats
                        )));

                } else {

                    // Add a new screening to the cart
                    $_SESSION['cart'][] = [
                        'screening_id' => $screeningId,
                        'title' => $screening['title'],
                        'cinema' => $screening['cinema'],
                        'date' => $screening['screening_date'],
                        'time' => $screening['screening_time'],
                        'seats' => $selectedSeats
                    ];
                }

                // Redirect to prevent duplicate form submissions
                header("Location: cart.php");
                exit();
            }
        }
    }
}


// =====================================
// REMOVE SCREENING FROM CART
// =====================================

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


// =====================================
// CALCULATE CART TOTALS
// =====================================

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Your Cart | RaZor Theatres</title>

    <link rel="stylesheet" href="style.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >
</head>

<body>

    <!-- HEADER -->

    <header class="site-header">

        <a href="index.php" class="logo">
            <h1>RaZor Theatres</h1>
        </a>

        <nav>
            <a href="index.php">Home</a>
            <a href="index.php#movies">Movies</a>
            <a href="theatres.php">Theatres</a>
            <a href="promotions.php">Promotions</a>
            <a href="bookings.php">My Bookings</a>

            <a
                href="cart.php"
                class="nav-cart"
                aria-label="View shopping cart"
            >
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
        </nav>

    </header>


    <!-- CART CONTENT -->

    <main class="cart-page">

        <div class="cart-header">
            <h2>Your Cart</h2>
            <p>
                Review your selected movie screenings before checkout.
            </p>
        </div>

        <!-- Error message -->

        <?php if ($error !== "") { ?>

            <p class="cart-error">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php } ?>


        <?php if (empty($cart)) { ?>

            <!-- EMPTY CART -->

            <div class="cart-empty">

                <h3>Your cart is empty</h3>

                <p>
                    You haven't selected any movie tickets yet.
                </p>

                <a
                    href="index.php#movies"
                    class="cart-action-button"
                >
                    Browse Movies
                </a>

            </div>

        <?php } else { ?>

            <div class="cart-layout">

                <!-- CART ITEMS -->

                <section class="cart-items">

                    <?php foreach ($cart as $index => $item) { ?>

                        <div class="cart-item">

                            <div class="cart-item-details">

                                <h3>
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>

                                <p>
                                    <strong>Theatre:</strong>
                                    <?php echo htmlspecialchars($item['cinema']); ?>
                                </p>

                                <p>
                                    <strong>Date:</strong>
                                    <?php
                                    echo date(
                                        "j M Y",
                                        strtotime($item['date'])
                                    );
                                    ?>
                                </p>

                                <p>
                                    <strong>Time:</strong>
                                    <?php
                                    echo date(
                                        "g:i A",
                                        strtotime($item['time'])
                                    );
                                    ?>
                                </p>

                                <p>
                                    <strong>Seats:</strong>
                                    <?php
                                    echo htmlspecialchars(
                                        implode(', ', $item['seats'])
                                    );
                                    ?>
                                </p>

                                <p>
                                    <strong>Tickets:</strong>
                                    <?php echo count($item['seats']); ?>
                                </p>

                            </div>

                            <div class="cart-item-actions">

                                <strong class="cart-item-price">
                                    S$<?php
                                    echo number_format(
                                        count($item['seats']) * $ticketPrice,
                                        2
                                    );
                                    ?>
                                </strong>

                                <!-- Remove screening -->

                                <form action="cart.php" method="POST">

                                    <input
                                        type="hidden"
                                        name="remove_index"
                                        value="<?php echo $index; ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="cart-remove-button"
                                    >
                                        Remove
                                    </button>

                                </form>

                            </div>

                        </div>

                    <?php } ?>

                </section>


                <!-- ORDER SUMMARY -->

                <aside class="cart-summary">

                    <h3>Order Summary</h3>

                    <div class="cart-summary-row">
                        <span>Total Tickets</span>
                        <strong>
                            <?php echo $totalTickets; ?>
                        </strong>
                    </div>

                    <div class="cart-summary-row">
                        <span>Price per Ticket</span>
                        <span>
                            S$<?php echo number_format($ticketPrice, 2); ?>
                        </span>
                    </div>

                    <div class="cart-summary-row cart-grand-total">
                        <span>Total</span>
                        <strong>
                            S$<?php echo number_format($totalPrice, 2); ?>
                        </strong>
                    </div>

                    <button
                        type="button"
                        class="cart-checkout-button"
                        disabled
                    >
                        Checkout (Coming Soon)
                    </button>

                    <a
                        href="index.php#movies"
                        class="cart-continue-link"
                    >
                        Continue Browsing
                    </a>

                </aside>

            </div>

        <?php } ?>

    </main>


    <!-- FOOTER -->

    <footer class="site-footer">
        <p>
            &copy; 2026 RaZor Theatres. All Rights Reserved.
        </p>
    </footer>

</body>
</html>
