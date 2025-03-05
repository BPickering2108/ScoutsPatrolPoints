document.addEventListener("DOMContentLoaded", function() {
    const loginButton = document.getElementById("login-button");

    loginButton.addEventListener("click", function() {
        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        if (username === "your_username" && password === "your_password") { // Replace with your actual username and password
            sessionStorage.setItem("authenticated", "true");
            const redirectTo = sessionStorage.getItem("redirectTo") || "index.html";
            sessionStorage.removeItem("redirectTo");
            window.location.href = redirectTo;
        } else {
            alert("Incorrect username or password!");
        }
    });
});