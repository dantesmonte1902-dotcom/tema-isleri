<?php
defined('ABSPATH') || exit;

$cart_insights        = arim_cart_page_insights();
$cart_campaigns       = arim_single_product_campaigns(2);
$cart_recommendations = arim_cart_recommended_products(4);
$cart_steps           = [
    [
        'label'   => __('Sepet', 'arim'),
        'state'   => 'active',
        'url'     => '',
        'counter' => '01',
    ],
    [
        'label'   => __('Bilgiler', 'arim'),
        'state'   => 'upcoming',
        'url'     => wc_get_checkout_url(),
        'counter' => '02',
    ],
    [
        'label'   => __('Ödeme', 'arim'),
        'state'   => 'upcoming',
        'url'     => wc_get_checkout_url(),
        'counter' => '03',
    ],
];

do_action('woocommerce_before_cart'); ?>

<div class="arim-cart-page">
    <div class="arim-container">
        <div class="arim-cart-header">
            <h1><?php esc_html_e('Sepetim', 'arim'); ?></h1>
            <p><?php esc_html_e('Sepetindeki ürünleri kontrol et, adetleri güncelle ve siparişini tamamlamaya devam et.', 'arim'); ?></p>
        </div>

        <div class="arim-cart-steps" aria-label="<?php esc_attr_e('Sepet adımları', 'arim'); ?>">
            <?php foreach ($cart_steps as $step) : ?>
                <div class="arim-cart-step is-<?php echo esc_attr($step['state']); ?>">
                    <span class="arim-cart-step-counter"><?php echo esc_html($step['counter']); ?></span>
                    <?php if (!empty($step['url']) && $step['state'] !== 'active') : ?>
                        <a href="<?php echo esc_url($step['url']); ?>"><?php echo esc_html($step['label']); ?></a>
                    <?php else : ?>
                        <strong><?php echo esc_html($step['label']); ?></strong>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="arim-cart-trust-strip">
            <div class="arim-cart-trust-item">
                <strong><?php echo esc_html($cart_insights['deliveryBadge']); ?></strong>
                <span><?php echo esc_html($cart_insights['deliveryDate']); ?></span>
            </div>
            <div class="arim-cart-trust-item">
                <strong><?php esc_html_e('Toplam avantaj', 'arim'); ?></strong>
                <span><?php echo wp_kses_post($cart_insights['savingsText']); ?></span>
            </div>
            <div class="arim-cart-trust-item">
                <strong><?php echo esc_html($cart_insights['supportWindow']); ?></strong>
                <span><?php esc_html_e('Sipariş öncesi ve sonrası destek alanı hazır.', 'arim'); ?></span>
            </div>
        </div>

        <form class="woocommerce-cart-form arim-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
            <?php do_action('woocommerce_before_cart_table'); ?>

            <div class="arim-cart-layout">
                <div class="arim-cart-products">
                    <div class="arim-cart-box-head">
                        <div>
                            <span class="arim-cart-box-kicker"><?php esc_html_e('Sepet özeti', 'arim'); ?></span>
                            <h2><?php esc_html_e('Siparişini tek bakışta yönet', 'arim'); ?></h2>
                        </div>

                        <div class="arim-cart-summary-stats">
                            <div class="arim-cart-summary-stat">
                                <strong><?php echo esc_html(number_format_i18n((int) $cart_insights['itemCount'])); ?></strong>
                                <span><?php esc_html_e('paket', 'arim'); ?></span>
                            </div>
                            <div class="arim-cart-summary-stat">
                                <strong><?php echo esc_html(number_format_i18n((int) $cart_insights['productCount'])); ?></strong>
                                <span><?php esc_html_e('ürün', 'arim'); ?></span>
                            </div>
                            <div class="arim-cart-summary-stat">
                                <strong><?php echo wp_kses_post($cart_insights['savingsText']); ?></strong>
                                <span><?php esc_html_e('anlık avantaj', 'arim'); ?></span>
                            </div>
                        </div>
                    </div>

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

                    <?php if (!empty($cart_recommendations)) : ?>
                        <section class="arim-cart-recommendations">
                            <div class="arim-cart-box-head">
                                <div>
                                    <span class="arim-cart-box-kicker"><?php esc_html_e('Sepetine uygun', 'arim'); ?></span>
                                    <h2><?php esc_html_e('Tamamlayıcı öneriler', 'arim'); ?></h2>
                                </div>
                                <a href="<?php echo esc_url(arim_shop_url()); ?>" class="arim-cart-inline-link">
                                    <?php esc_html_e('Yeni ürünlere bak', 'arim'); ?>
                                </a>
                            </div>

                            <div class="arim-cart-recommendation-grid">
                                <?php foreach ($cart_recommendations as $recommendation) : ?>
                                    <article class="arim-cart-recommendation-card">
                                        <a href="<?php echo esc_url($recommendation['url']); ?>" class="arim-cart-recommendation-image">
                                            <img src="<?php echo esc_url($recommendation['image']); ?>" alt="<?php echo esc_attr($recommendation['title']); ?>">
                                            <?php if (!empty($recommendation['badge'])) : ?>
                                                <span class="arim-cart-recommendation-badge"><?php echo esc_html($recommendation['badge']); ?></span>
                                            <?php endif; ?>
                                        </a>

                                        <div class="arim-cart-recommendation-content">
                                            <span class="arim-cart-recommendation-brand"><?php echo esc_html($recommendation['brand']); ?></span>
                                            <h3>
                                                <a href="<?php echo esc_url($recommendation['url']); ?>"><?php echo esc_html($recommendation['title']); ?></a>
                                            </h3>
                                            <div class="arim-cart-recommendation-price"><?php echo esc_html($recommendation['price']); ?></div>
                                            <a href="<?php echo esc_url($recommendation['url']); ?>" class="arim-cart-recommendation-link">
                                                <?php esc_html_e('Ürünü incele', 'arim'); ?>
                                            </a>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>

                <aside class="arim-cart-summary">
                    <div class="arim-cart-highlight-box">
                        <span class="arim-cart-highlight-kicker"><?php echo esc_html($cart_insights['deliveryBadge']); ?></span>
                        <h2><?php echo esc_html($cart_insights['deliveryDate']); ?></h2>
                        <p><?php echo esc_html($cart_insights['deliveryNote']); ?></p>

                        <div class="arim-cart-highlight-stats">
                            <div class="arim-cart-highlight-stat">
                                <strong><?php esc_html_e('Kampanya', 'arim'); ?></strong>
                                <span><?php echo esc_html(number_format_i18n((int) $cart_insights['campaignCount'])); ?></span>
                            </div>
                            <div class="arim-cart-highlight-stat">
                                <strong><?php esc_html_e('Avantaj', 'arim'); ?></strong>
                                <span><?php echo wp_kses_post($cart_insights['savingsText']); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($cart_campaigns)) : ?>
                        <div class="arim-cart-campaign-box">
                            <div class="arim-cart-box-head">
                                <div>
                                    <span class="arim-cart-box-kicker"><?php esc_html_e('Sepette geçerli', 'arim'); ?></span>
                                    <h2><?php esc_html_e('Kampanya fırsatları', 'arim'); ?></h2>
                                </div>
                            </div>

                            <div class="arim-cart-campaign-grid">
                                <?php foreach ($cart_campaigns as $campaign) : ?>
                                    <div class="arim-cart-campaign-item">
                                        <strong><?php echo esc_html($campaign['value']); ?></strong>
                                        <span><?php echo esc_html($campaign['text']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

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
