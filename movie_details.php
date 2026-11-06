
<?php
require_once "db_connect.php";

// reject invalid movie IDs before loading
if (!isset($_GET['movieId']) || !ctype_digit($_GET['movieId'])) {
    header("Location: index.php");
    exit();
}

$movieId = (int) $_GET['movieId'];

// load movies
$query = "SELECT * FROM movies WHERE movie_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $movieId);
$stmt->execute();

$result = $stmt->get_result();
$movie = $result->fetch_assoc();

if (!$movie) {
    header("Location: index.php");
    exit();
}

$stmt->close();

$releaseDate = "";
if (!empty($movie['release_date'])) {
    $releaseDate = date("d M Y", strtotime($movie['release_date']));
}

// group showtimes for the date picker
$screeningQuery = "
    SELECT *
    FROM screenings
    WHERE movie_id = ?
      AND TIMESTAMP(screening_date, screening_time) >= NOW()
    ORDER BY screening_date, cinema, screening_time
";

$screeningStmt = $db->prepare($screeningQuery);
$screeningStmt->bind_param("i", $movieId);
$screeningStmt->execute();

$screeningResult = $screeningStmt->get_result();
$screenings = [];

while ($screening = $screeningResult->fetch_assoc()) {
    $date = $screening['screening_date'];
    $cinema = $screening['cinema'];

    $screenings[$date][$cinema][] = $screening;
}

$screeningStmt->close();

$selectedDate = !empty($screenings)
    ? array_key_first($screenings)
    : date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> | RaZor Theatres</title>
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
    <script src="script.js?v=5" defer></script>
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

    <main>
        <section class="movie-details-section">
            <a href="movies.php" class="back-link">
                &larr; Back to Movies List
            </a>

            <div class="movie-details-container row g-5">
                <div class="details-poster col-12 col-md-4 col-lg-3">
                    <img
                        src="<?php echo htmlspecialchars($movie['poster_url']); ?>"
                        alt="<?php echo htmlspecialchars($movie['title']); ?> poster"
                    >
                </div>

                <div class="details-info col-12 col-md-8 col-lg-9">
                    <h2><?php echo htmlspecialchars($movie['title']); ?></h2>

                    <div class="details-meta">
                        <img
                            src="images/ratings/<?php echo htmlspecialchars(strtolower($movie['rating'] ?? '')); ?>.webp"
                            alt="<?php echo htmlspecialchars($movie['rating'] ?? ''); ?> rating"
                            class="details-rating"
                        >
                        <span><?php echo htmlspecialchars($movie['genre'] ?? ''); ?></span>
                        <span><?php echo (int)$movie['duration']; ?> mins</span>
                    </div>

                    <p class="details-advisory">
                        <strong>Advisory:</strong>
                        <?php echo htmlspecialchars($movie['advisory'] ?? 'N/A'); ?>
                    </p>

                    <p class="details-release">
                        <strong>Release Date:</strong>
                        <?php echo htmlspecialchars($releaseDate); ?>
                    </p>

                    <div class="details-synopsis">
                        <h3>Synopsis</h3>
                        <p><?php echo nl2br(htmlspecialchars($movie['synopsis'] ?? '')); ?></p>
                    </div>
                    
                    <!-- screening options for now showing movies -->
                    <?php if ($movie['status'] === 'now-showing') { ?>
                        <div class="showtimes-section">
                            <div class="showtimes-header">
                                <h3>Showtimes</h3>

                                <div class="screening-calendar" data-screening-calendar="movie">
                                    <input
                                        type="date"
                                        id="screening-date"
                                        min="<?php echo date('Y-m-d'); ?>"
                                        value="<?php echo htmlspecialchars($selectedDate); ?>"
                                        aria-label="Select screening date"
                                    >
                                </div>
                            </div>

                            <?php if (empty($screenings)) { ?>
                                <p class="no-screenings-message">
                                    No upcoming screenings available.
                                </p>
                            <?php } else { ?>
                                <?php foreach ($screenings as $date => $cinemas) { ?>
                                    <div class="screening-date"
                                         data-date="<?php echo htmlspecialchars($date); ?>"
                                         hidden>

                                        <?php foreach ($cinemas as $cinema => $times) { ?>
                                            <div class="cinema-showtimes">
                                                <h4><?php echo htmlspecialchars($cinema); ?></h4>

                                                <div class="showtime-buttons">
                                                    <?php foreach ($times as $screening) { ?>
                                                        <a href="booking.php?screeningId=<?php echo (int)$screening['screening_id']; ?>" class="showtime-btn">
                                                            <?php echo date("g:i A", strtotime($screening['screening_time'])); ?>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    </div>
                                <?php } ?>

                                <p id="no-screenings" hidden>
                                    No screenings available for this date.
                                </p>
                            <?php } ?>
                        </div>
                    <!-- for coming soon movies -->
                    <?php } else { ?>
                        <p class="coming-soon-label">
                            In Cinemas <?php echo htmlspecialchars($releaseDate); ?>
                        </p>
                    <?php } ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 RaZor Theatres. All Rights Reserved.</p>
    </footer>
</body>
</html>
