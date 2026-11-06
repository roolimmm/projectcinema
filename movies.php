
<?php
require_once "db_connect.php";

// load the movie fields for filters
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
$genres = [];
$ratings = [];

while ($movie = $result->fetch_assoc()) {

    // separate movies into the two tabs, collect filter options
    if ($movie['status'] === 'now-showing') {
        $nowShowing[] = $movie;
    } elseif ($movie['status'] === 'coming-soon') {
        $comingSoon[] = $movie;
    }

    if (in_array($movie['status'], ['now-showing', 'coming-soon'], true)) {
        if (!empty($movie['genre'])) {
            $genres[] = $movie['genre'];
        }
        if (!empty($movie['rating'])) {
            $ratings[] = $movie['rating'];
        }
    }
}

sort($genres, SORT_NATURAL | SORT_FLAG_CASE);
$genres = array_unique($genres);
sort($ratings, SORT_NATURAL | SORT_FLAG_CASE);
$ratings = array_unique($ratings);

// function to display a movie card
function displayMovieCard($movie, $showReleaseDate = false) {
    $movieId = (int) $movie['movie_id'];
    $movieUrl = "movie_details.php?movieId=" . $movieId;
?>
    <article class="movies-page-card" 
        data-genre="<?php echo htmlspecialchars($movie['genre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
        data-rating="<?php echo htmlspecialchars($movie['rating'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
    >
        <a href="<?php echo $movieUrl; ?>" class="movies-page-poster-link">
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

            <a href="<?php echo $movieUrl; ?>" class="movies-page-details-button">
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
    <!-- Bootstrap supplies the responsive CSS foundation; the local stylesheet below keeps the RaZor theme. -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="style.css?v=10">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >
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

        <!-- filter section for genre and rating -->
        <div class="movies-page-filters row g-3" aria-label="Filter movies">
            <div class="movies-page-filter col-12 col-md-6 col-lg-3">
                <label class="form-label" for="movie-genre-filter">Genre</label>
                <select id="movie-genre-filter" class="form-select">
                    <option value="">All genres</option>
                    <?php foreach ($genres as $genre) { ?>
                        <option value="<?php echo htmlspecialchars($genre, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($genre); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="movies-page-filter col-12 col-md-6 col-lg-3">
                <label class="form-label" for="movie-rating-filter">Rating</label>
                <select id="movie-rating-filter" class="form-select">
                    <option value="">All ratings</option>
                    <?php foreach ($ratings as $rating) { ?>
                        <option value="<?php echo htmlspecialchars($rating, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($rating); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="button" id="apply-movie-filters" class="btn movies-page-apply-filters col-12 col-md-6 col-lg-3">
                Apply filters
            </button>
            <button type="button" id="clear-movie-filters" class="btn movies-page-clear-filters col-12 col-md-6 col-lg-3">
                Clear filters
            </button>
            <p id="movie-filter-status" class="movies-page-filter-status col-12" aria-live="polite"></p>
        </div>

        <!-- now showing movie sections -->
        <section id="now-showing-content" class="movies-page-section">
            <div class="movies-page-grid">
                <?php foreach ($nowShowing as $movie) { ?>
                    <?php displayMovieCard($movie); ?>
                <?php } ?>
            </div>
            <p class="movies-page-empty" id="now-showing-empty" <?php echo !empty($nowShowing) ? 'hidden' : ''; ?>>
                No movies are currently showing.
            </p>
        </section>

        <!-- coming soon movie sections -->
        <section id="coming-soon-content" class="movies-page-section" hidden>
            <div class="movies-page-grid">
                <?php foreach ($comingSoon as $movie) { ?>
                    <?php displayMovieCard($movie, true); ?>
                <?php } ?>
            </div>
            <p class="movies-page-empty" id="coming-soon-empty" <?php echo !empty($comingSoon) ? 'hidden' : ''; ?>>
                No upcoming movies at the moment.
            </p>
        </section>
    </main>

    <footer class="site-footer">
        <p>
            &copy; 2026 RaZor Theatres.
            All Rights Reserved.
        </p>
    </footer>
</body>
</html>
