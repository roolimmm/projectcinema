
// =========================
// SAMPLE MOVIE DATA
// =========================

const movies = [
    {
        id: 1,
        title: "The Last Horizon",
        runtime: 125,
        rating: "PG13",
        advisory: "Some Violence",
        poster: "images/movie1.jpg",
        status: "now-showing"
    },
    {
        id: 2,
        title: "Midnight Mystery",
        runtime: 110,
        rating: "M18",
        advisory: "Violence and Coarse Language",
        poster: "images/movie2.jpg",
        status: "now-showing"
    },
    {
        id: 3,
        title: "A Summer Story",
        runtime: 105,
        rating: "PG",
        advisory: "Some Mature Themes",
        poster: "images/movie3.jpg",
        status: "now-showing"
    },
    {
        id: 4,
        title: "Beyond the Stars",
        runtime: 135,
        rating: "PG13",
        advisory: "Some Violence",
        poster: "images/movie4.jpg",
        status: "now-showing"
    },
    {
        id: 5,
        title: "The Final Voyage",
        runtime: 130,
        rating: "PG13",
        advisory: "Some Violence",
        poster: "images/movie5.jpg",
        status: "coming-soon"
    },
    {
        id: 6,
        title: "Echoes of Tomorrow",
        runtime: 115,
        rating: "PG",
        advisory: "Some Frightening Scenes",
        poster: "images/movie6.jpg",
        status: "coming-soon"
    }
];


// =========================
// INITIALISE WEBSITE
// =========================

document.addEventListener("DOMContentLoaded", function () {

    const categoryTitle =
        document.getElementById("category-title");

    const categoryCounter =
        document.getElementById("category-counter");

    const categoryRange =
        document.getElementById("category-range");

    const carousel =
        document.getElementById("movie-carousel");

    const previousButton =
        document.querySelector(".carousel-prev");

    const nextButton =
        document.querySelector(".carousel-next");

    let currentCategory = "now-showing";
    let transitionTimer = null;


    // =========================
    // CREATE MOVIE CARDS
    // =========================

    function displayMovies(category) {

        const filteredMovies = movies.filter(function (movie) {
            return movie.status === category;
        });

        carousel.innerHTML = "";

        if (filteredMovies.length === 0) {

            const message = document.createElement("p");
            message.className = "empty-message";
            message.textContent =
                "No movies available at the moment.";

            carousel.appendChild(message);
            updateArrows();
            return;
        }

        filteredMovies.forEach(function (movie) {

            // Main card
            const card = document.createElement("div");
            card.className = "movie-card";

            // Poster
            const poster = document.createElement("img");
            poster.className = "movie-poster";
            poster.src = movie.poster;
            poster.alt = movie.title + " poster";
            poster.loading = "lazy";

            // Hover overlay
            const overlay = document.createElement("div");
            overlay.className = "movie-overlay";

            // Title
            const title = document.createElement("h3");
            title.textContent = movie.title;

            // Runtime
            const runtime = document.createElement("p");
            runtime.className = "movie-runtime";
            runtime.textContent = movie.runtime + " mins";

            // Rating
            const rating = document.createElement("span");
            rating.className = "movie-rating";
            rating.textContent = movie.rating;

            // Advisory
            const advisory = document.createElement("p");
            advisory.className = "movie-advisory";
            advisory.textContent = movie.advisory;

            // Booking button
            const button = document.createElement("a");
            button.className = "buy-btn";

            button.textContent =
                category === "now-showing"
                    ? "BUY TICKETS"
                    : "VIEW DETAILS";

            button.href =
                "movie-details.html?movieId=" +
                encodeURIComponent(movie.id);

            // Assemble overlay
            overlay.append(
                title,
                runtime,
                rating,
                advisory,
                button
            );

            // Assemble card
            card.append(poster, overlay);

            // Add to carousel
            carousel.appendChild(card);
        });

        // Reset carousel position
        carousel.scrollLeft = 0;

        updateArrows();
    }


    // =========================
    // SLIDER APPEARANCE
    // =========================

    function updateSliderAppearance() {

        const progress = Number(categoryRange.value);

        categoryRange.style.background = `
            linear-gradient(
                to right,
                #e50914 0%,
                #e50914 ${progress}%,
                #44444b ${progress}%,
                #44444b 100%
            )
        `;
    }


    // =========================
    // CATEGORY SWITCHING
    // =========================

    function changeCategory(category) {

        if (category === currentCategory) {
            return;
        }

        currentCategory = category;

        // Update heading
        categoryTitle.textContent =
            category === "now-showing"
                ? "Now Showing"
                : "Coming Soon";

        // Update counter
        categoryCounter.textContent =
            category === "now-showing"
                ? "01 / 02"
                : "02 / 02";

        categoryRange.setAttribute(
            "aria-valuetext",
            category === "now-showing"
                ? "Now Showing"
                : "Coming Soon"
        );

        // Fade out current movies
        carousel.classList.add("is-changing");

        clearTimeout(transitionTimer);

        const reducedMotion = window.matchMedia(
            "(prefers-reduced-motion: reduce)"
        ).matches;

        transitionTimer = setTimeout(function () {

            // Replace movies
            displayMovies(category);

            // Fade in new movies
            carousel.classList.remove("is-changing");

        }, reducedMotion ? 0 : 250);
    }


    // =========================
    // DRAG SLIDER
    // =========================

    categoryRange.addEventListener("input", function () {

        updateSliderAppearance();

        const category =
            Number(categoryRange.value) >= 50
                ? "coming-soon"
                : "now-showing";

        changeCategory(category);
    });

    // Snap slider to either end
    categoryRange.addEventListener("change", function () {

        categoryRange.value =
            Number(categoryRange.value) >= 50
                ? 100
                : 0;

        updateSliderAppearance();
    });


    // =========================
    // CLICK HEADING TO SWITCH
    // =========================

    categoryTitle.addEventListener("click", function () {

        const nextCategory =
            currentCategory === "now-showing"
                ? "coming-soon"
                : "now-showing";

        categoryRange.value =
            nextCategory === "now-showing"
                ? 0
                : 100;

        updateSliderAppearance();
        changeCategory(nextCategory);
    });


    // =========================
    // CAROUSEL ARROWS
    // =========================

    function getScrollDistance() {

        const card = carousel.querySelector(".movie-card");

        if (!card) return 200;

        const gap = parseFloat(
            window.getComputedStyle(carousel).columnGap
        ) || 0;

        return card.getBoundingClientRect().width + gap;
    }

    function updateArrows() {

        const maxScroll =
            carousel.scrollWidth - carousel.clientWidth;

        previousButton.disabled =
            carousel.scrollLeft <= 1;

        nextButton.disabled =
            carousel.scrollLeft >= maxScroll - 1;
    }

    previousButton.addEventListener("click", function () {

        carousel.scrollBy({
            left: -getScrollDistance(),
            behavior: "smooth"
        });
    });

    nextButton.addEventListener("click", function () {

        carousel.scrollBy({
            left: getScrollDistance(),
            behavior: "smooth"
        });
    });

    carousel.addEventListener("scroll", updateArrows);

    window.addEventListener("resize", updateArrows);


    // =========================
    // START WEBSITE
    // =========================

    updateSliderAppearance();
    displayMovies(currentCategory);

});
