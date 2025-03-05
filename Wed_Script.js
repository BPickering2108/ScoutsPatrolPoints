document.addEventListener("DOMContentLoaded", function() {
    let maxPoints = localStorage.getItem("wedMaxPoints") || 100; // Load max points from local storage or default to 100
    const progressContainers = document.querySelectorAll(".progress-container");

    // Update colors dynamically
    const colorSettings = {
        Kestrel: localStorage.getItem("wedColorKestrel"),
        Curlew: localStorage.getItem("wedColorCurlew"),
        Eagle: localStorage.getItem("wedColorEagle"),
        Woodpecker: localStorage.getItem("wedColorWoodpecker")
    };

    progressContainers.forEach(container => {
        let points = localStorage.getItem(container.dataset.label) || container.getAttribute("data-points");
        points = parseInt(points); // Ensure points is a number
        const progressBar = container.querySelector(".progress-bar");
        const label = container.querySelector(".label");

        const heightPercentage = (points / maxPoints) * 100;
        progressBar.style.height = heightPercentage + "%";
        label.textContent = points;

        // Apply colors
        const patrolLabel = container.dataset.label;
        if (colorSettings[patrolLabel]) {
            progressBar.style.backgroundColor = colorSettings[patrolLabel];
        }

        container.addEventListener("click", function() {
            const adjustButtons = container.parentElement.querySelector(".adjust-buttons");
            if (adjustButtons.style.display === "flex") {
                adjustButtons.style.display = "none";
            } else {
                hideAllAdjustButtons();
                adjustButtons.style.display = "flex";
            }
        });

        createAdjustButtons(container);
    });

    const resetButton = document.getElementById("reset-button");

    resetButton.addEventListener("click", function() {
        if (sessionStorage.getItem("authenticated") === "true") {
            progressContainers.forEach(container => {
                const progressBar = container.querySelector(".progress-bar");
                const label = container.querySelector(".label");

                progressBar.style.height = "0%";
                label.textContent = "0";
                localStorage.setItem(container.dataset.label, 0);
            });
        } else {
            sessionStorage.setItem("redirectTo", window.location.pathname);
            window.location.href = "login.html";
        }
    });

    function createAdjustButtons(container) {
        const adjustButtons = container.parentElement.querySelector(".adjust-buttons");

        const plusButton = adjustButtons.querySelector(".plus-button");
        plusButton.addEventListener("click", function(event) {
            event.stopPropagation();
            adjustPoints(container, 1);
        });

        const minusButton = adjustButtons.querySelector(".minus-button");
        minusButton.addEventListener("click", function(event) {
            event.stopPropagation();
            adjustPoints(container, -1);
        });
    }

    function adjustPoints(container, amount) {
        if (sessionStorage.getItem("authenticated") === "true") {
            const progressBar = container.querySelector(".progress-bar");
            const label = container.querySelector(".label");
            let points = parseInt(localStorage.getItem(container.dataset.label)) || parseInt(container.getAttribute("data-points"));

            points = Math.min(maxPoints, Math.max(0, points + amount));
            container.setAttribute("data-points", points);
            localStorage.setItem(container.dataset.label, points);
            const heightPercentage = (points / maxPoints) * 100;
            progressBar.style.height = heightPercentage + "%";
            label.textContent = points;
        } else {
            sessionStorage.setItem("redirectTo", window.location.pathname);
            window.location.href = "login.html";
        }
    }

    function hideAllAdjustButtons() {
        const allAdjustButtons = document.querySelectorAll(".adjust-buttons");
        allAdjustButtons.forEach(buttons => {
            buttons.style.display = "none";
        });
    }

    // Check if bars need to be reset
    if (localStorage.getItem("wedResetBars") === "true") {
        progressContainers.forEach(container => {
            const progressBar = container.querySelector(".progress-bar");
            const label = container.querySelector(".label");

            progressBar.style.height = "0%";
            label.textContent = "0";
            localStorage.setItem(container.dataset.label, 0);
        });
        localStorage.removeItem("wedResetBars");
    }
});