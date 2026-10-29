<?php
require_once "db_connect.php";

if (!isset($_GET['screeningId']) || !ctype_digit($_GET['screeningId'])) {
    header("Location: index.php");
    exit();
}

$screeningId = (int) $_GET['screeningId'];

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

if (!$screening) {
    header("Location: index.php");
    exit();
}

$rows = range('A', 'H');
$seatCount = 10;
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <header class="site-header">
        <a href="index.php" class="logo"><h1>RaZor Theatres</h1></a>
        <nav>
            <a href="index.php">Home</a>
            <a href="index.php#movies">Movies</a>
            <a href="theatres.php">Theatres</a>
            <a href="promotions.php">Promotions</a>
            <a href="bookings.html">My Bookings</a>
            <a href="cart.php" class="nav-cart">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
        </nav>
    </header>

    <main class="seat-selection-page">
        <div class="seat-selection-header">
            <a href="movie_details.php?movieId=<?php echo (int) $screening['movie_id']; ?>" class="back-link">
                &larr; Back to Showtimes
            </a>
            <h2>Select Seats</h2>
            <p>
                <strong><?php echo htmlspecialchars($screening['title']); ?></strong>
                <?php echo htmlspecialchars($screening['cinema']); ?>
                &middot; <?php echo date("j M Y", strtotime($screening['screening_date'])); ?>
                &middot; <?php echo date("g:i A", strtotime($screening['screening_time'])); ?>
            </p>
        </div>

        <div class="seat-layout-container">
            <section class="cinema-seat-plan" aria-labelledby="seat-plan-heading">
                <h3 id="seat-plan-heading" class="visually-hidden">Cinema seating plan</h3>
                <div class="screen-label">Screen</div>
                <div class="screen-line" aria-hidden="true"></div>

                <div class="seat-grid">
                    <?php foreach ($rows as $row) { ?>
                        <div class="seat-row">
                            <span class="seat-row-label" aria-hidden="true"><?php echo $row; ?></span>
                            <?php for ($seat = 1; $seat <= $seatCount; $seat++) {
                                $seatNumber = $row . $seat;
                                $occupied = ($seatStatuses[$seatNumber] ?? 'available') !== 'available';
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

            <aside class="seat-cart" aria-labelledby="seat-cart-heading">
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

    <script src="script.js?v=3"></script>
</body>
</html>
