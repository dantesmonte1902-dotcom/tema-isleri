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

        if (getCards().length > getVisibleCount()) {
            setInterval(function () {
                const cards = getCards();
                const visibleCount = getVisibleCount();
                const maxIndex = Math.max(0, cards.length - visibleCount);

                currentIndex = currentIndex >= maxIndex ? 0 : currentIndex + 1;
                updateSlider();
            }, 5000);
        }
    });

    const countdownBlocks = document.querySelectorAll('[data-countdown-ts]');

    countdownBlocks.forEach(function (block) {
        const targetTime = Number(block.getAttribute('data-countdown-ts') || 0);
        if (!Number.isFinite(targetTime) || targetTime <= 0) return;

        const daysEl = block.querySelector('[data-countdown-days]');
        const hoursEl = block.querySelector('[data-countdown-hours]');
        const minutesEl = block.querySelector('[data-countdown-minutes]');
        const secondsEl = block.querySelector('[data-countdown-seconds]');

        function setValue(element, value) {
            if (element) {
                element.textContent = String(Math.max(0, value)).padStart(2, '0');
            }
        }

        function updateCountdown() {
            const now = Date.now();
            const distance = Math.max(0, targetTime - now);
            const totalSeconds = Math.floor(distance / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            setValue(daysEl, days);
            setValue(hoursEl, hours);
            setValue(minutesEl, minutes);
            setValue(secondsEl, seconds);
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
});
