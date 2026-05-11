<?php
defined('ABSPATH') || exit;

get_header();

if (!function_exists('WC')) {
    echo '<div class="arim-container" style="padding:40px 0;">WooCommerce aktif değil.</div>';
    get_footer();
    return;
}

$shop_url    = function_exists('arim_shop_url') ? arim_shop_url() : home_url('/shop');
$account_url = function_exists('arim_account_url') ? arim_account_url() : wp_login_url();

function arim_homepage_get_category_slugs_with_children($category_slug) {
    if (!$category_slug) {
        return [];
    }

    $term = get_term_by('slug', $category_slug, 'product_cat');
    if (!$term || is_wp_error($term)) {
        return [$category_slug];
    }

    $child_ids = get_term_children($term->term_id, 'product_cat');
    $slugs = [$term->slug];

    if (!empty($child_ids) && !is_wp_error($child_ids)) {
        foreach ($child_ids as $child_id) {
            $child_term = get_term($child_id, 'product_cat');
            if ($child_term && !is_wp_error($child_term)) {
                $slugs[] = $child_term->slug;
            }
        }
    }

    return array_unique($slugs);
}

function arim_homepage_parse_manual_ids($manual_ids) {
    if (!$manual_ids) {
        return [];
    }

    $parts = explode(',', $manual_ids);
    $ids = array_map('intval', $parts);
    $ids = array_filter($ids, function($id) {
        return $id > 0;
    });

    return array_values(array_unique($ids));
}

function arim_homepage_get_products_by_source($source = 'latest', $limit = 10, $category_slug = '', $include_children = false, $manual_ids = '') {
    $limit = max(1, intval($limit));

    if ($source === 'manual') {
        $ids = arim_homepage_parse_manual_ids($manual_ids);

        if (empty($ids)) {
            return [];
        }

        return wc_get_products([
            'status'   => 'publish',
            'include'  => $ids,
            'limit'    => $limit,
            'orderby'  => 'include',
        ]);
    }

    $args = [
        'status' => 'publish',
        'limit'  => $limit,
    ];

    switch ($source) {
        case 'featured':
            $args['featured'] = true;
            break;

        case 'on_sale':
            $args['on_sale'] = true;
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;

        case 'popularity':
            $args['orderby'] = 'popularity';
            $args['order']   = 'DESC';
            break;

        case 'category':
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            if ($category_slug) {
                $args['category'] = $include_children ? arim_homepage_get_category_slugs_with_children($category_slug) : [$category_slug];
            }
            break;

        case 'latest':
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    return wc_get_products($args);
}

$featured_products = wc_get_products([
    'status'   => 'publish',
    'limit'    => 12,
    'featured' => true,
]);

$product_categories = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
    'number'     => 12,
]);

$homepage_categories = array_slice(is_array($product_categories) ? $product_categories : [], 0, 10);
$flash_sale_products = arim_homepage_get_products_by_source('on_sale', 6);

if (empty($flash_sale_products)) {
    $flash_sale_products = arim_homepage_get_products_by_source('featured', 6);
}

$flash_sale_deadline    = strtotime('+2 days 21:00:00');
$flash_sale_deadline_ts = $flash_sale_deadline ? $flash_sale_deadline * 1000 : 0;

$show_hero       = arim_homepage_option('arim_section_show_hero', '1') === '1';
$show_coupons    = arim_homepage_option('arim_section_show_coupons', '1') === '1';
$show_mosaic     = arim_homepage_option('arim_section_show_mosaic', '1') === '1';
$show_showcase1  = arim_homepage_option('arim_section_show_showcase_1', '1') === '1';
$show_showcase2  = arim_homepage_option('arim_section_show_showcase_2', '1') === '1';
$show_showcase3  = arim_homepage_option('arim_section_show_showcase_3', '1') === '1';
$show_seo        = arim_homepage_option('arim_section_show_seo', '1') === '1';

$section_order_hero       = intval(arim_homepage_option('arim_section_order_hero', '10'));
$section_order_coupons    = intval(arim_homepage_option('arim_section_order_coupons', '20'));
$section_order_slider     = intval(arim_homepage_option('arim_section_order_slider', '30'));
$section_order_mosaic     = intval(arim_homepage_option('arim_section_order_mosaic', '40'));
$section_order_showcase_1 = intval(arim_homepage_option('arim_section_order_showcase_1', '50'));
$section_order_showcase_2 = intval(arim_homepage_option('arim_section_order_showcase_2', '60'));
$section_order_showcase_3 = intval(arim_homepage_option('arim_section_order_showcase_3', '70'));
$section_order_seo        = intval(arim_homepage_option('arim_section_order_seo', '80'));

$showcase_1_source           = arim_homepage_option('arim_showcase_1_source', 'latest');
$showcase_1_category_slug    = arim_homepage_option('arim_showcase_1_category_slug', '');
$showcase_1_limit            = intval(arim_homepage_option('arim_showcase_1_limit', '10'));
$showcase_1_include_children = arim_homepage_option('arim_showcase_1_include_children', '0') === '1';
$showcase_1_manual_ids       = arim_homepage_option('arim_showcase_1_manual_ids', '');

$showcase_2_source           = arim_homepage_option('arim_showcase_2_source', 'on_sale');
$showcase_2_category_slug    = arim_homepage_option('arim_showcase_2_category_slug', '');
$showcase_2_limit            = intval(arim_homepage_option('arim_showcase_2_limit', '8'));
$showcase_2_include_children = arim_homepage_option('arim_showcase_2_include_children', '0') === '1';
$showcase_2_manual_ids       = arim_homepage_option('arim_showcase_2_manual_ids', '');

$showcase_3_source           = arim_homepage_option('arim_showcase_3_source', 'popularity');
$showcase_3_category_slug    = arim_homepage_option('arim_showcase_3_category_slug', '');
$showcase_3_limit            = intval(arim_homepage_option('arim_showcase_3_limit', '10'));
$showcase_3_include_children = arim_homepage_option('arim_showcase_3_include_children', '0') === '1';
$showcase_3_manual_ids       = arim_homepage_option('arim_showcase_3_manual_ids', '');

$showcase_1_preset = arim_homepage_option('arim_showcase_1_preset', 'orange');
$showcase_2_preset = arim_homepage_option('arim_showcase_2_preset', 'pink');
$showcase_3_preset = arim_homepage_option('arim_showcase_3_preset', 'clean');

$showcase_1_products = arim_homepage_get_products_by_source($showcase_1_source, $showcase_1_limit, $showcase_1_category_slug, $showcase_1_include_children, $showcase_1_manual_ids);
$showcase_2_products = arim_homepage_get_products_by_source($showcase_2_source, $showcase_2_limit, $showcase_2_category_slug, $showcase_2_include_children, $showcase_2_manual_ids);
$showcase_3_products = arim_homepage_get_products_by_source($showcase_3_source, $showcase_3_limit, $showcase_3_category_slug, $showcase_3_include_children, $showcase_3_manual_ids);

$hero_main_badge = arim_homepage_option('arim_hero_main_badge', 'Yeni Sezon');
$hero_main_title = arim_homepage_option('arim_hero_main_title', 'Trend ürünleri keşfet ve vitrini güçlendir');
$hero_main_text  = arim_homepage_option('arim_hero_main_text', 'Yoğun, modern ve marketplace hissi veren güçlü kampanya yüzeyi ile öne çıkan ürünlerini sergile.');
$hero_main_link  = arim_homepage_option('arim_hero_main_link', home_url('/shop'));
$hero_main_image = arim_homepage_option('arim_hero_main_image', '');
$hero_main_mode  = arim_homepage_option('arim_hero_main_image_mode', 'background');

$hero_point_1 = arim_homepage_option('arim_hero_point_1', 'Hızlı Teslimat');
$hero_point_2 = arim_homepage_option('arim_hero_point_2', 'Güvenli Ödeme');
$hero_point_3 = arim_homepage_option('arim_hero_point_3', 'Seçili Ürünlerde Fırsat');

$hero_chip_1 = arim_homepage_option('arim_hero_chip_1', 'Sepette Ekstra İndirim');
$hero_chip_2 = arim_homepage_option('arim_hero_chip_2', 'Hızlı Teslimat');
$hero_chip_3 = arim_homepage_option('arim_hero_chip_3', 'Çok Satanlar');
$hero_chip_4 = arim_homepage_option('arim_hero_chip_4', 'Yeni Sezon Fırsatları');

$hero_box_1_badge = arim_homepage_option('arim_hero_box_1_badge', 'Kampanya');
$hero_box_1_title = arim_homepage_option('arim_hero_box_1_title', 'Ücretsiz Kargo Fırsatı');
$hero_box_1_text  = arim_homepage_option('arim_hero_box_1_text', 'Seçili ürünlerde avantaj');
$hero_box_1_link  = arim_homepage_option('arim_hero_box_1_link', home_url('/shop'));
$hero_box_1_image = arim_homepage_option('arim_hero_box_1_image', '');
$hero_box_1_mode  = arim_homepage_option('arim_hero_box_1_image_mode', 'background');

$hero_box_2_badge = arim_homepage_option('arim_hero_box_2_badge', 'Popüler');
$hero_box_2_title = arim_homepage_option('arim_hero_box_2_title', 'En Çok İlgi Görenler');
$hero_box_2_text  = arim_homepage_option('arim_hero_box_2_text', 'Yoğun ilgi gören ürünler');
$hero_box_2_link  = arim_homepage_option('arim_hero_box_2_link', home_url('/shop'));
$hero_box_2_image = arim_homepage_option('arim_hero_box_2_image', '');
$hero_box_2_mode  = arim_homepage_option('arim_hero_box_2_image_mode', 'background');

$hero_float_1_label = arim_homepage_option('arim_hero_float_1_label', 'Kampanya');
$hero_float_1_title = arim_homepage_option('arim_hero_float_1_title', '%30');
$hero_float_1_text  = arim_homepage_option('arim_hero_float_1_text', 'Sepette ekstra indirim');

$hero_float_2_label = arim_homepage_option('arim_hero_float_2_label', 'Popüler');
$hero_float_2_title = arim_homepage_option('arim_hero_float_2_title', 'Yeni Koleksiyon');
$hero_float_2_text  = arim_homepage_option('arim_hero_float_2_text', 'Sezonun öne çıkan ürünleri');

$mosaic_1_badge = arim_homepage_option('arim_mosaic_1_badge', 'Flaş Fırsat');
$mosaic_1_title = arim_homepage_option('arim_mosaic_1_title', 'Bugüne özel seçili kampanyalar');
$mosaic_1_link  = arim_homepage_option('arim_mosaic_1_link', home_url('/shop'));
$mosaic_1_image = arim_homepage_option('arim_mosaic_1_image', '');
$mosaic_1_mode  = arim_homepage_option('arim_mosaic_1_image_mode', 'background');

$mosaic_2_badge = arim_homepage_option('arim_mosaic_2_badge', 'Kadın');
$mosaic_2_title = arim_homepage_option('arim_mosaic_2_title', 'Stilini yenile');
$mosaic_2_link  = arim_homepage_option('arim_mosaic_2_link', home_url('/shop'));
$mosaic_2_image = arim_homepage_option('arim_mosaic_2_image', '');
$mosaic_2_mode  = arim_homepage_option('arim_mosaic_2_image_mode', 'background');

$mosaic_3_badge = arim_homepage_option('arim_mosaic_3_badge', 'Kozmetik');
$mosaic_3_title = arim_homepage_option('arim_mosaic_3_title', 'Günlük bakım seçkisi');
$mosaic_3_link  = arim_homepage_option('arim_mosaic_3_link', home_url('/shop'));
$mosaic_3_image = arim_homepage_option('arim_mosaic_3_image', '');
$mosaic_3_mode  = arim_homepage_option('arim_mosaic_3_image_mode', 'background');

$mosaic_4_badge = arim_homepage_option('arim_mosaic_4_badge', 'Ev & Yaşam');
$mosaic_4_title = arim_homepage_option('arim_mosaic_4_title', 'Yaşam alanını yenile');
$mosaic_4_link  = arim_homepage_option('arim_mosaic_4_link', home_url('/shop'));
$mosaic_4_image = arim_homepage_option('arim_mosaic_4_image', '');
$mosaic_4_mode  = arim_homepage_option('arim_mosaic_4_image_mode', 'background');

$mosaic_5_badge = arim_homepage_option('arim_mosaic_5_badge', 'Moda');
$mosaic_5_title = arim_homepage_option('arim_mosaic_5_title', 'Trend parçaları keşfet');
$mosaic_5_link  = arim_homepage_option('arim_mosaic_5_link', home_url('/shop'));
$mosaic_5_image = arim_homepage_option('arim_mosaic_5_image', '');
$mosaic_5_mode  = arim_homepage_option('arim_mosaic_5_image_mode', 'background');

$mosaic_6_badge = arim_homepage_option('arim_mosaic_6_badge', 'Aksesuar');
$mosaic_6_title = arim_homepage_option('arim_mosaic_6_title', 'Kombinini tamamla');
$mosaic_6_link  = arim_homepage_option('arim_mosaic_6_link', home_url('/shop'));
$mosaic_6_image = arim_homepage_option('arim_mosaic_6_image', '');
$mosaic_6_mode  = arim_homepage_option('arim_mosaic_6_image_mode', 'background');

$coupon_1_value = arim_homepage_option('arim_coupon_1_value', '%35');
$coupon_1_text  = arim_homepage_option('arim_coupon_1_text', 'Sepette ekstra indirim');

$coupon_2_value = arim_homepage_option('arim_coupon_2_value', '%10');
$coupon_2_text  = arim_homepage_option('arim_coupon_2_text', 'Yeni üyeye özel fırsat');

$coupon_3_value = arim_homepage_option('arim_coupon_3_value', '%30');
$coupon_3_text  = arim_homepage_option('arim_coupon_3_text', 'Seçili kategorilerde avantaj');

$coupon_4_value = arim_homepage_option('arim_coupon_4_value', '%50');
$coupon_4_text  = arim_homepage_option('arim_coupon_4_text', 'Kampanya ürünlerinde fırsat');

$mixed_promo_1_badge = arim_homepage_option('arim_mixed_promo_1_badge', 'Flaş Kampanya');
$mixed_promo_1_title = arim_homepage_option('arim_mixed_promo_1_title', 'Sepette ekstra avantaj fırsatı');
$mixed_promo_1_text  = arim_homepage_option('arim_mixed_promo_1_text', 'Belirli ürünlerde güncel fırsatları kaçırma.');

$mixed_promo_2_badge = arim_homepage_option('arim_mixed_promo_2_badge', 'Trend Alan');
$mixed_promo_2_title = arim_homepage_option('arim_mixed_promo_2_title', 'Yeni sezon fırsatlarını keşfet');
$mixed_promo_2_text  = arim_homepage_option('arim_mixed_promo_2_text', 'Kısa süreli öne çıkan kampanya seçkileri.');

$showcase_1_title     = arim_homepage_option('arim_showcase_1_title', 'Senin İçin Seçtiklerimiz');
$showcase_1_link_text = arim_homepage_option('arim_showcase_1_link_text', 'Hepsini Gör');
$showcase_1_link_url  = arim_homepage_option('arim_showcase_1_link_url', home_url('/shop'));

$showcase_2_title     = arim_homepage_option('arim_showcase_2_title', 'Fırsat Ürünleri');
$showcase_2_link_text = arim_homepage_option('arim_showcase_2_link_text', 'İndirimdekileri Gör');
$showcase_2_link_url  = arim_homepage_option('arim_showcase_2_link_url', home_url('/shop/?on_sale=1'));

$showcase_3_title     = arim_homepage_option('arim_showcase_3_title', 'Çok Satanlar');
$showcase_3_link_text = arim_homepage_option('arim_showcase_3_link_text', 'En Çok Satanları Gör');
$showcase_3_link_url  = arim_homepage_option('arim_showcase_3_link_url', home_url('/shop'));

function arim_home_brand_name($product_id) {
    $brand = get_post_meta($product_id, 'brand', true);

    if (!$brand) {
        $terms = get_the_terms($product_id, 'product_brand');
        if (!empty($terms) && !is_wp_error($terms)) {
            $brand = $terms[0]->name;
        }
    }

    return $brand;
}

function arim_home_store_name($product_id) {
    $store = get_post_meta($product_id, 'store_name', true);
    return $store ? $store : __('ARIM Store', 'arim');
}

function arim_home_category_image_url($term) {
    if (!$term || is_wp_error($term)) {
        return '';
    }

    $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);

    if (!$thumbnail_id) {
        return '';
    }

    return wp_get_attachment_image_url($thumbnail_id, 'woocommerce_thumbnail');
}

function arim_home_collect_brand_names($products, $limit = 8) {
    $brands = [];

    foreach ((array) $products as $product) {
        if (!$product instanceof WC_Product) {
            continue;
        }

        $brand = arim_home_brand_name($product->get_id());

        if (!$brand) {
            $terms = get_the_terms($product->get_id(), 'product_cat');
            if (!empty($terms) && !is_wp_error($terms)) {
                $brand = $terms[0]->name;
            }
        }

        if ($brand) {
            $brands[sanitize_title($brand)] = $brand;
        }

        if (count($brands) >= $limit) {
            break;
        }
    }

    return array_values($brands);
}

function arim_render_home_product_card_v5($product, $badge = '', $context = 'slider') {
    if (!$product) return;

    $product_id    = $product->get_id();
    $image_id      = $product->get_image_id();
    $image_url     = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src();
    $brand         = arim_home_brand_name($product_id);
    $store_name    = arim_home_store_name($product_id);
    $rating        = $product->get_average_rating();
    $reviews       = $product->get_review_count();
    $product_url   = get_permalink($product_id);
    $price_html    = $product->get_price_html();
    $price_text    = arim_product_price_text($product);
    $current_price_value = (float) $product->get_price();
    $regular_price_value = (float) $product->get_regular_price();
    $article_class = 'arim-v5-product-card';

    if ($context === 'slider') {
        $article_class .= ' arim-slider-card';
    }

    $micro_badge = __('Hızlı Teslimat', 'arim');
    if ($reviews > 20) {
        $micro_badge = __('Çok Satan', 'arim');
    } elseif ($product->is_featured()) {
        $micro_badge = __('Öne Çıkan', 'arim');
    }
    ?>
    <article class="<?php echo esc_attr($article_class); ?>">
        <a href="<?php echo esc_url($product_url); ?>" class="arim-v5-product-link">
            <div class="arim-v5-product-image-wrap">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">

                <button
                    class="arim-favorite-btn"
                    type="button"
                    aria-label="<?php esc_attr_e('Favorilere ekle', 'arim'); ?>"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                    data-product-price="<?php echo esc_attr($price_text); ?>"
                    data-product-image="<?php echo esc_url($image_url); ?>"
                    data-product-url="<?php echo esc_url($product_url); ?>"
                    data-product-brand="<?php echo esc_attr($brand ? $brand : __('ARIM', 'arim')); ?>"
                    data-product-store="<?php echo esc_attr($store_name); ?>"
                    data-product-badge="<?php echo esc_attr($badge ? $badge : $micro_badge); ?>"
                    data-product-current-price="<?php echo esc_attr($current_price_value); ?>"
                    data-product-regular-price="<?php echo esc_attr($regular_price_value); ?>"
                >♡</button>

                <button
                    class="arim-compare-btn"
                    type="button"
                    aria-label="<?php esc_attr_e('Karşılaştırmaya ekle', 'arim'); ?>"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                    data-product-price="<?php echo esc_attr($price_text); ?>"
                    data-product-image="<?php echo esc_url($image_url); ?>"
                    data-product-url="<?php echo esc_url($product_url); ?>"
                    data-product-brand="<?php echo esc_attr($brand ? $brand : __('ARIM', 'arim')); ?>"
                    data-product-store="<?php echo esc_attr($store_name); ?>"
                    data-product-badge="<?php echo esc_attr($badge ? $badge : $micro_badge); ?>"
                    data-product-current-price="<?php echo esc_attr($current_price_value); ?>"
                    data-product-regular-price="<?php echo esc_attr($regular_price_value); ?>"
                >⇄</button>

                <?php if ($badge) : ?>
                    <span class="arim-v5-product-badge"><?php echo esc_html($badge); ?></span>
                <?php endif; ?>

                <span class="arim-v5-product-micro-badge"><?php echo esc_html($micro_badge); ?></span>
            </div>

            <div class="arim-v5-product-content">
                <div class="arim-v5-product-store"><?php echo esc_html($store_name); ?></div>

                <h3 class="arim-v5-product-title">
                    <?php if ($brand) : ?>
                        <strong><?php echo esc_html($brand); ?></strong>
                    <?php endif; ?>
                    <span><?php echo esc_html($product->get_name()); ?></span>
                </h3>

                <?php if ($rating > 0) : ?>
                    <div class="arim-v5-product-rating">
                        <span class="arim-rating-score"><?php echo esc_html(number_format((float) $rating, 1)); ?></span>
                        <span class="arim-rating-stars">★★★★★</span>
                        <span class="arim-rating-count">(<?php echo esc_html($reviews); ?>)</span>
                    </div>
                <?php else : ?>
                    <div class="arim-v5-product-rating arim-v5-product-rating-empty">
                        <span class="arim-rating-stars">★★★★★</span>
                        <span class="arim-rating-count"><?php esc_html_e('Yeni ürün', 'arim'); ?></span>
                    </div>
                <?php endif; ?>

                <div class="arim-v5-product-price">
                    <?php echo wp_kses_post($price_html); ?>
                </div>

                <div class="arim-v5-product-meta-row">
                    <span class="shipping"><?php esc_html_e('Kargo Bedava', 'arim'); ?></span>
                    <span class="basket-discount"><?php esc_html_e('Sepette indirim', 'arim'); ?></span>
                </div>
            </div>
        </a>
    </article>
    <?php
}

function arim_render_mixed_promo_card($modifier, $badge, $title, $text = '') {
    ?>
    <div class="arim-v4-mixed-promo-card <?php echo esc_attr($modifier); ?>">
        <span><?php echo esc_html($badge); ?></span>
        <strong><?php echo esc_html($title); ?></strong>
        <?php if ($text) : ?>
            <p><?php echo esc_html($text); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

function arim_media_mode_class($mode) {
    $allowed = ['background', 'soft', 'cover', 'visual'];
    if (!in_array($mode, $allowed, true)) {
        $mode = 'background';
    }
    return 'is-media-' . $mode;
}

function arim_media_inline_style($image_url, $mode, $palette = 'hero') {
    if (!$image_url) {
        return '';
    }

    $image_url = esc_url_raw($image_url);

    $styles = [
        'hero' => [
            'background' => "background-image:linear-gradient(135deg, rgba(255,146,46,0.88), rgba(255,179,71,0.82)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(135deg, rgba(255,146,46,0.93), rgba(255,179,71,0.90)), url('{$image_url}'); background-size:cover; background-position:center;",
            'cover'      => "background-image:linear-gradient(135deg, rgba(255,146,46,0.50), rgba(255,179,71,0.42)), url('{$image_url}'); background-size:cover; background-position:center;",
            'visual'     => "background-image:url('{$image_url}'); background-size:cover; background-position:center;",
        ],
        'herobox1' => [
            'background' => "background-image:linear-gradient(180deg, rgba(255,247,239,0.86), rgba(255,255,255,0.92)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(180deg, rgba(255,247,239,0.95), rgba(255,255,255,0.98)), url('{$image_url}'); background-size:cover; background-position:center;",
        ],
        'herobox2' => [
            'background' => "background-image:linear-gradient(180deg, rgba(250,250,250,0.88), rgba(255,255,255,0.94)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(180deg, rgba(250,250,250,0.96), rgba(255,255,255,0.99)), url('{$image_url}'); background-size:cover; background-position:center;",
        ],
        'coral' => [
            'background' => "background-image:linear-gradient(135deg, rgba(255,126,95,0.78), rgba(254,180,123,0.74)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(135deg, rgba(255,126,95,0.90), rgba(254,180,123,0.86)), url('{$image_url}'); background-size:cover; background-position:center;",
            'cover'      => "background-image:linear-gradient(135deg, rgba(255,126,95,0.50), rgba(254,180,123,0.44)), url('{$image_url}'); background-size:cover; background-position:center;",
            'visual'     => "background-image:linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.42)), url('{$image_url}'); background-size:cover; background-position:center;",
        ],
        'pink' => [
            'background' => "background-image:linear-gradient(135deg, rgba(255,117,140,0.78), rgba(255,126,179,0.74)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(135deg, rgba(255,117,140,0.90), rgba(255,126,179,0.86)), url('{$image_url}'); background-size:cover; background-position:center;",
            'cover'      => "background-image:linear-gradient(135deg, rgba(255,117,140,0.50), rgba(255,126,179,0.44)), url('{$image_url}'); background-size:cover; background-position:center;",
            'visual'     => "background-image:linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.42)), url('{$image_url}'); background-size:cover; background-position:center;",
        ],
        'yellow' => [
            'background' => "background-image:linear-gradient(135deg, rgba(247,151,30,0.80), rgba(255,210,0,0.74)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(135deg, rgba(247,151,30,0.92), rgba(255,210,0,0.86)), url('{$image_url}'); background-size:cover; background-position:center;",
            'cover'      => "background-image:linear-gradient(135deg, rgba(247,151,30,0.52), rgba(255,210,0,0.44)), url('{$image_url}'); background-size:cover; background-position:center;",
            'visual'     => "background-image:linear-gradient(180deg, rgba(0,0,0,0.00), rgba(0,0,0,0.30)), url('{$image_url}'); background-size:cover; background-position:center;",
        ],
        'purple' => [
            'background' => "background-image:linear-gradient(135deg, rgba(139,92,246,0.78), rgba(109,40,217,0.74)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(135deg, rgba(139,92,246,0.90), rgba(109,40,217,0.86)), url('{$image_url}'); background-size:cover; background-position:center;",
            'cover'      => "background-image:linear-gradient(135deg, rgba(139,92,246,0.50), rgba(109,40,217,0.44)), url('{$image_url}'); background-size:cover; background-position:center;",
            'visual'     => "background-image:linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.44)), url('{$image_url}'); background-size:cover; background-position:center;",
        ],
        'orange' => [
            'background' => "background-image:linear-gradient(135deg, rgba(255,147,66,0.80), rgba(255,126,47,0.74)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(135deg, rgba(255,147,66,0.92), rgba(255,126,47,0.86)), url('{$image_url}'); background-size:cover; background-position:center;",
            'cover'      => "background-image:linear-gradient(135deg, rgba(255,147,66,0.50), rgba(255,126,47,0.44)), url('{$image_url}'); background-size:cover; background-position:center;",
            'visual'     => "background-image:linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.42)), url('{$image_url}'); background-size:cover; background-position:center;",
        ],
        'blue' => [
            'background' => "background-image:linear-gradient(135deg, rgba(54,209,220,0.80), rgba(91,134,229,0.74)), url('{$image_url}'); background-size:cover; background-position:center;",
            'soft'       => "background-image:linear-gradient(135deg, rgba(54,209,220,0.92), rgba(91,134,229,0.86)), url('{$image_url}'); background-size:cover; background-position:center;",
            'cover'      => "background-image:linear-gradient(135deg, rgba(54,209,220,0.50), rgba(91,134,229,0.44)), url('{$image_url}'); background-size:cover; background-position:center;",
            'visual'     => "background-image:linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.42)), url('{$image_url}'); background-size:cover; background-position:center;",
        ],
    ];

    if (!isset($styles[$palette])) {
        return '';
    }

    if (!isset($styles[$palette][$mode])) {
        $mode = 'background';
    }

    return $styles[$palette][$mode];
}

function arim_showcase_preset_class($showcase_number, $preset) {
    return 'arim-showcase-preset-' . absint($showcase_number) . '-' . sanitize_html_class($preset);
}

function arim_render_homepage_section($key, $callback, &$sections) {
    $sections[] = [
        'key'      => $key,
        'order'    => isset($callback['order']) ? intval($callback['order']) : 999,
        'render'   => $callback['render'],
        'enabled'  => !empty($callback['enabled']),
    ];
}

$marketplace_label_limit = 8;

$marketplace_labels = arim_home_collect_brand_names(
    array_merge($featured_products, $showcase_1_products, $showcase_2_products, $showcase_3_products),
    $marketplace_label_limit
);

if (empty($marketplace_labels) && !empty($homepage_categories)) {
    $marketplace_labels = array_values(array_filter(array_map(static function($term) {
        return (is_object($term) && isset($term->name)) ? $term->name : '';
    }, array_slice($homepage_categories, 0, $marketplace_label_limit))));
}
?>

<?php
$homepage_sections = [];

arim_render_homepage_section('categories', [
    'order'   => 5,
    'enabled' => !empty($homepage_categories),
    'render'  => function() use ($homepage_categories) {
        ?>
        <section class="arim-v4-categories arim-v4-categories-strong">
            <div class="arim-container">
                <div class="arim-v4-categories-strip">
                    <?php foreach ($homepage_categories as $category) : ?>
                        <?php $category_image = arim_home_category_image_url($category); ?>
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="arim-v4-category-item">
                            <span class="arim-v4-category-image">
                                <?php if ($category_image) : ?>
                                    <img src="<?php echo esc_url($category_image); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                <?php else : ?>
                                    <span class="arim-v4-category-fallback"><?php echo esc_html(mb_substr($category->name, 0, 1)); ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="arim-v4-category-text"><?php echo esc_html($category->name); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('hero', [
    'order'   => $section_order_hero,
    'enabled' => $show_hero,
    'render'  => function() use (
        $hero_main_link, $hero_main_mode, $hero_main_image, $hero_main_badge, $hero_main_title, $hero_main_text,
        $hero_point_1, $hero_point_2, $hero_point_3,
        $hero_float_1_label, $hero_float_1_title, $hero_float_1_text,
        $hero_float_2_label, $hero_float_2_title, $hero_float_2_text,
        $hero_box_1_link, $hero_box_1_mode, $hero_box_1_image, $hero_box_1_badge, $hero_box_1_title, $hero_box_1_text,
        $hero_box_2_link, $hero_box_2_mode, $hero_box_2_image, $hero_box_2_badge, $hero_box_2_title, $hero_box_2_text,
        $hero_chip_1, $hero_chip_2, $hero_chip_3, $hero_chip_4
    ) {
        ?>
        <section class="arim-v4-hero arim-v4-hero-polished">
            <div class="arim-container">
                <div class="arim-v4-hero-grid">
                    <a href="<?php echo esc_url($hero_main_link); ?>"
                       class="arim-v4-hero-main arim-v4-hero-campaign-surface <?php echo esc_attr(arim_media_mode_class($hero_main_mode)); ?>"
                       <?php if ($hero_main_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($hero_main_image, $hero_main_mode, 'hero')); ?>"<?php endif; ?>>
                        <div class="arim-v4-hero-surface-glow one"></div>
                        <div class="arim-v4-hero-surface-glow two"></div>
                        <div class="arim-v4-hero-surface-glow three"></div>

                        <div class="arim-v4-hero-main-content">
                            <span class="arim-v4-hero-badge"><?php echo esc_html($hero_main_badge); ?></span>
                            <h1><?php echo esc_html($hero_main_title); ?></h1>
                            <p><?php echo esc_html($hero_main_text); ?></p>

                            <div class="arim-v4-hero-mini-points">
                                <span><?php echo esc_html($hero_point_1); ?></span>
                                <span><?php echo esc_html($hero_point_2); ?></span>
                                <span><?php echo esc_html($hero_point_3); ?></span>
                            </div>
                        </div>

                        <div class="arim-v4-hero-floating-card card-one">
                            <span class="label"><?php echo esc_html($hero_float_1_label); ?></span>
                            <strong><?php echo esc_html($hero_float_1_title); ?></strong>
                            <small><?php echo esc_html($hero_float_1_text); ?></small>
                        </div>

                        <div class="arim-v4-hero-floating-card card-two">
                            <span class="label"><?php echo esc_html($hero_float_2_label); ?></span>
                            <strong><?php echo esc_html($hero_float_2_title); ?></strong>
                            <small><?php echo esc_html($hero_float_2_text); ?></small>
                        </div>
                    </a>

                    <div class="arim-v4-hero-side">
                        <a href="<?php echo esc_url($hero_box_1_link); ?>"
                           class="arim-v4-hero-small orange arim-v4-hero-promo-card <?php echo esc_attr(arim_media_mode_class($hero_box_1_mode)); ?>"
                           <?php if ($hero_box_1_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($hero_box_1_image, $hero_box_1_mode, 'herobox1')); ?>"<?php endif; ?>>
                            <span><?php echo esc_html($hero_box_1_badge); ?></span>
                            <strong><?php echo esc_html($hero_box_1_title); ?></strong>
                            <em><?php echo esc_html($hero_box_1_text); ?></em>
                        </a>

                        <a href="<?php echo esc_url($hero_box_2_link); ?>"
                           class="arim-v4-hero-small light arim-v4-hero-promo-card <?php echo esc_attr(arim_media_mode_class($hero_box_2_mode)); ?>"
                           <?php if ($hero_box_2_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($hero_box_2_image, $hero_box_2_mode, 'herobox2')); ?>"<?php endif; ?>>
                            <span><?php echo esc_html($hero_box_2_badge); ?></span>
                            <strong><?php echo esc_html($hero_box_2_title); ?></strong>
                            <em><?php echo esc_html($hero_box_2_text); ?></em>
                        </a>
                    </div>
                </div>

                <div class="arim-v5-hero-chip-bar">
                    <a href="<?php echo esc_url(home_url('/shop')); ?>" class="arim-v5-hero-chip"><?php echo esc_html($hero_chip_1); ?></a>
                    <a href="<?php echo esc_url(home_url('/shop')); ?>" class="arim-v5-hero-chip"><?php echo esc_html($hero_chip_2); ?></a>
                    <a href="<?php echo esc_url(home_url('/shop')); ?>" class="arim-v5-hero-chip"><?php echo esc_html($hero_chip_3); ?></a>
                    <a href="<?php echo esc_url(home_url('/shop')); ?>" class="arim-v5-hero-chip"><?php echo esc_html($hero_chip_4); ?></a>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('coupons', [
    'order'   => $section_order_coupons,
    'enabled' => $show_coupons,
    'render'  => function() use ($coupon_1_value, $coupon_1_text, $coupon_2_value, $coupon_2_text, $coupon_3_value, $coupon_3_text, $coupon_4_value, $coupon_4_text) {
        ?>
        <section class="arim-v4-coupons">
            <div class="arim-container">
                <div class="arim-v4-coupon-grid">
                    <div class="arim-v4-coupon orange">
                        <strong><?php echo esc_html($coupon_1_value); ?></strong>
                        <span><?php echo esc_html($coupon_1_text); ?></span>
                    </div>
                    <div class="arim-v4-coupon green">
                        <strong><?php echo esc_html($coupon_2_value); ?></strong>
                        <span><?php echo esc_html($coupon_2_text); ?></span>
                    </div>
                    <div class="arim-v4-coupon blue">
                        <strong><?php echo esc_html($coupon_3_value); ?></strong>
                        <span><?php echo esc_html($coupon_3_text); ?></span>
                    </div>
                    <div class="arim-v4-coupon red">
                        <strong><?php echo esc_html($coupon_4_value); ?></strong>
                        <span><?php echo esc_html($coupon_4_text); ?></span>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('slider', [
    'order'   => $section_order_slider,
    'enabled' => !empty($featured_products),
    'render'  => function() use ($featured_products) {
        ?>
        <section class="arim-v4-slider-section">
            <div class="arim-container">
                <div class="arim-v4-section-head">
                    <h2><?php esc_html_e('Popüler Ürünler', 'arim'); ?></h2>
                    <a href="<?php echo esc_url(home_url('/shop')); ?>"><?php esc_html_e('Tümünü Gör', 'arim'); ?></a>
                </div>

                <div class="arim-widget-slider" data-slider>
                    <button class="arim-widget-arrow prev" type="button" data-prev aria-label="<?php esc_attr_e('Önceki ürünler', 'arim'); ?>">‹</button>
                    <div class="arim-widget-track-wrap">
                        <div class="arim-widget-track" data-track>
                            <?php foreach ($featured_products as $product) : ?>
                                <?php arim_render_home_product_card_v5($product, __('Öne Çıkan', 'arim'), 'slider'); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="arim-widget-arrow next" type="button" data-next aria-label="<?php esc_attr_e('Sonraki ürünler', 'arim'); ?>">›</button>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('flash_deals', [
    'order'   => 35,
    'enabled' => !empty($flash_sale_products),
    'render'  => function() use ($flash_sale_products, $flash_sale_deadline_ts, $shop_url) {
        ?>
        <section class="arim-v6-flash-deals">
            <div class="arim-container">
                <div class="arim-v6-flash-shell">
                    <div class="arim-v6-flash-intro" data-countdown-ts="<?php echo esc_attr($flash_sale_deadline_ts); ?>">
                        <span class="arim-v6-flash-badge"><?php esc_html_e('Flaş Fırsatlar', 'arim'); ?></span>
                        <h2><?php esc_html_e('Bugünün en güçlü kampanya vitrini', 'arim'); ?></h2>
                        <p><?php esc_html_e('Trendyol hissini güçlendiren yoğun indirim alanı ile hızlı karar verdiren ürünleri tek blokta öne çıkar.', 'arim'); ?></p>

                        <div class="arim-v6-flash-countdown">
                            <div><strong data-countdown-days>00</strong><span><?php esc_html_e('Gün', 'arim'); ?></span></div>
                            <div><strong data-countdown-hours>00</strong><span><?php esc_html_e('Saat', 'arim'); ?></span></div>
                            <div><strong data-countdown-minutes>00</strong><span><?php esc_html_e('Dakika', 'arim'); ?></span></div>
                            <div><strong data-countdown-seconds>00</strong><span><?php esc_html_e('Saniye', 'arim'); ?></span></div>
                        </div>

                        <div class="arim-v6-flash-perks">
                            <span><?php esc_html_e('Sepette ekstra indirim', 'arim'); ?></span>
                            <span><?php esc_html_e('Sınırlı stok fırsatları', 'arim'); ?></span>
                            <span><?php esc_html_e('Hızlı teslimat seçkisi', 'arim'); ?></span>
                        </div>

                        <a href="<?php echo esc_url($shop_url); ?>" class="arim-v6-flash-link"><?php esc_html_e('Tüm kampanyaya git', 'arim'); ?></a>
                    </div>

                    <div class="arim-v6-flash-products">
                        <?php foreach ($flash_sale_products as $product) : ?>
                            <?php arim_render_home_product_card_v5($product, __('Fırsat', 'arim'), 'grid'); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('mosaic', [
    'order'   => $section_order_mosaic,
    'enabled' => $show_mosaic,
    'render'  => function() use (
        $mosaic_1_link, $mosaic_1_mode, $mosaic_1_image, $mosaic_1_badge, $mosaic_1_title,
        $mosaic_2_link, $mosaic_2_mode, $mosaic_2_image, $mosaic_2_badge, $mosaic_2_title,
        $mosaic_3_link, $mosaic_3_mode, $mosaic_3_image, $mosaic_3_badge, $mosaic_3_title,
        $mosaic_4_link, $mosaic_4_mode, $mosaic_4_image, $mosaic_4_badge, $mosaic_4_title,
        $mosaic_5_link, $mosaic_5_mode, $mosaic_5_image, $mosaic_5_badge, $mosaic_5_title,
        $mosaic_6_link, $mosaic_6_mode, $mosaic_6_image, $mosaic_6_badge, $mosaic_6_title
    ) {
        ?>
        <section class="arim-v4-mosaic">
            <div class="arim-container">
                <div class="arim-v4-mosaic-grid">
                    <a href="<?php echo esc_url($mosaic_1_link); ?>"
                       class="arim-v4-mosaic-card large coral <?php echo esc_attr(arim_media_mode_class($mosaic_1_mode)); ?>"
                       <?php if ($mosaic_1_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($mosaic_1_image, $mosaic_1_mode, 'coral')); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($mosaic_1_badge); ?></span>
                        <strong><?php echo esc_html($mosaic_1_title); ?></strong>
                    </a>

                    <a href="<?php echo esc_url($mosaic_2_link); ?>"
                       class="arim-v4-mosaic-card pink <?php echo esc_attr(arim_media_mode_class($mosaic_2_mode)); ?>"
                       <?php if ($mosaic_2_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($mosaic_2_image, $mosaic_2_mode, 'pink')); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($mosaic_2_badge); ?></span>
                        <strong><?php echo esc_html($mosaic_2_title); ?></strong>
                    </a>

                    <a href="<?php echo esc_url($mosaic_3_link); ?>"
                       class="arim-v4-mosaic-card yellow <?php echo esc_attr(arim_media_mode_class($mosaic_3_mode)); ?>"
                       <?php if ($mosaic_3_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($mosaic_3_image, $mosaic_3_mode, 'yellow')); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($mosaic_3_badge); ?></span>
                        <strong><?php echo esc_html($mosaic_3_title); ?></strong>
                    </a>

                    <a href="<?php echo esc_url($mosaic_4_link); ?>"
                       class="arim-v4-mosaic-card purple <?php echo esc_attr(arim_media_mode_class($mosaic_4_mode)); ?>"
                       <?php if ($mosaic_4_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($mosaic_4_image, $mosaic_4_mode, 'purple')); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($mosaic_4_badge); ?></span>
                        <strong><?php echo esc_html($mosaic_4_title); ?></strong>
                    </a>

                    <a href="<?php echo esc_url($mosaic_5_link); ?>"
                       class="arim-v4-mosaic-card orange <?php echo esc_attr(arim_media_mode_class($mosaic_5_mode)); ?>"
                       <?php if ($mosaic_5_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($mosaic_5_image, $mosaic_5_mode, 'orange')); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($mosaic_5_badge); ?></span>
                        <strong><?php echo esc_html($mosaic_5_title); ?></strong>
                    </a>

                    <a href="<?php echo esc_url($mosaic_6_link); ?>"
                       class="arim-v4-mosaic-card blue <?php echo esc_attr(arim_media_mode_class($mosaic_6_mode)); ?>"
                       <?php if ($mosaic_6_image) : ?>style="<?php echo esc_attr(arim_media_inline_style($mosaic_6_image, $mosaic_6_mode, 'blue')); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($mosaic_6_badge); ?></span>
                        <strong><?php echo esc_html($mosaic_6_title); ?></strong>
                    </a>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('showcase_1', [
    'order'   => $section_order_showcase_1,
    'enabled' => $show_showcase1 && !empty($showcase_1_products),
    'render'  => function() use ($showcase_1_preset, $showcase_1_title, $showcase_1_link_url, $showcase_1_link_text, $showcase_1_products) {
        ?>
        <section class="arim-v4-showcase <?php echo esc_attr(arim_showcase_preset_class(1, $showcase_1_preset)); ?>">
            <div class="arim-container">
                <div class="arim-v4-section-head light">
                    <h2><?php echo esc_html($showcase_1_title); ?></h2>
                    <a href="<?php echo esc_url($showcase_1_link_url); ?>"><?php echo esc_html($showcase_1_link_text); ?></a>
                </div>

                <div class="arim-v4-showcase-grid">
                    <?php foreach ($showcase_1_products as $product) : ?>
                        <?php arim_render_home_product_card_v5($product, __('Yeni', 'arim'), 'grid'); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('showcase_2', [
    'order'   => $section_order_showcase_2,
    'enabled' => $show_showcase2 && !empty($showcase_2_products),
    'render'  => function() use ($showcase_2_preset, $showcase_2_title, $showcase_2_link_url, $showcase_2_link_text, $showcase_2_products, $mixed_promo_1_badge, $mixed_promo_1_title, $mixed_promo_1_text, $mixed_promo_2_badge, $mixed_promo_2_title, $mixed_promo_2_text) {
        ?>
        <section class="arim-v4-mixed-showcase <?php echo esc_attr(arim_showcase_preset_class(2, $showcase_2_preset)); ?>">
            <div class="arim-container">
                <div class="arim-v4-section-head light">
                    <h2><?php echo esc_html($showcase_2_title); ?></h2>
                    <a href="<?php echo esc_url($showcase_2_link_url); ?>"><?php echo esc_html($showcase_2_link_text); ?></a>
                </div>

                <div class="arim-v4-mixed-showcase-grid">
                    <?php
                    $sale_index = 0;

                    foreach ($showcase_2_products as $product) {
                        if ($sale_index === 2) {
                            arim_render_mixed_promo_card(
                                'promo-coral large',
                                $mixed_promo_1_badge,
                                $mixed_promo_1_title,
                                $mixed_promo_1_text
                            );
                        }

                        if ($sale_index === 5) {
                            arim_render_mixed_promo_card(
                                'promo-purple',
                                $mixed_promo_2_badge,
                                $mixed_promo_2_title,
                                $mixed_promo_2_text
                            );
                        }

                        arim_render_home_product_card_v5($product, __('İndirim', 'arim'), 'grid');
                        $sale_index++;
                    }
                    ?>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('showcase_3', [
    'order'   => $section_order_showcase_3,
    'enabled' => $show_showcase3 && !empty($showcase_3_products),
    'render'  => function() use ($showcase_3_preset, $showcase_3_title, $showcase_3_link_url, $showcase_3_link_text, $showcase_3_products) {
        ?>
        <section class="arim-v5-extra-showcase <?php echo esc_attr(arim_showcase_preset_class(3, $showcase_3_preset)); ?>">
            <div class="arim-container">
                <div class="arim-v5-extra-showcase-box">
                    <div class="arim-v4-section-head">
                        <h2><?php echo esc_html($showcase_3_title); ?></h2>
                        <a href="<?php echo esc_url($showcase_3_link_url); ?>"><?php echo esc_html($showcase_3_link_text); ?></a>
                    </div>

                    <div class="arim-v5-extra-showcase-grid">
                        <?php foreach ($showcase_3_products as $product) : ?>
                            <?php arim_render_home_product_card_v5($product, __('Çok Satan', 'arim'), 'grid'); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

arim_render_homepage_section('seo', [
    'order'   => $section_order_seo,
    'enabled' => $show_seo,
    'render'  => function() use ($homepage_categories, $featured_products, $marketplace_labels, $shop_url, $account_url) {
        ?>
        <section class="arim-v6-marketplace">
            <div class="arim-container">
                <div class="arim-v6-marketplace-grid">
                    <div class="arim-v6-marketplace-main">
                        <span class="arim-v6-marketplace-badge"><?php esc_html_e('Marketplace Yoğunluğu', 'arim'); ?></span>
                        <h2><?php esc_html_e('ARIM ile güçlü marketplace tarzı vitrin deneyimi', 'arim'); ?></h2>
                        <p><?php esc_html_e('ARIM anasayfa yapısı, yoğun kampanya blokları, kategori şeritleri, yatay ürün alanları ve vitrin odaklı showcase düzeni ile kullanıcıya daha aktif bir keşif deneyimi sunmak için hazırlanmıştır. Bu yapı, klasik tek mağaza görünümünden çıkarak daha büyük ölçekli bir alışveriş platformu etkisi oluşturur.', 'arim'); ?></p>
                        <p><?php esc_html_e('Kampanyalar, ürünler ve kategori geçişleri birlikte çalışarak anasayfayı yalnızca giriş alanı olmaktan çıkarır ve aktif bir satış vitrini haline getirir. Böylece ziyaretçilerin daha fazla ürün görmesi ve daha uzun süre sayfada kalması hedeflenir.', 'arim'); ?></p>

                        <div class="arim-v6-marketplace-stats">
                            <div>
                                <strong><?php echo esc_html(count($homepage_categories)); ?>+</strong>
                                <span><?php esc_html_e('Kategori vitrini', 'arim'); ?></span>
                            </div>
                            <div>
                                <strong><?php echo esc_html(count($featured_products)); ?>+</strong>
                                <span><?php esc_html_e('Öne çıkan ürün', 'arim'); ?></span>
                            </div>
                            <div>
                                <strong><?php echo esc_html(count($marketplace_labels)); ?>+</strong>
                                <span><?php esc_html_e('Marka / mağaza teması', 'arim'); ?></span>
                            </div>
                        </div>

                        <div class="arim-v6-marketplace-actions">
                            <a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Ürün vitrini', 'arim'); ?></a>
                            <a href="<?php echo esc_url($account_url); ?>" class="is-secondary"><?php esc_html_e('Müşteri alanı', 'arim'); ?></a>
                        </div>
                    </div>

                    <div class="arim-v6-marketplace-side">
                        <div class="arim-v6-marketplace-card">
                            <h3><?php esc_html_e('Öne çıkan marka akışı', 'arim'); ?></h3>
                            <div class="arim-v6-brand-cloud">
                                <?php foreach ($marketplace_labels as $brand_name) : ?>
                                    <span><?php echo esc_html($brand_name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="arim-v6-marketplace-card is-dark">
                            <h3><?php esc_html_e('Trendyol hissini güçlendiren detaylar', 'arim'); ?></h3>
                            <ul>
                                <li><?php esc_html_e('Yoğun kampanya kartları ve indirim yüzeyleri', 'arim'); ?></li>
                                <li><?php esc_html_e('Kategori keşfi için hızlı yatay gezinme', 'arim'); ?></li>
                                <li><?php esc_html_e('Satıcı / marka odaklı ürün kart yapısı', 'arim'); ?></li>
                                <li><?php esc_html_e('Mobilde de korunan marketplace yoğunluğu', 'arim'); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="arim-home-seo-text">
            <div class="arim-container">
                <div class="arim-home-seo-box">
                    <h2><?php esc_html_e('ARIM ile güçlü marketplace tarzı vitrin deneyimi', 'arim'); ?></h2>
                    <p><?php esc_html_e('ARIM anasayfa yapısı, yoğun kampanya blokları, kategori şeritleri, yatay ürün alanları ve vitrin odaklı showcase düzeni ile kullanıcıya daha aktif bir keşif deneyimi sunmak için hazırlanmıştır. Bu yapı, klasik tek mağaza görünümünden çıkarak daha büyük ölçekli bir alışveriş platformu etkisi oluşturur.', 'arim'); ?></p>
                    <p><?php esc_html_e('Kampanyalar, ürünler ve kategori geçişleri birlikte çalışarak anasayfayı yalnızca giriş alanı olmaktan çıkarır ve aktif bir satış vitrini haline getirir. Böylece ziyaretçilerin daha fazla ürün görmesi ve daha uzun süre sayfada kalması hedeflenir.', 'arim'); ?></p>
                </div>
            </div>
        </section>
        <?php
    }
], $homepage_sections);

usort($homepage_sections, function($a, $b) {
    if ($a['order'] === $b['order']) {
        return strcmp($a['key'], $b['key']);
    }
    return $a['order'] <=> $b['order'];
});

foreach ($homepage_sections as $section) {
    if (!empty($section['enabled']) && is_callable($section['render'])) {
        call_user_func($section['render']);
    }
}

get_footer();
