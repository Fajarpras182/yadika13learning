import './bootstrap';

// Toggle sidebar function for hamburger menu
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.mobile-overlay');

    if (sidebar && overlay) {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }
}

// Make function globally available
window.toggleSidebar = toggleSidebar;
