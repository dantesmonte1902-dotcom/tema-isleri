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
$archive_reset_url   = arim_shop_archive_current_url();
$query_object        = get_queried_object();
$archive_title       = woocommerce_page_title(false);
$archive_description = trim(wp_strip_all_tags(do_shortcode(term_description())));
$archive_total       = isset($GLOBALS['wp_query']->found_posts) ? (int) $GLOBALS['wp_query']->found_posts : 0;
$archive_total_text  = $archive_total > 99999
    ? __('100000+ Ürün', 'arim')
    : sprintf(_n('%s Ürün', '%s Ürün', max(1, $archive_total), 'arim'), number_format_i18n(max(1, $archive_total)));

$quick_filter_links = [];

if ($query_object instanceof WP_Term && isset($query_object->taxonomy) && 'product_cat' === $query_object->taxonomy) {
    $child_categories = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => (int) $query_object->term_id,
        'number'     => 8,
    ]);

    if (!empty($child_categories) && !is_wp_error($child_categories)) {
        foreach ($child_categories as $child_category) {
            $quick_filter_links[] = [
                'label'    => $child_category->name,
                'url'      => get_term_link($child_category),
                'isActive' => false,
                'count'    => (int) $child_category->count,
            ];
        }
    }
}

if (empty($quick_filter_links) && !empty($archive_collections)) {
    foreach (array_slice($archive_collections, 0, 6) as $collection) {
        $quick_filter_links[] = [
            'label'    => $collection['title'],
            'url'      => $collection['url'],
            'isActive' => !empty($collection['isActive']),
            'count'    => (int) $collection['count'],
        ];
    }
}

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
                <?php if (!empty($quick_filter_links)) : ?>
                    <div class="arim-category-quick-filters" aria-label="<?php esc_attr_e('Hızlı filtreler', 'arim'); ?>">
                        <?php foreach ($quick_filter_links as $quick_filter_link) : ?>
                            <a class="arim-category-quick-filter<?php echo !empty($quick_filter_link['isActive']) ? ' is-active' : ''; ?>" href="<?php echo esc_url($quick_filter_link['url']); ?>">
                                <span><?php echo esc_html($quick_filter_link['label']); ?></span>
                                <?php if (!empty($quick_filter_link['count'])) : ?>
                                    <strong><?php echo esc_html(number_format_i18n((int) $quick_filter_link['count'])); ?></strong>
                                <?php endif; ?>
                            </a>
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

            <?php if (!empty($active_filters)) : ?>
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

            <div class="arim-category-search-layout">
                <aside class="arim-category-search-sidebar">
                    <form class="arim-woo-filter-form arim-category-filter-form" method="get">
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

                        <div class="arim-category-sidebar-card">
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

                        <div class="arim-category-sidebar-card">
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
                </aside>

                <div class="arim-category-search-results">
                    <?php if (woocommerce_product_loop()) : ?>
                        <?php woocommerce_product_loop_start(); ?>

                        <?php if (wc_get_loop_prop('total')) : ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <?php do_action('woocommerce_shop_loop'); ?>
                                <?php wc_get_template_part('content', 'product'); ?>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <?php woocommerce_product_loop_end(); ?>

                        <div class="arim-woo-pagination arim-category-pagination">
                            <?php do_action('woocommerce_after_shop_loop'); ?>
                        </div>
                    <?php else : ?>
                        <div class="arim-woo-empty arim-category-empty-state">
                            <p class="arim-woo-empty-note"><?php esc_html_e('Bu filtrelerle eşleşen ürün bulunamadı. Kategori veya fiyat aralığını gevşeterek devam edebilirsin.', 'arim'); ?></p>
                            <div class="arim-woo-empty-actions">
                                <a href="<?php echo esc_url($archive_reset_url); ?>" class="arim-woo-empty-link is-primary"><?php esc_html_e('Tüm ürünleri göster', 'arim'); ?></a>
                                <a href="<?php echo esc_url(arim_shop_url()); ?>" class="arim-woo-empty-link"><?php esc_html_e('Genel kategoriye dön', 'arim'); ?></a>
                            </div>
                            <?php do_action('woocommerce_no_products_found'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php do_action('woocommerce_after_main_content'); ?>
    </div>
</div>

<?php
get_footer('shop');
