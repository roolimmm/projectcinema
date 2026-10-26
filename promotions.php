<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions | RaZor Theatres</title>
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
            <a href="promotions.php">Promotions</a>
            <a href="bookings.html">My Bookings</a>
        </nav>
    </header>

    <main>
        <section class="page-banner">
            <div class="page-banner-content">
                <p class="hero-label">A LITTLE MORE TO LOVE</p>
                <h2>Promotions</h2>
                <p>
                    Great films, better value. Take a look at the latest ways
                    to make your next cinema visit even sweeter.
                </p>
            </div>
        </section>

        <section class="content-section" aria-labelledby="promotion-list-heading">
            <h3 id="promotion-list-heading">Happening Now</h3>
            <p class="content-intro">
                Treat yourself to more movie magic with these limited-time offers.
            </p>

            <div class="promotion-list">
                <article class="promotion-card">
                    <div class="promotion-card-image">
                        <img src="images/phm.jpg" alt="Matinee movie deal placeholder image">
                    </div>
                    <div class="promotion-card-content">
                        <p class="promotion-value">Weekdays, your way</p>
                        <h4>Matinee Movie Deal</h4>
                        <p>
                            Catch a show before 5 pm, Monday to Thursday,
                            and enjoy special value on selected sessions.
                        </p>
                        <p class="promotion-terms">
                            Valid on eligible weekday sessions. Excludes public
                            holidays and special screenings.
                        </p>
                        <a href="index.php#movies" class="hero-btn">
                            Find a Show
                        </a>
                    </div>
                </article>

                <article class="promotion-card">
                    <div class="promotion-card-image">
                        <img src="images/theinvite.jpg" alt="Movie night for two placeholder image">
                    </div>
                    <div class="promotion-card-content">
                        <p class="promotion-value">More is more</p>
                        <h4>Movie Night for Two</h4>
                        <p>
                            Bring your favourite person along. Save when you book
                            two standard tickets together for selected films.
                        </p>
                        <p class="promotion-terms">
                            Available for selected films and sessions while seats
                            last. Offer applied at checkout.
                        </p>
                        <a href="index.php#movies" class="hero-btn">
                            Pick a Movie
                        </a>
                    </div>
                </article>

                <article class="promotion-card">
                    <div class="promotion-card-image">
                        <img src="images/hope.jpg" alt="Student movie night placeholder image">
                    </div>
                    <div class="promotion-card-content">
                        <p class="promotion-value">Student nights</p>
                        <h4>Your Midweek Movie Break</h4>
                        <p>
                            Flash your valid student ID at the box office on
                            Wednesday evenings for a little extra off your ticket.
                        </p>
                        <p class="promotion-terms">
                            Valid Wednesdays after 6 pm. One discounted ticket
                            per valid student ID.
                        </p>
                        <a href="index.php#movies" class="hero-btn">
                            See What's On
                        </a>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 RaZor Theatres. All Rights Reserved.</p>
    </footer>
</body>
</html>
