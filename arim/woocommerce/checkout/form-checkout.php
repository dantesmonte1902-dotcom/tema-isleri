<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout arim-checkout-form" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
    <div class="arim-checkout-page">
        <div class="arim-container">
            <div class="arim-checkout-header">
                <h1><?php esc_html_e('Ödeme', 'arim'); ?></h1>
                <p><?php esc_html_e('Teslimat, fatura ve ödeme bilgilerini tamamlayarak siparişini oluştur.', 'arim'); ?></p>
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
                    <div class="arim-checkout-box arim-checkout-order">
                        <h2><?php esc_html_e('Sipariş Özeti', 'arim'); ?></h2>

                        <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>

                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>