<?php
defined('ABSPATH') || exit;

global $product;

if (empty($product) || !$product->is_visible()) {
    return;
}

$product_id          = $product->get_id();
$image_id            = $product->get_image_id();
$image_url           = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src();
$rating              = (float) $product->get_average_rating();
$reviews             = (int) $product->get_review_count();
$price_html          = $product->get_price_html();
$price_text          = arim_product_price_text($product);
$product_url         = get_permalink($product_id);
$current_price_value = (float) $product->get_price();
$regular_price_value = (float) $product->get_regular_price();
$discount_amount     = max(0, $regular_price_value - $current_price_value);
$discount_percent    = ($regular_price_value > 0 && $discount_amount > 0) ? (int) round(($discount_amount / $regular_price_value) * 100) : 0;
$brand               = get_post_meta($product_id, 'brand', true);
$store_name          = function_exists('arim_product_store_name') ? arim_product_store_name($product_id) : __('ARIM Store', 'arim');
$shipping_note       = $product->is_in_stock() ? __('Hızlı teslimat', 'arim') : __('Siparişe göre hazırlanır', 'arim');
$campaign_note       = $discount_percent > 0 ? sprintf(__('Sepette %% %s avantaj', 'arim'), $discount_percent) : __('Kargo bedava seçenekler', 'arim');

if (!$brand) {
    $terms = get_the_terms($product_id, 'product_brand');
    if (!empty($terms) && !is_wp_error($terms)) {
        $brand = $terms[0]->name;
    }
}

$badges = [];
if ($discount_percent > 0) {
    $badges[] = '%' . $discount_percent;
}
if ($product->is_featured()) {
    $badges[] = __('Öne Çıkan', 'arim');
}
if ($product->is_on_sale() && 0 === $discount_percent) {
    $badges[] = __('İndirim', 'arim');
}
?>
<li <?php wc_product_class('arim-woo-product-item', $product); ?>>
    <article class="<?php echo esc_attr(arim_get_shop_archive_card_classes()); ?>">
        <a href="<?php echo esc_url($product_url); ?>" class="arim-search-product-link">
            <div class="arim-search-product-media">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">

                <button
                    class="arim-favorite-btn arim-search-product-favorite"
                    type="button"
                    aria-label="<?php esc_attr_e('Favorilere ekle', 'arim'); ?>"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                    data-product-price="<?php echo esc_attr($price_text); ?>"
                    data-product-image="<?php echo esc_url($image_url); ?>"
                    data-product-url="<?php echo esc_url($product_url); ?>"
                    data-product-brand="<?php echo esc_attr($brand ? $brand : __('ARIM', 'arim')); ?>"
                    data-product-store="<?php echo esc_attr($store_name); ?>"
                    data-product-badge="<?php echo esc_attr(implode(', ', $badges)); ?>"
                    data-product-current-price="<?php echo esc_attr($current_price_value); ?>"
                    data-product-regular-price="<?php echo esc_attr($regular_price_value); ?>"
                >♡</button>

                <?php if (!empty($badges)) : ?>
                    <div class="arim-search-product-badges">
                        <?php foreach (array_slice($badges, 0, 2) as $badge) : ?>
                            <span><?php echo esc_html($badge); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="arim-search-product-body">
                <div class="arim-search-product-topline">
                    <span class="arim-search-product-store"><?php echo esc_html($store_name); ?></span>
                </div>

                <h2 class="arim-search-product-title">
                    <?php if ($brand) : ?>
                        <strong><?php echo esc_html($brand); ?></strong>
                    <?php endif; ?>
                    <span><?php echo esc_html($product->get_name()); ?></span>
                </h2>

                <?php if ($rating > 0 || $reviews > 0) : ?>
                    <div class="arim-search-product-rating">
                        <?php if ($rating > 0) : ?>
                            <span class="arim-search-product-rating-score"><?php echo esc_html(number_format($rating, 1)); ?></span>
                        <?php endif; ?>
                        <span class="arim-search-product-rating-stars">★★★★★</span>
                        <?php if ($reviews > 0) : ?>
                            <span class="arim-search-product-rating-count">(<?php echo esc_html(number_format_i18n($reviews)); ?>)</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="arim-search-product-price"><?php echo wp_kses_post($price_html); ?></div>
                <div class="arim-search-product-promo"><?php echo esc_html($campaign_note); ?></div>
                <div class="arim-search-product-shipping"><?php echo esc_html($shipping_note); ?></div>
            </div>
        </a>

        <div class="arim-search-product-actions">
            <?php
            echo apply_filters(
                'woocommerce_loop_add_to_cart_link',
                sprintf(
                    '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
                    esc_url($product->add_to_cart_url()),
                    esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
                    esc_attr(isset($args['class']) ? $args['class'] : 'button'),
                    isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
                    esc_html($product->add_to_cart_text())
                ),
                $product,
                isset($args) ? $args : []
            );
            ?>
        </div>
    </article>
</li>
