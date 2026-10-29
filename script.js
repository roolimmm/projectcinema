
document.addEventListener("DOMContentLoaded", function () {

    // ==================================
    // MOVIE TABS
    // ==================================

    const nowShowingTab = document.getElementById("now-showing-tab");
    const comingSoonTab = document.getElementById("coming-soon-tab");

    const nowShowingContent = document.getElementById("now-showing-content");
    const comingSoonContent = document.getElementById("coming-soon-content");

    if (nowShowingTab && comingSoonTab && nowShowingContent && comingSoonContent) {
        nowShowingTab.addEventListener("click", function () {
            nowShowingContent.hidden = false;
            comingSoonContent.hidden = true;

            nowShowingTab.classList.add("active");
            comingSoonTab.classList.remove("active");
        });

        comingSoonTab.addEventListener("click", function () {
            nowShowingContent.hidden = true;
            comingSoonContent.hidden = false;

            comingSoonTab.classList.add("active");
            nowShowingTab.classList.remove("active");
        });
    }


    // ==================================
    // MOVIE CAROUSELS
    // ==================================

    const carousels = document.querySelectorAll(".carousel-container");

    carousels.forEach(function (container) {
        const carousel = container.querySelector(".movie-carousel");
        const prevButton = container.querySelector(".carousel-prev");
        const nextButton = container.querySelector(".carousel-next");

        if (carousel && prevButton && nextButton) {
            prevButton.addEventListener("click", function () {
                carousel.scrollBy({
                    left: -225,
                    behavior: "smooth"
                });
            });

            nextButton.addEventListener("click", function () {
                carousel.scrollBy({
                    left: 225,
                    behavior: "smooth"
                });
            });
        }
    });


    // ==================================
    // MOVIE SCREENING DATE PICKER
    // ==================================

    const datePicker = document.getElementById("screening-date");

    if (datePicker) {
        const screeningDates = document.querySelectorAll(".screening-date");
        const noScreenings = document.getElementById("no-screenings");

        function updateScreenings() {
            const selectedDate = datePicker.value;
            let hasScreenings = false;

            screeningDates.forEach(function (section) {
                if (section.dataset.date === selectedDate) {
                    section.hidden = false;
                    hasScreenings = true;
                } else {
                    section.hidden = true;
                }
            });

            if (noScreenings) {
                noScreenings.hidden = hasScreenings;
            }
        }

        datePicker.addEventListener("change", updateScreenings);
        updateScreenings();
    }


    // ==================================
    // THEATRE SHOWTIMES
    // ==================================

    const theatreButtons = document.querySelectorAll(".theatre-showtimes-btn");
    const theatrePanel = document.getElementById("theatre-showtimes-panel");
    const theatreDatePicker = document.getElementById("theatre-screening-date");
    const theatreGroups = document.querySelectorAll(".theatre-screening-group");
    const theatreNoScreenings = document.getElementById("theatre-no-screenings");
    const selectedTheatreName = document.getElementById("selected-theatre-name");
    const closeTheatreButton = document.getElementById("close-theatre-showtimes");

    if (theatrePanel && theatreDatePicker) {
        let selectedCinema = "";

        function updateTheatreScreenings() {
            const selectedDate = theatreDatePicker.value;
            let hasScreenings = false;

            theatreGroups.forEach(function (group) {
                const matchesCinema = group.dataset.cinema === selectedCinema;
                const matchesDate = group.dataset.date === selectedDate;

                if (matchesCinema && matchesDate) {
                    group.hidden = false;
                    hasScreenings = true;
                } else {
                    group.hidden = true;
                }
            });

            if (theatreNoScreenings) {
                theatreNoScreenings.hidden = hasScreenings;
            }
        }

        theatreButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                const cinema = button.dataset.cinema;

                if (!theatrePanel.hidden && selectedCinema === cinema) {
                    theatrePanel.hidden = true;
                    selectedCinema = "";
                    return;
                }

                selectedCinema = cinema;

                const theatreName = button.closest(".theatre-card")
                    .querySelector("h4").textContent;

                selectedTheatreName.textContent = theatreName + " - Showtimes";

                const availableDates = Array.from(theatreGroups)
                    .filter(function (group) {
                        return group.dataset.cinema === selectedCinema;
                    })
                    .map(function (group) {
                        return group.dataset.date;
                    })
                    .sort();

                theatreDatePicker.value = availableDates.length > 0
                    ? availableDates[0]
                    : theatreDatePicker.min;

                theatrePanel.hidden = false;
                updateTheatreScreenings();

                theatrePanel.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            });
        });

        theatreDatePicker.addEventListener("change", updateTheatreScreenings);

        if (closeTheatreButton) {
            closeTheatreButton.addEventListener("click", function () {
                theatrePanel.hidden = true;
                selectedCinema = "";
            });
        }
    }


    // ==================================
    // SEAT SELECTION AND ADD TO CART
    // ==================================

    const seatButtons = document.querySelectorAll(".seat-button:not(.occupied)");
    const selectedSeatsList = document.getElementById("selected-seats-list");
    const selectedSeatCount = document.getElementById("selected-seat-count");
    const seatCartEmpty = document.getElementById("seat-cart-empty");
    const addSeatsButton = document.getElementById("add-seats-to-cart");

    // New: Form elements for PHP cart submission
    const selectedSeatsInput = document.getElementById("selected-seats-input");
    const seatBookingForm = document.getElementById("seat-booking-form");

    if (seatButtons.length > 0 && selectedSeatsList &&
        selectedSeatCount && addSeatsButton &&
        selectedSeatsInput && seatBookingForm) {

        const selectedSeats = new Set();

        function updateSeatCart() {

            selectedSeatsList.replaceChildren();

            Array.from(selectedSeats).sort().forEach(function (seat) {

                const item = document.createElement("li");
                const removeButton = document.createElement("button");

                removeButton.type = "button";
                removeButton.textContent = "Remove " + seat;
                removeButton.className = "remove-seat-button";

                removeButton.addEventListener("click", function () {
                    selectedSeats.delete(seat);

                    const seatButton = document.querySelector(
                        '[data-seat="' + seat + '"]'
                    );

                    if (seatButton) {
                        seatButton.classList.remove("selected");
                        seatButton.setAttribute("aria-pressed", "false");
                    }

                    updateSeatCart();
                });

                item.append(
                    document.createTextNode("Seat " + seat + " "),
                    removeButton
                );

                selectedSeatsList.appendChild(item);
            });

            const count = selectedSeats.size;

            selectedSeatCount.textContent = count;

            if (seatCartEmpty) {
                seatCartEmpty.hidden = count > 0;
            }

            addSeatsButton.disabled = count === 0;

            // New: Update the hidden input for PHP
            selectedSeatsInput.value = Array.from(selectedSeats)
                .sort()
                .join(",");
        }

        seatButtons.forEach(function (button) {

            button.setAttribute("aria-pressed", "false");

            button.addEventListener("click", function () {

                const seat = button.dataset.seat;

                if (selectedSeats.has(seat)) {
                    selectedSeats.delete(seat);
                    button.classList.remove("selected");
                    button.setAttribute("aria-pressed", "false");
                } else {
                    selectedSeats.add(seat);
                    button.classList.add("selected");
                    button.setAttribute("aria-pressed", "true");
                }

                updateSeatCart();
            });
        });

        // Prevent submitting an empty seat selection
        seatBookingForm.addEventListener("submit", function (event) {
            if (selectedSeats.size === 0) {
                event.preventDefault();
            }
        });

        updateSeatCart();
    }
});
