<?php
defined('ABSPATH') || exit;

$header_categories = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
    'number'     => 10,
]);
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
                    <a href="<?php echo esc_url(home_url('/my-account')); ?>">
                        <?php esc_html_e('Hesabım', 'arim'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/shop')); ?>">
                        <?php esc_html_e('Mağaza', 'arim'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/my-account/orders')); ?>">
                        <?php esc_html_e('Siparişlerim', 'arim'); ?>
                    </a>
                </div>

                <div class="arim-topbar-right">
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
                    <a class="arim-header-action" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">
                        <span class="arim-header-action-icon">👤</span>
                        <span class="arim-header-action-text"><?php esc_html_e('Hesabım', 'arim'); ?></span>
                    </a>

                    <a class="arim-header-action" href="<?php echo esc_url(home_url('/favorites')); ?>">
                        <span class="arim-header-action-icon">♡</span>
                        <span class="arim-header-action-text"><?php esc_html_e('Favoriler', 'arim'); ?></span>
                        <span class="arim-cart-badge arim-favorites-count">0</span>
                    </a>

                    <a class="arim-header-action arim-cart-link" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                        <span class="arim-header-action-icon">🛒</span>
                        <span class="arim-header-action-text"><?php esc_html_e('Sepetim', 'arim'); ?></span>
                        <span class="arim-cart-badge"><?php echo esc_html(arim_cart_count()); ?></span>
                    </a>
                </div>
            </div>

            <nav class="arim-nav">
                <div class="arim-container arim-nav-inner">
                    <div class="arim-mega-menu-item">
                        <a href="<?php echo esc_url(home_url('/shop')); ?>" class="arim-nav-all arim-has-mega">
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

                    <a href="<?php echo esc_url(home_url('/shop')); ?>" class="arim-nav-link">
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
                <a href="<?php echo esc_url(home_url('/shop')); ?>"><?php esc_html_e('Tüm Ürünler', 'arim'); ?></a>
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php esc_html_e('Hesabım', 'arim'); ?></a>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>"><?php esc_html_e('Sepetim', 'arim'); ?></a>
                <a href="<?php echo esc_url(home_url('/favorites')); ?>"><?php esc_html_e('Favoriler', 'arim'); ?></a>

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