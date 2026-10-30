
<?php
require_once "db_connect.php";

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

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="script.js" defer></script>
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
        <section class="hero-banner">
            <div class="hero-content">
                <p class="hero-label">
                    RAZOR THEATRES PRESENTS
                </p>

                <h2>The Big Screen Experience</h2>

                <p>
                    Discover stories worth watching.
                </p>

                <a href="#movies" class="hero-btn">
                    Explore Movies
                </a>
            </div>
        </section>

        <section id="movies" class="movie-section">
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

            <div id="now-showing-content" class="movie-category">
                <div class="carousel-container">
                    <button class="carousel-prev" type="button">
                        &#10094;
                    </button>

                    <div class="movie-carousel">
                        <?php while ($movie = $nowShowingResult->fetch_assoc()) {?>
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

                                    <a href="movie_details.php?movieId=<?php echo (int)$movie['movie_id']; ?>" class="buy-btn">
                                       BUY TICKETS
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
