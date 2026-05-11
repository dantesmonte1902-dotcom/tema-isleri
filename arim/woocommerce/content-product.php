<?php
defined('ABSPATH') || exit;

global $product;

if (empty($product) || !$product->is_visible()) {
    return;
}

$product_id  = $product->get_id();
$image_id    = $product->get_image_id();
$image_url   = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src();
$rating      = $product->get_average_rating();
$reviews     = $product->get_review_count();
$price_html  = $product->get_price_html();
$product_url = get_permalink($product_id);

$brand = get_post_meta($product_id, 'brand', true);

if (!$brand) {
    $terms = get_the_terms($product_id, 'product_brand');
    if (!empty($terms) && !is_wp_error($terms)) {
        $brand = $terms[0]->name;
    }
}

$badge_text = '';
if ($product->is_on_sale()) {
    $badge_text = __('Fırsat', 'arim');
} elseif ($product->is_featured()) {
    $badge_text = __('Öne Çıkan', 'arim');
}
?>
<li <?php wc_product_class('arim-woo-product-item', $product); ?>>
    <article class="arim-product-card arim-woo-product-card arim-listing-card">
        <a href="<?php echo esc_url($product_url); ?>" class="arim-product-link">
            <div class="arim-product-image-wrap arim-listing-image-wrap">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">

                <button
                    class="arim-favorite-btn"
                    type="button"
                    aria-label="<?php esc_attr_e('Favorilere ekle', 'arim'); ?>"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                    data-product-price="<?php echo esc_attr(wp_strip_all_tags($price_html)); ?>"
                    data-product-image="<?php echo esc_url($image_url); ?>"
                    data-product-url="<?php echo esc_url($product_url); ?>"
                >♡</button>

                <?php if ($badge_text) : ?>
                    <span class="arim-product-badge <?php echo $product->is_on_sale() ? 'sale' : ''; ?>">
                        <?php echo esc_html($badge_text); ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="arim-product-content arim-listing-product-content">
                <h2 class="arim-product-title arim-listing-product-title">
                    <?php if ($brand) : ?>
                        <strong><?php echo esc_html($brand); ?></strong>
                    <?php endif; ?>
                    <span><?php echo esc_html($product->get_name()); ?></span>
                </h2>

                <?php if ($rating > 0) : ?>
                    <div class="arim-product-rating arim-listing-rating">
                        <span class="arim-rating-score"><?php echo esc_html(number_format((float)$rating, 1)); ?></span>
                        <span class="arim-rating-stars">★★★★★</span>
                        <span class="arim-rating-count">(<?php echo esc_html($reviews); ?>)</span>
                    </div>
                <?php endif; ?>

                <div class="arim-product-price arim-listing-price">
                    <?php echo wp_kses_post($price_html); ?>
                </div>

                <div class="arim-listing-shipping-note">
                    <?php esc_html_e('Kargo bedava fırsatlı teslimat', 'arim'); ?>
                </div>
            </div>
        </a>

        <div class="arim-woo-card-actions arim-listing-card-actions">
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