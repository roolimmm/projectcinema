
<?php
require_once "db_connect.php";

$query = "
    SELECT
        s.screening_id,
        s.screening_date,
        s.screening_time,
        s.cinema,
        m.title
    FROM screenings s
    JOIN movies m ON s.movie_id = m.movie_id
    WHERE TIMESTAMP(s.screening_date, s.screening_time) >= NOW()
    ORDER BY s.cinema, s.screening_date, m.title, s.screening_time
";

$result = $db->query($query);

$screenings = [];

while ($row = $result->fetch_assoc()) {
    $cinema = $row['cinema'];
    $date = $row['screening_date'];
    $movie = $row['title'];

    $screenings[$cinema][$date][$movie][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theatres | RaZor Theatres</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="site-header">
        <a href="index.php" class="logo">
            <h1>RaZor Theatres</h1>
        </a>

        <nav>
            <a href="index.php">Home</a>
            <a href="index.php#movies">Movies</a>
            <a href="theatres.php">Theatres</a>
            <a href="promotions.html">Promotions</a>
            <a href="bookings.html">My Bookings</a>
        </nav>
    </header>

    <main>
        <section class="page-banner">
            <div class="page-banner-content">
                <p class="hero-label">FIND YOUR RAZOR THEATRE</p>
                <h2>Our Theatres</h2>
                <p>
                    Wherever you are, there is a seat waiting for you.
                    Find your nearest cinema and make it a movie night.
                </p>
            </div>
        </section>

        <section class="content-section" aria-labelledby="theatre-list-heading">
            <h3 id="theatre-list-heading">Find your cinema</h3>
            <p class="content-intro">
                Explore our city locations, each designed to make your
                time at the movies feel special.
            </p>

            <div class="theatre-grid">
                <article class="theatre-card">
                    <p class="card-kicker">Central Singapore</p>
                    <h4>RaZor Theatres Orchard</h4>
                    <p class="theatre-location">Orchard Road</p>
                    <p>
                        Step out of the city buzz and into a comfortable,
                        big-screen escape right in the heart of town.
                    </p>

                    <ul class="amenity-list">
                        <li>Recliner seats</li>
                        <li>Premium screens</li>
                        <li>Snack bar</li>
                    </ul>

                    <button class="theatre-showtimes-btn" data-cinema="Orchard">
                        View Showtimes
                    </button>
                </article>

                <article class="theatre-card">
                    <p class="card-kicker">Waterfront</p>
                    <h4>RaZor Theatres Harbour</h4>
                    <p class="theatre-location">Harbourfront</p>
                    <p>
                        Make a day of it with the latest releases,
                        inviting lounges, and a little extra room to unwind.
                    </p>

                    <ul class="amenity-list">
                        <li>IMAX experience</li>
                        <li>Accessible seating</li>
                        <li>Snack bar</li>
                    </ul>

                    <button class="theatre-showtimes-btn" data-cinema="Harbour">
                        View Showtimes
                    </button>
                </article>

                <article class="theatre-card">
                    <p class="card-kicker">North Singapore</p>
                    <h4>RaZor Theatres North</h4>
                    <p class="theatre-location">Yishun</p>
                    <p>
                        Your neighbourhood spot for family outings,
                        date nights, and everything worth watching.
                    </p>

                    <ul class="amenity-list">
                        <li>Family-friendly</li>
                        <li>Accessible seating</li>
                        <li>Snack bar</li>
                    </ul>

                    <button class="theatre-showtimes-btn" data-cinema="North">
                        View Showtimes
                    </button>
                </article>
            </div>

            <section class="theatre-showtimes-panel" id="theatre-showtimes-panel" hidden>
                <div class="theatre-showtimes-header">
                    <h3 id="selected-theatre-name">Showtimes</h3>
                    <button id="close-theatre-showtimes" type="button" aria-label="Close showtimes">
                        &times;
                    </button>
                </div>

                <div class="screening-calendar">
                    <label for="theatre-screening-date">Select Date:</label>
                    <input type="date" id="theatre-screening-date" min="<?php echo date('Y-m-d'); ?>">
                </div>

                <?php foreach ($screenings as $cinema => $dates) { ?>
                    <?php foreach ($dates as $date => $movies) { ?>
                        <div class="theatre-screening-group"
                             data-cinema="<?php echo htmlspecialchars($cinema); ?>"
                             data-date="<?php echo htmlspecialchars($date); ?>"
                             hidden>

                            <?php foreach ($movies as $movie => $times) { ?>
                                <div class="theatre-movie-showtimes">
                                    <h4><?php echo htmlspecialchars($movie); ?></h4>

                                    <div class="showtime-buttons">
                                        <?php foreach ($times as $screening) { ?>
                                            <a href="booking.php?screeningId=<?php echo (int)$screening['screening_id']; ?>"
                                               class="showtime-btn">
                                                <?php echo date("g:i A", strtotime($screening['screening_time'])); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>

                <p id="theatre-no-screenings" hidden>
                    No screenings available for this theatre on the selected date.
                </p>
            </section>
        </section>
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 RaZor Theatres. All Rights Reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
