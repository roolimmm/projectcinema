
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

    function createScreeningCalendar(datePicker, getAvailableDates) {
        const calendar = document.createElement("div");
        calendar.className = "screening-week-picker";
        calendar.setAttribute("role", "group");

        const label = datePicker.labels && datePicker.labels.length > 0
            ? datePicker.labels[0]
            : datePicker.parentElement.querySelector(".screening-calendar-label");

        if (label) {
            if (!label.id) {
                label.id = datePicker.id + "-label";
            }
            calendar.setAttribute("aria-labelledby", label.id);
        } else {
            calendar.setAttribute(
                "aria-label",
                datePicker.getAttribute("aria-label") || "Choose a screening date"
            );
        }

        const monthControls = document.createElement("div");
        monthControls.className = "screening-week-controls";

        const previousButton = document.createElement("button");
        previousButton.type = "button";
        previousButton.className = "screening-week-button";
        previousButton.setAttribute("aria-label", "Previous week with screenings");
        previousButton.textContent = "\u2039";

        const weekLabel = document.createElement("span");
        weekLabel.className = "screening-week-label";
        weekLabel.setAttribute("aria-live", "polite");

        const nextButton = document.createElement("button");
        nextButton.type = "button";
        nextButton.className = "screening-week-button";
        nextButton.setAttribute("aria-label", "Next week with screenings");
        nextButton.textContent = "\u203a";

        monthControls.append(previousButton, weekLabel, nextButton);

        const days = document.createElement("div");
        days.className = "screening-week-dates";

        const hint = document.createElement("p");
        hint.className = "screening-week-hint";
        hint.textContent = "Choose a date with screenings.";

        calendar.append(monthControls, days, hint);
        datePicker.insertAdjacentElement("afterend", calendar);

        let visibleWeek;

        function parseDate(value) {
            const parts = value.split("-").map(Number);
            return new Date(parts[0], parts[1] - 1, parts[2]);
        }

        function dateString(date) {
            return [
                date.getFullYear(),
                String(date.getMonth() + 1).padStart(2, "0"),
                String(date.getDate()).padStart(2, "0")
            ].join("-");
        }

        function getAvailableWeeks() {
            const weeks = new Map();

            Array.from(new Set(getAvailableDates()))
                .filter(Boolean)
                .sort()
                .forEach(function (value) {
                    const date = parseDate(value);
                    const firstDay = new Date(
                        date.getFullYear(),
                        date.getMonth(),
                        date.getDate() - date.getDay()
                    );
                    const weekKey = dateString(firstDay);

                    if (!weeks.has(weekKey)) {
                        weeks.set(weekKey, []);
                    }

                    weeks.get(weekKey).push(value);
                });

            return Array.from(weeks.entries()).map(function (week) {
                return {
                    start: week[0],
                    dates: week[1]
                };
            });
        }

        function render(resetWeek) {
            const selectedValue = datePicker.value || datePicker.min;
            const weeks = getAvailableWeeks();
            days.replaceChildren();

            if (weeks.length === 0) {
                visibleWeek = "";
                weekLabel.textContent = "No upcoming screenings";
                previousButton.disabled = true;
                nextButton.disabled = true;
                hint.hidden = true;
                return;
            }

            if (resetWeek || !visibleWeek) {
                const selectedDate = weeks.find(function (week) {
                    return week.dates.includes(selectedValue);
                });
                visibleWeek = selectedDate
                    ? selectedDate.start
                    : weeks[0].start;
            }

            let visibleWeekIndex = weeks.findIndex(function (week) {
                return week.start === visibleWeek;
            });

            if (visibleWeekIndex < 0) {
                visibleWeekIndex = 0;
                visibleWeek = weeks[0].start;
            }

            const currentWeek = weeks[visibleWeekIndex];
            const weekStart = parseDate(currentWeek.start);
            const weekEnd = new Date(
                weekStart.getFullYear(),
                weekStart.getMonth(),
                weekStart.getDate() + 6
            );
            const startLabel = new Intl.DateTimeFormat(undefined, {
                month: "short",
                day: "numeric"
            }).format(weekStart);
            const endLabel = new Intl.DateTimeFormat(undefined, {
                month: "short",
                day: "numeric",
                year: "numeric"
            }).format(weekEnd);

            weekLabel.textContent = startLabel + " \u2013 " + endLabel;
            previousButton.disabled = visibleWeekIndex === 0;
            nextButton.disabled = visibleWeekIndex === weeks.length - 1;
            hint.hidden = false;

            currentWeek.dates.forEach(function (value) {
                const date = parseDate(value);
                const button = document.createElement("button");
                button.type = "button";
                button.className = "screening-week-date";
                button.classList.add("has-screenings");
                button.setAttribute(
                    "aria-label",
                    new Intl.DateTimeFormat(undefined, {
                        weekday: "long",
                        month: "long",
                        day: "numeric",
                        year: "numeric"
                    }).format(date)
                );
                button.title = "View screenings";

                if (value === selectedValue) {
                    button.setAttribute("aria-pressed", "true");
                } else {
                    button.setAttribute("aria-pressed", "false");
                }

                const weekday = document.createElement("span");
                weekday.className = "screening-week-date-day";
                weekday.textContent = new Intl.DateTimeFormat(undefined, {
                    weekday: "short"
                }).format(date);

                const dayNumber = document.createElement("strong");
                dayNumber.className = "screening-week-date-number";
                dayNumber.textContent = String(date.getDate());

                button.append(weekday, dayNumber);
                button.addEventListener("click", function () {
                    datePicker.value = value;
                    datePicker.dispatchEvent(new Event("change", { bubbles: true }));
                });
                days.appendChild(button);
            });
        }

        function moveWeek(direction) {
            const weeks = getAvailableWeeks();
            const currentIndex = weeks.findIndex(function (week) {
                return week.start === visibleWeek;
            });
            const nextWeek = weeks[currentIndex + direction];

            if (!nextWeek) {
                return;
            }

            visibleWeek = nextWeek.start;
            datePicker.value = nextWeek.dates[0];
            datePicker.dispatchEvent(new Event("change", { bubbles: true }));
        }

        previousButton.addEventListener("click", function () {
            moveWeek(-1);
        });

        nextButton.addEventListener("click", function () {
            moveWeek(1);
        });

        datePicker.classList.add("screening-calendar-input-enhanced");
        datePicker.tabIndex = -1;
        datePicker.setAttribute("aria-hidden", "true");

        return {
            render: render
        };
    }

    const datePicker = document.getElementById("screening-date");

    if (datePicker) {
        const screeningDates = document.querySelectorAll(".screening-date");
        const noScreenings = document.getElementById("no-screenings");
        const movieCalendar = createScreeningCalendar(
            datePicker,
            function () {
                return Array.from(screeningDates).map(function (section) {
                    return section.dataset.date;
                });
            }
        );

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

            movieCalendar.render(false);
        }

        datePicker.addEventListener("change", updateScreenings);
        movieCalendar.render(true);
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
        const theatreCalendar = createScreeningCalendar(
            theatreDatePicker,
            function () {
                return Array.from(theatreGroups)
                    .filter(function (group) {
                        return group.dataset.cinema === selectedCinema;
                    })
                    .map(function (group) {
                        return group.dataset.date;
                    });
            }
        );

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

            theatreCalendar.render(false);
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
                theatreCalendar.render(true);

                theatrePanel.hidden = false;
                updateTheatreScreenings();

                theatrePanel.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            });
        });

        theatreDatePicker.addEventListener("change", updateTheatreScreenings);
        theatreCalendar.render(true);

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

    const checkoutForm = document.getElementById("checkout-form");
    
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", function (event) {
            const email = document.getElementById("customer_email").value.trim();
            const phone = document.getElementById("customer_phone").value.trim();

            const emailRegex = /^[\w.-]+@(?:\w+\.){1,3}\w{2,3}$/;
            const phoneRegex = /^\+[1-9]\d{1,14}$/;
            
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                event.preventDefault();
                return;
            }

            if (!phoneRegex.test(phone)) {
                alert("Please enter a valid phone number with country code (e.g. +6591234567).");
                event.preventDefault();
                return;
            }
        });
    }       
}
);
