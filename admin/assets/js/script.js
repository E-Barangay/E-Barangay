document.addEventListener("DOMContentLoaded", () => {
    const toggleSidebar = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const mobileToggle = document.getElementById("mobileMenuToggle");

    if (toggleSidebar) {
        toggleSidebar.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
        });
    }

    if (mobileToggle) {
        mobileToggle.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
    }
});