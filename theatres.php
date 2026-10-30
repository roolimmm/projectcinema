
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
    <link rel="stylesheet" href="style.css?v=4">
    <link rel="stylesheet"
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
            <a href="bookings.php">My Bookings</a>
            <a href="cart.php" class="nav-cart">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
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
                <article class="theatre-card" id="theatre-orchard">
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

                <article class="theatre-card" id="theatre-harbour">
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

                <article class="theatre-card" id="theatre-north">
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

                    <button class="theatre-showtimes-btn" data-cinema="Yishun">
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
                            <div class="theatre-table-wrapper">
                                <table class="theatre-screenings-table">
                                    <caption>
                                        <?php echo htmlspecialchars($cinema); ?> screenings on
                                        <?php echo date("j M Y", strtotime($date)); ?>
                                    </caption>
                                    <thead>
                                        <tr>
                                            <th scope="col">Movie</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Time</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($movies as $movie => $times) { ?>
                                            <?php foreach ($times as $screening) { ?>
                                                <tr>
                                                    <th scope="row"><?php echo htmlspecialchars($movie); ?></th>
                                                    <td><?php echo date("j M Y", strtotime($screening['screening_date'])); ?></td>
                                                    <td><?php echo date("g:i A", strtotime($screening['screening_time'])); ?></td>
                                                    <td>
                                                        <a href="booking.php?screeningId=<?php echo (int)$screening['screening_id']; ?>"
                                                           class="showtime-btn">
                                                            Select seats
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

                <p id="theatre-no-screenings" hidden>
                    No screenings available for this theatre on the selected date.
                </p>
            </section>
        </section>

        <section class="theatre-contact" aria-labelledby="theatre-contact-heading">
            <h4 id="theatre-contact-heading">Visit &amp; Contact</h4>
            <div class="theatre-contact-grid">
                <div class="theatre-contact-item">
                    <h5>Operation Hours</h5>
                    <p><strong>Mon - Fri:</strong> 12:00pm - 11:30pm</p>
                    <p><strong>Sat &amp; Sun:</strong> 10:30am - 11:30pm</p>
                    <p><strong>Public Holidays:</strong> 10:30am - 11:30pm</p>
                </div>
                <div class="theatre-contact-item">
                    <h5>Call Us</h5>
                    <p><a href="tel:+6561234567">+65 6123 4567</a></p>
                    <p>Monday - Sunday</p>
                    <p>10:30am - 11:30pm</p>
                </div>
                <div class="theatre-contact-item">
                    <h5>Email Us</h5>
                    <p>hello@razortheatres.com</p>
                    <p>We aim to reply within two working days.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 RaZor Theatres. All Rights Reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
