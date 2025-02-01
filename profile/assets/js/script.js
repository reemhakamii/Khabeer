document.getElementById('dark-mode-toggle').addEventListener('click', function () {
    // Toggle dark mode on the body element
    document.body.classList.toggle('dark-mode');

    // Save the preference in localStorage so it persists across page reloads
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('dark-mode', 'enabled');
    } else {
        localStorage.removeItem('dark-mode');
    }
});

// Check for saved dark mode preference and apply it when the page loads
window.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('dark-mode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
});