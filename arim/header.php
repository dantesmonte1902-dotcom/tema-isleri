<?php
defined('ABSPATH') || exit;

$header_categories = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
    'number'     => 10,
]);

$shop_url    = function_exists('arim_shop_url') ? arim_shop_url() : home_url('/shop');
$account_url = function_exists('arim_account_url') ? arim_account_url() : wp_login_url();
$cart_url    = function_exists('arim_cart_url') ? arim_cart_url() : home_url('/cart');
$favorites_url = function_exists('arim_favorites_url') ? arim_favorites_url() : add_query_arg('arim_favorites', '1', home_url('/'));

$header_campaign_links = [
    [
        'label' => __('Süper Fiyat', 'arim'),
        'url'   => add_query_arg('orderby', 'popularity', $shop_url),
    ],
    [
        'label' => __('Bugüne Özel', 'arim'),
        'url'   => $shop_url,
    ],
    [
        'label' => __('Çok Satanlar', 'arim'),
        'url'   => add_query_arg('orderby', 'popularity', $shop_url),
    ],
    [
        'label' => __('Kuponlu Ürünler', 'arim'),
        'url'   => $shop_url,
    ],
    [
        'label' => __('Hızlı Teslimat', 'arim'),
        'url'   => $shop_url,
    ],
    [
        'label' => __('Trend Liste', 'arim'),
        'url'   => $shop_url,
    ],
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<div class="arim-site">
    <header class="arim-header-area">
        <div class="arim-topbar">
            <div class="arim-container arim-topbar-inner">
                <div class="arim-topbar-left">
                    <a href="<?php echo esc_url($account_url); ?>">
                        <?php esc_html_e('Hesabım', 'arim'); ?>
                    </a>
                    <a href="<?php echo esc_url($shop_url); ?>">
                        <?php esc_html_e('Mağaza', 'arim'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/my-account/orders')); ?>">
                        <?php esc_html_e('Siparişlerim', 'arim'); ?>
                    </a>
                </div>

                <div class="arim-topbar-right">
                    <span class="arim-topbar-promo"><?php esc_html_e('750 TL ve üzeri alışverişe ücretsiz kargo', 'arim'); ?></span>
                    <a href="#"><?php esc_html_e('Yardım & Destek', 'arim'); ?></a>
                    <a href="#"><?php esc_html_e('Güvenli Alışveriş', 'arim'); ?></a>
                </div>
            </div>
        </div>

        <div class="arim-header">
            <div class="arim-container arim-header-main">
                <div class="arim-header-left">
                    <button class="arim-mobile-menu-toggle" type="button" aria-label="<?php esc_attr_e('Menüyü Aç', 'arim'); ?>">
                        ☰
                    </button>

                    <div class="arim-brand">
                        <a class="arim-logo" href="<?php echo esc_url(home_url('/')); ?>">
                            <span class="arim-logo-text">ARIM</span>
                        </a>
                    </div>
                </div>

                <div class="arim-search">
                    <?php get_product_search_form(); ?>
                </div>

                <div class="arim-header-actions">
                    <a class="arim-header-action" href="<?php echo esc_url($account_url); ?>">
                        <span class="arim-header-action-icon">👤</span>
                        <span class="arim-header-action-text"><?php esc_html_e('Hesabım', 'arim'); ?></span>
                    </a>

                    <a class="arim-header-action" href="<?php echo esc_url($favorites_url); ?>">
                        <span class="arim-header-action-icon">♡</span>
                        <span class="arim-header-action-text"><?php esc_html_e('Favoriler', 'arim'); ?></span>
                        <span class="arim-cart-badge arim-favorites-count">0</span>
                    </a>

                    <a class="arim-header-action arim-cart-link" href="<?php echo esc_url($cart_url); ?>">
                        <span class="arim-header-action-icon">🛒</span>
                        <span class="arim-header-action-text"><?php esc_html_e('Sepetim', 'arim'); ?></span>
                        <span class="arim-cart-badge"><?php echo esc_html(arim_cart_count()); ?></span>
                    </a>
                </div>
            </div>

            <nav class="arim-nav">
                <div class="arim-container arim-nav-inner">
                    <div class="arim-mega-menu-item">
                        <a href="<?php echo esc_url($shop_url); ?>" class="arim-nav-all arim-has-mega">
                            <?php esc_html_e('Kategoriler', 'arim'); ?>
                        </a>

                        <div class="arim-mega-menu">
                            <div class="arim-mega-menu-grid">
                                <?php
                                if (!empty($header_categories) && !is_wp_error($header_categories)) :
                                    foreach ($header_categories as $cat) :
                                        $children = get_terms([
                                            'taxonomy'   => 'product_cat',
                                            'hide_empty' => true,
                                            'parent'     => $cat->term_id,
                                            'number'     => 6,
                                        ]);
                                        ?>
                                        <div class="arim-mega-col">
                                            <a class="arim-mega-title" href="<?php echo esc_url(get_term_link($cat)); ?>">
                                                <?php echo esc_html($cat->name); ?>
                                            </a>

                                            <?php if (!empty($children) && !is_wp_error($children)) : ?>
                                                <ul class="arim-mega-list">
                                                    <?php foreach ($children as $child) : ?>
                                                        <li>
                                                            <a href="<?php echo esc_url(get_term_link($child)); ?>">
                                                                <?php echo esc_html($child->name); ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>

                    <a href="<?php echo esc_url($shop_url); ?>" class="arim-nav-link">
                        <?php esc_html_e('Tüm Ürünler', 'arim'); ?>
                    </a>

                    <?php
                    if (!empty($header_categories) && !is_wp_error($header_categories)) :
                        foreach ($header_categories as $cat) :
                            ?>
                            <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="arim-nav-link">
                                <?php echo esc_html($cat->name); ?>
                            </a>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </nav>

            <div class="arim-header-trend-strip">
                <div class="arim-container arim-header-trend-inner">
                    <span class="arim-header-trend-label"><?php esc_html_e('Öne Çıkanlar', 'arim'); ?></span>

                    <div class="arim-header-trend-links">
                        <?php foreach ($header_campaign_links as $campaign_link) : ?>
                            <a href="<?php echo esc_url($campaign_link['url']); ?>">
                                <?php echo esc_html($campaign_link['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="arim-mobile-menu-overlay"></div>

        <div class="arim-mobile-menu-panel">
            <div class="arim-mobile-menu-head">
                <strong>ARIM</strong>
                <button class="arim-mobile-menu-close" type="button" aria-label="<?php esc_attr_e('Menüyü Kapat', 'arim'); ?>">
                    ✕
                </button>
            </div>

            <div class="arim-mobile-menu-body">
                <a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Tüm Ürünler', 'arim'); ?></a>
                <a href="<?php echo esc_url($account_url); ?>"><?php esc_html_e('Hesabım', 'arim'); ?></a>
                <a href="<?php echo esc_url($cart_url); ?>"><?php esc_html_e('Sepetim', 'arim'); ?></a>
                <a href="<?php echo esc_url($favorites_url); ?>"><?php esc_html_e('Favoriler', 'arim'); ?></a>

                <div class="arim-mobile-menu-divider"></div>

                <?php foreach ($header_campaign_links as $campaign_link) : ?>
                    <a href="<?php echo esc_url($campaign_link['url']); ?>">
                        <?php echo esc_html($campaign_link['label']); ?>
                    </a>
                <?php endforeach; ?>

                <div class="arim-mobile-menu-divider"></div>

                <?php
                if (!empty($header_categories) && !is_wp_error($header_categories)) :
                    foreach ($header_categories as $cat) :
                        ?>
                        <a href="<?php echo esc_url(get_term_link($cat)); ?>">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </header>

    <main class="arim-main">
