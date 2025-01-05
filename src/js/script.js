const toggleSidebar = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
const sidebar = document.getElementById('logo-sidebar');
const closeSidebarButton = document.getElementById('close-sidebar');

// Open sidebar
toggleSidebar.addEventListener('click', function () {
    sidebar.classList.toggle('-translate-x-full');
});

// Close sidebar
closeSidebarButton.addEventListener('click', function () {
    sidebar.classList.add('-translate-x-full');
});