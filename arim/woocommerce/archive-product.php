<?php
defined('ABSPATH') || exit;

get_header('shop');

$current_min_price = isset($_GET['min_price']) ? wc_clean(wp_unslash($_GET['min_price'])) : '';
$current_max_price = isset($_GET['max_price']) ? wc_clean(wp_unslash($_GET['max_price'])) : '';
$current_stock     = isset($_GET['stock_status']) ? wc_clean(wp_unslash($_GET['stock_status'])) : '';
$current_on_sale   = isset($_GET['on_sale']) ? wc_clean(wp_unslash($_GET['on_sale'])) : '';
$current_featured  = isset($_GET['featured']) ? wc_clean(wp_unslash($_GET['featured'])) : '';

$product_attributes  = wc_get_attribute_taxonomies();
$archive_insights    = arim_shop_archive_insights();
$active_filters      = arim_shop_active_filter_chips();
$archive_collections = arim_shop_archive_collection_cards();
$archive_brand_links = arim_shop_archive_brand_links();
$quick_filter_sections = arim_shop_archive_quick_filter_sections();
$archive_store_highlights = arim_shop_archive_store_highlights();
$archive_feature_modules = arim_shop_archive_feature_modules();
$archive_reset_url   = arim_shop_archive_current_url();
$shop_settings       = arim_get_shop_archive_settings();
$show_sidebar        = isset($shop_settings['show_sidebar']) && '1' === (string) $shop_settings['show_sidebar'];
$show_filters        = isset($shop_settings['show_filters']) && '1' === (string) $shop_settings['show_filters'];
$default_view        = isset($shop_settings['default_view']) ? (string) $shop_settings['default_view'] : 'grid';
$card_style          = isset($shop_settings['card_style']) ? (string) $shop_settings['card_style'] : 'default';
$hover_effect        = isset($shop_settings['hover_effect']) ? (string) $shop_settings['hover_effect'] : 'lift';
$pagination_mode     = arim_get_shop_archive_pagination_mode();
$query_object        = get_queried_object();
$archive_title       = woocommerce_page_title(false);
$archive_description = trim(wp_strip_all_tags(do_shortcode(term_description())));
$archive_total       = isset($GLOBALS['wp_query']->found_posts) ? (int) $GLOBALS['wp_query']->found_posts : 0;
$max_pages           = isset($GLOBALS['wp_query']->max_num_pages) ? (int) $GLOBALS['wp_query']->max_num_pages : 1;
$next_page_url       = $max_pages > 1 ? get_next_posts_page_link($max_pages) : '';
$archive_total_text  = $archive_total > 99999
    ? __('100000+ Ürün', 'arim')
    : sprintf(_n('%s Ürün', '%s Ürün', max(1, $archive_total), 'arim'), number_format_i18n(max(1, $archive_total)));
$layout_classes      = 'arim-category-search-layout' . (!$show_sidebar ? ' is-sidebar-hidden' : '');
$results_classes     = trim(
    'arim-category-search-results is-view-' . sanitize_html_class($default_view) .
    ' is-card-style-' . sanitize_html_class($card_style) .
    ' is-hover-effect-' . sanitize_html_class($hover_effect) .
    ' is-pagination-' . sanitize_html_class($pagination_mode)
);

$shop_categories = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
    'number'     => 12,
]);
?>

<div class="arim-woo-page arim-category-search-page">
    <div class="arim-container">
        <?php do_action('woocommerce_before_main_content'); ?>

        <div class="arim-category-search-shell">
            <?php if (function_exists('woocommerce_breadcrumb')) : ?>
                <div class="arim-category-breadcrumb-wrap">
                    <?php woocommerce_breadcrumb(); ?>
                </div>
            <?php endif; ?>

            <section class="arim-category-search-header">
                <div class="arim-category-search-header-main">
                    <div class="arim-category-search-title-group">
                        <h1 class="arim-category-search-title"><?php echo esc_html($archive_title); ?></h1>
                        <span class="arim-category-search-count"><?php echo esc_html($archive_total_text); ?></span>
                    </div>

                    <?php if ($archive_description) : ?>
                        <p class="arim-category-search-description"><?php echo esc_html($archive_description); ?></p>
                    <?php endif; ?>
                </div>

                <div class="arim-category-search-header-note">
                    <strong><?php esc_html_e('Aradığın görünüm burada', 'arim'); ?></strong>
                    <span><?php echo esc_html($archive_insights['deliveryText']); ?></span>
                </div>
            </section>

            <div class="arim-category-search-subheader">
                <?php if ($show_filters && !empty($quick_filter_sections)) : ?>
                    <div class="arim-category-quick-filter-groups" aria-label="<?php esc_attr_e('Hızlı filtreler', 'arim'); ?>">
                        <?php foreach ($quick_filter_sections as $quick_filter_section) : ?>
                            <div class="arim-category-quick-filter-section">
                                <span class="arim-category-quick-filter-title"><?php echo esc_html($quick_filter_section['title']); ?></span>
                                <div class="arim-category-quick-filters">
                                    <?php foreach ($quick_filter_section['items'] as $quick_filter_link) : ?>
                                        <a class="arim-category-quick-filter<?php echo !empty($quick_filter_link['isActive']) ? ' is-active' : ''; ?>" href="<?php echo esc_url($quick_filter_link['url']); ?>">
                                            <span><?php echo esc_html($quick_filter_link['label']); ?></span>
                                            <?php if (!empty($quick_filter_link['count'])) : ?>
                                                <strong><?php echo esc_html(number_format_i18n((int) $quick_filter_link['count'])); ?></strong>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="arim-category-sort-box">
                    <span class="arim-category-sort-label"><?php esc_html_e('Sıralama', 'arim'); ?></span>
                    <div class="arim-woo-ordering">
                        <?php woocommerce_catalog_ordering(); ?>
                    </div>
                </div>
            </div>

            <div class="arim-category-mobile-toolbar<?php echo !$show_filters ? ' is-filter-hidden' : ''; ?>">
                <?php if ($show_filters) : ?>
                    <a href="#arim-category-filter-form" class="arim-category-mobile-toolbar-link"><?php esc_html_e('Filtreler', 'arim'); ?></a>
                <?php endif; ?>
                <div class="arim-category-mobile-toolbar-ordering">
                    <?php woocommerce_catalog_ordering(); ?>
                </div>
            </div>

            <?php if ($show_filters && !empty($active_filters)) : ?>
                <div class="arim-category-active-filters" aria-label="<?php esc_attr_e('Aktif filtreler', 'arim'); ?>">
                    <?php foreach ($active_filters as $filter_chip) : ?>
                        <a href="<?php echo esc_url($filter_chip['url']); ?>" class="arim-category-active-filter-chip">
                            <span><?php echo esc_html($filter_chip['label']); ?></span>
                            <strong aria-hidden="true">×</strong>
                        </a>
                    <?php endforeach; ?>

                    <a href="<?php echo esc_url($archive_reset_url); ?>" class="arim-category-clear-filters">
                        <?php esc_html_e('Temizle', 'arim'); ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="<?php echo esc_attr($layout_classes); ?>">
                <?php if ($show_sidebar) : ?>
                <aside class="arim-category-search-sidebar<?php echo !$show_filters ? ' is-filter-disabled' : ''; ?>">
                    <?php if ($show_filters) : ?>
                    <form id="arim-category-filter-form" class="arim-woo-filter-form arim-category-filter-form" method="get">
                        <div class="arim-category-sidebar-card arim-category-sidebar-card-highlight">
                            <span class="arim-category-sidebar-kicker"><?php echo esc_html($archive_insights['deliveryBadge']); ?></span>
                            <h3><?php esc_html_e('Kategori vitrini', 'arim'); ?></h3>
                            <p><?php echo esc_html($archive_insights['supportText']); ?></p>

                            <div class="arim-category-sidebar-stats">
                                <div class="arim-category-sidebar-stat">
                                    <strong><?php echo esc_html(number_format_i18n((int) $archive_total)); ?></strong>
                                    <span><?php esc_html_e('sonuç', 'arim'); ?></span>
                                </div>
                                <div class="arim-category-sidebar-stat">
                                    <strong><?php echo esc_html(number_format_i18n((int) $archive_insights['featuredCount'])); ?></strong>
                                    <span><?php esc_html_e('öne çıkan', 'arim'); ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($archive_feature_modules)) : ?>
                            <div class="arim-category-sidebar-card">
                                <span class="arim-category-sidebar-kicker"><?php esc_html_e('Sponsorlu bloklar', 'arim'); ?></span>
                                <h3><?php esc_html_e('Özel listeleme modülleri', 'arim'); ?></h3>
                                <div class="arim-category-feature-modules">
                                    <?php foreach ($archive_feature_modules as $feature_module) : ?>
                                        <a class="arim-category-feature-module<?php echo !empty($feature_module['isActive']) ? ' is-active' : ''; ?>" href="<?php echo esc_url($feature_module['url']); ?>">
                                            <span class="arim-category-feature-module-badge"><?php echo esc_html($feature_module['badge']); ?></span>
                                            <strong><?php echo esc_html($feature_module['title']); ?></strong>
                                            <small><?php echo esc_html($feature_module['text']); ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="arim-category-sidebar-card">
                            <span class="arim-category-sidebar-kicker"><?php esc_html_e('Gerçek kategori ağacı', 'arim'); ?></span>
                            <h3><?php esc_html_e('Kategoriler', 'arim'); ?></h3>
                            <ul class="arim-woo-category-list arim-category-list-clean">
                                <?php if (!empty($shop_categories) && !is_wp_error($shop_categories)) : ?>
                                    <?php foreach ($shop_categories as $category) : ?>
                                        <li>
                                            <a href="<?php echo esc_url(get_term_link($category)); ?>"><?php echo esc_html($category->name); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <?php if (!empty($archive_brand_links)) : ?>
                            <div class="arim-category-sidebar-card">
                                <span class="arim-category-sidebar-kicker"><?php esc_html_e('Facet filtreleri', 'arim'); ?></span>
                                <h3><?php esc_html_e('Popüler markalar', 'arim'); ?></h3>
                                <div class="arim-category-brand-list">
                                    <?php foreach ($archive_brand_links as $brand_link) : ?>
                                        <a class="arim-category-brand-pill<?php echo !empty($brand_link['isActive']) ? ' is-active' : ''; ?>" href="<?php echo esc_url($brand_link['url']); ?>">
                                            <span><?php echo esc_html($brand_link['label']); ?></span>
                                            <strong><?php echo esc_html(number_format_i18n((int) $brand_link['count'])); ?></strong>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($archive_store_highlights)) : ?>
                            <div class="arim-category-sidebar-card">
                                <span class="arim-category-sidebar-kicker"><?php esc_html_e('Mağaza widget', 'arim'); ?></span>
                                <h3><?php esc_html_e('Öne çıkan mağazalar', 'arim'); ?></h3>
                                <div class="arim-category-store-list">
                                    <?php foreach ($archive_store_highlights as $store_highlight) : ?>
                                        <a class="arim-category-store-item" href="<?php echo esc_url($store_highlight['url']); ?>">
                                            <span><?php echo esc_html($store_highlight['label']); ?></span>
                                            <strong><?php echo esc_html(number_format_i18n((int) $store_highlight['count'])); ?></strong>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="arim-category-sidebar-card">
                            <span class="arim-category-sidebar-kicker"><?php esc_html_e('Referans hizalama', 'arim'); ?></span>
                            <h3><?php esc_html_e('Fiyat', 'arim'); ?></h3>
                            <div class="arim-filter-fields">
                                <div class="arim-filter-field">
                                    <label for="min_price"><?php esc_html_e('En az', 'arim'); ?></label>
                                    <input type="number" id="min_price" name="min_price" value="<?php echo esc_attr($current_min_price); ?>" placeholder="0">
                                </div>

                                <div class="arim-filter-field">
                                    <label for="max_price"><?php esc_html_e('En çok', 'arim'); ?></label>
                                    <input type="number" id="max_price" name="max_price" value="<?php echo esc_attr($current_max_price); ?>" placeholder="5000">
                                </div>
                            </div>
                        </div>

                        <div class="arim-category-sidebar-card">
                            <span class="arim-category-sidebar-kicker"><?php esc_html_e('Hızlı seçim', 'arim'); ?></span>
                            <h3><?php esc_html_e('Teslimat ve ürün tipi', 'arim'); ?></h3>

                            <label class="arim-filter-check">
                                <input type="checkbox" name="stock_status" value="instock" <?php checked($current_stock, 'instock'); ?>>
                                <span><?php esc_html_e('Stokta olanlar', 'arim'); ?></span>
                            </label>

                            <label class="arim-filter-check">
                                <input type="checkbox" name="on_sale" value="1" <?php checked($current_on_sale, '1'); ?>>
                                <span><?php esc_html_e('İndirimdekiler', 'arim'); ?></span>
                            </label>

                            <label class="arim-filter-check">
                                <input type="checkbox" name="featured" value="1" <?php checked($current_featured, '1'); ?>>
                                <span><?php esc_html_e('Öne çıkanlar', 'arim'); ?></span>
                            </label>
                        </div>

                        <?php if (!empty($product_attributes)) : ?>
                            <?php foreach ($product_attributes as $attribute) : ?>
                                <?php
                                $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);

                                if (!taxonomy_exists($taxonomy)) {
                                    continue;
                                }

                                $terms = get_terms([
                                    'taxonomy'   => $taxonomy,
                                    'hide_empty' => true,
                                ]);

                                if (empty($terms) || is_wp_error($terms)) {
                                    continue;
                                }

                                $selected_terms = isset($_GET[$taxonomy]) ? (array) wp_unslash($_GET[$taxonomy]) : [];
                                ?>
                                <div class="arim-category-sidebar-card">
                                    <span class="arim-category-sidebar-kicker"><?php esc_html_e('Facet', 'arim'); ?></span>
                                    <h3><?php echo esc_html($attribute->attribute_label); ?></h3>
                                    <div class="arim-attribute-filter-list">
                                        <?php foreach ($terms as $term) : ?>
                                            <label class="arim-filter-check">
                                                <input
                                                    type="checkbox"
                                                    name="<?php echo esc_attr($taxonomy); ?>[]"
                                                    value="<?php echo esc_attr($term->slug); ?>"
                                                    <?php checked(in_array($term->slug, $selected_terms, true)); ?>
                                                >
                                                <span><?php echo esc_html($term->name); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php
                        foreach ($_GET as $key => $value) {
                            if (arim_shop_archive_is_reserved_filter_key((string) $key)) {
                                continue;
                            }

                            if (is_array($value)) {
                                foreach ($value as $sub_value) {
                                    echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr(wc_clean(wp_unslash($sub_value))) . '">';
                                }
                            } else {
                                echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr(wc_clean(wp_unslash($value))) . '">';
                            }
                        }
                        ?>

                        <div class="arim-category-sidebar-card arim-category-sidebar-actions">
                            <button type="submit" name="submit_filter" value="1" class="arim-filter-submit">
                                <?php esc_html_e('Filtrele', 'arim'); ?>
                            </button>

                            <a href="<?php echo esc_url($archive_reset_url); ?>" class="arim-filter-reset">
                                <?php esc_html_e('Filtreleri temizle', 'arim'); ?>
                            </a>
                        </div>
                    </form>
                    <?php else : ?>
                        <div class="arim-category-sidebar-card arim-category-sidebar-card-highlight">
                            <span class="arim-category-sidebar-kicker"><?php echo esc_html($archive_insights['deliveryBadge']); ?></span>
                            <h3><?php esc_html_e('Sidebar aktif', 'arim'); ?></h3>
                            <p><?php esc_html_e('Shop Archive ayarlarından filtre alanı kapatıldı. Kategori vitrini sade görünümde gösteriliyor.', 'arim'); ?></p>
                        </div>

                        <div class="arim-category-sidebar-card">
                            <span class="arim-category-sidebar-kicker"><?php esc_html_e('Kategori ağacı', 'arim'); ?></span>
                            <h3><?php esc_html_e('Kategoriler', 'arim'); ?></h3>
                            <ul class="arim-woo-category-list arim-category-list-clean">
                                <?php if (!empty($shop_categories) && !is_wp_error($shop_categories)) : ?>
                                    <?php foreach ($shop_categories as $category) : ?>
                                        <li>
                                            <a href="<?php echo esc_url(get_term_link($category)); ?>"><?php echo esc_html($category->name); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </aside>
                <?php endif; ?>

                <div
                    class="<?php echo esc_attr($results_classes); ?>"
                    data-arim-shop-results
                    data-view-mode="<?php echo esc_attr($default_view); ?>"
                    data-card-style="<?php echo esc_attr($card_style); ?>"
                    data-hover-effect="<?php echo esc_attr($hover_effect); ?>"
                >
                    <?php if (woocommerce_product_loop()) : ?>
                        <?php woocommerce_product_loop_start(); ?>

                        <?php while (have_posts()) : the_post(); ?>
                            <?php do_action('woocommerce_shop_loop'); ?>
                            <?php wc_get_template_part('content', 'product'); ?>
                        <?php endwhile; ?>

                        <?php woocommerce_product_loop_end(); ?>

                        <div class="arim-woo-pagination arim-category-pagination">
                            <?php if ('numbers' === $pagination_mode) : ?>
                                <?php do_action('woocommerce_after_shop_loop'); ?>
                            <?php elseif ($next_page_url) : ?>
                                <div
                                    class="arim-shop-archive-pager"
                                    data-arim-shop-pagination
                                    data-mode="<?php echo esc_attr($pagination_mode); ?>"
                                    data-next-url="<?php echo esc_url($next_page_url); ?>"
                                    data-loading-label="<?php echo esc_attr__('Ürünler yükleniyor...', 'arim'); ?>"
                                    data-button-label="<?php echo esc_attr__('Daha fazla ürün yükle', 'arim'); ?>"
                                    data-error-label="<?php echo esc_attr__('Sonraki ürünler yüklenemedi.', 'arim'); ?>"
                                >
                                    <button type="button" class="arim-shop-archive-load-more">
                                        <?php esc_html_e('Daha fazla ürün yükle', 'arim'); ?>
                                    </button>
                                    <div class="arim-shop-archive-status" aria-live="polite"></div>
                                    <?php if ('infinite' === $pagination_mode) : ?>
                                        <div class="arim-shop-archive-sentinel" data-arim-shop-sentinel></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <?php do_action('woocommerce_no_products_found'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php do_action('woocommerce_after_main_content'); ?>
    </div>
</div>

<?php
get_footer('shop');
