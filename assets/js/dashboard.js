

// Consultant Search Functionality
document.getElementById('search').addEventListener('input', function () {
    let query = this.value.toLowerCase();
    let consultants = document.querySelectorAll('.consultant-card');

    consultants.forEach(card => {
        let name = card.querySelector('h3').textContent.toLowerCase();
        if (name.includes(query)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
});














document.addEventListener("DOMContentLoaded", function () {
    console.log("Dashboard Loaded!");

    const links = document.querySelectorAll(".sidebar ul li a");
    links.forEach(link => {
        link.addEventListener("mouseenter", () => {
            link.style.transform = "scale(1.1)";
        });

        link.addEventListener("mouseleave", () => {
            link.style.transform = "scale(1)";
        });
    });
});