
<?php
require_once "db_connect.php";

// ==================================
// RETRIEVE MOVIES FROM DATABASE
// ==================================

$query = "
    SELECT
        movie_id,
        title,
        synopsis,
        genre,
        duration,
        rating,
        advisory,
        poster_url,
        release_date,
        status
    FROM movies
    ORDER BY release_date DESC, title ASC
";

$result = $db->query($query);

$nowShowing = [];
$comingSoon = [];

while ($movie = $result->fetch_assoc()) {

    if ($movie['status'] === 'now-showing') {
        $nowShowing[] = $movie;
    } elseif ($movie['status'] === 'coming-soon') {
        $comingSoon[] = $movie;
    }
}

// ==================================
// FUNCTION TO DISPLAY MOVIE CARDS
// ==================================

function displayMovieCard($movie, $showReleaseDate = false) {
    $movieId = (int) $movie['movie_id'];
    $movieUrl = "movie_details.php?movieId=" . $movieId;
?>

    <article class="movies-page-card">

        <!-- MOVIE POSTER -->

        <a
            href="<?php echo $movieUrl; ?>"
            class="movies-page-poster-link"
        >
            <img
                src="<?php echo htmlspecialchars($movie['poster_url'] ?? ''); ?>"
                alt="<?php echo htmlspecialchars($movie['title']); ?> poster"
                class="movies-page-poster"
                loading="lazy"
            >
        </a>


        <!-- MOVIE INFORMATION -->

        <div class="movies-page-card-info">

            <h3>
                <?php echo htmlspecialchars($movie['title']); ?>
            </h3>


            <!-- RATING, GENRE AND DURATION -->

            <div class="movies-page-meta-row">

                <span class="movies-page-rating">
                    <?php echo htmlspecialchars($movie['rating'] ?? 'N/A'); ?>
                </span>

                <span>
                    <?php echo htmlspecialchars($movie['genre'] ?? ''); ?>
                </span>

                <span>
                    <?php echo (int) $movie['duration']; ?> min
                </span>

            </div>


            <!-- ADVISORY -->

            <p class="movies-page-advisory">
                <strong>Advisory:</strong>
                <?php echo htmlspecialchars($movie['advisory'] ?: 'None'); ?>
            </p>


            <!-- MOVIE DESCRIPTION -->

            <p class="movies-page-synopsis">
                <?php echo htmlspecialchars($movie['synopsis'] ?? ''); ?>
            </p>


            <!-- RELEASE DATE FOR COMING SOON -->

            <?php if ($showReleaseDate && !empty($movie['release_date'])) { ?>

                <p class="movies-page-release">
                    <strong>Release Date:</strong>
                    <?php
                        echo date(
                            "j M Y",
                            strtotime($movie['release_date'])
                        );
                    ?>
                </p>

            <?php } ?>


            <!-- VIEW DETAILS -->

            <a
                href="<?php echo $movieUrl; ?>"
                class="movies-page-details-button"
            >
                View Details
            </a>

        </div>

    </article>

<?php
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Movies | RaZor Theatres</title>

    <link rel="stylesheet" href="style.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >
</head>

<body>

    <!-- ==================================
         HEADER
         ================================== -->

    <header class="site-header">

        <a href="index.php" class="logo">
            <h1>RaZor Theatres</h1>
        </a>

        <nav>
            <a href="index.php">Home</a>
            <a href="movies.php" class="active">Movies</a>
            <a href="theatres.php">Theatres</a>
            <a href="promotions.php">Promotions</a>
            <a href="viewbookings.php">My Bookings</a>

            <a
                href="cart.php"
                class="nav-cart"
                aria-label="View shopping cart"
            >
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
        </nav>

    </header>


    <!-- ==================================
         MOVIES PAGE
         ================================== -->

    <main class="movies-page">

        <!-- PAGE HEADING -->

        <div class="movies-page-header">

            <h2>Movies</h2>

            <p>
                Explore the latest movies and upcoming releases
                at RaZor Theatres.
            </p>

        </div>


        <!-- ==================================
             MOVIE TABS
             ================================== -->

        <div class="movies-page-tabs">

            <button
                type="button"
                id="now-showing-tab"
                class="movies-page-tab active"
            >
                Now Showing
            </button>

            <button
                type="button"
                id="coming-soon-tab"
                class="movies-page-tab"
            >
                Coming Soon
            </button>

        </div>


        <!-- ==================================
             NOW SHOWING
             ================================== -->

        <section
            id="now-showing-content"
            class="movies-page-section"
        >

            <?php if (!empty($nowShowing)) { ?>

                <div class="movies-page-grid">

                    <?php foreach ($nowShowing as $movie) { ?>

                        <?php displayMovieCard($movie); ?>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <p class="movies-page-empty">
                    No movies are currently showing.
                </p>

            <?php } ?>

        </section>


        <!-- ==================================
             COMING SOON
             ================================== -->

        <section
            id="coming-soon-content"
            class="movies-page-section"
            hidden
        >

            <?php if (!empty($comingSoon)) { ?>

                <div class="movies-page-grid">

                    <?php foreach ($comingSoon as $movie) { ?>

                        <?php displayMovieCard($movie, true); ?>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <p class="movies-page-empty">
                    No upcoming movies at the moment.
                </p>

            <?php } ?>

        </section>

    </main>


    <!-- ==================================
         FOOTER
         ================================== -->

    <footer class="site-footer">

        <p>
            &copy; 2026 RaZor Theatres.
            All Rights Reserved.
        </p>

    </footer>


    <!-- EXISTING JAVASCRIPT -->

    <script src="script.js?v=3"></script>

</body>
</html>
