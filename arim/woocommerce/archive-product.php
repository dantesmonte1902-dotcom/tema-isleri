<?php
defined('ABSPATH') || exit;

get_header('shop');

$current_min_price = isset($_GET['min_price']) ? wc_clean(wp_unslash($_GET['min_price'])) : '';
$current_max_price = isset($_GET['max_price']) ? wc_clean(wp_unslash($_GET['max_price'])) : '';
$current_stock     = isset($_GET['stock_status']) ? wc_clean(wp_unslash($_GET['stock_status'])) : '';
$current_on_sale   = isset($_GET['on_sale']) ? wc_clean(wp_unslash($_GET['on_sale'])) : '';
$current_featured  = isset($_GET['featured']) ? wc_clean(wp_unslash($_GET['featured'])) : '';

$product_attributes = wc_get_attribute_taxonomies();
?>

<div class="arim-woo-page">
    <div class="arim-container">
        <?php do_action('woocommerce_before_main_content'); ?>

        <div class="arim-woo-hero">
            <div class="arim-woo-hero-content">
                <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
                    <h1 class="arim-woo-page-title"><?php woocommerce_page_title(); ?></h1>
                <?php endif; ?>

                <div class="arim-woo-page-description">
                    <?php do_action('woocommerce_archive_description'); ?>
                </div>
            </div>

            <div class="arim-woo-hero-badges">
                <div class="arim-woo-hero-badge">
                    <strong><?php esc_html_e('Hızlı Teslimat', 'arim'); ?></strong>
                    <span><?php esc_html_e('Seçili ürünlerde avantajlı gönderim', 'arim'); ?></span>
                </div>
                <div class="arim-woo-hero-badge">
                    <strong><?php esc_html_e('Güvenli Ödeme', 'arim'); ?></strong>
                    <span><?php esc_html_e('Korunan ödeme altyapısı', 'arim'); ?></span>
                </div>
            </div>
        </div>

        <div class="arim-woo-toolbar arim-woo-toolbar-enhanced">
            <div class="arim-woo-toolbar-left">
                <div class="arim-woo-result-count">
                    <?php woocommerce_result_count(); ?>
                </div>

                <?php if (function_exists('woocommerce_breadcrumb')) : ?>
                    <div class="arim-woo-breadcrumb">
                        <?php woocommerce_breadcrumb(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="arim-woo-toolbar-right">
                <div class="arim-woo-toolbar-chip"><?php esc_html_e('Trendy görünüm', 'arim'); ?></div>
                <div class="arim-woo-ordering">
                    <?php woocommerce_catalog_ordering(); ?>
                </div>
            </div>
        </div>

        <div class="arim-woo-content-layout">
            <aside class="arim-woo-sidebar">
                <form class="arim-woo-filter-form" method="get">
                    <div class="arim-woo-sidebar-box">
                        <h3><?php esc_html_e('Kategoriler', 'arim'); ?></h3>
                        <ul class="arim-woo-category-list">
                            <?php
                            $shop_categories = get_terms([
                                'taxonomy'   => 'product_cat',
                                'hide_empty' => true,
                                'parent'     => 0,
                                'number'     => 12,
                            ]);

                            if (!empty($shop_categories) && !is_wp_error($shop_categories)) :
                                foreach ($shop_categories as $category) :
                                    ?>
                                    <li>
                                        <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    </li>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>

                    <div class="arim-woo-sidebar-box">
                        <h3><?php esc_html_e('Fiyat Aralığı', 'arim'); ?></h3>

                        <div class="arim-filter-fields">
                            <div class="arim-filter-field">
                                <label for="min_price"><?php esc_html_e('Min', 'arim'); ?></label>
                                <input type="number" id="min_price" name="min_price" value="<?php echo esc_attr($current_min_price); ?>" placeholder="0">
                            </div>

                            <div class="arim-filter-field">
                                <label for="max_price"><?php esc_html_e('Max', 'arim'); ?></label>
                                <input type="number" id="max_price" name="max_price" value="<?php echo esc_attr($current_max_price); ?>" placeholder="5000">
                            </div>
                        </div>
                    </div>

                    <div class="arim-woo-sidebar-box">
                        <h3><?php esc_html_e('Ürün Durumu', 'arim'); ?></h3>

                        <label class="arim-filter-check">
                            <input type="checkbox" name="stock_status" value="instock" <?php checked($current_stock, 'instock'); ?>>
                            <span><?php esc_html_e('Sadece stokta olanlar', 'arim'); ?></span>
                        </label>

                        <label class="arim-filter-check">
                            <input type="checkbox" name="on_sale" value="1" <?php checked($current_on_sale, '1'); ?>>
                            <span><?php esc_html_e('İndirimli ürünler', 'arim'); ?></span>
                        </label>

                        <label class="arim-filter-check">
                            <input type="checkbox" name="featured" value="1" <?php checked($current_featured, '1'); ?>>
                            <span><?php esc_html_e('Öne çıkan ürünler', 'arim'); ?></span>
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
                            <div class="arim-woo-sidebar-box">
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
                        if (
                            in_array($key, ['min_price', 'max_price', 'stock_status', 'on_sale', 'featured', 'submit_filter'], true) ||
                            strpos($key, 'pa_') === 0
                        ) {
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

                    <div class="arim-woo-sidebar-box arim-woo-sidebar-actions-box">
                        <button type="submit" name="submit_filter" value="1" class="arim-filter-submit">
                            <?php esc_html_e('Filtreyi Uygula', 'arim'); ?>
                        </button>

                        <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="arim-filter-reset">
                            <?php esc_html_e('Filtreleri Temizle', 'arim'); ?>
                        </a>
                    </div>
                </form>
            </aside>

            <div class="arim-woo-main">
                <?php if (woocommerce_product_loop()) : ?>
                    <?php woocommerce_product_loop_start(); ?>

                        <?php if (wc_get_loop_prop('total')) : ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <?php do_action('woocommerce_shop_loop'); ?>
                                <?php wc_get_template_part('content', 'product'); ?>
                            <?php endwhile; ?>
                        <?php endif; ?>

                    <?php woocommerce_product_loop_end(); ?>

                    <div class="arim-woo-pagination">
                        <?php do_action('woocommerce_after_shop_loop'); ?>
                    </div>
                <?php else : ?>
                    <div class="arim-woo-empty">
                        <?php do_action('woocommerce_no_products_found'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php do_action('woocommerce_after_main_content'); ?>
    </div>
</div>

<?php
get_footer('shop');