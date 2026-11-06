
<?php
require_once "db_connect.php";

// retrieve movies from database //
$nowShowingQuery = "SELECT * FROM movies
                    WHERE status = 'now-showing'
                    ORDER BY release_date ASC";
$nowShowingResult = $db->query($nowShowingQuery);

$comingSoonQuery = "SELECT * FROM movies
                    WHERE status = 'coming-soon'
                    ORDER BY release_date ASC";
$comingSoonResult = $db->query($comingSoonQuery);

if (!$nowShowingResult || !$comingSoonResult) {
    exit("Unable to retrieve movies.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RaZor Theatres</title>

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
    <script src="script.js" defer></script>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
        defer
    ></script>
</head>

<body>
    <header class="site-header">
        <!-- Bootstrap's navbar collapses on smaller screens and opens with its bundled JavaScript. -->
        <nav class="navbar navbar-expand-lg navbar-dark w-100">
            <div class="container">
                <a href="index.php" class="navbar-brand logo">
                    <h1>RaZor Theatres</h1>
                </a>
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
        <section class="hero-banner">
            <div class="hero-content">
                <p class="hero-label">
                    RAZOR THEATRES PRESENTS
                </p>

                <h2>The Big Screen Experience</h2>

                <p>
                    Discover stories worth watching.
                </p>
            </div>
        </section>

        <section id="movies" class="movie-section">
            <!-- movie tabs for now showing and coming soon -->
            <div class="movie-tabs">
                <button
                    id="now-showing-tab"
                    class="movie-tab active"
                    type="button">
                    Now Showing
                </button>

                <button
                    id="coming-soon-tab"
                    class="movie-tab"
                    type="button">
                    Coming Soon
                </button>
            </div>

            <!-- movie carousel for now showing movies -->
            <div id="now-showing-content" class="movie-category">
                <div class="carousel-container">
                    <button class="carousel-prev" type="button">
                        &#10094;
                    </button>

                    <div class="movie-carousel">
                        <?php while ($movie = $nowShowingResult->fetch_assoc()) { ?>
                            <div class="movie-card">
                                <img
                                    src="<?php echo htmlspecialchars($movie['poster_url']); ?>"
                                    alt="<?php echo htmlspecialchars($movie['title']); ?> poster"
                                    class="movie-poster"
                                >

                                <div class="movie-overlay">
                                    <h3>
                                        <?php echo htmlspecialchars($movie['title']); ?>
                                    </h3>

                                    <p class="movie-runtime">
                                        <?php echo (int)$movie['duration']; ?> mins
                                    </p>

                                    <img
                                        src="images/ratings/<?php echo htmlspecialchars($movie['rating']); ?>.webp"
                                        alt="<?php echo htmlspecialchars($movie['rating']); ?> rating"
                                        class="movie-rating-img"
                                    >

                                    <p class="movie-advisory">
                                        <?php echo htmlspecialchars($movie['advisory'] ?? ''); ?>
                                    </p>

                                    <a href="movie_details.php?movieId=<?php echo (int)$movie['movie_id']; ?>"
                                       class="buy-btn">
                                        VIEW DETAILS
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <button class="carousel-next" type="button">
                        &#10095;
                    </button>
                </div>
            </div>

            <!-- movie carousel for coming soon movies -->
            <div id="coming-soon-content" class="movie-category" hidden>
                <div class="carousel-container">
                    <button class="carousel-prev" type="button">
                        &#10094;
                    </button>

                    <div class="movie-carousel">
                        <?php while ($movie = $comingSoonResult->fetch_assoc()) { ?>
                            <div class="movie-card">
                                <img
                                    src="<?php echo htmlspecialchars($movie['poster_url']); ?>"
                                    alt="<?php echo htmlspecialchars($movie['title']); ?> poster"
                                    class="movie-poster"
                                >

                                <div class="movie-overlay">
                                    <h3>
                                        <?php echo htmlspecialchars($movie['title']); ?>
                                    </h3>

                                    <p class="movie-runtime">
                                        <?php echo (int)$movie['duration']; ?> mins
                                    </p>

                                    <p class="movie-release">
                                        In Cinemas
                                        <?php echo date(
                                            "d M Y",
                                            strtotime($movie['release_date'])
                                        ); ?>
                                    </p>

                                    <img
                                        src="images/ratings/<?php echo htmlspecialchars($movie['rating']); ?>.webp"
                                        alt="<?php echo htmlspecialchars($movie['rating']); ?> rating"
                                        class="movie-rating-img"
                                    >

                                    <p class="movie-advisory">
                                        <?php echo htmlspecialchars($movie['advisory'] ?? ''); ?>
                                    </p>

                                    <a href="movie_details.php?movieId=<?php echo (int)$movie['movie_id']; ?>"
                                       class="buy-btn">
                                        VIEW DETAILS
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <button class="carousel-next" type="button">
                        &#10095;
                    </button>
                </div>
            </div>

            <!-- link to explore all movies -->
            <div class="explore-movies-container">
                <a href="movies.php" class="hero-btn">
                    Explore All Movies
                </a>
            </div>
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
