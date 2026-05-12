<?php
defined('ABSPATH') || exit;

get_header('shop');

$current_min_price = isset($_GET['min_price']) ? wc_clean(wp_unslash($_GET['min_price'])) : '';
$current_max_price = isset($_GET['max_price']) ? wc_clean(wp_unslash($_GET['max_price'])) : '';
$current_stock     = isset($_GET['stock_status']) ? wc_clean(wp_unslash($_GET['stock_status'])) : '';
$current_on_sale   = isset($_GET['on_sale']) ? wc_clean(wp_unslash($_GET['on_sale'])) : '';
$current_featured  = isset($_GET['featured']) ? wc_clean(wp_unslash($_GET['featured'])) : '';

$product_attributes = wc_get_attribute_taxonomies();
$archive_insights   = arim_shop_archive_insights();
$active_filters     = arim_shop_active_filter_chips();
$archive_campaigns  = arim_single_product_campaigns(2);
$archive_collections = arim_shop_archive_collection_cards();
$archive_brand_links = arim_shop_archive_brand_links();
$archive_reset_url  = arim_shop_archive_current_url();
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

        <div class="arim-woo-insight-strip">
            <div class="arim-woo-insight-card">
                <span class="arim-woo-insight-label"><?php echo esc_html($archive_insights['contextTitle']); ?></span>
                <strong><?php echo esc_html(number_format_i18n((int) $archive_insights['currentCount'])); ?></strong>
                <small><?php esc_html_e('bu vitrinde görünen ürün', 'arim'); ?></small>
            </div>
            <div class="arim-woo-insight-card">
                <span class="arim-woo-insight-label"><?php esc_html_e('Toplam kategori', 'arim'); ?></span>
                <strong><?php echo esc_html(number_format_i18n((int) $archive_insights['categoryCount'])); ?></strong>
                <small><?php esc_html_e('aktif üst kategori', 'arim'); ?></small>
            </div>
            <div class="arim-woo-insight-card">
                <span class="arim-woo-insight-label"><?php esc_html_e('Öne çıkan ürünler', 'arim'); ?></span>
                <strong><?php echo esc_html(number_format_i18n((int) $archive_insights['featuredCount'])); ?></strong>
                <small><?php esc_html_e('öne alınmış ürün seçkisi', 'arim'); ?></small>
            </div>
        </div>

        <?php if (!empty($archive_campaigns)) : ?>
            <div class="arim-woo-campaign-strip">
                <div class="arim-woo-campaign-lead">
                    <span><?php echo esc_html($archive_insights['campaignKicker']); ?></span>
                    <strong><?php esc_html_e('Listelemede de kampanya görünürlüğünü koru', 'arim'); ?></strong>
                    <small><?php echo esc_html($archive_insights['deliveryText']); ?></small>
                </div>

                <div class="arim-woo-campaign-grid">
                    <?php foreach ($archive_campaigns as $campaign) : ?>
                        <div class="arim-woo-campaign-item">
                            <strong><?php echo esc_html($campaign['value']); ?></strong>
                            <span><?php echo esc_html($campaign['text']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($archive_collections)) : ?>
            <section class="arim-woo-discovery-hub" aria-label="<?php esc_attr_e('Hızlı keşif rotaları', 'arim'); ?>">
                <div class="arim-woo-discovery-head">
                    <div>
                        <span class="arim-woo-discovery-kicker"><?php esc_html_e('Hızlı keşif', 'arim'); ?></span>
                        <h2><?php esc_html_e('Alışveriş niyetine göre vitrine tek tıkla geç', 'arim'); ?></h2>
                    </div>
                    <p><?php esc_html_e('En geniş görünüm, editör seçkisi, indirim rotası ve yeni gelenleri filtre formuna inmeden ayır.', 'arim'); ?></p>
                </div>

                <div class="arim-woo-discovery-grid">
                    <?php foreach ($archive_collections as $collection) : ?>
                        <a class="arim-woo-discovery-card<?php echo !empty($collection['isActive']) ? ' is-active' : ''; ?>" href="<?php echo esc_url($collection['url']); ?>">
                            <span class="arim-woo-discovery-card-badge"><?php echo esc_html($collection['badge']); ?></span>
                            <h3><?php echo esc_html($collection['title']); ?></h3>
                            <p><?php echo esc_html($collection['text']); ?></p>
                            <strong>
                                <?php
                                printf(
                                    /* translators: %s: number of products */
                                    esc_html__('%s ürün', 'arim'),
                                    esc_html(number_format_i18n((int) $collection['count']))
                                );
                                ?>
                            </strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

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

                <?php if (!empty($active_filters)) : ?>
                    <div class="arim-woo-toolbar-chip arim-woo-toolbar-chip-muted">
                        <?php
                        printf(
                            esc_html__('%d aktif filtre', 'arim'),
                            count($active_filters)
                        );
                        ?>
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

        <?php if (!empty($active_filters)) : ?>
            <div class="arim-woo-active-filters" aria-label="<?php esc_attr_e('Aktif filtreler', 'arim'); ?>">
                <div class="arim-woo-active-filters-head">
                    <strong><?php esc_html_e('Seçili filtreler', 'arim'); ?></strong>
                    <a href="<?php echo esc_url($archive_reset_url); ?>"><?php esc_html_e('Bu vitrini sıfırla', 'arim'); ?></a>
                </div>

                <div class="arim-woo-active-filter-list">
                    <?php foreach ($active_filters as $filter_chip) : ?>
                        <a href="<?php echo esc_url($filter_chip['url']); ?>" class="arim-woo-active-filter-chip">
                            <span><?php echo esc_html($filter_chip['label']); ?></span>
                            <strong aria-hidden="true">×</strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="arim-woo-content-layout">
            <aside class="arim-woo-sidebar">
                <form class="arim-woo-filter-form" method="get">
                    <div class="arim-woo-sidebar-box arim-woo-sidebar-highlight">
                        <span class="arim-woo-sidebar-kicker"><?php echo esc_html($archive_insights['deliveryBadge']); ?></span>
                        <h3><?php esc_html_e('Keşif rehberi', 'arim'); ?></h3>
                        <p class="arim-woo-mini-note"><?php echo esc_html($archive_insights['supportText']); ?></p>

                        <div class="arim-woo-sidebar-metrics">
                            <div class="arim-woo-sidebar-metric">
                                <strong><?php echo esc_html(number_format_i18n((int) $archive_insights['catalogTotal'])); ?></strong>
                                <span><?php esc_html_e('toplam ürün', 'arim'); ?></span>
                            </div>
                            <div class="arim-woo-sidebar-metric">
                                <strong><?php echo esc_html(number_format_i18n((int) $archive_insights['featuredCount'])); ?></strong>
                                <span><?php esc_html_e('öne çıkan seçenek', 'arim'); ?></span>
                            </div>
                        </div>
                    </div>

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

                    <?php if (!empty($archive_brand_links)) : ?>
                        <div class="arim-woo-sidebar-box">
                            <h3><?php esc_html_e('Popüler markalar', 'arim'); ?></h3>
                            <div class="arim-woo-brand-pills">
                                <?php foreach ($archive_brand_links as $brand_link) : ?>
                                    <a class="arim-woo-brand-pill<?php echo !empty($brand_link['isActive']) ? ' is-active' : ''; ?>" href="<?php echo esc_url($brand_link['url']); ?>">
                                        <span><?php echo esc_html($brand_link['label']); ?></span>
                                        <strong><?php echo esc_html(number_format_i18n((int) $brand_link['count'])); ?></strong>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

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

                    <div class="arim-woo-sidebar-box arim-woo-sidebar-actions-box">
                        <button type="submit" name="submit_filter" value="1" class="arim-filter-submit">
                            <?php esc_html_e('Filtreyi Uygula', 'arim'); ?>
                        </button>

                        <a href="<?php echo esc_url($archive_reset_url); ?>" class="arim-filter-reset">
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
                        <p class="arim-woo-empty-note"><?php esc_html_e('Filtreleri biraz gevşeterek veya kampanya alanından farklı vitrinlere geçerek daha fazla ürün keşfedebilirsin.', 'arim'); ?></p>
                        <div class="arim-woo-empty-actions">
                            <a href="<?php echo esc_url($archive_reset_url); ?>" class="arim-woo-empty-link is-primary"><?php esc_html_e('Genel vitrini aç', 'arim'); ?></a>
                            <?php if (!empty($archive_collections[1]['url'])) : ?>
                                <a href="<?php echo esc_url($archive_collections[1]['url']); ?>" class="arim-woo-empty-link"><?php esc_html_e('Öne çıkanları keşfet', 'arim'); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($archive_collections[2]['url'])) : ?>
                                <a href="<?php echo esc_url($archive_collections[2]['url']); ?>" class="arim-woo-empty-link"><?php esc_html_e('İndirim rotasına geç', 'arim'); ?></a>
                            <?php endif; ?>
                        </div>
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
