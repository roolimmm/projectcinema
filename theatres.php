
<?php
require_once "db_connect.php";

// load upcoming screenings
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
    // render schedule
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

            <div class="theatre-grid row g-4">
                <article class="theatre-card col-12 col-md-6 col-lg-4" id="theatre-orchard">
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

                    <button class="btn theatre-showtimes-btn" data-cinema="Orchard">
                        View Showtimes
                    </button>
                </article>

                <article class="theatre-card col-12 col-md-6 col-lg-4" id="theatre-harbour">
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

                    <button class="btn theatre-showtimes-btn" data-cinema="Harbour">
                        View Showtimes
                    </button>
                </article>

                <article class="theatre-card col-12 col-md-6 col-lg-4" id="theatre-north">
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

                    <button class="btn theatre-showtimes-btn" data-cinema="Yishun">
                        View Showtimes
                    </button>
                </article>
            </div>

            <!-- JavaScript reveals table and filters by theatre and date. -->
            <section class="theatre-showtimes-panel" id="theatre-showtimes-panel" hidden>
                <div class="theatre-showtimes-header">
                    <h3 id="selected-theatre-name">Showtimes</h3>
                    <button id="close-theatre-showtimes" type="button" aria-label="Close showtimes">
                        &times;
                    </button>
                </div>

                <div class="screening-calendar" data-screening-calendar="theatre">
                    <span id="theatre-screening-date-label" class="screening-calendar-label">Select Date:</span>
                    <input type="date" id="theatre-screening-date" min="<?php echo date('Y-m-d'); ?>">
                </div>

                <?php foreach ($screenings as $cinema => $dates) { ?>
                    <?php foreach ($dates as $date => $movies) { ?>
                        <div class="theatre-screening-group"
                             data-cinema="<?php echo htmlspecialchars($cinema); ?>"
                             data-date="<?php echo htmlspecialchars($date); ?>"
                             hidden>
                            <div class="theatre-table-wrapper">
                                <table class="table theatre-screenings-table">
                                    <caption>
                                        <?php echo htmlspecialchars($cinema); ?> screenings on
                                        <?php echo date("j M Y", strtotime($date)); ?>
                                    </caption>
                                    <thead>
                                        <tr>
                                            <th scope="col">Movie</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($movies as $movie => $times) { ?>
                                            <?php foreach ($times as $screening) { ?>
                                                <tr>
                                                    <th scope="row"><?php echo htmlspecialchars($movie); ?></th>
                                                    <td><?php echo date("j M Y", strtotime($screening['screening_date'])); ?></td>
                                                    <td>
                                                        <a href="booking.php?screeningId=<?php echo (int)$screening['screening_id']; ?>"
                                                        class="showtime-btn">
                                                        <?php echo date("g:i A", strtotime($screening['screening_time'])); ?>
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
            <div class="theatre-contact-grid row g-4">
                <div class="theatre-contact-item col-12 col-md-6 col-lg-4">
                    <h5>Operation Hours</h5>
                    <p><strong>Mon - Fri:</strong> 12:00pm - 11:30pm</p>
                    <p><strong>Sat &amp; Sun:</strong> 10:30am - 11:30pm</p>
                    <p><strong>Public Holidays:</strong> 10:30am - 11:30pm</p>
                </div>
                <div class="theatre-contact-item col-12 col-md-6 col-lg-4">
                    <h5>Call Us</h5>
                    <p><a href="tel:+6561234567">+65 6123 4567</a></p>
                    <p>Monday - Sunday</p>
                    <p>10:30am - 11:30pm</p>
                </div>
                <div class="theatre-contact-item col-12 col-md-6 col-lg-4">
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
</body>
</html>
