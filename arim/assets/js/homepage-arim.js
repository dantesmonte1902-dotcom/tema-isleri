document.addEventListener('DOMContentLoaded', function () {
    const sliders = document.querySelectorAll('[data-slider]');

    sliders.forEach(function (slider) {
        const track = slider.querySelector('[data-track]');
        const prev = slider.querySelector('[data-prev]');
        const next = slider.querySelector('[data-next]');

        if (!track) return;

        let currentIndex = 0;

        function getCards() {
            return track.querySelectorAll('.arim-slider-card');
        }

        function getCardWidth() {
            const firstCard = track.querySelector('.arim-slider-card');
            if (!firstCard) return 0;

            const style = window.getComputedStyle(track);
            const gap = parseFloat(style.gap || style.columnGap || 0);
            return firstCard.offsetWidth + gap;
        }

        function getVisibleCount() {
            const wrap = slider.querySelector('.arim-widget-track-wrap');
            const firstCard = track.querySelector('.arim-slider-card');
            if (!wrap || !firstCard) return 1;

            return Math.max(1, Math.floor(wrap.offsetWidth / firstCard.offsetWidth));
        }

        function updateSlider() {
            const cardWidth = getCardWidth();
            track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
        }

        function nextSlide() {
            const cards = getCards();
            const visibleCount = getVisibleCount();
            const maxIndex = Math.max(0, cards.length - visibleCount);

            currentIndex = Math.min(currentIndex + 1, maxIndex);
            updateSlider();
        }

        function prevSlide() {
            currentIndex = Math.max(currentIndex - 1, 0);
            updateSlider();
        }

        if (next) {
            next.addEventListener('click', nextSlide);
        }

        if (prev) {
            prev.addEventListener('click', prevSlide);
        }

        window.addEventListener('resize', function () {
            currentIndex = 0;
            updateSlider();
        });

        updateSlider();
    });
});