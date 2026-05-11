document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.arim-mobile-menu-toggle');
    const mobilePanel = document.querySelector('.arim-mobile-menu-panel');
    const mobileOverlay = document.querySelector('.arim-mobile-menu-overlay');
    const mobileClose = document.querySelector('.arim-mobile-menu-close');

    if (menuToggle && mobilePanel && mobileOverlay) {
        const openMenu = () => {
            mobilePanel.classList.add('is-open');
            mobileOverlay.classList.add('is-open');
            document.body.classList.add('arim-no-scroll');
        };

        const closeMenu = () => {
            mobilePanel.classList.remove('is-open');
            mobileOverlay.classList.remove('is-open');
            document.body.classList.remove('arim-no-scroll');
        };

        menuToggle.addEventListener('click', openMenu);

        if (mobileClose) {
            mobileClose.addEventListener('click', closeMenu);
        }

        mobileOverlay.addEventListener('click', closeMenu);
    }

    const sliders = document.querySelectorAll('.arim-banner-slider');

    sliders.forEach(function (slider) {
        const slides = slider.querySelectorAll('.arim-banner-slide');
        const prevBtn = slider.querySelector('.arim-banner-prev');
        const nextBtn = slider.querySelector('.arim-banner-next');

        if (!slides.length) return;

        let current = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('is-active', i === index);
            });
        }

        function nextSlide() {
            current = (current + 1) % slides.length;
            showSlide(current);
        }

        function prevSlideFn() {
            current = (current - 1 + slides.length) % slides.length;
            showSlide(current);
        }

        if (nextBtn) nextBtn.addEventListener('click', nextSlide);
        if (prevBtn) prevBtn.addEventListener('click', prevSlideFn);

        showSlide(current);

        setInterval(nextSlide, 5000);
    });

    const productThumbs = document.querySelectorAll('.arim-single-thumb');
    const mainImage = document.querySelector('.arim-single-main-image img');

    if (productThumbs.length && mainImage) {
        productThumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function (e) {
                e.preventDefault();
                const img = thumb.querySelector('img');
                if (img) {
                    const fullUrl = thumb.getAttribute('href') || img.getAttribute('src');
                    mainImage.setAttribute('src', fullUrl);

                    productThumbs.forEach(t => t.classList.remove('is-active'));
                    thumb.classList.add('is-active');
                }
            });
        });
    }
});