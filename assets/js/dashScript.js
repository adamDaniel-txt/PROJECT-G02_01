function toggleSidebar() {
    const sidebar = document.getElementById('sidebar')
    sidebar.classList.toggle('active')
}

document.addEventListener('click', function (event) {
    const sidebar = document.getElementById('sidebar')
    const toggle = document.querySelector('.mobile-menu-toggle')

    if (
        window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggle.contains(event.target) && sidebar.classList.contains('active')
    ) {
        sidebar.classList.remove('active')
    }
})