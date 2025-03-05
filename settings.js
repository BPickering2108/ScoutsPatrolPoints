document.addEventListener("DOMContentLoaded", function() {
    // Monday Scouts settings
    const monMaxPointsInput = document.getElementById("mon-max-points");
    const monColorKestrelInput = document.getElementById("mon-color-kestrel");
    const monColorCurlewInput = document.getElementById("mon-color-curlew");
    const monColorEagleInput = document.getElementById("mon-color-eagle");
    const monColorWoodpeckerInput = document.getElementById("mon-color-woodpecker");
    const saveMonSettingsButton = document.getElementById("save-mon-settings");
    const resetMonBarsButton = document.getElementById("reset-mon-bars");

    // Load saved settings for Monday Scouts
    const savedMonMaxPoints = localStorage.getItem("monMaxPoints");
    if (savedMonMaxPoints) {
        monMaxPointsInput.value = savedMonMaxPoints;
    }
    const savedMonColors = {
        kestrel: localStorage.getItem("monColorKestrel"),
        curlew: localStorage.getItem("monColorCurlew"),
        eagle: localStorage.getItem("monColorEagle"),
        woodpecker: localStorage.getItem("monColorWoodpecker")
    };
    if (savedMonColors.kestrel) monColorKestrelInput.value = savedMonColors.kestrel;
    if (savedMonColors.curlew) monColorCurlewInput.value = savedMonColors.curlew;
    if (savedMonColors.eagle) monColorEagleInput.value = savedMonColors.eagle;
    if (savedMonColors.woodpecker) monColorWoodpeckerInput.value = savedMonColors.woodpecker;

    saveMonSettingsButton.addEventListener("click", function() {
        const monMaxPoints = monMaxPointsInput.value;
        localStorage.setItem("monMaxPoints", monMaxPoints);
        localStorage.setItem("monColorKestrel", monColorKestrelInput.value);
        localStorage.setItem("monColorCurlew", monColorCurlewInput.value);
        localStorage.setItem("monColorEagle", monColorEagleInput.value);
        localStorage.setItem("monColorWoodpecker", monColorWoodpeckerInput.value);
        alert("Monday Scouts settings saved!");
    });

    resetMonBarsButton.addEventListener("click", function() {
        if (sessionStorage.getItem("authenticated") === "true") {
            localStorage.setItem("monResetBars", "true");
            alert("Monday Scouts bars reset!");
        } else {
            alert("You need to be authenticated to reset bars.");
        }
    });

    // Wednesday Scouts settings
    const wedMaxPointsInput = document.getElementById("wed-max-points");
    const wedColorKestrelInput = document.getElementById("wed-color-kestrel");
    const wedColorCurlewInput = document.getElementById("wed-color-curlew");
    const wedColorEagleInput = document.getElementById("wed-color-eagle");
    const wedColorWoodpeckerInput = document.getElementById("wed-color-woodpecker");
    const saveWedSettingsButton = document.getElementById("save-wed-settings");
    const resetWedBarsButton = document.getElementById("reset-wed-bars");

    // Load saved settings for Wednesday Scouts
    const savedWedMaxPoints = localStorage.getItem("wedMaxPoints");
    if (savedWedMaxPoints) {
        wedMaxPointsInput.value = savedWedMaxPoints;
    }
    const savedWedColors = {
        kestrel: localStorage.getItem("wedColorKestrel"),
        curlew: localStorage.getItem("wedColorCurlew"),
        eagle: localStorage.getItem("wedColorEagle"),
        woodpecker: localStorage.getItem("wedColorWoodpecker")
    };
    if (savedWedColors.kestrel) wedColorKestrelInput.value = savedWedColors.kestrel;
    if (savedWedColors.curlew) wedColorCurlewInput.value = savedWedColors.curlew;
    if (savedWedColors.eagle) wedColorEagleInput.value = savedWedColors.eagle;
    if (savedWedColors.woodpecker) wedColorWoodpeckerInput.value = savedWedColors.woodpecker;

    saveWedSettingsButton.addEventListener("click", function() {
        const wedMaxPoints = wedMaxPointsInput.value;
        localStorage.setItem("wedMaxPoints", wedMaxPoints);
        localStorage.setItem("wedColorKestrel", wedColorKestrelInput.value);
        localStorage.setItem("wedColorCurlew", wedColorCurlewInput.value);
        localStorage.setItem("wedColorEagle", wedColorEagleInput.value);
        localStorage.setItem("wedColorWoodpecker", wedColorWoodpeckerInput.value);
        alert("Wednesday Scouts settings saved!");
    });

    resetWedBarsButton.addEventListener("click", function() {
        if (sessionStorage.getItem("authenticated") === "true") {
            localStorage.setItem("wedResetBars", "true");
            alert("Wednesday Scouts bars reset!");
        } else {
            alert("You need to be authenticated to reset bars.");
        }
    });
});