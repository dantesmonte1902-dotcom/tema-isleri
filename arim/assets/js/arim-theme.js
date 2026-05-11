document.addEventListener('DOMContentLoaded', function () {
    const themeConfig = window.arimTheme || {};
    const favoriteStorageKey = 'arimFavorites';
    const favoriteLabels = themeConfig.labels || {};
    const activeIntervals = [];

    function registerInterval(callback, delay) {
        const intervalId = window.setInterval(callback, delay);
        activeIntervals.push(intervalId);
        return intervalId;
    }

    window.addEventListener('pagehide', function () {
        activeIntervals.forEach(function (intervalId) {
            window.clearInterval(intervalId);
        });
    }, { once: true });

    const menuToggle = document.querySelector('.arim-mobile-menu-toggle');
    const mobilePanel = document.querySelector('.arim-mobile-menu-panel');
    const mobileOverlay = document.querySelector('.arim-mobile-menu-overlay');
    const mobileClose = document.querySelector('.arim-mobile-menu-close');

    if (menuToggle && mobilePanel && mobileOverlay) {
        const openMenu = function () {
            mobilePanel.classList.add('is-open');
            mobileOverlay.classList.add('is-open');
            document.body.classList.add('arim-no-scroll');
        };

        const closeMenu = function () {
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
        let current = 0;

        if (!slides.length) {
            return;
        }

        function showSlide(index) {
            slides.forEach(function (slide, slideIndex) {
                slide.classList.toggle('is-active', slideIndex === index);
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

        if (nextBtn) {
            nextBtn.addEventListener('click', nextSlide);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', prevSlideFn);
        }

        showSlide(current);

        if (slides.length > 1) {
            registerInterval(nextSlide, 5000);
        }
    });

    const productThumbs = document.querySelectorAll('.arim-single-thumb');
    const mainImage = document.querySelector('.arim-single-main-image img');

    if (productThumbs.length && mainImage) {
        productThumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function (event) {
                event.preventDefault();

                const image = thumb.querySelector('img');
                if (!image) {
                    return;
                }

                const fullUrl = thumb.getAttribute('href') || image.getAttribute('src');
                mainImage.setAttribute('src', fullUrl);

                productThumbs.forEach(function (thumbItem) {
                    thumbItem.classList.remove('is-active');
                });

                thumb.classList.add('is-active');
            });
        });
    }

    function safeParseFavorites() {
        try {
            const storedFavorites = window.localStorage.getItem(favoriteStorageKey);
            const parsedFavorites = storedFavorites ? JSON.parse(storedFavorites) : [];

            if (!Array.isArray(parsedFavorites)) {
                return [];
            }

            return parsedFavorites
                .map(function (item) {
                    if (!item || typeof item !== 'object') {
                        return null;
                    }

                    const id = String(item.id || '').trim();
                    if (!id) {
                        return null;
                    }

                    return {
                        id: id,
                        title: String(item.title || ''),
                        price: String(item.price || ''),
                        image: String(item.image || ''),
                        url: String(item.url || ''),
                        brand: String(item.brand || ''),
                        store: String(item.store || ''),
                        badge: String(item.badge || ''),
                        currentPrice: Number.isFinite(Number(item.currentPrice)) ? Number(item.currentPrice) : 0,
                        regularPrice: Number.isFinite(Number(item.regularPrice)) ? Number(item.regularPrice) : 0,
                    };
                })
                .filter(Boolean);
        } catch (error) {
            return [];
        }
    }

    function saveFavorites(items) {
        try {
            window.localStorage.setItem(favoriteStorageKey, JSON.stringify(items));
        } catch (error) {
            return;
        }

        updateFavoriteButtons();
        updateFavoriteCounters();
        renderFavoritesPage();
    }

    function getFavoriteProductMap() {
        return safeParseFavorites().reduce(function (accumulator, item) {
            accumulator[item.id] = item;
            return accumulator;
        }, {});
    }

    function toNumber(value) {
        const numericValue = Number(value);
        return Number.isFinite(numericValue) ? numericValue : 0;
    }

    function collectProductData(button) {
        const productId = String(button.getAttribute('data-product-id') || '').trim();

        if (!productId) {
            return null;
        }

        const currentPrice = toNumber(button.getAttribute('data-product-current-price'));
        const regularPrice = toNumber(button.getAttribute('data-product-regular-price'));

        return {
            id: productId,
            title: String(button.getAttribute('data-product-title') || '').trim(),
            price: String(button.getAttribute('data-product-price') || '').trim(),
            image: String(button.getAttribute('data-product-image') || '').trim(),
            url: String(button.getAttribute('data-product-url') || '').trim(),
            brand: String(button.getAttribute('data-product-brand') || '').trim(),
            store: String(button.getAttribute('data-product-store') || '').trim(),
            badge: String(button.getAttribute('data-product-badge') || '').trim(),
            currentPrice: currentPrice,
            regularPrice: regularPrice,
        };
    }

    function setFavoriteButtonState(button, isFavorited) {
        button.classList.toggle('is-favorited', isFavorited);
        button.setAttribute('aria-pressed', isFavorited ? 'true' : 'false');
        button.setAttribute('aria-label', isFavorited ? (favoriteLabels.removeFavorite || 'Favorilerden kaldır') : (favoriteLabels.addToFavorites || 'Favorilere ekle'));
        button.textContent = isFavorited ? '♥' : '♡';
    }

    function updateFavoriteButtons() {
        const favorites = getFavoriteProductMap();

        document.querySelectorAll('.arim-favorite-btn[data-product-id]').forEach(function (button) {
            const productId = String(button.getAttribute('data-product-id') || '').trim();
            setFavoriteButtonState(button, Boolean(productId && favorites[productId]));
        });
    }

    function formatCurrency(amount) {
        try {
            return new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: 'TRY',
                maximumFractionDigits: 0,
            }).format(amount);
        } catch (error) {
            return '₺' + Math.round(amount);
        }
    }

    function updateFavoriteCounters() {
        const favorites = safeParseFavorites();

        document.querySelectorAll('.arim-favorites-count').forEach(function (counter) {
            counter.textContent = String(favorites.length);
        });
    }

    function createFavoriteCard(item) {
        const card = document.createElement('article');
        card.className = 'arim-favorites-card arim-favorite-page-card';

        const media = document.createElement('div');
        media.className = 'arim-favorites-card-media';

        const productLink = document.createElement('a');
        productLink.className = 'arim-favorites-card-image-link';
        productLink.href = item.url || themeConfig.shopUrl || '#';

        const imageWrap = document.createElement('div');
        imageWrap.className = 'arim-product-image-wrap arim-favorites-card-image';

        if (item.image) {
            const image = document.createElement('img');
            image.src = item.image;
            image.alt = item.title || '';
            image.loading = 'lazy';
            imageWrap.appendChild(image);
        } else {
            const placeholder = document.createElement('div');
            placeholder.className = 'arim-favorites-card-image-placeholder';
            placeholder.textContent = item.brand || 'ARIM';
            imageWrap.appendChild(placeholder);
        }

        if (item.badge) {
            const badge = document.createElement('span');
            badge.className = 'arim-product-badge';
            badge.textContent = item.badge;

            if (item.currentPrice > 0 && item.regularPrice > item.currentPrice) {
                badge.classList.add('sale');
            }

            imageWrap.appendChild(badge);
        }

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'arim-favorite-btn is-favorited arim-favorites-remove-btn';
        removeButton.setAttribute('aria-label', favoriteLabels.removeFavorite || 'Favorilerden kaldır');
        removeButton.setAttribute('data-product-id', item.id);
        removeButton.textContent = '♥';
        imageWrap.appendChild(removeButton);

        productLink.appendChild(imageWrap);
        media.appendChild(productLink);
        card.appendChild(media);

        const content = document.createElement('div');
        content.className = 'arim-favorites-card-content';

        const meta = document.createElement('div');
        meta.className = 'arim-favorites-card-meta';

        if (item.store) {
            const store = document.createElement('span');
            store.className = 'arim-favorites-card-store';
            store.textContent = item.store;
            meta.appendChild(store);
        }

        if (item.brand) {
            const brand = document.createElement('span');
            brand.className = 'arim-favorites-card-brand';
            brand.textContent = item.brand;
            meta.appendChild(brand);
        }

        content.appendChild(meta);

        const title = document.createElement('a');
        title.className = 'arim-favorites-card-title';
        title.href = item.url || themeConfig.shopUrl || '#';
        title.textContent = item.title || '';
        content.appendChild(title);

        const price = document.createElement('div');
        price.className = 'arim-favorites-card-price';
        price.textContent = item.price || formatCurrency(item.currentPrice || 0);
        content.appendChild(price);

        if (item.currentPrice > 0 && item.regularPrice > item.currentPrice) {
            const savings = document.createElement('div');
            savings.className = 'arim-favorites-card-savings';
            savings.textContent = (favoriteLabels.savingsLabel || 'Toplam Avantaj') + ': ' + formatCurrency(item.regularPrice - item.currentPrice);
            content.appendChild(savings);
        }

        const footer = document.createElement('div');
        footer.className = 'arim-favorites-card-footer';

        const viewLink = document.createElement('a');
        viewLink.className = 'arim-favorites-card-link';
        viewLink.href = item.url || themeConfig.shopUrl || '#';
        viewLink.textContent = favoriteLabels.viewProduct || 'Ürünü İncele';
        footer.appendChild(viewLink);

        content.appendChild(footer);
        card.appendChild(content);

        return card;
    }

    function renderFavoritesPage() {
        const favoritesPage = document.querySelector('[data-arim-favorites-page]');
        if (!favoritesPage) {
            return;
        }

        const favorites = safeParseFavorites();
        const countTarget = document.querySelector('[data-arim-favorites-count]');
        const saleCountTarget = document.querySelector('[data-arim-favorites-sale-count]');
        const savingsTarget = document.querySelector('[data-arim-favorites-savings]');

        const saleCount = favorites.filter(function (item) {
            return item.currentPrice > 0 && item.regularPrice > item.currentPrice;
        }).length;

        const totalSavings = favorites.reduce(function (total, item) {
            if (item.currentPrice > 0 && item.regularPrice > item.currentPrice) {
                return total + (item.regularPrice - item.currentPrice);
            }

            return total;
        }, 0);

        if (countTarget) {
            countTarget.textContent = String(favorites.length);
        }

        if (saleCountTarget) {
            saleCountTarget.textContent = String(saleCount);
        }

        if (savingsTarget) {
            savingsTarget.textContent = formatCurrency(totalSavings);
        }

        favoritesPage.innerHTML = '';

        if (!favorites.length) {
            const emptyState = document.createElement('div');
            emptyState.className = 'arim-favorites-empty';

            const title = document.createElement('h2');
            title.textContent = favoriteLabels.favoritesEmptyTitle || 'Favori listen henüz boş';
            emptyState.appendChild(title);

            const description = document.createElement('p');
            description.textContent = favoriteLabels.favoritesEmptyText || 'Beğendiğin ürünleri kalp ikonuyla favorilerine ekle.';
            emptyState.appendChild(description);

            const link = document.createElement('a');
            link.className = 'arim-favorites-empty-link';
            link.href = themeConfig.shopUrl || '#';
            link.textContent = favoriteLabels.browseProducts || 'Ürünleri Keşfet';
            emptyState.appendChild(link);

            favoritesPage.appendChild(emptyState);
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'arim-favorites-grid';

        favorites.forEach(function (item) {
            grid.appendChild(createFavoriteCard(item));
        });

        favoritesPage.appendChild(grid);
    }

    document.addEventListener('click', function (event) {
        const favoriteButton = event.target.closest('.arim-favorite-btn[data-product-id]');

        if (!favoriteButton) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const product = collectProductData(favoriteButton);
        if (!product) {
            return;
        }

        const favorites = safeParseFavorites();
        const existingIndex = favorites.findIndex(function (item) {
            return item.id === product.id;
        });

        if (existingIndex >= 0) {
            favorites.splice(existingIndex, 1);
        } else {
            favorites.unshift(product);
        }

        saveFavorites(favorites);
    });

    window.addEventListener('storage', function (event) {
        if (event.key !== favoriteStorageKey) {
            return;
        }

        updateFavoriteButtons();
        updateFavoriteCounters();
        renderFavoritesPage();
    });

    updateFavoriteButtons();
    updateFavoriteCounters();
    renderFavoritesPage();
});
