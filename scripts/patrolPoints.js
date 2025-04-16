// Toggle visibility of patrol buttons
function showButtons(patrol) {
    const buttonsDiv = document.getElementById(`${patrol}-buttons`);

    document.querySelectorAll('.buttons').forEach(btn => {
        if (btn !== buttonsDiv) {
            btn.style.display = 'none';
        }
    });

    buttonsDiv.style.display = (buttonsDiv.style.display === 'block') ? 'none' : 'block';
}

// Update patrol points via AJAX
function updatePoints(patrol, action, count = 1, page) {
    // Fetch CSRF token from a hidden input field (Better practice than embedding PHP in JS)
    const csrfToken = document.getElementById('csrf_token').value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/backend/updatePoints.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    const pointsElement = document.getElementById(`${patrol}-fill`);
    let currentPoints = parseInt(pointsElement.innerText) || 0;

    currentPoints = action === 'increment' ? currentPoints + count : currentPoints - count;

    pointsElement.innerText = currentPoints;
    pointsElement.style.height = `${currentPoints}px`;

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                console.log(`${count} points updated successfully!`);
            } else if (xhr.status === 401) {
                alert("Session expired. Redirecting to login...");
                window.location.href = "../login.php";
            } else {
                alert("An error occurred. Please try again.");
            }
        }
    };

    xhr.send(`patrol=${patrol}&action=${action}&count=${count}&page=${page}&csrf_token=${csrfToken}`);
}

// Ensure CSRF token is dynamically inserted into the page
document.addEventListener("DOMContentLoaded", () => {
    const hiddenCsrfInput = document.createElement("input");
    hiddenCsrfInput.type = "hidden";
    hiddenCsrfInput.id = "csrf_token";
    hiddenCsrfInput.value = document.body.getAttribute("data-csrf") || "";
    document.body.appendChild(hiddenCsrfInput);
});