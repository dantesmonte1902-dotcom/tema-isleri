<?php
defined('ABSPATH') || exit;

global $product;

if (!$product) {
    return;
}

$product_id = $product->get_id();
$gallery_ids = $product->get_gallery_image_ids();
$main_image_id = $product->get_image_id();
$main_image_url = $main_image_id ? wp_get_attachment_image_url($main_image_id, 'large') : wc_placeholder_img_src();

$brand = get_post_meta($product_id, 'brand', true);
if (!$brand) {
    $terms = get_the_terms($product_id, 'product_brand');
    if (!empty($terms) && !is_wp_error($terms)) {
        $brand = $terms[0]->name;
    }
}

$rating = $product->get_average_rating();
$review_count = $product->get_review_count();
$short_description = $product->get_short_description();
$price_html = $product->get_price_html();
$price_text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($price_html)));
$current_price_value = (float) $product->get_price();
$regular_price_value = (float) $product->get_regular_price();
$store_name = get_post_meta($product_id, 'store_name', true);
$store_name = $store_name ? $store_name : __('ARIM Store', 'arim');

$favorite_badge = '';
if ($product->is_on_sale()) {
    $favorite_badge = __('Fırsat', 'arim');
} elseif ($product->is_featured()) {
    $favorite_badge = __('Öne Çıkan', 'arim');
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('arim-single-product', $product); ?>>

    <div class="arim-single-grid">
        <div class="arim-single-gallery">
            <div class="arim-single-main-image">
                <img src="<?php echo esc_url($main_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
            </div>

            <?php if (!empty($gallery_ids)) : ?>
                <div class="arim-single-thumbs">
                    <?php foreach ($gallery_ids as $gallery_id) : 
                        $thumb = wp_get_attachment_image_url($gallery_id, 'woocommerce_thumbnail');
                        $full  = wp_get_attachment_image_url($gallery_id, 'large');
                    ?>
                        <a href="<?php echo esc_url($full); ?>" class="arim-single-thumb">
                            <img src="<?php echo esc_url($thumb); ?>" alt="">
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="arim-single-summary">
            <?php if ($brand) : ?>
                <div class="arim-single-brand"><?php echo esc_html($brand); ?></div>
            <?php endif; ?>

            <h1 class="arim-single-title"><?php the_title(); ?></h1>

            <?php if ($rating > 0) : ?>
                <div class="arim-single-rating">
                    <span class="arim-rating-score"><?php echo esc_html(number_format((float)$rating, 1)); ?></span>
                    <span class="arim-rating-stars">★★★★★</span>
                    <span class="arim-rating-count">(<?php echo esc_html($review_count); ?>)</span>
                </div>
            <?php endif; ?>

            <div class="arim-single-price">
                <?php echo wp_kses_post($price_html); ?>
            </div>

            <div class="arim-single-favorite-wrap">
                <button
                    class="arim-favorite-btn arim-single-favorite-btn"
                    type="button"
                    aria-label="<?php esc_attr_e('Favorilere ekle', 'arim'); ?>"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                    data-product-price="<?php echo esc_attr($price_text); ?>"
                    data-product-image="<?php echo esc_url($main_image_url); ?>"
                    data-product-url="<?php echo esc_url(get_permalink($product_id)); ?>"
                    data-product-brand="<?php echo esc_attr($brand ? $brand : __('ARIM', 'arim')); ?>"
                    data-product-store="<?php echo esc_attr($store_name); ?>"
                    data-product-badge="<?php echo esc_attr($favorite_badge); ?>"
                    data-product-current-price="<?php echo esc_attr($current_price_value); ?>"
                    data-product-regular-price="<?php echo esc_attr($regular_price_value); ?>"
                >♡</button>
            </div>

            <?php if (!empty($short_description)) : ?>
                <div class="arim-single-short-description">
                    <?php echo wp_kses_post(wpautop($short_description)); ?>
                </div>
            <?php endif; ?>

            <div class="arim-single-cart-box">
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>

            <div class="arim-single-meta-box">
                <div class="arim-single-meta-item">
                    <strong><?php esc_html_e('Stok Durumu:', 'arim'); ?></strong>
                    <span><?php echo $product->is_in_stock() ? esc_html__('Stokta var', 'arim') : esc_html__('Stokta yok', 'arim'); ?></span>
                </div>

                <?php if ($product->get_sku()) : ?>
                    <div class="arim-single-meta-item">
                        <strong><?php esc_html_e('SKU:', 'arim'); ?></strong>
                        <span><?php echo esc_html($product->get_sku()); ?></span>
                    </div>
                <?php endif; ?>

                <div class="arim-single-meta-item">
                    <strong><?php esc_html_e('Kategori:', 'arim'); ?></strong>
                    <span><?php echo wc_get_product_category_list($product_id, ', '); ?></span>
                </div>
            </div>

            <div class="arim-single-benefits">
                <div class="arim-single-benefit"><?php esc_html_e('Güvenli ödeme', 'arim'); ?></div>
                <div class="arim-single-benefit"><?php esc_html_e('Kolay iade', 'arim'); ?></div>
                <div class="arim-single-benefit"><?php esc_html_e('Hızlı teslimat', 'arim'); ?></div>
            </div>
        </div>
    </div>

    <div class="arim-single-tabs">
        <?php
        $tabs = apply_filters('woocommerce_product_tabs', []);
        if (!empty($tabs)) :
        ?>
            <div class="arim-tabs-wrapper">
                <?php foreach ($tabs as $key => $tab) : ?>
                    <div class="arim-tab-block">
                        <h2 class="arim-tab-title">
                            <?php echo esc_html($tab['title']); ?>
                        </h2>
                        <div class="arim-tab-content">
                            <?php
                            if (isset($tab['callback'])) {
                                call_user_func($tab['callback'], $key, $tab);
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="arim-single-related">
        <?php woocommerce_output_related_products(); ?>
    </div>
</div>
