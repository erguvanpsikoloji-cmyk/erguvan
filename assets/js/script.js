// Basic Interactivity
document.addEventListener('DOMContentLoaded', () => {
    // Scroll Effect for Navbar
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            if (navbar) navbar.classList.add('scrolled');
        } else {
            if (navbar) navbar.classList.remove('scrolled');
        }
    });

    // ==================== HAMBURGER MENU ====================
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    function openMenu() {
        navMenu.classList.add('active');
        navToggle.classList.add('active');
        navToggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden'; // Lock scroll while menu open
    }

    function closeMenu() {
        navMenu.classList.remove('active');
        navToggle.classList.remove('active');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = ''; // Restore scroll
    }

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            if (navMenu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Close menu when any nav link is clicked
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        // Close when clicking outside menu
        document.addEventListener('click', (e) => {
            if (navMenu.classList.contains('active') &&
                !navMenu.contains(e.target) &&
                !navToggle.contains(e.target)) {
                closeMenu();
            }
        });
    }

    // ==================== FAQ ACCORDION ====================
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const btn = item.querySelector('.faq-btn, .faq-header');
        if (btn) {
            btn.addEventListener('click', () => {
                faqItems.forEach(other => {
                    if (other !== item) other.classList.remove('active');
                });
                item.classList.toggle('active');
            });
        }
    });

    // ==================== SMOOTH SCROLL ====================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const navbarEl = document.querySelector('.navbar');
                const offset = navbarEl ? navbarEl.offsetHeight : 80;
                const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        });
    });
});
