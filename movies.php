
<?php
require_once "db_connect.php";

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

function displayMovieCard($movie, $showReleaseDate = false) {
    $movieId = (int) $movie['movie_id'];
    $movieUrl = "movie_details.php?movieId=" . $movieId;
?>
    <article class="movies-page-card">
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

        <div class="movies-page-card-info">
            <h3>
                <?php echo htmlspecialchars($movie['title']); ?>
            </h3>

            <div class="movies-page-meta-row">
                <img
                    src="images/ratings/<?php echo htmlspecialchars(strtolower($movie['rating'] ?? '')); ?>.webp"
                    alt="<?php echo htmlspecialchars($movie['rating'] ?? ''); ?> rating"
                    class="details-rating"
                >

                <span>
                    <?php echo htmlspecialchars($movie['genre'] ?? ''); ?>
                </span>

                <span>
                    <?php echo (int) $movie['duration']; ?> min
                </span>
            </div>

            <p class="movies-page-advisory">
                <strong>Advisory:</strong>
                <?php echo htmlspecialchars($movie['advisory'] ?: 'None'); ?>
            </p>

            <p class="movies-page-synopsis">
                <?php echo htmlspecialchars($movie['synopsis'] ?? ''); ?>
            </p>

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

            <a
                href="cart.php"
                class="nav-cart"
                aria-label="View shopping cart"
            >
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
        </nav>

    </header>

    <main class="movies-page">
        <div class="movies-page-header">
            <h2>Movies</h2>
            <p>
                Explore the latest movies and upcoming releases
                at RaZor Theatres.
            </p>
        </div>

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

    <footer class="site-footer">
        <p>
            &copy; 2026 RaZor Theatres.
            All Rights Reserved.
        </p>
    </footer>

    <script src="script.js?v=3"></script>
</body>
</html>
