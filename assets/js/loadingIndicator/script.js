window.addEventListener("beforeunload", function () {
    document.getElementById("loadingIndicator").style.display = "block";
});

document.querySelectorAll("form").forEach(form => {
    form.addEventListener("submit", () => {
        document.getElementById("loadingIndicator").style.display = "block";
    });
});