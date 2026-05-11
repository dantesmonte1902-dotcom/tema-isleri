<?php
defined('ABSPATH') || exit;

/**
 * Tema kurulum ayarları
 */
function arim_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('woocommerce');

    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    register_nav_menus([
        'primary' => __('Primary Menu', 'arim'),
    ]);
}
add_action('after_setup_theme', 'arim_theme_setup');


/**
 * CSS ve JS yükle
 */
function arim_enqueue_assets() {
    $theme_style_path = get_stylesheet_directory() . '/style.css';
    $theme_js_path    = get_template_directory() . '/assets/js/arim-theme.js';

    wp_enqueue_style(
        'arim-style',
        get_stylesheet_uri(),
        [],
        file_exists($theme_style_path) ? (string) filemtime($theme_style_path) : '1.0.0'
    );

    wp_enqueue_script(
        'arim-theme-js',
        get_template_directory_uri() . '/assets/js/arim-theme.js',
        [],
        file_exists($theme_js_path) ? (string) filemtime($theme_js_path) : '1.0.0',
        true
    );

    wp_localize_script('arim-theme-js', 'arimTheme', [
        'favoritesUrl' => arim_favorites_url(),
        'compareUrl'   => arim_favorites_url() . '#compare',
        'shopUrl'      => arim_shop_url(),
        'ajaxUrl'      => admin_url('admin-ajax.php'),
        'searchNonce'  => wp_create_nonce('arim_public_product_search'),
        'recommendationsNonce' => wp_create_nonce('arim_personalized_recommendations'),
        'currencyCode' => function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'TRY',
        'searchMinChars' => arim_live_search_min_chars(),
        'searchDebounce' => arim_live_search_debounce_ms(),
        'recentlyViewedLimit' => 6,
        'compareLimit' => 4,
        'labels'       => [
            'favoritesTitle'       => __('Favorilerim', 'arim'),
            'favoritesDescription' => __('Beğendiğin ürünleri burada sakla, karşılaştır ve alışverişe kaldığın yerden devam et.', 'arim'),
            'favoritesEmptyTitle'  => __('Favori listen henüz boş', 'arim'),
            'favoritesEmptyText'   => __('Beğendiğin ürünleri kalp ikonuyla favorilerine ekle, hepsini tek ekranda yeniden keşfet.', 'arim'),
            'browseProducts'       => __('Ürünleri Keşfet', 'arim'),
            'viewProduct'          => __('Ürünü İncele', 'arim'),
            'removeFavorite'       => __('Favorilerden kaldır', 'arim'),
            'addedToFavorites'     => __('Favorilere eklendi', 'arim'),
            'addToFavorites'       => __('Favorilere ekle', 'arim'),
            'compareTitle'         => __('Karşılaştırma Listem', 'arim'),
            'compareDescription'   => __('Seçtiğin ürünleri aynı tabloda kıyasla, en iyi fiyatı yakala ve kararını hızlandır.', 'arim'),
            'compareEmptyTitle'    => __('Karşılaştırma listen hazır değil', 'arim'),
            'compareEmptyText'     => __('Ürün kartlarındaki karşılaştır butonuyla en fazla 4 ürünü yan yana inceleyebilirsin.', 'arim'),
            'addToCompare'         => __('Karşılaştırmaya ekle', 'arim'),
            'removeFromCompare'    => __('Karşılaştırmadan kaldır', 'arim'),
            'compareNow'           => __('Karşılaştır', 'arim'),
            'compareCountLabel'    => __('Karşılaştırma', 'arim'),
            'compareBestPrice'     => __('En iyi fiyat', 'arim'),
            'compareSavings'       => __('İndirim farkı', 'arim'),
            'compareNoDifference'  => __('Fiyat farkı bulunmuyor', 'arim'),
            'compareMaxNotice'     => __('Karşılaştırma listesinde en fazla 4 ürün tutulur.', 'arim'),
            'compareBrandLabel'    => __('Marka', 'arim'),
            'compareStoreLabel'    => __('Mağaza', 'arim'),
            'comparePriceLabel'    => __('Fiyat', 'arim'),
            'compareDiscountLabel' => __('İndirim', 'arim'),
            'compareBadgeLabel'    => __('Öne çıkan', 'arim'),
            'compareActionLabel'   => __('İncele', 'arim'),
            'itemsLabel'           => __('Ürün', 'arim'),
            'saleItemsLabel'       => __('İndirimli', 'arim'),
            'savingsLabel'         => __('Toplam Avantaj', 'arim'),
            'searchPlaceholder'    => __('Ürün, kategori veya marka ara', 'arim'),
            'searchLoading'        => __('Ürünler yükleniyor...', 'arim'),
            'searchNoResults'      => __('Aramana uygun ürün bulunamadı.', 'arim'),
            'searchViewAll'        => __('Tüm sonuçları gör', 'arim'),
            'searchPopular'        => __('Hızlı arama', 'arim'),
            'recentlyViewedTitle'  => __('Son görüntülenen ürünler', 'arim'),
            'recentlyViewedText'   => __('İncelediğin ürünleri burada tut, dilediğin zaman hızlıca geri dön.', 'arim'),
            'recentlyViewedEmpty'  => __('Bir ürün detay sayfasını ziyaret ettiğinde burada görünür.', 'arim'),
            'viewAgain'            => __('Tekrar İncele', 'arim'),
            'recommendationsTitle' => __('Sana Özel Öneriler', 'arim'),
            'recommendationsText'  => __('Favorilerin, karşılaştırmaların ve son ziyaretlerine göre seçilmiş ürünlerle vitrini genişlet.', 'arim'),
            'recommendationsLoading' => __('Senin için öneriler hazırlanıyor...', 'arim'),
            'recommendationsEmptyTitle' => __('Öneri alanı seni bekliyor', 'arim'),
            'recommendationsEmptyText'  => __('Favori ekledikçe veya ürün inceledikçe burada sana daha uygun öneriler gösterilir.', 'arim'),
            'recommendationsRefresh'    => __('Önerileri yenile', 'arim'),
            'recommendationsBadge'      => __('Sana Özel', 'arim'),
        ],
    ]);

    if (is_front_page()) {
        wp_enqueue_style(
            'arim-homepage',
            get_template_directory_uri() . '/assets/css/homepage-arim.css',
            ['arim-style'],
            '1.0.0'
        );

        wp_enqueue_script(
            'arim-homepage-js',
            get_template_directory_uri() . '/assets/js/homepage-arim.js',
            [],
            '1.0.0',
            true
        );
    }

    if (function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page())) {
        wp_enqueue_style(
            'arim-woocommerce',
            get_template_directory_uri() . '/assets/css/woocommerce-arim.css',
            ['arim-style'],
            '1.0.0'
        );
    }

    if (function_exists('is_product') && is_product()) {
        wp_enqueue_style(
            'arim-single-product',
            get_template_directory_uri() . '/assets/css/single-product-arim.css',
            ['arim-style', 'arim-woocommerce'],
            '1.0.0'
        );
    }

    if ((function_exists('is_cart') && is_cart()) || (function_exists('is_checkout') && is_checkout())) {
        wp_enqueue_style(
            'arim-cart-checkout',
            get_template_directory_uri() . '/assets/css/cart-checkout-arim.css',
            ['arim-style', 'arim-woocommerce'],
            '1.0.0'
        );
    }

    if (function_exists('is_account_page') && is_account_page()) {
        wp_enqueue_style(
            'arim-myaccount',
            get_template_directory_uri() . '/assets/css/myaccount-arim.css',
            ['arim-style', 'arim-woocommerce'],
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'arim_enqueue_assets');


/**
 * Admin homepage manager style + media picker + tabs + preview + product picker
 */
function arim_homepage_manager_admin_assets($hook) {
    if ($hook !== 'toplevel_page_arim-homepage-manager') {
        return;
    }

    wp_enqueue_media();

    wp_add_inline_style('wp-admin', '
        .arim-admin-wrap {
            max-width: 1280px;
        }
        .arim-admin-hero {
            background: linear-gradient(135deg, #ff922e 0%, #ffb347 100%);
            color: #fff;
            padding: 24px 28px;
            border-radius: 18px;
            margin: 20px 0 18px;
            box-shadow: 0 14px 28px rgba(242,122,26,0.18);
        }
        .arim-admin-hero h1 {
            color: #fff;
            margin: 0 0 8px;
            font-size: 28px;
        }
        .arim-admin-hero p {
            margin: 0;
            font-size: 14px;
            color: rgba(255,255,255,0.92);
            max-width: 860px;
            line-height: 1.7;
        }
        .arim-admin-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 0 0 22px;
            padding: 12px;
            background: #fff;
            border: 1px solid #e6e6e6;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.04);
            position: sticky;
            top: 32px;
            z-index: 20;
        }
        .arim-admin-tab-btn {
            border: 1px solid transparent;
            background: #f7f7f7;
            color: #333;
            border-radius: 999px;
            min-height: 38px;
            padding: 0 16px;
            cursor: pointer;
            font-weight: 700;
        }
        .arim-admin-tab-btn.is-active,
        .arim-admin-tab-btn:hover {
            background: #fff2e8;
            color: #f27a1a;
            border-color: #ffd8b5;
        }
        .arim-admin-tab-panel {
            display: none;
        }
        .arim-admin-tab-panel.is-active {
            display: block;
        }
        .arim-admin-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .arim-admin-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.04);
        }
        .arim-admin-card h2 {
            margin: 0 0 16px;
            font-size: 18px;
            line-height: 1.3;
        }
        .arim-admin-fields {
            display: grid;
            gap: 14px;
        }
        .arim-admin-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #222;
        }
        .arim-admin-field input[type="text"],
        .arim-admin-field textarea,
        .arim-admin-field input[type="number"],
        .arim-admin-field select {
            width: 100%;
            max-width: 100%;
            border-radius: 10px;
            border: 1px solid #dcdcdc;
            padding: 10px 12px;
            min-height: 42px;
            background: #fff;
        }
        .arim-admin-field textarea {
            min-height: 88px;
            resize: vertical;
        }
        .arim-admin-section-title {
            margin: 4px 0 10px;
            font-size: 22px;
            color: #222;
        }
        .arim-admin-submit {
            margin-top: 24px;
            padding-bottom: 20px;
        }
        .arim-admin-media-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .arim-admin-media-preview {
            margin-top: 10px;
            width: 100%;
            max-width: 180px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e5e5e5;
            background: #fafafa;
        }
        .arim-admin-media-preview img {
            display: block;
            width: 100%;
            height: auto;
        }
        .arim-admin-toggle-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 18px;
        }
        .arim-admin-toggle-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.04);
        }
        .arim-admin-toggle-card label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #222;
        }
        .arim-admin-toggle-card p {
            margin: 8px 0 0;
            color: #666;
            font-size: 13px;
            line-height: 1.6;
        }
        .arim-admin-preview-wrap {
            margin-top: 18px;
            padding: 16px;
            border-radius: 16px;
            background: #fffaf5;
            border: 1px dashed #ffd2ab;
        }
        .arim-admin-preview-wrap h3 {
            margin: 0 0 12px;
            font-size: 14px;
            color: #a85a13;
        }
        .arim-preview-hero {
            min-height: 150px;
            border-radius: 16px;
            background: linear-gradient(135deg, #ff922e 0%, #ffb347 100%);
            color: #fff;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        .arim-preview-hero-badge {
            display: inline-flex;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.18);
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .arim-preview-hero-title {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 8px;
            max-width: 75%;
        }
        .arim-preview-hero-text {
            font-size: 13px;
            line-height: 1.6;
            max-width: 70%;
            color: rgba(255,255,255,0.92);
        }
        .arim-preview-floating {
            position: absolute;
            right: 16px;
            bottom: 16px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 14px;
            padding: 10px 12px;
            min-width: 130px;
        }
        .arim-preview-floating .label {
            font-size: 10px;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }
        .arim-preview-floating strong {
            display: block;
            font-size: 16px;
            line-height: 1.2;
            margin-bottom: 3px;
        }
        .arim-preview-floating small {
            display: block;
            font-size: 10px;
            line-height: 1.4;
        }
        .arim-preview-mosaic-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .arim-preview-mosaic-item {
            min-height: 90px;
            border-radius: 14px;
            padding: 12px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
        }
        .arim-preview-mosaic-item span {
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .arim-preview-mosaic-item strong {
            font-size: 14px;
            line-height: 1.25;
        }
        .arim-preview-coupon-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .arim-preview-coupon-item {
            min-height: 76px;
            border-radius: 14px;
            padding: 12px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .arim-preview-coupon-item strong {
            font-size: 20px;
            line-height: 1;
            margin-bottom: 4px;
        }
        .arim-preview-coupon-item span {
            font-size: 11px;
            line-height: 1.4;
        }
        .arim-preview-showcase-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 48px;
            border-radius: 12px;
            background: linear-gradient(180deg, #ff8b68 0%, #ff7c56 100%);
            color: #fff;
            padding: 0 14px;
            font-weight: 700;
        }
        .arim-product-picker {
            position: relative;
        }
        .arim-product-picker-search {
            width: 100%;
        }
        .arim-product-picker-results {
            margin-top: 8px;
            border: 1px solid #e6e6e6;
            border-radius: 12px;
            background: #fff;
            max-height: 220px;
            overflow-y: auto;
            display: none;
        }
        .arim-product-picker-results.is-active {
            display: block;
        }
        .arim-product-picker-result {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f1f1;
            cursor: pointer;
        }
        .arim-product-picker-result:last-child {
            border-bottom: none;
        }
        .arim-product-picker-result:hover {
            background: #fff7ef;
        }
        .arim-product-picker-result strong {
            display: block;
            color: #222;
            margin-bottom: 3px;
        }
        .arim-product-picker-result span {
            color: #777;
            font-size: 12px;
        }
        .arim-product-picker-selected {
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }
        .arim-product-picker-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid #e7e7e7;
            background: #fafafa;
            border-radius: 10px;
            padding: 10px 12px;
        }
        .arim-product-picker-chip strong {
            display: block;
            color: #222;
            margin-bottom: 2px;
        }
        .arim-product-picker-chip span {
            color: #777;
            font-size: 12px;
        }
        .arim-product-picker-remove {
            border: none;
            background: #ffe5e5;
            color: #c62828;
            border-radius: 999px;
            min-width: 28px;
            height: 28px;
            cursor: pointer;
            font-weight: 700;
        }
        .arim-product-picker-help {
            margin-top: 8px;
            color: #666;
            font-size: 12px;
            line-height: 1.5;
        }
        .arim-order-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }
        .arim-order-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.04);
        }
        .arim-order-card strong {
            display: block;
            margin-bottom: 10px;
            color: #222;
            font-size: 14px;
        }
        .arim-order-card input {
            width: 100%;
        }
        @media (max-width: 900px) {
            .arim-admin-grid,
            .arim-admin-toggle-grid,
            .arim-preview-mosaic-grid,
            .arim-preview-coupon-grid,
            .arim-order-grid {
                grid-template-columns: 1fr;
            }
            .arim-admin-tabs {
                position: static;
            }
            .arim-preview-hero-title,
            .arim-preview-hero-text {
                max-width: 100%;
            }
            .arim-preview-floating {
                position: static;
                margin-top: 12px;
            }
        }
    ');

    wp_register_script('arim-homepage-admin-inline', false, ['jquery'], null, true);
    wp_enqueue_script('arim-homepage-admin-inline');

    wp_localize_script('arim-homepage-admin-inline', 'arimHomepageAdmin', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('arim_homepage_product_search'),
    ]);

    wp_add_inline_script('arim-homepage-admin-inline', "
        jQuery(document).ready(function($) {
            function updatePreview(input) {
                const preview = $(input).closest('.arim-admin-field').find('.arim-admin-media-preview');
                const value = $(input).val();

                if (!preview.length) return;

                if (value) {
                    preview.html('<img src=\"' + value + '\" alt=\"Preview\">').show();
                } else {
                    preview.html('').hide();
                }
            }

            function renderSelectedProducts(wrapper) {
                const hiddenInput = wrapper.find('.arim-product-picker-hidden');
                const selectedBox = wrapper.find('.arim-product-picker-selected');
                let selected = [];

                try {
                    selected = JSON.parse(hiddenInput.attr('data-selected-products') || '[]');
                } catch (e) {
                    selected = [];
                }

                selectedBox.html('');

                selected.forEach(function(item) {
                    const chip = $('<div class=\"arim-product-picker-chip\"></div>');
                    const info = $('<div><strong></strong><span></span></div>');
                    info.find('strong').text(item.name);
                    info.find('span').text('ID: ' + item.id);
                    const removeBtn = $('<button type=\"button\" class=\"arim-product-picker-remove\">×</button>');
                    removeBtn.attr('data-id', item.id);
                    chip.append(info).append(removeBtn);
                    selectedBox.append(chip);
                });

                hiddenInput.val(selected.map(function(item) { return item.id; }).join(','));
            }

            function getSelectedProducts(wrapper) {
                try {
                    return JSON.parse(wrapper.find('.arim-product-picker-hidden').attr('data-selected-products') || '[]');
                } catch (e) {
                    return [];
                }
            }

            function setSelectedProducts(wrapper, products) {
                wrapper.find('.arim-product-picker-hidden').attr('data-selected-products', JSON.stringify(products));
                renderSelectedProducts(wrapper);
            }

            $('.arim-media-input').each(function() {
                updatePreview(this);
            });

            $('.arim-product-picker').each(function() {
                renderSelectedProducts($(this));
            });

            $(document).on('click', '.arim-media-upload', function(e) {
                e.preventDefault();

                const button = $(this);
                const fieldWrap = button.closest('.arim-admin-field');
                const input = fieldWrap.find('.arim-media-input');

                const frame = wp.media({
                    title: 'Görsel Seç',
                    button: {
                        text: 'Bu görseli kullan'
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    input.val(attachment.url).trigger('change').trigger('input');
                });

                frame.open();
            });

            $(document).on('click', '.arim-media-clear', function(e) {
                e.preventDefault();

                const fieldWrap = $(this).closest('.arim-admin-field');
                const input = fieldWrap.find('.arim-media-input');

                input.val('').trigger('change').trigger('input');
            });

            $(document).on('change input', '.arim-media-input', function() {
                updatePreview(this);
            });

            $('.arim-admin-tab-btn').on('click', function() {
                const tab = $(this).data('tab');

                $('.arim-admin-tab-btn').removeClass('is-active');
                $(this).addClass('is-active');

                $('.arim-admin-tab-panel').removeClass('is-active');
                $('.arim-admin-tab-panel[data-tab-panel=\"' + tab + '\"]').addClass('is-active');
            });

            function updateShowcasePreview() {
                $('.arim-preview-showcase-1-title').text($('input[name=\"arim_showcase_1_title\"]').val());
                $('.arim-preview-showcase-1-link').text($('input[name=\"arim_showcase_1_link_text\"]').val());
                $('.arim-preview-showcase-2-title').text($('input[name=\"arim_showcase_2_title\"]').val());
                $('.arim-preview-showcase-2-link').text($('input[name=\"arim_showcase_2_link_text\"]').val());
            }

            function updateHeroPreview() {
                $('.arim-preview-hero-badge').text($('input[name=\"arim_hero_main_badge\"]').val());
                $('.arim-preview-hero-title').text($('input[name=\"arim_hero_main_title\"]').val());
                $('.arim-preview-hero-text').text($('textarea[name=\"arim_hero_main_text\"]').val());
                $('.arim-preview-floating-1-label').text($('input[name=\"arim_hero_float_1_label\"]').val());
                $('.arim-preview-floating-1-title').text($('input[name=\"arim_hero_float_1_title\"]').val());
                $('.arim-preview-floating-1-text').text($('textarea[name=\"arim_hero_float_1_text\"]').val());
            }

            function updateMosaicPreview() {
                for (let i = 1; i <= 3; i++) {
                    $('.arim-preview-mosaic-' + i + '-badge').text($('input[name=\"arim_mosaic_' + i + '_badge\"]').val());
                    $('.arim-preview-mosaic-' + i + '-title').text($('input[name=\"arim_mosaic_' + i + '_title\"]').val());
                }
            }

            function updateCouponPreview() {
                for (let i = 1; i <= 4; i++) {
                    $('.arim-preview-coupon-' + i + '-value').text($('input[name=\"arim_coupon_' + i + '_value\"]').val());
                    $('.arim-preview-coupon-' + i + '-text').text($('textarea[name=\"arim_coupon_' + i + '_text\"]').val());
                }
            }

            let searchTimeout = null;

            $(document).on('input', '.arim-product-picker-search', function() {
                const input = $(this);
                const wrapper = input.closest('.arim-product-picker');
                const results = wrapper.find('.arim-product-picker-results');
                const query = input.val().trim();

                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    results.removeClass('is-active').html('');
                    return;
                }

                searchTimeout = setTimeout(function() {
                    $.post(arimHomepageAdmin.ajaxUrl, {
                        action: 'arim_homepage_product_search',
                        nonce: arimHomepageAdmin.nonce,
                        q: query
                    }, function(response) {
                        results.html('');

                        if (!response || !response.success || !response.data || !response.data.length) {
                            results.addClass('is-active').html('<div class=\"arim-product-picker-result\"><span>Sonuç bulunamadı</span></div>');
                            return;
                        }

                        response.data.forEach(function(item) {
                            const row = $('<div class=\"arim-product-picker-result\"></div>');
                            row.attr('data-id', item.id);
                            row.attr('data-name', item.name);
                            row.append('<strong>' + item.name + '</strong>');
                            row.append('<span>ID: ' + item.id + '</span>');
                            results.append(row);
                        });

                        results.addClass('is-active');
                    });
                }, 250);
            });

            $(document).on('click', '.arim-product-picker-result', function() {
                const row = $(this);
                const wrapper = row.closest('.arim-product-picker');
                const results = wrapper.find('.arim-product-picker-results');
                const searchInput = wrapper.find('.arim-product-picker-search');

                const product = {
                    id: parseInt(row.attr('data-id'), 10),
                    name: row.attr('data-name')
                };

                let selected = getSelectedProducts(wrapper);

                const exists = selected.some(function(item) {
                    return parseInt(item.id, 10) === product.id;
                });

                if (!exists) {
                    selected.push(product);
                    setSelectedProducts(wrapper, selected);
                }

                searchInput.val('');
                results.removeClass('is-active').html('');
            });

            $(document).on('click', '.arim-product-picker-remove', function() {
                const btn = $(this);
                const wrapper = btn.closest('.arim-product-picker');
                const id = parseInt(btn.attr('data-id'), 10);

                let selected = getSelectedProducts(wrapper);
                selected = selected.filter(function(item) {
                    return parseInt(item.id, 10) !== id;
                });

                setSelectedProducts(wrapper, selected);
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.arim-product-picker').length) {
                    $('.arim-product-picker-results').removeClass('is-active');
                }
            });

            $(document).on('input change', 'input, textarea, select', function() {
                updateHeroPreview();
                updateMosaicPreview();
                updateCouponPreview();
                updateShowcasePreview();
            });

            $('.arim-admin-tab-btn').first().trigger('click');
            updateHeroPreview();
            updateMosaicPreview();
            updateCouponPreview();
            updateShowcasePreview();
        });
    ");
}
add_action('admin_enqueue_scripts', 'arim_homepage_manager_admin_assets');


/**
 * AJAX ürün arama
 */
function arim_homepage_product_search_ajax() {
    check_ajax_referer('arim_homepage_product_search', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error([]);
    }

    $query = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';

    if (mb_strlen($query) < 2) {
        wp_send_json_success([]);
    }

    $products = wc_get_products([
        'status' => 'publish',
        'limit'  => 15,
        'search' => $query,
    ]);

    $results = [];

    if (!empty($products)) {
        foreach ($products as $product) {
            $results[] = [
                'id'   => $product->get_id(),
                'name' => $product->get_name(),
            ];
        }
    }

    wp_send_json_success($results);
}
add_action('wp_ajax_arim_homepage_product_search', 'arim_homepage_product_search_ajax');

/**
 * Ürün için gösterilecek marka adını döndürür.
 *
 * @param int $product_id Ürün kimliği.
 * @return string
 */
function arim_product_brand_name($product_id) {
    $brand = get_post_meta($product_id, 'brand', true);

    if (!$brand) {
        $terms = get_the_terms($product_id, 'product_brand');

        if (!empty($terms) && !is_wp_error($terms)) {
            $brand = $terms[0]->name;
        }
    }

    return is_string($brand) ? $brand : '';
}

/**
 * Ürün için gösterilecek mağaza adını döndürür.
 *
 * @param int $product_id Ürün kimliği.
 * @return string
 */
function arim_product_store_name($product_id) {
    $store = get_post_meta($product_id, 'store_name', true);

    if (!$store) {
        $store = __('ARIM Store', 'arim');
    }

    return is_string($store) ? $store : __('ARIM Store', 'arim');
}

/**
 * WooCommerce fiyat HTML çıktısını sade metne dönüştürür.
 *
 * @param WC_Product $product Ürün nesnesi.
 * @return string
 */
function arim_product_price_text($product) {
    if (!$product instanceof WC_Product) {
        return '';
    }

    return trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($product->get_price_html())));
}

/**
 * Ürün kartı için standart veri yapısı üretir.
 *
 * @param WC_Product $product Ürün nesnesi.
 * @return array<string, mixed>
 */
function arim_prepare_product_card_payload($product) {
    if (!$product instanceof WC_Product) {
        return [];
    }

    $product_id = $product->get_id();
    $image_id   = $product->get_image_id();
    $badge      = '';

    if ($product->is_on_sale()) {
        $badge = __('Fırsat', 'arim');
    } elseif ($product->is_featured()) {
        $badge = __('Öne Çıkan', 'arim');
    }

    return [
        'id'           => $product_id,
        'title'        => $product->get_name(),
        'url'          => get_permalink($product_id),
        'image'        => $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src(),
        'price'        => arim_product_price_text($product),
        'brand'        => arim_product_brand_name($product_id),
        'store'        => arim_product_store_name($product_id),
        'badge'        => $badge,
        'currentPrice' => (float) $product->get_price(),
        'regularPrice' => (float) $product->get_regular_price(),
    ];
}

/**
 * Ürün detay sayfası için teslimat vaat bilgisini döndürür.
 *
 * @param WC_Product $product Ürün nesnesi.
 * @hook arim_single_delivery_date_format Teslimat tarihini biçimlendirmek için PHP tarih formatı bekler. Örn: 'j F l', 'd.m.Y'.
 * @return array<string, string>
 */
function arim_single_product_delivery_details($product) {
    if (!$product instanceof WC_Product) {
        return [
            'badge' => __('Standart teslimat', 'arim'),
            'date'  => '',
            'note'  => __('Siparişin onaylandıktan sonra en kısa sürede hazırlanır.', 'arim'),
        ];
    }

    $is_in_stock        = $product->is_in_stock();
    $delivery_offset    = $is_in_stock ? 2 : 5;
    $delivery_timestamp = current_time('timestamp') + (DAY_IN_SECONDS * $delivery_offset);

    $date_format = (string) apply_filters('arim_single_delivery_date_format', 'j F l');

    return [
        'badge' => $is_in_stock ? __('Tahmini teslimat', 'arim') : __('Siparişe özel tedarik', 'arim'),
        'date'  => wp_date($date_format, $delivery_timestamp, wp_timezone()),
        'note'  => $is_in_stock
            ? __('Hızlı kargo ağıyla teslim edilir, sipariş durumun panelden takip edilebilir.', 'arim')
            : __('Ürün hazırlanır hazırlanmaz öncelikli gönderim planına alınır.', 'arim'),
    ];
}

/**
 * Ürün detay sayfasında gösterilecek kampanya kartlarını döndürür.
 *
 * @param int $limit Maksimum kampanya sayısı.
 * @return array<int, array<string, string>>
 */
function arim_single_product_campaigns($limit = 3) {
    $campaigns = [];
    $limit     = max(1, (int) $limit);

    for ($i = 1; $i <= 4; $i++) {
        $value = sanitize_text_field((string) arim_homepage_option("arim_coupon_{$i}_value", ''));
        $text  = sanitize_text_field((string) arim_homepage_option("arim_coupon_{$i}_text", ''));

        if ($value === '' && $text === '') {
            continue;
        }

        $campaigns[] = [
            'value' => $value,
            'text'  => $text,
        ];
    }

    return array_slice($campaigns, 0, $limit);
}

/**
 * Checkout sayfası için teslimat özetini döndürür.
 *
 * @return array<string, string|int>
 */
function arim_checkout_delivery_details() {
    $item_count     = 0;
    $product_count  = 0;
    $delivery_days  = 2;
    $delivery_badge = __('Tahmini teslimat', 'arim');
    $delivery_note  = __('Siparişin onaylandıktan sonra kargo adımları panelinden takip edilir.', 'arim');

    if (function_exists('WC') && WC()->cart) {
        $product_count = max(0, (int) WC()->cart->get_cart_contents_count());
        $item_count    = count((array) WC()->cart->get_cart());

        foreach ((array) WC()->cart->get_cart() as $cart_item) {
            $product = isset($cart_item['data']) ? $cart_item['data'] : null;

            if ($product instanceof WC_Product && !$product->is_in_stock()) {
                $delivery_days  = 4;
                $delivery_badge = __('Siparişe göre teslimat', 'arim');
                $delivery_note  = __('Sepetindeki bazı ürünler siparişe göre hazırlanır; kargo akışı hazır olduğunda bilgilendirme yapılır.', 'arim');
                break;
            }
        }
    }

    return [
        'badge'         => $delivery_badge,
        'date'          => wp_date((string) apply_filters('arim_single_delivery_date_format', 'j F l'), current_time('timestamp') + (DAY_IN_SECONDS * $delivery_days), wp_timezone()),
        'note'          => $delivery_note,
        'itemCount'     => $item_count,
        'productCount'  => $product_count,
        'supportWindow' => __('7/24 canlı destek', 'arim'),
    ];
}

function arim_live_search_min_chars() {
    return max(1, (int) apply_filters('arim_live_search_min_chars', 2));
}

function arim_live_search_debounce_ms() {
    return max(100, (int) apply_filters('arim_live_search_debounce_ms', 220));
}

function arim_live_search_max_query_length() {
    return max(10, (int) apply_filters('arim_live_search_max_query_length', 100));
}

function arim_require_post_request() {
    $request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_UNSAFE_RAW);
    $request_method = is_string($request_method) ? strtoupper(sanitize_text_field($request_method)) : '';

    if ($request_method !== 'POST') {
        wp_send_json_error([], 405);
    }
}

function arim_public_product_search_ajax() {
    arim_require_post_request();

    check_ajax_referer('arim_public_product_search', 'nonce');

    if (!function_exists('wc_get_products')) {
        wp_send_json_error([]);
    }

    $query = filter_input(INPUT_POST, 'q', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $query = is_string($query) ? sanitize_text_field(wp_unslash($query)) : '';
    $query = mb_substr($query, 0, arim_live_search_max_query_length());

    if (mb_strlen($query) < arim_live_search_min_chars()) {
        wp_send_json_success([
            'items'      => [],
            'resultsUrl' => add_query_arg([
                's'         => $query,
                'post_type' => 'product',
            ], home_url('/')),
        ]);
    }

    $products = wc_get_products([
        'status' => 'publish',
        'limit'  => 6,
        'search' => $query,
    ]);

    $results = [];

    foreach ((array) $products as $product) {
        if (!$product instanceof WC_Product) {
            continue;
        }

        $results[] = arim_prepare_product_card_payload($product);
    }

    wp_send_json_success([
        'items'      => $results,
        'resultsUrl' => add_query_arg([
            's'         => $query,
            'post_type' => 'product',
        ], home_url('/')),
    ]);
}
add_action('wp_ajax_arim_public_product_search', 'arim_public_product_search_ajax');
add_action('wp_ajax_nopriv_arim_public_product_search', 'arim_public_product_search_ajax');

/**
 * Kişiselleştirilmiş öneriler için ürünleri döndürür.
 */
function arim_personalized_recommendations_ajax() {
    arim_require_post_request();

    check_ajax_referer('arim_personalized_recommendations', 'nonce');

    if (!function_exists('wc_get_product')) {
        wp_send_json_error([]);
    }

    $raw_product_ids = filter_input(INPUT_POST, 'productIds', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $product_ids_json = is_string($raw_product_ids) ? html_entity_decode(wp_unslash($raw_product_ids), ENT_QUOTES, 'UTF-8') : '[]';
    $decoded_product_ids = json_decode($product_ids_json, true);
    $product_ids     = wp_parse_id_list(is_array($decoded_product_ids) ? $decoded_product_ids : []);
    $product_ids     = array_slice($product_ids, 0, 24);

    if (empty($product_ids)) {
        wp_send_json_success([
            'items' => [],
        ]);
    }

    $category_ids   = [];
    $brand_term_ids = [];
    $brand_names    = [];

    foreach ($product_ids as $product_id) {
        $category_ids = array_merge($category_ids, wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']));
        $brand_term_ids = array_merge($brand_term_ids, wp_get_post_terms($product_id, 'product_brand', ['fields' => 'ids']));

        $brand_name = arim_product_brand_name($product_id);
        if ($brand_name !== '') {
            $brand_names[] = $brand_name;
        }
    }

    $category_ids   = array_values(array_unique(array_filter(array_map('absint', $category_ids))));
    $brand_term_ids = array_values(array_unique(array_filter(array_map('absint', $brand_term_ids))));
    $brand_names    = array_values(array_unique(array_filter(array_map('sanitize_text_field', $brand_names))));
    $recommended_ids = [];

    $tax_query = [];

    if (!empty($category_ids) || !empty($brand_term_ids)) {
        $tax_query['relation'] = 'OR';

        if (!empty($category_ids)) {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_ids,
            ];
        }

        if (!empty($brand_term_ids)) {
            $tax_query[] = [
                'taxonomy' => 'product_brand',
                'field'    => 'term_id',
                'terms'    => $brand_term_ids,
            ];
        }
    }

    $primary_query_args = [
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'posts_per_page'      => 6,
        'post__not_in'        => $product_ids,
        'ignore_sticky_posts' => true,
        'fields'              => 'ids',
        'orderby'             => 'date',
        'order'               => 'DESC',
    ];

    if (!empty($tax_query)) {
        $primary_query_args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
    } elseif (!empty($brand_names)) {
        $primary_query_args['meta_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            [
                'key'     => 'brand',
                'value'   => $brand_names,
                'compare' => 'IN',
            ],
        ];
    }

    $recommended_ids = get_posts($primary_query_args);

    if (count($recommended_ids) < 6 && !empty($brand_names)) {
        $brand_query_args = [
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'posts_per_page'      => 6 - count($recommended_ids),
            'post__not_in'        => array_merge($product_ids, $recommended_ids),
            'ignore_sticky_posts' => true,
            'fields'              => 'ids',
            'meta_query'          => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
                [
                    'key'     => 'brand',
                    'value'   => $brand_names,
                    'compare' => 'IN',
                ],
            ],
            'orderby'             => 'date',
            'order'               => 'DESC',
        ];

        $recommended_ids = array_merge($recommended_ids, get_posts($brand_query_args));
    }

    if (count($recommended_ids) < 6) {
        $fallback_query_args = [
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'posts_per_page'      => 6 - count($recommended_ids),
            'post__not_in'        => array_merge($product_ids, $recommended_ids),
            'ignore_sticky_posts' => true,
            'fields'              => 'ids',
            'tax_query'           => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
                [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => ['featured'],
                ],
            ],
            'orderby'             => 'date',
            'order'               => 'DESC',
        ];

        $recommended_ids = array_merge($recommended_ids, get_posts($fallback_query_args));
    }

    $items = [];

    foreach (array_slice(array_values(array_unique(array_map('absint', $recommended_ids))), 0, 6) as $recommended_id) {
        $product = wc_get_product($recommended_id);
        if (!$product instanceof WC_Product) {
            continue;
        }

        $payload = arim_prepare_product_card_payload($product);
        if (!empty($payload)) {
            $items[] = $payload;
        }
    }

    wp_send_json_success([
        'items' => $items,
    ]);
}
add_action('wp_ajax_arim_personalized_recommendations', 'arim_personalized_recommendations_ajax');
add_action('wp_ajax_nopriv_arim_personalized_recommendations', 'arim_personalized_recommendations_ajax');


/**
 * Header sepet sayısı
 */
function arim_cart_count() {
    if (function_exists('WC') && WC()->cart) {
        return WC()->cart->get_cart_contents_count();
    }
    return 0;
}

function arim_shop_url() {
    if (function_exists('wc_get_page_permalink')) {
        return wc_get_page_permalink('shop');
    }

    return home_url('/shop');
}

function arim_account_url() {
    if (function_exists('wc_get_page_permalink')) {
        return wc_get_page_permalink('myaccount');
    }

    return wp_login_url();
}

function arim_cart_url() {
    if (function_exists('wc_get_cart_url')) {
        return wc_get_cart_url();
    }

    return home_url('/cart');
}

function arim_checkout_url() {
    if (function_exists('wc_get_checkout_url')) {
        return wc_get_checkout_url();
    }

    return home_url('/checkout');
}

function arim_favorites_url() {
    return add_query_arg('arim_favorites', '1', home_url('/'));
}

function arim_get_frontend_query_flag($key) {
    $value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    return is_string($value) ? sanitize_text_field($value) : '';
}

function arim_is_favorites_page() {
    if (is_admin()) {
        return false;
    }

    return arim_get_frontend_query_flag('arim_favorites') === '1';
}

function arim_favorites_template($template) {
    if (!arim_is_favorites_page()) {
        return $template;
    }

    $favorites_template = get_template_directory() . '/favorites-template.php';

    if (file_exists($favorites_template)) {
        return $favorites_template;
    }

    return $template;
}
add_filter('template_include', 'arim_favorites_template');

function arim_favorites_document_title_parts($title_parts) {
    if (!arim_is_favorites_page()) {
        return $title_parts;
    }

    $title_parts['title'] = __('Favorilerim', 'arim');

    return $title_parts;
}
add_filter('document_title_parts', 'arim_favorites_document_title_parts');

function arim_favorites_body_class($classes) {
    if (arim_is_favorites_page()) {
        $classes[] = 'arim-favorites-template';
    }

    return $classes;
}
add_filter('body_class', 'arim_favorites_body_class');


/**
 * WooCommerce ürün sayısı
 */
function arim_loop_shop_per_page($cols) {
    return 12;
}
add_filter('loop_shop_per_page', 'arim_loop_shop_per_page', 20);


/**
 * WooCommerce wrapper kaldır
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

function arim_woocommerce_wrapper_start() {
    echo '';
}

function arim_woocommerce_wrapper_end() {
    echo '';
}

add_action('woocommerce_before_main_content', 'arim_woocommerce_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'arim_woocommerce_wrapper_end', 10);


/**
 * Sidebar kaldır
 */
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);


/**
 * Shop kolon
 */
function arim_loop_columns() {
    return 4;
}
add_filter('loop_shop_columns', 'arim_loop_columns');


/**
 * Ürün listesi başlangıcı
 */
function arim_product_loop_start($html) {
    return '<ul class="products arim-products-grid">';
}
add_filter('woocommerce_product_loop_start', 'arim_product_loop_start');


/**
 * Related products
 */
function arim_related_products_args($args) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;
    return $args;
}
add_filter('woocommerce_output_related_products_args', 'arim_related_products_args');


/**
 * Cross sells
 */
function arim_cross_sells_total($limit) {
    return 4;
}
add_filter('woocommerce_cross_sells_total', 'arim_cross_sells_total');

function arim_cross_sells_columns($columns) {
    return 4;
}
add_filter('woocommerce_cross_sells_columns', 'arim_cross_sells_columns');


/**
 * Mağaza filtreleri
 */
function arim_custom_product_query($q) {
    if (is_admin() || !$q->is_main_query()) {
        return;
    }

    if (!(function_exists('is_shop') && (is_shop() || is_product_taxonomy()))) {
        return;
    }

    $meta_query = (array) $q->get('meta_query');
    $tax_query  = (array) $q->get('tax_query');

    if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
        $min_price = floatval(wp_unslash($_GET['min_price']));
        $meta_query[] = [
            'key'     => '_price',
            'value'   => $min_price,
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ];
    }

    if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
        $max_price = floatval(wp_unslash($_GET['max_price']));
        $meta_query[] = [
            'key'     => '_price',
            'value'   => $max_price,
            'compare' => '<=',
            'type'    => 'NUMERIC',
        ];
    }

    if (isset($_GET['stock_status']) && $_GET['stock_status'] === 'instock') {
        $meta_query[] = [
            'key'   => '_stock_status',
            'value' => 'instock',
        ];
    }

    if (isset($_GET['featured']) && $_GET['featured'] === '1') {
        $tax_query[] = [
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => ['featured'],
            'operator' => 'IN',
        ];
    }

    if (isset($_GET['on_sale']) && $_GET['on_sale'] === '1') {
        $sale_ids = wc_get_product_ids_on_sale();
        $q->set('post__in', !empty($sale_ids) ? $sale_ids : [0]);
    }

    if (!empty($meta_query)) {
        $q->set('meta_query', $meta_query);
    }

    if (!empty($tax_query)) {
        $q->set('tax_query', $tax_query);
    }
}
add_action('pre_get_posts', 'arim_custom_product_query');


/**
 * Homepage manager admin menu
 */
function arim_register_homepage_manager_menu() {
    add_menu_page(
        __('ARIM Homepage Manager', 'arim'),
        __('ARIM Homepage', 'arim'),
        'manage_options',
        'arim-homepage-manager',
        'arim_homepage_manager_page',
        'dashicons-images-alt2',
        61
    );
}
add_action('admin_menu', 'arim_register_homepage_manager_menu');


/**
 * Homepage manager save
 */
function arim_save_homepage_manager() {
    if (!isset($_POST['arim_homepage_manager_nonce'])) {
        return;
    }

    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['arim_homepage_manager_nonce'])), 'arim_save_homepage_manager')) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $fields = [
        'arim_section_show_hero',
        'arim_section_show_coupons',
        'arim_section_show_mosaic',
        'arim_section_show_showcase_1',
        'arim_section_show_showcase_2',
        'arim_section_show_showcase_3',
        'arim_section_show_seo',

        'arim_section_order_hero',
        'arim_section_order_coupons',
        'arim_section_order_slider',
        'arim_section_order_mosaic',
        'arim_section_order_showcase_1',
        'arim_section_order_showcase_2',
        'arim_section_order_showcase_3',
        'arim_section_order_seo',

        'arim_hero_main_badge',
        'arim_hero_main_title',
        'arim_hero_main_text',
        'arim_hero_main_link',
        'arim_hero_main_image',
        'arim_hero_main_image_mode',

        'arim_hero_point_1',
        'arim_hero_point_2',
        'arim_hero_point_3',

        'arim_hero_chip_1',
        'arim_hero_chip_2',
        'arim_hero_chip_3',
        'arim_hero_chip_4',

        'arim_hero_box_1_badge',
        'arim_hero_box_1_title',
        'arim_hero_box_1_text',
        'arim_hero_box_1_link',
        'arim_hero_box_1_image',
        'arim_hero_box_1_image_mode',

        'arim_hero_box_2_badge',
        'arim_hero_box_2_title',
        'arim_hero_box_2_text',
        'arim_hero_box_2_link',
        'arim_hero_box_2_image',
        'arim_hero_box_2_image_mode',

        'arim_hero_float_1_label',
        'arim_hero_float_1_title',
        'arim_hero_float_1_text',

        'arim_hero_float_2_label',
        'arim_hero_float_2_title',
        'arim_hero_float_2_text',

        'arim_mosaic_1_badge',
        'arim_mosaic_1_title',
        'arim_mosaic_1_link',
        'arim_mosaic_1_image',
        'arim_mosaic_1_image_mode',

        'arim_mosaic_2_badge',
        'arim_mosaic_2_title',
        'arim_mosaic_2_link',
        'arim_mosaic_2_image',
        'arim_mosaic_2_image_mode',

        'arim_mosaic_3_badge',
        'arim_mosaic_3_title',
        'arim_mosaic_3_link',
        'arim_mosaic_3_image',
        'arim_mosaic_3_image_mode',

        'arim_mosaic_4_badge',
        'arim_mosaic_4_title',
        'arim_mosaic_4_link',
        'arim_mosaic_4_image',
        'arim_mosaic_4_image_mode',

        'arim_mosaic_5_badge',
        'arim_mosaic_5_title',
        'arim_mosaic_5_link',
        'arim_mosaic_5_image',
        'arim_mosaic_5_image_mode',

        'arim_mosaic_6_badge',
        'arim_mosaic_6_title',
        'arim_mosaic_6_link',
        'arim_mosaic_6_image',
        'arim_mosaic_6_image_mode',

        'arim_coupon_1_value',
        'arim_coupon_1_text',
        'arim_coupon_2_value',
        'arim_coupon_2_text',
        'arim_coupon_3_value',
        'arim_coupon_3_text',
        'arim_coupon_4_value',
        'arim_coupon_4_text',

        'arim_mixed_promo_1_badge',
        'arim_mixed_promo_1_title',
        'arim_mixed_promo_1_text',

        'arim_mixed_promo_2_badge',
        'arim_mixed_promo_2_title',
        'arim_mixed_promo_2_text',

        'arim_showcase_1_title',
        'arim_showcase_1_link_text',
        'arim_showcase_1_link_url',
        'arim_showcase_1_source',
        'arim_showcase_1_category_slug',
        'arim_showcase_1_limit',
        'arim_showcase_1_include_children',
        'arim_showcase_1_manual_ids',
        'arim_showcase_1_preset',

        'arim_showcase_2_title',
        'arim_showcase_2_link_text',
        'arim_showcase_2_link_url',
        'arim_showcase_2_source',
        'arim_showcase_2_category_slug',
        'arim_showcase_2_limit',
        'arim_showcase_2_include_children',
        'arim_showcase_2_manual_ids',
        'arim_showcase_2_preset',

        'arim_showcase_3_title',
        'arim_showcase_3_link_text',
        'arim_showcase_3_link_url',
        'arim_showcase_3_source',
        'arim_showcase_3_category_slug',
        'arim_showcase_3_limit',
        'arim_showcase_3_include_children',
        'arim_showcase_3_manual_ids',
        'arim_showcase_3_preset',
    ];

    $checkbox_fields = [
        'arim_section_show_hero',
        'arim_section_show_coupons',
        'arim_section_show_mosaic',
        'arim_section_show_showcase_1',
        'arim_section_show_showcase_2',
        'arim_section_show_showcase_3',
        'arim_section_show_seo',
        'arim_showcase_1_include_children',
        'arim_showcase_2_include_children',
        'arim_showcase_3_include_children',
    ];

    $numeric_fields = [
        'arim_section_order_hero',
        'arim_section_order_coupons',
        'arim_section_order_slider',
        'arim_section_order_mosaic',
        'arim_section_order_showcase_1',
        'arim_section_order_showcase_2',
        'arim_section_order_showcase_3',
        'arim_section_order_seo',
        'arim_showcase_1_limit',
        'arim_showcase_2_limit',
        'arim_showcase_3_limit',
    ];

    $data = [];

    foreach ($fields as $field) {
        if (in_array($field, $checkbox_fields, true)) {
            $data[$field] = isset($_POST[$field]) ? '1' : '0';
            continue;
        }

        $value = isset($_POST[$field]) ? wp_unslash($_POST[$field]) : '';

        if (in_array($field, $numeric_fields, true)) {
            $data[$field] = max(1, intval($value));
            continue;
        }

        if (in_array($field, ['arim_showcase_1_manual_ids', 'arim_showcase_2_manual_ids', 'arim_showcase_3_manual_ids'], true)) {
            $clean = preg_replace('/[^0-9,]/', '', (string) $value);
            $data[$field] = sanitize_text_field($clean);
            continue;
        }

        $data[$field] = is_string($value) ? sanitize_text_field($value) : '';
    }

    update_option('arim_homepage_manager_data', $data);

    wp_safe_redirect(admin_url('admin.php?page=arim-homepage-manager&updated=1'));
    exit;
}
add_action('admin_init', 'arim_save_homepage_manager');


/**
 * Homepage manager helper
 */
function arim_homepage_option($key, $default = '') {
    $data = get_option('arim_homepage_manager_data', []);
    return isset($data[$key]) && $data[$key] !== '' ? $data[$key] : $default;
}


/**
 * Homepage manager image field
 */
function arim_admin_image_field($label, $name, $value = '') {
    ?>
    <div class="arim-admin-field">
        <label><?php echo esc_html($label); ?></label>
        <input type="text" class="arim-media-input" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>">
        <div class="arim-admin-media-row">
            <button class="button button-secondary arim-media-upload" type="button"><?php esc_html_e('Görsel Seç', 'arim'); ?></button>
            <button class="button button-link-delete arim-media-clear" type="button"><?php esc_html_e('Temizle', 'arim'); ?></button>
        </div>
        <div class="arim-admin-media-preview" <?php echo $value ? '' : 'style="display:none;"'; ?>>
            <?php if ($value) : ?>
                <img src="<?php echo esc_url($value); ?>" alt="">
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function arim_admin_select_field($label, $name, $value, $options) {
    ?>
    <div class="arim-admin-field">
        <label><?php echo esc_html($label); ?></label>
        <select name="<?php echo esc_attr($name); ?>">
            <?php foreach ($options as $option_value => $option_label) : ?>
                <option value="<?php echo esc_attr($option_value); ?>" <?php selected($value, $option_value); ?>>
                    <?php echo esc_html($option_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}

function arim_admin_product_cat_dropdown($label, $name, $selected_slug = '') {
    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ]);
    ?>
    <div class="arim-admin-field">
        <label><?php echo esc_html($label); ?></label>
        <select name="<?php echo esc_attr($name); ?>">
            <option value=""><?php esc_html_e('Kategori seç', 'arim'); ?></option>
            <?php if (!empty($terms) && !is_wp_error($terms)) : ?>
                <?php foreach ($terms as $term) : ?>
                    <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected_slug, $term->slug); ?>>
                        <?php echo esc_html($term->name . ' (' . $term->slug . ')'); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <?php
}

function arim_admin_product_picker_field($label, $name, $value = '') {
    $ids = array_filter(array_map('intval', explode(',', (string) $value)));
    $selected_products = [];

    if (!empty($ids)) {
        $products = wc_get_products([
            'status'  => 'publish',
            'include' => $ids,
            'limit'   => count($ids),
            'orderby' => 'include',
        ]);

        foreach ($products as $product) {
            $selected_products[] = [
                'id'   => $product->get_id(),
                'name' => $product->get_name(),
            ];
        }
    }
    ?>
    <div class="arim-admin-field">
        <label><?php echo esc_html($label); ?></label>

        <div class="arim-product-picker">
            <input type="text" class="arim-product-picker-search" placeholder="<?php esc_attr_e('Ürün adıyla ara...', 'arim'); ?>">
            <div class="arim-product-picker-results"></div>
            <div class="arim-product-picker-selected"></div>
            <input
                type="hidden"
                class="arim-product-picker-hidden"
                name="<?php echo esc_attr($name); ?>"
                value="<?php echo esc_attr($value); ?>"
                data-selected-products="<?php echo esc_attr(wp_json_encode($selected_products)); ?>"
            >
            <div class="arim-product-picker-help">
                <?php esc_html_e('Ürün ara, listeden seç, seçilen ürünler otomatik olarak saklanır.', 'arim'); ?>
            </div>
        </div>
    </div>
    <?php
}


/**
 * Homepage manager page
 */
function arim_homepage_manager_page() {
    $image_mode_options_full = [
        'background' => __('Arka plan odaklı', 'arim'),
        'cover'      => __('Daha baskın / kapak görsel', 'arim'),
        'soft'       => __('Yumuşak / hafif destek', 'arim'),
        'visual'     => __('Tam görsel odaklı', 'arim'),
    ];

    $image_mode_options_box = [
        'background' => __('Arka plan odaklı', 'arim'),
        'soft'       => __('Yumuşak / hafif destek', 'arim'),
    ];

    $showcase_source_options = [
        'latest'     => __('En Yeni Ürünler', 'arim'),
        'featured'   => __('Öne Çıkan Ürünler', 'arim'),
        'on_sale'    => __('İndirimli Ürünler', 'arim'),
        'popularity' => __('Popüler / Çok Satanlar', 'arim'),
        'category'   => __('Belirli Kategori', 'arim'),
        'manual'     => __('Manuel Ürün Listesi', 'arim'),
    ];

    $showcase_preset_1 = [
        'orange' => __('Turuncu Yoğun', 'arim'),
        'light'  => __('Açık Yüzey', 'arim'),
        'clean'  => __('Temiz Beyaz', 'arim'),
    ];

    $showcase_preset_2 = [
        'pink'   => __('Pembe Yoğun', 'arim'),
        'purple' => __('Mor Vurgu', 'arim'),
        'dark'   => __('Koyu Vitrin', 'arim'),
    ];

    $showcase_preset_3 = [
        'clean'       => __('Temiz Beyaz', 'arim'),
        'gray'        => __('Gri Yüzey', 'arim'),
        'soft-orange' => __('Yumuşak Turuncu', 'arim'),
    ];
    ?>
    <div class="wrap arim-admin-wrap">
        <div class="arim-admin-hero">
            <h1><?php esc_html_e('ARIM Homepage Manager', 'arim'); ?></h1>
            <p><?php esc_html_e('Anasayfanın üst kampanya alanlarını, hero yüzeyini, floating kartları, mosaic kartlarını, kuponları, mixed showcase promo kutularını ve vitrin başlıklarını tek yerden yönet. Section sıralama sistemi ile bölümlerin akışını panelden değiştirebilirsin.', 'arim'); ?></p>
        </div>

        <?php if (isset($_GET['updated'])) : ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Homepage ayarları kaydedildi.', 'arim'); ?></p>
            </div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field('arim_save_homepage_manager', 'arim_homepage_manager_nonce'); ?>

            <div class="arim-admin-tabs">
                <button type="button" class="arim-admin-tab-btn" data-tab="visibility"><?php esc_html_e('Bölümler', 'arim'); ?></button>
                <button type="button" class="arim-admin-tab-btn" data-tab="hero"><?php esc_html_e('Hero', 'arim'); ?></button>
                <button type="button" class="arim-admin-tab-btn" data-tab="floating"><?php esc_html_e('Floating', 'arim'); ?></button>
                <button type="button" class="arim-admin-tab-btn" data-tab="mosaic"><?php esc_html_e('Mosaic', 'arim'); ?></button>
                <button type="button" class="arim-admin-tab-btn" data-tab="coupons"><?php esc_html_e('Kuponlar', 'arim'); ?></button>
                <button type="button" class="arim-admin-tab-btn" data-tab="mixed"><?php esc_html_e('Mixed Promo', 'arim'); ?></button>
                <button type="button" class="arim-admin-tab-btn" data-tab="showcase"><?php esc_html_e('Showcase', 'arim'); ?></button>
            </div>

            <div class="arim-admin-tab-panel" data-tab-panel="visibility">
                <h2 class="arim-admin-section-title"><?php esc_html_e('Bölüm Görünürlüğü', 'arim'); ?></h2>
                <div class="arim-admin-toggle-grid">
                    <div class="arim-admin-toggle-card">
                        <label><input type="checkbox" name="arim_section_show_hero" value="1" <?php checked(arim_homepage_option('arim_section_show_hero', '1'), '1'); ?>> <?php esc_html_e('Hero Alanı Göster', 'arim'); ?></label>
                        <p><?php esc_html_e('Ana hero yüzeyi ve sağ promo kutularını gösterir veya gizler.', 'arim'); ?></p>
                    </div>

                    <div class="arim-admin-toggle-card">
                        <label><input type="checkbox" name="arim_section_show_coupons" value="1" <?php checked(arim_homepage_option('arim_section_show_coupons', '1'), '1'); ?>> <?php esc_html_e('Kupon Strip Göster', 'arim'); ?></label>
                        <p><?php esc_html_e('Yüzde kampanya kutularının bulunduğu kupon satırını yönetir.', 'arim'); ?></p>
                    </div>

                    <div class="arim-admin-toggle-card">
                        <label><input type="checkbox" name="arim_section_show_mosaic" value="1" <?php checked(arim_homepage_option('arim_section_show_mosaic', '1'), '1'); ?>> <?php esc_html_e('Mosaic Alanı Göster', 'arim'); ?></label>
                        <p><?php esc_html_e('Renkli kampanya kartlarının bulunduğu mosaic bölümünü kontrol eder.', 'arim'); ?></p>
                    </div>

                    <div class="arim-admin-toggle-card">
                        <label><input type="checkbox" name="arim_section_show_showcase_1" value="1" <?php checked(arim_homepage_option('arim_section_show_showcase_1', '1'), '1'); ?>> <?php esc_html_e('İlk Showcase Göster', 'arim'); ?></label>
                        <p><?php esc_html_e('İlk ürün vitrini alanını açar veya kapatır.', 'arim'); ?></p>
                    </div>

                    <div class="arim-admin-toggle-card">
                        <label><input type="checkbox" name="arim_section_show_showcase_2" value="1" <?php checked(arim_homepage_option('arim_section_show_showcase_2', '1'), '1'); ?>> <?php esc_html_e('İkinci Showcase Göster', 'arim'); ?></label>
                        <p><?php esc_html_e('Mixed showcase ve ikinci vitrin bölümünü açar veya kapatır.', 'arim'); ?></p>
                    </div>

                    <div class="arim-admin-toggle-card">
                        <label><input type="checkbox" name="arim_section_show_showcase_3" value="1" <?php checked(arim_homepage_option('arim_section_show_showcase_3', '1'), '1'); ?>> <?php esc_html_e('Üçüncü Showcase Göster', 'arim'); ?></label>
                        <p><?php esc_html_e('Ekstra ürün vitrini alanını açar veya kapatır.', 'arim'); ?></p>
                    </div>

                    <div class="arim-admin-toggle-card">
                        <label><input type="checkbox" name="arim_section_show_seo" value="1" <?php checked(arim_homepage_option('arim_section_show_seo', '1'), '1'); ?>> <?php esc_html_e('SEO Metin Alanı Göster', 'arim'); ?></label>
                        <p><?php esc_html_e('Alt taraftaki açıklama/SEO içerik kutusunu gösterir veya gizler.', 'arim'); ?></p>
                    </div>
                </div>

                <h2 class="arim-admin-section-title" style="margin-top:28px;"><?php esc_html_e('Bölüm Sıraları', 'arim'); ?></h2>
                <div class="arim-order-grid">
                    <div class="arim-order-card">
                        <strong><?php esc_html_e('Hero', 'arim'); ?></strong>
                        <input type="number" min="1" name="arim_section_order_hero" value="<?php echo esc_attr(arim_homepage_option('arim_section_order_hero', '10')); ?>">
                    </div>
                    <div class="arim-order-card">
                        <strong><?php esc_html_e('Kuponlar', 'arim'); ?></strong>
                        <input type="number" min="1" name="arim_section_order_coupons" value="<?php echo esc_attr(arim_homepage_option('arim_section_order_coupons', '20')); ?>">
                    </div>
                    <div class="arim-order-card">
                        <strong><?php esc_html_e('Popüler Slider', 'arim'); ?></strong>
                        <input type="number" min="1" name="arim_section_order_slider" value="<?php echo esc_attr(arim_homepage_option('arim_section_order_slider', '30')); ?>">
                    </div>
                    <div class="arim-order-card">
                        <strong><?php esc_html_e('Mosaic', 'arim'); ?></strong>
                        <input type="number" min="1" name="arim_section_order_mosaic" value="<?php echo esc_attr(arim_homepage_option('arim_section_order_mosaic', '40')); ?>">
                    </div>
                    <div class="arim-order-card">
                        <strong><?php esc_html_e('Showcase 1', 'arim'); ?></strong>
                        <input type="number" min="1" name="arim_section_order_showcase_1" value="<?php echo esc_attr(arim_homepage_option('arim_section_order_showcase_1', '50')); ?>">
                    </div>
                    <div class="arim-order-card">
                        <strong><?php esc_html_e('Showcase 2', 'arim'); ?></strong>
                        <input type="number" min="1" name="arim_section_order_showcase_2" value="<?php echo esc_attr(arim_homepage_option('arim_section_order_showcase_2', '60')); ?>">
                    </div>
                    <div class="arim-order-card">
                        <strong><?php esc_html_e('Showcase 3', 'arim'); ?></strong>
                        <input type="number" min="1" name="arim_section_order_showcase_3" value="<?php echo esc_attr(arim_homepage_option('arim_section_order_showcase_3', '70')); ?>">
                    </div>
                    <div class="arim-order-card">
                        <strong><?php esc_html_e('SEO Metni', 'arim'); ?></strong>
                        <input type="number" min="1" name="arim_section_order_seo" value="<?php echo esc_attr(arim_homepage_option('arim_section_order_seo', '80')); ?>">
                    </div>
                </div>
            </div>

            <div class="arim-admin-tab-panel" data-tab-panel="hero">
                <h2 class="arim-admin-section-title"><?php esc_html_e('Hero Alanı', 'arim'); ?></h2>
                <div class="arim-admin-grid">
                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Hero Main', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Badge</label><input type="text" name="arim_hero_main_badge" value="<?php echo esc_attr(arim_homepage_option('arim_hero_main_badge', 'Yeni Sezon')); ?>"></div>
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_hero_main_title" value="<?php echo esc_attr(arim_homepage_option('arim_hero_main_title', 'Trend ürünleri keşfet ve vitrini güçlendir')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_hero_main_text"><?php echo esc_textarea(arim_homepage_option('arim_hero_main_text', 'Yoğun, modern ve marketplace hissi veren güçlü kampanya yüzeyi ile öne çıkan ürünlerini sergile.')); ?></textarea></div>
                            <div class="arim-admin-field"><label>Link</label><input type="text" name="arim_hero_main_link" value="<?php echo esc_attr(arim_homepage_option('arim_hero_main_link', home_url('/shop'))); ?>"></div>
                            <?php arim_admin_image_field('Arka Plan Görsel URL', 'arim_hero_main_image', arim_homepage_option('arim_hero_main_image', '')); ?>
                            <?php arim_admin_select_field('Görsel Kullanım Tipi', 'arim_hero_main_image_mode', arim_homepage_option('arim_hero_main_image_mode', 'background'), $image_mode_options_full); ?>
                        </div>

                        <div class="arim-admin-preview-wrap">
                            <h3><?php esc_html_e('Canlı Önizleme', 'arim'); ?></h3>
                            <div class="arim-preview-hero">
                                <span class="arim-preview-hero-badge"><?php echo esc_html(arim_homepage_option('arim_hero_main_badge', 'Yeni Sezon')); ?></span>
                                <div class="arim-preview-hero-title"><?php echo esc_html(arim_homepage_option('arim_hero_main_title', 'Trend ürünleri keşfet ve vitrini güçlendir')); ?></div>
                                <div class="arim-preview-hero-text"><?php echo esc_html(arim_homepage_option('arim_hero_main_text', 'Yoğun, modern ve marketplace hissi veren güçlü kampanya yüzeyi ile öne çıkan ürünlerini sergile.')); ?></div>
                                <div class="arim-preview-floating">
                                    <span class="label arim-preview-floating-1-label"><?php echo esc_html(arim_homepage_option('arim_hero_float_1_label', 'Kampanya')); ?></span>
                                    <strong class="arim-preview-floating-1-title"><?php echo esc_html(arim_homepage_option('arim_hero_float_1_title', '%30')); ?></strong>
                                    <small class="arim-preview-floating-1-text"><?php echo esc_html(arim_homepage_option('arim_hero_float_1_text', 'Sepette ekstra indirim')); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Hero Mini Points', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Point 1</label><input type="text" name="arim_hero_point_1" value="<?php echo esc_attr(arim_homepage_option('arim_hero_point_1', 'Hızlı Teslimat')); ?>"></div>
                            <div class="arim-admin-field"><label>Point 2</label><input type="text" name="arim_hero_point_2" value="<?php echo esc_attr(arim_homepage_option('arim_hero_point_2', 'Güvenli Ödeme')); ?>"></div>
                            <div class="arim-admin-field"><label>Point 3</label><input type="text" name="arim_hero_point_3" value="<?php echo esc_attr(arim_homepage_option('arim_hero_point_3', 'Seçili Ürünlerde Fırsat')); ?>"></div>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Hero Altı Hızlı Chip Bar', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Chip 1</label><input type="text" name="arim_hero_chip_1" value="<?php echo esc_attr(arim_homepage_option('arim_hero_chip_1', 'Sepette Ekstra İndirim')); ?>"></div>
                            <div class="arim-admin-field"><label>Chip 2</label><input type="text" name="arim_hero_chip_2" value="<?php echo esc_attr(arim_homepage_option('arim_hero_chip_2', 'Hızlı Teslimat')); ?>"></div>
                            <div class="arim-admin-field"><label>Chip 3</label><input type="text" name="arim_hero_chip_3" value="<?php echo esc_attr(arim_homepage_option('arim_hero_chip_3', 'Çok Satanlar')); ?>"></div>
                            <div class="arim-admin-field"><label>Chip 4</label><input type="text" name="arim_hero_chip_4" value="<?php echo esc_attr(arim_homepage_option('arim_hero_chip_4', 'Yeni Sezon Fırsatları')); ?>"></div>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Hero Sağ Kutusu 1', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Badge</label><input type="text" name="arim_hero_box_1_badge" value="<?php echo esc_attr(arim_homepage_option('arim_hero_box_1_badge', 'Kampanya')); ?>"></div>
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_hero_box_1_title" value="<?php echo esc_attr(arim_homepage_option('arim_hero_box_1_title', 'Ücretsiz Kargo Fırsatı')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_hero_box_1_text"><?php echo esc_textarea(arim_homepage_option('arim_hero_box_1_text', 'Seçili ürünlerde avantaj')); ?></textarea></div>
                            <div class="arim-admin-field"><label>Link</label><input type="text" name="arim_hero_box_1_link" value="<?php echo esc_attr(arim_homepage_option('arim_hero_box_1_link', home_url('/shop'))); ?>"></div>
                            <?php arim_admin_image_field('Arka Plan Görsel URL', 'arim_hero_box_1_image', arim_homepage_option('arim_hero_box_1_image', '')); ?>
                            <?php arim_admin_select_field('Görsel Kullanım Tipi', 'arim_hero_box_1_image_mode', arim_homepage_option('arim_hero_box_1_image_mode', 'background'), $image_mode_options_box); ?>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Hero Sağ Kutusu 2', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Badge</label><input type="text" name="arim_hero_box_2_badge" value="<?php echo esc_attr(arim_homepage_option('arim_hero_box_2_badge', 'Popüler')); ?>"></div>
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_hero_box_2_title" value="<?php echo esc_attr(arim_homepage_option('arim_hero_box_2_title', 'En Çok İlgi Görenler')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_hero_box_2_text"><?php echo esc_textarea(arim_homepage_option('arim_hero_box_2_text', 'Yoğun ilgi gören ürünler')); ?></textarea></div>
                            <div class="arim-admin-field"><label>Link</label><input type="text" name="arim_hero_box_2_link" value="<?php echo esc_attr(arim_homepage_option('arim_hero_box_2_link', home_url('/shop'))); ?>"></div>
                            <?php arim_admin_image_field('Arka Plan Görsel URL', 'arim_hero_box_2_image', arim_homepage_option('arim_hero_box_2_image', '')); ?>
                            <?php arim_admin_select_field('Görsel Kullanım Tipi', 'arim_hero_box_2_image_mode', arim_homepage_option('arim_hero_box_2_image_mode', 'background'), $image_mode_options_box); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="arim-admin-tab-panel" data-tab-panel="floating">
                <h2 class="arim-admin-section-title"><?php esc_html_e('Floating Kartlar', 'arim'); ?></h2>
                <div class="arim-admin-grid">
                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Floating Card 1', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Label</label><input type="text" name="arim_hero_float_1_label" value="<?php echo esc_attr(arim_homepage_option('arim_hero_float_1_label', 'Kampanya')); ?>"></div>
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_hero_float_1_title" value="<?php echo esc_attr(arim_homepage_option('arim_hero_float_1_title', '%30')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_hero_float_1_text"><?php echo esc_textarea(arim_homepage_option('arim_hero_float_1_text', 'Sepette ekstra indirim')); ?></textarea></div>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Floating Card 2', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Label</label><input type="text" name="arim_hero_float_2_label" value="<?php echo esc_attr(arim_homepage_option('arim_hero_float_2_label', 'Popüler')); ?>"></div>
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_hero_float_2_title" value="<?php echo esc_attr(arim_homepage_option('arim_hero_float_2_title', 'Yeni Koleksiyon')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_hero_float_2_text"><?php echo esc_textarea(arim_homepage_option('arim_hero_float_2_text', 'Sezonun öne çıkan ürünleri')); ?></textarea></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="arim-admin-tab-panel" data-tab-panel="mosaic">
                <h2 class="arim-admin-section-title"><?php esc_html_e('Mosaic Kampanya Kartları', 'arim'); ?></h2>
                <div class="arim-admin-grid">
                    <?php for ($i = 1; $i <= 6; $i++) : ?>
                        <div class="arim-admin-card">
                            <h2><?php echo esc_html(sprintf(__('Mosaic Kart %d', 'arim'), $i)); ?></h2>
                            <div class="arim-admin-fields">
                                <div class="arim-admin-field"><label>Badge</label><input type="text" name="arim_mosaic_<?php echo esc_attr($i); ?>_badge" value="<?php echo esc_attr(arim_homepage_option("arim_mosaic_{$i}_badge", '')); ?>"></div>
                                <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_mosaic_<?php echo esc_attr($i); ?>_title" value="<?php echo esc_attr(arim_homepage_option("arim_mosaic_{$i}_title", '')); ?>"></div>
                                <div class="arim-admin-field"><label>Link</label><input type="text" name="arim_mosaic_<?php echo esc_attr($i); ?>_link" value="<?php echo esc_attr(arim_homepage_option("arim_mosaic_{$i}_link", home_url('/shop'))); ?>"></div>
                                <?php arim_admin_image_field('Görsel URL', "arim_mosaic_{$i}_image", arim_homepage_option("arim_mosaic_{$i}_image", '')); ?>
                                <?php arim_admin_select_field('Görsel Kullanım Tipi', "arim_mosaic_{$i}_image_mode", arim_homepage_option("arim_mosaic_{$i}_image_mode", 'background'), $image_mode_options_full); ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="arim-admin-preview-wrap">
                    <h3><?php esc_html_e('Canlı Önizleme', 'arim'); ?></h3>
                    <div class="arim-preview-mosaic-grid">
                        <div class="arim-preview-mosaic-item">
                            <span class="arim-preview-mosaic-1-badge"><?php echo esc_html(arim_homepage_option('arim_mosaic_1_badge', 'Flaş Fırsat')); ?></span>
                            <strong class="arim-preview-mosaic-1-title"><?php echo esc_html(arim_homepage_option('arim_mosaic_1_title', 'Bugüne özel seçili kampanyalar')); ?></strong>
                        </div>
                        <div class="arim-preview-mosaic-item" style="background:linear-gradient(135deg,#ff758c,#ff7eb3);">
                            <span class="arim-preview-mosaic-2-badge"><?php echo esc_html(arim_homepage_option('arim_mosaic_2_badge', 'Kadın')); ?></span>
                            <strong class="arim-preview-mosaic-2-title"><?php echo esc_html(arim_homepage_option('arim_mosaic_2_title', 'Stilini yenile')); ?></strong>
                        </div>
                        <div class="arim-preview-mosaic-item" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                            <span class="arim-preview-mosaic-3-badge"><?php echo esc_html(arim_homepage_option('arim_mosaic_3_badge', 'Kozmetik')); ?></span>
                            <strong class="arim-preview-mosaic-3-title"><?php echo esc_html(arim_homepage_option('arim_mosaic_3_title', 'Günlük bakım seçkisi')); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="arim-admin-tab-panel" data-tab-panel="coupons">
                <h2 class="arim-admin-section-title"><?php esc_html_e('Kupon Strip', 'arim'); ?></h2>
                <div class="arim-admin-grid">
                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Kupon 1', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Değer</label><input type="text" name="arim_coupon_1_value" value="<?php echo esc_attr(arim_homepage_option('arim_coupon_1_value', '%35')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_coupon_1_text"><?php echo esc_textarea(arim_homepage_option('arim_coupon_1_text', 'Sepette ekstra indirim')); ?></textarea></div>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Kupon 2', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Değer</label><input type="text" name="arim_coupon_2_value" value="<?php echo esc_attr(arim_homepage_option('arim_coupon_2_value', '%10')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_coupon_2_text"><?php echo esc_textarea(arim_homepage_option('arim_coupon_2_text', 'Yeni üyeye özel fırsat')); ?></textarea></div>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Kupon 3', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Değer</label><input type="text" name="arim_coupon_3_value" value="<?php echo esc_attr(arim_homepage_option('arim_coupon_3_value', '%30')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_coupon_3_text"><?php echo esc_textarea(arim_homepage_option('arim_coupon_3_text', 'Seçili kategorilerde avantaj')); ?></textarea></div>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Kupon 4', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Değer</label><input type="text" name="arim_coupon_4_value" value="<?php echo esc_attr(arim_homepage_option('arim_coupon_4_value', '%50')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_coupon_4_text"><?php echo esc_textarea(arim_homepage_option('arim_coupon_4_text', 'Kampanya ürünlerinde fırsat')); ?></textarea></div>
                        </div>
                    </div>
                </div>

                <div class="arim-admin-preview-wrap">
                    <h3><?php esc_html_e('Canlı Önizleme', 'arim'); ?></h3>
                    <div class="arim-preview-coupon-grid">
                        <div class="arim-preview-coupon-item" style="background:linear-gradient(135deg,#ff9342,#ff7e2f);">
                            <strong class="arim-preview-coupon-1-value"><?php echo esc_html(arim_homepage_option('arim_coupon_1_value', '%35')); ?></strong>
                            <span class="arim-preview-coupon-1-text"><?php echo esc_html(arim_homepage_option('arim_coupon_1_text', 'Sepette ekstra indirim')); ?></span>
                        </div>
                        <div class="arim-preview-coupon-item" style="background:linear-gradient(135deg,#43c06b,#24a758);">
                            <strong class="arim-preview-coupon-2-value"><?php echo esc_html(arim_homepage_option('arim_coupon_2_value', '%10')); ?></strong>
                            <span class="arim-preview-coupon-2-text"><?php echo esc_html(arim_homepage_option('arim_coupon_2_text', 'Yeni üyeye özel fırsat')); ?></span>
                        </div>
                        <div class="arim-preview-coupon-item" style="background:linear-gradient(135deg,#4da3ff,#2f7cff);">
                            <strong class="arim-preview-coupon-3-value"><?php echo esc_html(arim_homepage_option('arim_coupon_3_value', '%30')); ?></strong>
                            <span class="arim-preview-coupon-3-text"><?php echo esc_html(arim_homepage_option('arim_coupon_3_text', 'Seçili kategorilerde avantaj')); ?></span>
                        </div>
                        <div class="arim-preview-coupon-item" style="background:linear-gradient(135deg,#ff6f88,#ff4d6d);">
                            <strong class="arim-preview-coupon-4-value"><?php echo esc_html(arim_homepage_option('arim_coupon_4_value', '%50')); ?></strong>
                            <span class="arim-preview-coupon-4-text"><?php echo esc_html(arim_homepage_option('arim_coupon_4_text', 'Kampanya ürünlerinde fırsat')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="arim-admin-tab-panel" data-tab-panel="mixed">
                <h2 class="arim-admin-section-title"><?php esc_html_e('Mixed Showcase Promo Kutuları', 'arim'); ?></h2>
                <div class="arim-admin-grid">
                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Promo Kutu 1', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Badge</label><input type="text" name="arim_mixed_promo_1_badge" value="<?php echo esc_attr(arim_homepage_option('arim_mixed_promo_1_badge', 'Flaş Kampanya')); ?>"></div>
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_mixed_promo_1_title" value="<?php echo esc_attr(arim_homepage_option('arim_mixed_promo_1_title', 'Sepette ekstra avantaj fırsatı')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_mixed_promo_1_text"><?php echo esc_textarea(arim_homepage_option('arim_mixed_promo_1_text', 'Belirli ürünlerde güncel fırsatları kaçırma.')); ?></textarea></div>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Promo Kutu 2', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Badge</label><input type="text" name="arim_mixed_promo_2_badge" value="<?php echo esc_attr(arim_homepage_option('arim_mixed_promo_2_badge', 'Trend Alan')); ?>"></div>
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_mixed_promo_2_title" value="<?php echo esc_attr(arim_homepage_option('arim_mixed_promo_2_title', 'Yeni sezon fırsatlarını keşfet')); ?>"></div>
                            <div class="arim-admin-field"><label>Açıklama</label><textarea name="arim_mixed_promo_2_text"><?php echo esc_textarea(arim_homepage_option('arim_mixed_promo_2_text', 'Kısa süreli öne çıkan kampanya seçkileri.')); ?></textarea></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="arim-admin-tab-panel" data-tab-panel="showcase">
                <h2 class="arim-admin-section-title"><?php esc_html_e('Showcase Yönetimi', 'arim'); ?></h2>
                <div class="arim-admin-grid">
                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('İlk Showcase', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_showcase_1_title" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_1_title', 'Senin İçin Seçtiklerimiz')); ?>"></div>
                            <div class="arim-admin-field"><label>Link Yazısı</label><input type="text" name="arim_showcase_1_link_text" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_1_link_text', 'Hepsini Gör')); ?>"></div>
                            <div class="arim-admin-field"><label>Link URL</label><input type="text" name="arim_showcase_1_link_url" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_1_link_url', home_url('/shop'))); ?>"></div>
                            <?php arim_admin_select_field('Ürün Kaynağı', 'arim_showcase_1_source', arim_homepage_option('arim_showcase_1_source', 'latest'), $showcase_source_options); ?>
                            <?php arim_admin_product_cat_dropdown('Kategori', 'arim_showcase_1_category_slug', arim_homepage_option('arim_showcase_1_category_slug', '')); ?>
                            <div class="arim-admin-field"><label>Ürün Adedi</label><input type="number" min="1" max="24" name="arim_showcase_1_limit" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_1_limit', '10')); ?>"></div>
                            <div class="arim-admin-field"><label><input type="checkbox" name="arim_showcase_1_include_children" value="1" <?php checked(arim_homepage_option('arim_showcase_1_include_children', '0'), '1'); ?>> <?php esc_html_e('Alt kategorileri dahil et', 'arim'); ?></label></div>
                            <?php arim_admin_product_picker_field('Manuel Ürün Seçici', 'arim_showcase_1_manual_ids', arim_homepage_option('arim_showcase_1_manual_ids', '')); ?>
                            <?php arim_admin_select_field('Yüzey Preset', 'arim_showcase_1_preset', arim_homepage_option('arim_showcase_1_preset', 'orange'), $showcase_preset_1); ?>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('İkinci Showcase', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_showcase_2_title" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_2_title', 'Fırsat Ürünleri')); ?>"></div>
                            <div class="arim-admin-field"><label>Link Yazısı</label><input type="text" name="arim_showcase_2_link_text" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_2_link_text', 'İndirimdekileri Gör')); ?>"></div>
                            <div class="arim-admin-field"><label>Link URL</label><input type="text" name="arim_showcase_2_link_url" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_2_link_url', home_url('/shop/?on_sale=1'))); ?>"></div>
                            <?php arim_admin_select_field('Ürün Kaynağı', 'arim_showcase_2_source', arim_homepage_option('arim_showcase_2_source', 'on_sale'), $showcase_source_options); ?>
                            <?php arim_admin_product_cat_dropdown('Kategori', 'arim_showcase_2_category_slug', arim_homepage_option('arim_showcase_2_category_slug', '')); ?>
                            <div class="arim-admin-field"><label>Ürün Adedi</label><input type="number" min="1" max="24" name="arim_showcase_2_limit" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_2_limit', '8')); ?>"></div>
                            <div class="arim-admin-field"><label><input type="checkbox" name="arim_showcase_2_include_children" value="1" <?php checked(arim_homepage_option('arim_showcase_2_include_children', '0'), '1'); ?>> <?php esc_html_e('Alt kategorileri dahil et', 'arim'); ?></label></div>
                            <?php arim_admin_product_picker_field('Manuel Ürün Seçici', 'arim_showcase_2_manual_ids', arim_homepage_option('arim_showcase_2_manual_ids', '')); ?>
                            <?php arim_admin_select_field('Yüzey Preset', 'arim_showcase_2_preset', arim_homepage_option('arim_showcase_2_preset', 'pink'), $showcase_preset_2); ?>
                        </div>
                    </div>

                    <div class="arim-admin-card">
                        <h2><?php esc_html_e('Üçüncü Showcase', 'arim'); ?></h2>
                        <div class="arim-admin-fields">
                            <div class="arim-admin-field"><label>Başlık</label><input type="text" name="arim_showcase_3_title" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_3_title', 'Çok Satanlar')); ?>"></div>
                            <div class="arim-admin-field"><label>Link Yazısı</label><input type="text" name="arim_showcase_3_link_text" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_3_link_text', 'En Çok Satanları Gör')); ?>"></div>
                            <div class="arim-admin-field"><label>Link URL</label><input type="text" name="arim_showcase_3_link_url" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_3_link_url', home_url('/shop'))); ?>"></div>
                            <?php arim_admin_select_field('Ürün Kaynağı', 'arim_showcase_3_source', arim_homepage_option('arim_showcase_3_source', 'popularity'), $showcase_source_options); ?>
                            <?php arim_admin_product_cat_dropdown('Kategori', 'arim_showcase_3_category_slug', arim_homepage_option('arim_showcase_3_category_slug', '')); ?>
                            <div class="arim-admin-field"><label>Ürün Adedi</label><input type="number" min="1" max="24" name="arim_showcase_3_limit" value="<?php echo esc_attr(arim_homepage_option('arim_showcase_3_limit', '10')); ?>"></div>
                            <div class="arim-admin-field"><label><input type="checkbox" name="arim_showcase_3_include_children" value="1" <?php checked(arim_homepage_option('arim_showcase_3_include_children', '0'), '1'); ?>> <?php esc_html_e('Alt kategorileri dahil et', 'arim'); ?></label></div>
                            <?php arim_admin_product_picker_field('Manuel Ürün Seçici', 'arim_showcase_3_manual_ids', arim_homepage_option('arim_showcase_3_manual_ids', '')); ?>
                            <?php arim_admin_select_field('Yüzey Preset', 'arim_showcase_3_preset', arim_homepage_option('arim_showcase_3_preset', 'clean'), $showcase_preset_3); ?>
                        </div>
                    </div>
                </div>

                <div class="arim-admin-preview-wrap">
                    <h3><?php esc_html_e('Canlı Önizleme', 'arim'); ?></h3>
                    <div class="arim-preview-showcase-head">
                        <span class="arim-preview-showcase-1-title"><?php echo esc_html(arim_homepage_option('arim_showcase_1_title', 'Senin İçin Seçtiklerimiz')); ?></span>
                        <span class="arim-preview-showcase-1-link"><?php echo esc_html(arim_homepage_option('arim_showcase_1_link_text', 'Hepsini Gör')); ?></span>
                    </div>
                    <div style="height:10px;"></div>
                    <div class="arim-preview-showcase-head" style="background:linear-gradient(180deg,#ff6ca8 0%, #ff7f9f 100%);">
                        <span class="arim-preview-showcase-2-title"><?php echo esc_html(arim_homepage_option('arim_showcase_2_title', 'Fırsat Ürünleri')); ?></span>
                        <span class="arim-preview-showcase-2-link"><?php echo esc_html(arim_homepage_option('arim_showcase_2_link_text', 'İndirimdekileri Gör')); ?></span>
                    </div>
                </div>
            </div>

            <div class="arim-admin-submit">
                <?php submit_button(__('Kaydet', 'arim')); ?>
            </div>
        </form>
    </div>
    <?php
}
