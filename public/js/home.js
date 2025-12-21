// Header functionality
document.addEventListener('DOMContentLoaded', function() {
    // Location selector functionality
    const locationSelector = document.querySelector('.location-selector');
    if (locationSelector) {
        locationSelector.addEventListener('click', function() {
            alert('Location selection dialog would open here');
        });
    }

    // Dropdown hover functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('mouseenter', function() {
            this.classList.add('show');
            this.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'true');
            this.querySelector('.dropdown-menu').classList.add('show');
        });
        
        dropdown.addEventListener('mouseleave', function() {
            this.classList.remove('show');
            this.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
            this.querySelector('.dropdown-menu').classList.remove('show');
        });
    });

    // Mobile menu close on click
    const navLinks = document.querySelectorAll('.nav-link');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        });
    });

    // Header buttons functionality
    const postPropertyBtn = document.querySelector('.btn-post-property');
    const signInBtn = document.querySelector('.btn-signin');
    
    if (postPropertyBtn) {
        postPropertyBtn.addEventListener('click', function() {
            alert('Post Property functionality would open here');
        });
    }
    
    if (signInBtn) {
        signInBtn.addEventListener('click', function() {
            alert('Sign In modal would open here');
        });
    }
});