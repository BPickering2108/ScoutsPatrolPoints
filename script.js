document.addEventListener("DOMContentLoaded", function() {
    const progressContainers = document.querySelectorAll(".progress-container");

    progressContainers.forEach(container => {
        const points = container.getAttribute("data-points");
        const progressBar = container.querySelector(".progress-bar");
        const label = container.querySelector(".label");

        progressBar.style.height = points + "%";
        label.textContent = points + "%";
    });

    const resetButton = document.getElementById("reset-button");

    resetButton.addEventListener("click", function() {
        if (sessionStorage.getItem("authenticated") === "true") {
            progressContainers.forEach(container => {
                const progressBar = container.querySelector(".progress-bar");
                const label = container.querySelector(".label");

                progressBar.style.height = "0%";
                label.textContent = "0%";
            });
        } else {
            sessionStorage.setItem("redirectTo", window.location.pathname);
            window.location.href = "login.html";
        }
    });
});