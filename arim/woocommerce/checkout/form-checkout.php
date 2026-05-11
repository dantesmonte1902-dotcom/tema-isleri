<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

$checkout_delivery = arim_checkout_delivery_details();
$checkout_campaigns = arim_single_product_campaigns(3);
$checkout_steps = [
    [
        'label'   => __('Sepet', 'arim'),
        'state'   => 'completed',
        'url'     => wc_get_cart_url(),
        'counter' => '01',
    ],
    [
        'label'   => __('Bilgiler', 'arim'),
        'state'   => 'active',
        'url'     => '',
        'counter' => '02',
    ],
    [
        'label'   => __('Ödeme', 'arim'),
        'state'   => 'pending',
        'url'     => '',
        'counter' => '03',
    ],
];
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout arim-checkout-form" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
    <div class="arim-checkout-page">
        <div class="arim-container">
            <div class="arim-checkout-header">
                <h1><?php esc_html_e('Ödeme', 'arim'); ?></h1>
                <p><?php esc_html_e('Teslimat, fatura ve ödeme bilgilerini tamamlayarak siparişini oluştur.', 'arim'); ?></p>
            </div>

            <div class="arim-checkout-steps" aria-label="<?php esc_attr_e('Ödeme adımları', 'arim'); ?>">
                <?php foreach ($checkout_steps as $step) : ?>
                    <div class="arim-checkout-step is-<?php echo esc_attr($step['state']); ?>">
                        <span class="arim-checkout-step-counter"><?php echo esc_html($step['counter']); ?></span>
                        <?php if (!empty($step['url'])) : ?>
                            <a href="<?php echo esc_url($step['url']); ?>"><?php echo esc_html($step['label']); ?></a>
                        <?php else : ?>
                            <strong><?php echo esc_html($step['label']); ?></strong>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="arim-checkout-trust-strip">
                <div class="arim-checkout-trust-item">
                    <strong><?php esc_html_e('Güvenli ödeme', 'arim'); ?></strong>
                    <span><?php esc_html_e('Korunan ödeme altyapısı ile siparişini tamamla.', 'arim'); ?></span>
                </div>
                <div class="arim-checkout-trust-item">
                    <strong><?php echo esc_html($checkout_delivery['badge']); ?></strong>
                    <span><?php echo esc_html($checkout_delivery['date']); ?></span>
                </div>
                <div class="arim-checkout-trust-item">
                    <strong><?php esc_html_e('Kolay iade', 'arim'); ?></strong>
                    <span><?php esc_html_e('Sipariş sonrası destek ve iade akışıyla korunursun.', 'arim'); ?></span>
                </div>
            </div>

            <div class="arim-checkout-layout">
                <div class="arim-checkout-main">
                    <?php if ($checkout->get_checkout_fields()) : ?>
                        <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                        <div class="arim-checkout-box arim-checkout-customer">
                            <div id="customer_details">
                                <div class="arim-checkout-section">
                                    <h2><?php esc_html_e('Fatura Bilgileri', 'arim'); ?></h2>
                                    <?php do_action('woocommerce_checkout_billing'); ?>
                                </div>

                                <div class="arim-checkout-section">
                                    <h2><?php esc_html_e('Teslimat Bilgileri', 'arim'); ?></h2>
                                    <?php do_action('woocommerce_checkout_shipping'); ?>
                                </div>
                            </div>
                        </div>

                        <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                    <?php endif; ?>
                </div>

                <aside class="arim-checkout-sidebar">
                    <div class="arim-checkout-box arim-checkout-highlight-box">
                        <span class="arim-checkout-highlight-kicker"><?php echo esc_html($checkout_delivery['badge']); ?></span>
                        <h2><?php echo esc_html($checkout_delivery['date']); ?></h2>
                        <p><?php echo esc_html($checkout_delivery['note']); ?></p>

                        <div class="arim-checkout-highlight-stats">
                            <div class="arim-checkout-highlight-stat">
                                <strong><?php esc_html_e('Paket', 'arim'); ?></strong>
                                <span><?php echo esc_html(number_format_i18n((int) $checkout_delivery['itemCount'])); ?></span>
                            </div>
                            <div class="arim-checkout-highlight-stat">
                                <strong><?php esc_html_e('Ürün', 'arim'); ?></strong>
                                <span><?php echo esc_html(number_format_i18n((int) $checkout_delivery['productCount'])); ?></span>
                            </div>
                        </div>

                        <div class="arim-checkout-highlight-note"><?php echo esc_html($checkout_delivery['supportWindow']); ?></div>
                    </div>

                    <?php if (!empty($checkout_campaigns)) : ?>
                        <div class="arim-checkout-box arim-checkout-campaign-box">
                            <div class="arim-checkout-box-head">
                                <h2><?php esc_html_e('Sepette ek avantajlar', 'arim'); ?></h2>
                                <span><?php esc_html_e('Kuponları siparişte değerlendir', 'arim'); ?></span>
                            </div>

                            <div class="arim-checkout-campaign-grid">
                                <?php foreach ($checkout_campaigns as $campaign) : ?>
                                    <div class="arim-checkout-campaign-item">
                                        <strong><?php echo esc_html($campaign['value']); ?></strong>
                                        <span><?php echo esc_html($campaign['text']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="arim-checkout-box arim-checkout-order">
                        <h2><?php esc_html_e('Sipariş Özeti', 'arim'); ?></h2>

                        <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>

                        <?php do_action('woocommerce_checkout_after_order_review'); ?>

                        <div class="arim-checkout-assurance-list">
                            <div class="arim-checkout-assurance-item"><?php esc_html_e('Ödemen SSL koruması ile güvence altında işlenir.', 'arim'); ?></div>
                            <div class="arim-checkout-assurance-item"><?php esc_html_e('Sipariş onayı ve kargo hareketleri e-posta ile paylaşılır.', 'arim'); ?></div>
                            <div class="arim-checkout-assurance-item"><?php esc_html_e('Soruların için canlı destek ve sipariş sonrası yardım hazır.', 'arim'); ?></div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
