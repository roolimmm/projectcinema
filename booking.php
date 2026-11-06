
<?php
require_once "db_connect.php";

// only a numeric screening ID allowed, invalid requests return to home page
if (!isset($_GET['screeningId']) || !ctype_digit($_GET['screeningId'])) {
    header("Location: index.php");
    exit();
}

$screeningId = (int) $_GET['screeningId'];

// Fetch the selected screening + movie details shown on this page
$query = "
    SELECT s.*, m.title, m.poster_url, m.rating
    FROM screenings s
    JOIN movies m ON m.movie_id = s.movie_id
    WHERE s.screening_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $screeningId);
$stmt->execute();
$result = $stmt->get_result();
$screening = $result->fetch_assoc();
$stmt->close();

// check if ID actually links to an actual screening, if not, return to home page
if (!$screening) {
    header("Location: index.php");
    exit();
}

$rows = range('A', 'H');
$seatCount = 10;
// load seat availability to mark booked or missing seats as unavailable
$seatQuery = "SELECT seat_label, status FROM screening_seats WHERE screening_id = ?";
$seatStmt = $db->prepare($seatQuery);
$seatStmt->bind_param("i", $screeningId);
$seatStmt->execute();
$seatResult = $seatStmt->get_result();
$seatStatuses = [];

while ($seat = $seatResult->fetch_assoc()) {
    $seatStatuses[$seat['seat_label']] = $seat['status'];
}

$seatStmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats | RaZor Theatres</title>
    <!-- Bootstrap supplies the responsive CSS foundation; the local stylesheet below keeps the RaZor theme. -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="style.css?v=10">
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="script.js?v=3"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
        defer
    ></script>
</head>

<body>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark w-100">
            <div class="container">
                <a href="index.php" class="navbar-brand logo"><h1>RaZor Theatres</h1></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="primaryNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="movies.php">Movies</a></li>
                        <li class="nav-item"><a class="nav-link" href="theatres.php">Theatres</a></li>
                        <li class="nav-item"><a class="nav-link" href="viewbookings.php">My Bookings</a></li>
                        <li class="nav-item"><a class="nav-link nav-cart" href="cart.php" aria-label="Shopping cart">
                            <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                        </a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="seat-selection-page">
        <!-- shows current booking step, and identify the chosen screening. -->
        <div class="seat-selection-header">
            <nav class="booking-progress" aria-label="Booking progress">
                <a href="movie_details.php?movieId=<?php echo (int) $screening['movie_id']; ?>">
                    Showtimes
                </a>
                <span class="current" aria-current="step">Seat Booking</span>
                <span>Cart</span>
                <span>Checkout</span>
            </nav>
            <h2>Select Seats</h2>
            <p>
                <strong><?php echo htmlspecialchars($screening['title']); ?></strong>
                <?php echo htmlspecialchars($screening['cinema']); ?>
                &middot; <?php echo date("j M Y", strtotime($screening['screening_date'])); ?>
                &middot; <?php echo date("g:i A", strtotime($screening['screening_time'])); ?>
            </p>
        </div>

        <div class="seat-layout-container row g-4">
            <!-- render seating plan and disable any seat not marked available -->
            <section class="cinema-seat-plan col-12 col-lg-8" aria-labelledby="seat-plan-heading">
                <h3 id="seat-plan-heading" class="visually-hidden">Cinema seating plan</h3>
                <div class="screen-label">Screen</div>
                <div class="screen-line" aria-hidden="true"></div>

                <div class="seat-grid">
                    <?php foreach ($rows as $row) { ?>
                        <div class="seat-row">
                            <span class="seat-row-label" aria-hidden="true"><?php echo $row; ?></span>
                            <?php for ($seat = 1; $seat <= $seatCount; $seat++) {
                                $seatNumber = $row . $seat;
                                $occupied = ($seatStatuses[$seatNumber] ?? 'unavailable') !== 'available';
                            ?>
                                <button
                                    type="button"
                                    class="seat-button<?php echo $occupied ? ' occupied' : ''; ?>"
                                    data-seat="<?php echo $seatNumber; ?>"
                                    <?php echo $occupied ? 'disabled aria-label="Seat ' . $seatNumber . ' unavailable"' : 'aria-label="Seat ' . $seatNumber . '"'; ?>>
                                    <?php echo $seat; ?>
                                </button>
                            <?php } ?>
                            <span class="seat-row-label" aria-hidden="true"><?php echo $row; ?></span>
                        </div>
                    <?php } ?>
                </div>

                <div class="seat-legend" aria-label="Seat status legend">
                    <span><i class="legend-seat available"></i> Available</span>
                    <span><i class="legend-seat selected"></i> Selected</span>
                    <span><i class="legend-seat occupied"></i> Unavailable</span>
                </div>
            </section>

            <!-- summary updates using javascript and submitted to cart handler. -->
            <aside class="seat-cart col-12 col-lg-4" aria-labelledby="seat-cart-heading">
                <h3 id="seat-cart-heading">Your Seats</h3>
                <p id="seat-cart-empty">No seats selected.</p>
                <ul id="selected-seats-list"></ul>
                <div class="seat-cart-total">
                    <span>Tickets</span>
                    <strong id="selected-seat-count">0</strong>
                </div>
                <form action="cart.php" method="POST" id="seat-booking-form">
                    <input
                       type="hidden"
                       name="screening_id"
                       value="<?php echo $screeningId; ?>"
                    >

                    <input
                       type="hidden"
                       name="selected_seats"
                       id="selected-seats-input"
                    >

                    <button
                       type="submit"
                       name="add_to_cart"
                       id="add-seats-to-cart"
                       class="seat-cart-button"
                       disabled
                    >
                    Add to Cart
                    </button>
                </form>
            </aside>
        </div>
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 RaZor Theatres. All Rights Reserved.</p>
    </footer>
</body>
</html>
