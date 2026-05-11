<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<div class="arim-cart-page">
    <div class="arim-container">
        <div class="arim-cart-header">
            <h1><?php esc_html_e('Sepetim', 'arim'); ?></h1>
            <p><?php esc_html_e('Sepetindeki ürünleri kontrol et, adetleri güncelle ve siparişini tamamlamaya devam et.', 'arim'); ?></p>
        </div>

        <form class="woocommerce-cart-form arim-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
            <?php do_action('woocommerce_before_cart_table'); ?>

            <div class="arim-cart-layout">
                <div class="arim-cart-products">
                    <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                        $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key);
                            $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                            $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                            $product_subtotal = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                            ?>
                            <div class="arim-cart-item">
                                <div class="arim-cart-item-image">
                                    <?php if (!$product_permalink) : ?>
                                        <?php echo $thumbnail; ?>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url($product_permalink); ?>">
                                            <?php echo $thumbnail; ?>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="arim-cart-item-content">
                                    <div class="arim-cart-item-top">
                                        <div class="arim-cart-item-info">
                                            <h3 class="arim-cart-item-title">
                                                <?php if (!$product_permalink) : ?>
                                                    <?php echo wp_kses_post($product_name); ?>
                                                <?php else : ?>
                                                    <a href="<?php echo esc_url($product_permalink); ?>">
                                                        <?php echo wp_kses_post($product_name); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </h3>

                                            <div class="arim-cart-item-meta">
                                                <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                                                <?php
                                                if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                    echo wp_kses_post(apply_filters(
                                                        'woocommerce_cart_item_backorder_notification',
                                                        '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>',
                                                        $product_id
                                                    ));
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="arim-cart-item-remove">
                                            <?php
                                            echo apply_filters(
                                                'woocommerce_cart_item_remove_link',
                                                sprintf(
                                                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">×</a>',
                                                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                    esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                                                    esc_attr($product_id),
                                                    esc_attr($_product->get_sku())
                                                ),
                                                $cart_item_key
                                            );
                                            ?>
                                        </div>
                                    </div>

                                    <div class="arim-cart-item-bottom">
                                        <div class="arim-cart-item-price">
                                            <span class="label"><?php esc_html_e('Fiyat', 'arim'); ?></span>
                                            <span class="value"><?php echo wp_kses_post($product_price); ?></span>
                                        </div>

                                        <div class="arim-cart-item-quantity">
                                            <span class="label"><?php esc_html_e('Adet', 'arim'); ?></span>
                                            <div class="value">
                                                <?php
                                                if ($_product->is_sold_individually()) {
                                                    $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                                } else {
                                                    $product_quantity = woocommerce_quantity_input(
                                                        [
                                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                                            'input_value'  => $cart_item['quantity'],
                                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                                            'min_value'    => '0',
                                                            'product_name' => $product_name,
                                                        ],
                                                        $_product,
                                                        false
                                                    );
                                                }

                                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                                                ?>
                                            </div>
                                        </div>

                                        <div class="arim-cart-item-subtotal">
                                            <span class="label"><?php esc_html_e('Toplam', 'arim'); ?></span>
                                            <span class="value"><?php echo wp_kses_post($product_subtotal); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    endforeach; ?>

                    <div class="arim-cart-actions">
                        <?php if (wc_coupons_enabled()) : ?>
                            <div class="arim-cart-coupon">
                                <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Kupon kodu', 'arim'); ?>" />
                                <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">
                                    <?php esc_html_e('Kupon Uygula', 'arim'); ?>
                                </button>
                                <?php do_action('woocommerce_cart_coupon'); ?>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                            <?php esc_html_e('Sepeti Güncelle', 'arim'); ?>
                        </button>

                        <?php do_action('woocommerce_cart_actions'); ?>
                        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                    </div>
                </div>

                <aside class="arim-cart-summary">
                    <?php do_action('woocommerce_before_cart_collaterals'); ?>

                    <div class="cart-collaterals">
                        <?php do_action('woocommerce_cart_collaterals'); ?>
                    </div>

                    <?php do_action('woocommerce_after_cart_collaterals'); ?>
                </aside>
            </div>

            <?php do_action('woocommerce_after_cart_table'); ?>
        </form>
    </div>
</div>

<?php do_action('woocommerce_after_cart'); ?>