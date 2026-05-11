document.addEventListener('DOMContentLoaded', function () {
    const CROSS_TAB_SYNC_DEBOUNCE = 80;
    const DEFAULT_COMPARE_LIMIT = 4;
    const DEFAULT_SLIDER_INTERVAL = 5000;
    const DEFAULT_SEARCH_DEBOUNCE = 220;
    const DEFAULT_SEARCH_MIN_CHARS = 2;
    const themeConfig = window.arimTheme || {};
    const favoriteStorageKey = 'arimFavorites';
    const compareStorageKey = 'arimCompare';
    const recentlyViewedStorageKey = 'arimRecentlyViewed';
    const favoriteLabels = themeConfig.labels || {};
    const activeIntervals = [];
    const currencyFormatter = typeof Intl !== 'undefined' && typeof Intl.NumberFormat === 'function'
        ? new Intl.NumberFormat('tr-TR', {
            style: 'currency',
            currency: themeConfig.currencyCode || 'TRY',
            maximumFractionDigits: 0,
        })
        : null;
    let crossTabSyncDebounceTimer = null;
    let recommendationRequestController = null;
    let recommendationRequestSignature = '';

    /**
     * Numeric config değerini güvenli şekilde normalize eder.
     * @param {*} value
     * @param {number} fallback
     * @param {number=} minValue
     * @returns {number}
     */
    function getFiniteConfigValue(value, fallback, minValue) {
        const parsedValue = Number(value);
        const normalizedValue = Number.isFinite(parsedValue) ? parsedValue : fallback;

        return typeof minValue === 'number' ? Math.max(minValue, normalizedValue) : normalizedValue;
    }

    const searchDebounceDelay = getFiniteConfigValue(themeConfig.searchDebounce, DEFAULT_SEARCH_DEBOUNCE);
    const liveSearchMinChars = getFiniteConfigValue(themeConfig.searchMinChars, DEFAULT_SEARCH_MIN_CHARS, 1);
    const compareLimit = getFiniteConfigValue(themeConfig.compareLimit, DEFAULT_COMPARE_LIMIT, 2);
    const recentlyViewedLimit = getFiniteConfigValue(themeConfig.recentlyViewedLimit, 6, 1);

    function trackInterval(callback, delay) {
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
            trackInterval(nextSlide, DEFAULT_SLIDER_INTERVAL);
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

    function safeParseCompare() {
        try {
            const storedProducts = window.localStorage.getItem(compareStorageKey);
            const parsedProducts = storedProducts ? JSON.parse(storedProducts) : [];

            if (!Array.isArray(parsedProducts)) {
                return [];
            }

            return parsedProducts
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

    function safeParseRecentlyViewed() {
        try {
            const storedProducts = window.localStorage.getItem(recentlyViewedStorageKey);
            const parsedProducts = storedProducts ? JSON.parse(storedProducts) : [];

            if (!Array.isArray(parsedProducts)) {
                return [];
            }

            return parsedProducts
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
        requestRecommendations();
    }

    function getFavoriteProductMap() {
        return safeParseFavorites().reduce(function (accumulator, item) {
            accumulator[item.id] = item;
            return accumulator;
        }, {});
    }

    function getCompareProductMap() {
        return safeParseCompare().reduce(function (accumulator, item) {
            accumulator[item.id] = item;
            return accumulator;
        }, {});
    }

    function toNumber(value) {
        const numericValue = Number(value);
        return Number.isFinite(numericValue) ? numericValue : 0;
    }

    function collectProductData(element) {
        const productId = String(element.getAttribute('data-product-id') || '').trim();

        if (!productId) {
            return null;
        }

        const currentPrice = toNumber(element.getAttribute('data-product-current-price'));
        const regularPrice = toNumber(element.getAttribute('data-product-regular-price'));

        return {
            id: productId,
            title: String(element.getAttribute('data-product-title') || '').trim(),
            price: String(element.getAttribute('data-product-price') || '').trim(),
            image: String(element.getAttribute('data-product-image') || '').trim(),
            url: String(element.getAttribute('data-product-url') || '').trim(),
            brand: String(element.getAttribute('data-product-brand') || '').trim(),
            store: String(element.getAttribute('data-product-store') || '').trim(),
            badge: String(element.getAttribute('data-product-badge') || '').trim(),
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

    function setCompareButtonState(button, isCompared) {
        button.classList.toggle('is-compared', isCompared);
        button.setAttribute('aria-pressed', isCompared ? 'true' : 'false');
        button.setAttribute('aria-label', isCompared ? (favoriteLabels.removeFromCompare || 'Karşılaştırmadan kaldır') : (favoriteLabels.addToCompare || 'Karşılaştırmaya ekle'));
        button.textContent = '⇄';
    }

    function updateFavoriteButtons() {
        const favorites = getFavoriteProductMap();

        document.querySelectorAll('.arim-favorite-btn[data-product-id]').forEach(function (button) {
            const productId = String(button.getAttribute('data-product-id') || '').trim();
            setFavoriteButtonState(button, Boolean(productId && favorites[productId]));
        });
    }

    function updateCompareButtons() {
        const compareItems = getCompareProductMap();

        document.querySelectorAll('.arim-compare-btn[data-product-id]').forEach(function (button) {
            const productId = String(button.getAttribute('data-product-id') || '').trim();
            setCompareButtonState(button, Boolean(productId && compareItems[productId]));
        });
    }

    function formatCurrency(amount) {
        try {
            return currencyFormatter ? currencyFormatter.format(amount) : '₺' + Math.round(amount);
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

    function updateCompareCounters() {
        const compareItems = safeParseCompare();

        document.querySelectorAll('.arim-compare-count').forEach(function (counter) {
            counter.textContent = String(compareItems.length);
        });
    }

    function saveCompare(items) {
        try {
            window.localStorage.setItem(compareStorageKey, JSON.stringify(items));
        } catch (error) {
            return false;
        }

        updateCompareButtons();
        updateCompareCounters();
        renderCompareSection();
        requestRecommendations();
        return true;
    }

    function createProductActionButton(type, item) {
        const button = document.createElement('button');
        button.type = 'button';

        if (type === 'compare') {
            button.className = 'arim-compare-btn';
            button.setAttribute('aria-label', favoriteLabels.addToCompare || 'Karşılaştırmaya ekle');
            button.textContent = '⇄';
        } else {
            button.className = 'arim-favorite-btn';
            button.setAttribute('aria-label', favoriteLabels.addToFavorites || 'Favorilere ekle');
            button.textContent = '♡';
        }

        button.setAttribute('data-product-id', item.id || '');
        button.setAttribute('data-product-title', item.title || '');
        button.setAttribute('data-product-price', item.price || '');
        button.setAttribute('data-product-image', item.image || '');
        button.setAttribute('data-product-url', item.url || '');
        button.setAttribute('data-product-brand', item.brand || '');
        button.setAttribute('data-product-store', item.store || '');
        button.setAttribute('data-product-badge', item.badge || '');
        button.setAttribute('data-product-current-price', toNumber(item.currentPrice));
        button.setAttribute('data-product-regular-price', toNumber(item.regularPrice));

        return button;
    }

    function createFavoriteCard(item, options) {
        const cardOptions = options || {};
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

        if (cardOptions.quickActions) {
            imageWrap.appendChild(createProductActionButton('favorite', item));
            imageWrap.appendChild(createProductActionButton('compare', item));
        }

        if (cardOptions.removable !== false) {
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'arim-favorite-btn is-favorited arim-favorites-remove-btn';
            removeButton.setAttribute('aria-label', favoriteLabels.removeFavorite || 'Favorilerden kaldır');
            removeButton.setAttribute('data-product-id', item.id);
            removeButton.textContent = '♥';
            imageWrap.appendChild(removeButton);
        }

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
        viewLink.textContent = cardOptions.viewLabel || favoriteLabels.viewProduct || 'Ürünü İncele';
        footer.appendChild(viewLink);

        content.appendChild(footer);
        card.appendChild(content);

        return card;
    }

    function getProductSavings(item) {
        if (item.currentPrice > 0 && item.regularPrice > item.currentPrice) {
            return item.regularPrice - item.currentPrice;
        }

        return 0;
    }

    function getLowestComparePrice(items) {
        const pricedItems = items.filter(function (item) {
            return item.currentPrice > 0;
        });

        if (!pricedItems.length) {
            return 0;
        }

        return pricedItems.reduce(function (lowest, item) {
            return item.currentPrice < lowest ? item.currentPrice : lowest;
        }, pricedItems[0].currentPrice);
    }

    function createCompareCard(item, lowestPrice) {
        const card = document.createElement('article');
        card.className = 'arim-compare-card';

        const top = document.createElement('div');
        top.className = 'arim-compare-card-top';

        const imageLink = document.createElement('a');
        imageLink.className = 'arim-compare-card-image';
        imageLink.href = item.url || themeConfig.shopUrl || '#';

        if (item.image) {
            const image = document.createElement('img');
            image.src = item.image;
            image.alt = item.title || '';
            image.loading = 'lazy';
            imageLink.appendChild(image);
        } else {
            imageLink.textContent = (item.brand || 'AR').slice(0, 2).toUpperCase();
        }

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'arim-compare-btn is-compared arim-compare-remove-btn';
        removeButton.setAttribute('aria-label', favoriteLabels.removeFromCompare || 'Karşılaştırmadan kaldır');
        removeButton.setAttribute('data-product-id', item.id);
        removeButton.textContent = '⇄';

        top.appendChild(imageLink);
        top.appendChild(removeButton);
        card.appendChild(top);

        const body = document.createElement('div');
        body.className = 'arim-compare-card-body';

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

        body.appendChild(meta);

        const title = document.createElement('a');
        title.className = 'arim-favorites-card-title';
        title.href = item.url || themeConfig.shopUrl || '#';
        title.textContent = item.title || '';
        body.appendChild(title);

        const price = document.createElement('div');
        price.className = 'arim-favorites-card-price';
        price.textContent = item.price || formatCurrency(item.currentPrice || 0);
        body.appendChild(price);

        const badges = document.createElement('div');
        badges.className = 'arim-compare-card-badges';

        if (lowestPrice > 0 && item.currentPrice === lowestPrice) {
            const bestPrice = document.createElement('span');
            bestPrice.className = 'arim-compare-pill is-best';
            bestPrice.textContent = favoriteLabels.compareBestPrice || 'En iyi fiyat';
            badges.appendChild(bestPrice);
        }

        if (getProductSavings(item) > 0) {
            const savings = document.createElement('span');
            savings.className = 'arim-compare-pill';
            savings.textContent = (favoriteLabels.compareSavings || 'İndirim farkı') + ': ' + formatCurrency(getProductSavings(item));
            badges.appendChild(savings);
        }

        if (item.badge) {
            const badge = document.createElement('span');
            badge.className = 'arim-compare-pill';
            badge.textContent = item.badge;
            badges.appendChild(badge);
        }

        if (badges.childNodes.length) {
            body.appendChild(badges);
        }

        const link = document.createElement('a');
        link.className = 'arim-favorites-card-link';
        link.href = item.url || themeConfig.shopUrl || '#';
        link.textContent = favoriteLabels.compareActionLabel || favoriteLabels.viewProduct || 'İncele';
        body.appendChild(link);

        card.appendChild(body);

        return card;
    }

    function createCompareTable(items, lowestPrice) {
        const tableWrap = document.createElement('div');
        tableWrap.className = 'arim-compare-table-wrap';

        const table = document.createElement('table');
        table.className = 'arim-compare-table';

        const headerRow = document.createElement('tr');
        const headerLabel = document.createElement('th');
        headerLabel.textContent = favoriteLabels.compareCountLabel || 'Karşılaştırma';
        headerRow.appendChild(headerLabel);

        items.forEach(function (item) {
            const headerCell = document.createElement('th');
            headerCell.textContent = item.title || '';

            if (lowestPrice > 0 && item.currentPrice === lowestPrice) {
                headerCell.classList.add('is-best');
            }

            headerRow.appendChild(headerCell);
        });

        const rows = [
            {
                label: favoriteLabels.compareBrandLabel || 'Marka',
                getValue: function (item) {
                    return item.brand || '—';
                },
            },
            {
                label: favoriteLabels.compareStoreLabel || 'Mağaza',
                getValue: function (item) {
                    return item.store || '—';
                },
            },
            {
                label: favoriteLabels.comparePriceLabel || 'Fiyat',
                getValue: function (item) {
                    return item.price || formatCurrency(item.currentPrice || 0);
                },
                highlightBest: true,
            },
            {
                label: favoriteLabels.compareDiscountLabel || 'İndirim',
                getValue: function (item) {
                    const savings = getProductSavings(item);
                    return savings > 0 ? formatCurrency(savings) : (favoriteLabels.compareNoDifference || 'Fiyat farkı bulunmuyor');
                },
            },
            {
                label: favoriteLabels.compareBadgeLabel || 'Öne çıkan',
                getValue: function (item) {
                    return item.badge || '—';
                },
            },
        ];

        const tbody = document.createElement('tbody');
        tbody.appendChild(headerRow);

        rows.forEach(function (rowConfig) {
            const row = document.createElement('tr');
            const label = document.createElement('td');
            label.textContent = rowConfig.label;
            row.appendChild(label);

            items.forEach(function (item) {
                const valueCell = document.createElement('td');
                valueCell.textContent = rowConfig.getValue(item);

                if (rowConfig.highlightBest && lowestPrice > 0 && item.currentPrice === lowestPrice) {
                    valueCell.classList.add('is-best');
                }

                row.appendChild(valueCell);
            });

            tbody.appendChild(row);
        });

        table.appendChild(tbody);
        tableWrap.appendChild(table);
        return tableWrap;
    }

    function renderCompareSection() {
        const comparePage = document.querySelector('[data-arim-compare-page]');
        if (!comparePage) {
            return;
        }

        const items = safeParseCompare();
        comparePage.innerHTML = '';

        if (!items.length) {
            const emptyState = document.createElement('div');
            emptyState.className = 'arim-favorites-empty arim-favorites-empty-secondary';

            const title = document.createElement('h2');
            title.textContent = favoriteLabels.compareEmptyTitle || 'Karşılaştırma listen hazır değil';
            emptyState.appendChild(title);

            const description = document.createElement('p');
            description.textContent = favoriteLabels.compareEmptyText || 'Ürün kartlarındaki karşılaştır butonuyla en fazla 4 ürünü yan yana inceleyebilirsin.';
            emptyState.appendChild(description);

            comparePage.appendChild(emptyState);
            return;
        }

        const intro = document.createElement('div');
        intro.className = 'arim-compare-intro';

        const introText = document.createElement('p');
        introText.className = 'arim-favorites-secondary-note';
        introText.textContent = favoriteLabels.compareDescription || 'Seçtiğin ürünleri aynı tabloda kıyasla, en iyi fiyatı yakala ve kararını hızlandır.';
        intro.appendChild(introText);

        const counter = document.createElement('span');
        counter.className = 'arim-compare-limit-note';
        counter.textContent = String(items.length) + '/' + String(compareLimit) + ' • ' + (favoriteLabels.compareMaxNotice || 'Karşılaştırma listesinde en fazla 4 ürün tutulur.');
        intro.appendChild(counter);
        comparePage.appendChild(intro);

        const lowestPrice = getLowestComparePrice(items);

        const grid = document.createElement('div');
        grid.className = 'arim-compare-grid';

        items.forEach(function (item) {
            grid.appendChild(createCompareCard(item, lowestPrice));
        });

        comparePage.appendChild(grid);
        comparePage.appendChild(createCompareTable(items, lowestPrice));
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

    function saveRecentlyViewed(items) {
        try {
            window.localStorage.setItem(recentlyViewedStorageKey, JSON.stringify(items));
        } catch (error) {
            return;
        }

        renderRecentlyViewedSection();
        requestRecommendations();
    }

    function trackRecentlyViewedProduct() {
        const productElement = document.querySelector('[data-arim-recent-product]');
        if (!productElement) {
            return;
        }

        const product = collectProductData(productElement);
        if (!product) {
            return;
        }

        const items = safeParseRecentlyViewed().filter(function (item) {
            return item.id !== product.id;
        });

        items.unshift(product);
        saveRecentlyViewed(items.slice(0, recentlyViewedLimit));
    }

    function renderRecentlyViewedSection() {
        const recentlyViewedPage = document.querySelector('[data-arim-recently-viewed-page]');
        const recentlyViewedSection = document.querySelector('[data-arim-recently-viewed-section]');

        if (!recentlyViewedPage || !recentlyViewedSection) {
            return;
        }

        const items = safeParseRecentlyViewed();
        recentlyViewedPage.innerHTML = '';

        if (!items.length) {
            const emptyState = document.createElement('div');
            emptyState.className = 'arim-favorites-empty arim-favorites-empty-secondary';

            const title = document.createElement('h2');
            title.textContent = favoriteLabels.recentlyViewedTitle || 'Son görüntülenen ürünler';
            emptyState.appendChild(title);

            const description = document.createElement('p');
            description.textContent = favoriteLabels.recentlyViewedEmpty || 'Bir ürün detay sayfasını ziyaret ettiğinde burada görünür.';
            emptyState.appendChild(description);

            recentlyViewedPage.appendChild(emptyState);
            return;
        }

        const intro = document.createElement('p');
        intro.className = 'arim-favorites-secondary-note';
        intro.textContent = favoriteLabels.recentlyViewedText || 'İncelediğin ürünleri burada tut, dilediğin zaman hızlıca geri dön.';
        recentlyViewedPage.appendChild(intro);

        const grid = document.createElement('div');
        grid.className = 'arim-favorites-grid arim-favorites-grid-secondary';

        items.forEach(function (item) {
            grid.appendChild(createFavoriteCard(item, {
                removable: false,
                viewLabel: favoriteLabels.viewAgain || favoriteLabels.viewProduct || 'Tekrar İncele',
            }));
        });

        recentlyViewedPage.appendChild(grid);
    }

    function createRecommendationState(titleText, descriptionText, extraClassName) {
        const state = document.createElement('div');
        state.className = 'arim-favorites-empty arim-favorites-empty-secondary';

        if (extraClassName) {
            state.classList.add(extraClassName);
        }

        const title = document.createElement('h2');
        title.textContent = titleText;
        state.appendChild(title);

        const description = document.createElement('p');
        description.textContent = descriptionText;
        state.appendChild(description);

        return state;
    }

    function getRecommendationSourceIds() {
        const sourceItems = []
            .concat(safeParseFavorites(), safeParseCompare(), safeParseRecentlyViewed());
        const uniqueIds = [];

        sourceItems.forEach(function (item) {
            const productId = String(item && item.id ? item.id : '').trim();

            if (!productId || uniqueIds.indexOf(productId) >= 0) {
                return;
            }

            uniqueIds.push(productId);
        });

        return uniqueIds.slice(0, 24);
    }

    function renderRecommendationsSection(items) {
        const recommendationsPage = document.querySelector('[data-arim-recommendations-page]');
        if (!recommendationsPage) {
            return;
        }

        recommendationsPage.innerHTML = '';

        if (!Array.isArray(items) || !items.length) {
            recommendationsPage.appendChild(createRecommendationState(
                favoriteLabels.recommendationsEmptyTitle || 'Öneri alanı seni bekliyor',
                favoriteLabels.recommendationsEmptyText || 'Favori ekledikçe veya ürün inceledikçe burada sana daha uygun öneriler gösterilir.'
            ));
            return;
        }

        const intro = document.createElement('p');
        intro.className = 'arim-favorites-secondary-note';
        intro.textContent = favoriteLabels.recommendationsText || 'Favorilerin, karşılaştırmaların ve son ziyaretlerine göre seçilmiş ürünlerle vitrini genişlet.';
        recommendationsPage.appendChild(intro);

        const grid = document.createElement('div');
        grid.className = 'arim-favorites-grid arim-favorites-grid-secondary arim-recommendations-grid';

        items.forEach(function (item) {
            const recommendationItem = {
                id: String(item.id || ''),
                title: String(item.title || ''),
                price: String(item.price || ''),
                image: String(item.image || ''),
                url: String(item.url || ''),
                brand: String(item.brand || ''),
                store: String(item.store || ''),
                badge: String(item.badge || favoriteLabels.recommendationsBadge || ''),
                currentPrice: toNumber(item.currentPrice),
                regularPrice: toNumber(item.regularPrice),
            };

            grid.appendChild(createFavoriteCard(recommendationItem, {
                removable: false,
                quickActions: true,
                viewLabel: favoriteLabels.viewProduct || 'Ürünü İncele',
            }));
        });

        recommendationsPage.appendChild(grid);
        updateFavoriteButtons();
        updateCompareButtons();
    }

    function requestRecommendations(forceRefresh) {
        const recommendationsPage = document.querySelector('[data-arim-recommendations-page]');
        if (!recommendationsPage || !themeConfig.ajaxUrl || !themeConfig.recommendationsNonce) {
            return;
        }

        const sourceIds = getRecommendationSourceIds();
        const signature = sourceIds.join(',');

        if (!sourceIds.length) {
            recommendationRequestSignature = '';
            if (recommendationRequestController) {
                recommendationRequestController.abort();
                recommendationRequestController = null;
            }

            recommendationsPage.innerHTML = '';
            recommendationsPage.appendChild(createRecommendationState(
                favoriteLabels.recommendationsEmptyTitle || 'Öneri alanı seni bekliyor',
                favoriteLabels.recommendationsEmptyText || 'Favori ekledikçe veya ürün inceledikçe burada sana daha uygun öneriler gösterilir.'
            ));
            return;
        }

        if (!forceRefresh && signature === recommendationRequestSignature) {
            return;
        }

        recommendationRequestSignature = signature;

        if (recommendationRequestController) {
            recommendationRequestController.abort();
        }

        recommendationRequestController = typeof AbortController !== 'undefined' ? new AbortController() : null;
        recommendationsPage.innerHTML = '';
        recommendationsPage.appendChild(createRecommendationState(
            favoriteLabels.recommendationsTitle || 'Sana Özel Öneriler',
            favoriteLabels.recommendationsLoading || 'Senin için öneriler hazırlanıyor...',
            'arim-recommendations-loading'
        ));

        const body = new URLSearchParams({
            action: 'arim_personalized_recommendations',
            nonce: themeConfig.recommendationsNonce,
        });

        sourceIds.forEach(function (productId) {
            body.append('productIds[]', productId);
        });

        fetch(themeConfig.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: body.toString(),
            signal: recommendationRequestController ? recommendationRequestController.signal : undefined,
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (response) {
                if (signature !== recommendationRequestSignature) {
                    return;
                }

                if (!response || !response.success) {
                    renderRecommendationsSection([]);
                    return;
                }

                renderRecommendationsSection(response.data && Array.isArray(response.data.items) ? response.data.items : []);
            })
            .catch(function (error) {
                if (error && error.name === 'AbortError') {
                    return;
                }

                renderRecommendationsSection([]);
            })
            .finally(function () {
                recommendationRequestController = null;
            });
    }

    function initLiveSearch() {
        const searchForms = document.querySelectorAll('[data-arim-live-search]');

        if (!searchForms.length || !themeConfig.ajaxUrl || !themeConfig.searchNonce) {
            return;
        }

        function hideSuggestions(panel) {
            panel.hidden = true;
            panel.classList.remove('is-active');
        }

        function showSuggestions(panel) {
            panel.hidden = false;
            panel.classList.add('is-active');
        }

        function createSearchState(text) {
            const state = document.createElement('div');
            state.className = 'arim-search-suggestion-state';
            state.textContent = text;
            return state;
        }

        function createSearchResultItem(item) {
            const link = document.createElement('a');
            link.className = 'arim-search-suggestion-item';
            link.href = item.url || themeConfig.shopUrl || '#';

            const imageWrap = document.createElement('span');
            imageWrap.className = 'arim-search-suggestion-image';

            if (item.image) {
                const image = document.createElement('img');
                image.src = item.image;
                image.alt = item.title || '';
                image.loading = 'lazy';
                imageWrap.appendChild(image);
            } else {
                imageWrap.textContent = (item.brand || 'AR').slice(0, 2).toUpperCase();
            }

            const content = document.createElement('span');
            content.className = 'arim-search-suggestion-content';

            const meta = document.createElement('span');
            meta.className = 'arim-search-suggestion-meta';
            meta.textContent = [item.store, item.brand].filter(Boolean).join(' • ');
            content.appendChild(meta);

            const title = document.createElement('strong');
            title.className = 'arim-search-suggestion-title';
            title.textContent = item.title || '';
            content.appendChild(title);

            const price = document.createElement('span');
            price.className = 'arim-search-suggestion-price';
            price.textContent = item.price || '';
            content.appendChild(price);

            link.appendChild(imageWrap);
            link.appendChild(content);

            return link;
        }

        function renderSearchResults(resultsWrap, panel, payload) {
            resultsWrap.innerHTML = '';

            const items = payload && Array.isArray(payload.items) ? payload.items : [];

            if (!items.length) {
                resultsWrap.appendChild(createSearchState(favoriteLabels.searchNoResults || 'Aramana uygun ürün bulunamadı.'));
                showSuggestions(panel);
                return;
            }

            const list = document.createElement('div');
            list.className = 'arim-search-suggestion-list';

            items.forEach(function (item) {
                list.appendChild(createSearchResultItem(item));
            });

            resultsWrap.appendChild(list);

            if (payload.resultsUrl) {
                const footer = document.createElement('div');
                footer.className = 'arim-search-suggestions-footer';

                const footerLink = document.createElement('a');
                footerLink.className = 'arim-search-suggestions-link';
                footerLink.href = payload.resultsUrl;
                footerLink.textContent = favoriteLabels.searchViewAll || 'Tüm sonuçları gör';
                footer.appendChild(footerLink);

                resultsWrap.appendChild(footer);
            }

            showSuggestions(panel);
        }

        searchForms.forEach(function (form) {
            const input = form.querySelector('[data-arim-search-input]');
            const panel = form.querySelector('[data-arim-search-suggestions]');
            const resultsWrap = form.querySelector('[data-arim-search-results]');
            let debounceTimer = null;
            let activeController = null;

            if (!input || !panel || !resultsWrap) {
                return;
            }

            function requestSearch(query) {
                if (activeController) {
                    activeController.abort();
                }

                activeController = typeof AbortController !== 'undefined' ? new AbortController() : null;
                resultsWrap.innerHTML = '';
                resultsWrap.appendChild(createSearchState(favoriteLabels.searchLoading || 'Ürünler yükleniyor...'));
                showSuggestions(panel);

                const body = new URLSearchParams({
                    action: 'arim_public_product_search',
                    nonce: themeConfig.searchNonce,
                    q: query,
                });

                fetch(themeConfig.ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    },
                    body: body.toString(),
                    signal: activeController ? activeController.signal : undefined,
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (response) {
                        if (input.value.trim() !== query) {
                            return;
                        }

                        if (!response || !response.success) {
                            resultsWrap.innerHTML = '';
                            resultsWrap.appendChild(createSearchState(favoriteLabels.searchNoResults || 'Aramana uygun ürün bulunamadı.'));
                            showSuggestions(panel);
                            return;
                        }

                        renderSearchResults(resultsWrap, panel, response.data || {});
                    })
                    .catch(function (error) {
                        if (error && error.name === 'AbortError') {
                            return;
                        }

                        resultsWrap.innerHTML = '';
                        resultsWrap.appendChild(createSearchState(favoriteLabels.searchNoResults || 'Aramana uygun ürün bulunamadı.'));
                        showSuggestions(panel);
                    });
            }

            input.addEventListener('input', function () {
                const query = input.value.trim();

                if (debounceTimer) {
                    window.clearTimeout(debounceTimer);
                }

                if (query.length < liveSearchMinChars) {
                    if (activeController) {
                        activeController.abort();
                    }

                    hideSuggestions(panel);
                    resultsWrap.innerHTML = '';
                    return;
                }

                debounceTimer = window.setTimeout(function () {
                    requestSearch(query);
                }, searchDebounceDelay);
            });

            input.addEventListener('focus', function () {
                if (input.value.trim().length >= liveSearchMinChars && resultsWrap.childNodes.length) {
                    showSuggestions(panel);
                }
            });

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    hideSuggestions(panel);
                }
            });

            document.addEventListener('click', function (event) {
                if (!form.contains(event.target)) {
                    hideSuggestions(panel);
                }
            });
        });
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

    document.addEventListener('click', function (event) {
        const compareButton = event.target.closest('.arim-compare-btn[data-product-id]');

        if (!compareButton) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const product = collectProductData(compareButton);
        if (!product) {
            return;
        }

        const compareItems = safeParseCompare();
        const existingIndex = compareItems.findIndex(function (item) {
            return item.id === product.id;
        });

        if (existingIndex >= 0) {
            compareItems.splice(existingIndex, 1);
            saveCompare(compareItems);
            return;
        }

        compareItems.unshift(product);
        saveCompare(compareItems.slice(0, compareLimit));
    });

    document.addEventListener('click', function (event) {
        const refreshButton = event.target.closest('[data-arim-refresh-recommendations]');

        if (!refreshButton) {
            return;
        }

        event.preventDefault();
        requestRecommendations(true);
    });

    window.addEventListener('storage', function (event) {
        if (event.key !== favoriteStorageKey && event.key !== compareStorageKey && event.key !== recentlyViewedStorageKey) {
            return;
        }

        if (crossTabSyncDebounceTimer) {
            window.clearTimeout(crossTabSyncDebounceTimer);
        }

        crossTabSyncDebounceTimer = window.setTimeout(function () {
            updateFavoriteButtons();
            updateFavoriteCounters();
            updateCompareButtons();
            updateCompareCounters();
            renderFavoritesPage();
            renderCompareSection();
            renderRecentlyViewedSection();
            requestRecommendations(true);
            crossTabSyncDebounceTimer = null;
        }, CROSS_TAB_SYNC_DEBOUNCE);
    });

    trackRecentlyViewedProduct();
    updateFavoriteButtons();
    updateFavoriteCounters();
    updateCompareButtons();
    updateCompareCounters();
    renderFavoritesPage();
    renderCompareSection();
    renderRecentlyViewedSection();
    requestRecommendations();
    initLiveSearch();
});
