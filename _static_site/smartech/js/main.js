window.addEventListener('load', () => {
    // 1. Ocultar Preloader
    const loader = document.getElementById('loader');
    setTimeout(() => {
        loader.classList.add('hidden');
    }, 500);

    // 2. Menú Móvil
    const menuBtn = document.getElementById('menu-btn');
    const nav = document.getElementById('nav');
    const navLinks = document.querySelectorAll('.nav-menu a');

    menuBtn.addEventListener('click', () => {
        nav.classList.toggle('active');
        // Cambiar icono
        const icon = menuBtn.querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    });

    // Cerrar menú al hacer clic
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            nav.classList.remove('active');
            menuBtn.querySelector('i').classList.remove('fa-times');
            menuBtn.querySelector('i').classList.add('fa-bars');
        });
    });

    // 3. Scroll Suave
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target) {
                const headerHeight = document.querySelector('.header').offsetHeight;
                const position = target.offsetTop - headerHeight;
                window.scrollTo({
                    top: position,
                    behavior: 'smooth'
                });
            }
        });
    });
});