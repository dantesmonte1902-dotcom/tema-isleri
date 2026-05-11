<?php
/**
 * View order
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined('ABSPATH') || exit;

$order_data = arim_myaccount_view_order_data($order_id);
$hero       = isset($order_data['hero']) && is_array($order_data['hero']) ? $order_data['hero'] : [];
$summary    = isset($order_data['summary']) && is_array($order_data['summary']) ? $order_data['summary'] : [];
$status     = isset($order_data['status']) && is_array($order_data['status']) ? $order_data['status'] : [];
$items      = isset($order_data['items']) && is_array($order_data['items']) ? $order_data['items'] : [];
$actions    = isset($order_data['actions']) && is_array($order_data['actions']) ? $order_data['actions'] : [];
$campaigns  = isset($order_data['campaigns']) && is_array($order_data['campaigns']) ? $order_data['campaigns'] : [];
$metrics    = isset($order_data['metrics']) && is_array($order_data['metrics']) ? $order_data['metrics'] : [];
$links      = isset($order_data['links']) && is_array($order_data['links']) ? $order_data['links'] : [];
?>

<div class="arim-myaccount-view-order">
    <div class="arim-myaccount-view-order-hero">
        <div>
            <span class="arim-myaccount-badge"><?php esc_html_e('Sipariş detayı', 'arim'); ?></span>
            <h2><?php echo esc_html($hero['title'] ?? __('Sipariş detayın', 'arim')); ?></h2>
            <p><?php echo esc_html($hero['text'] ?? __('Sipariş özetini tek ekrandan takip et.', 'arim')); ?></p>
        </div>

        <div class="arim-myaccount-view-order-hero-card">
            <span><?php esc_html_e('Sipariş durumu', 'arim'); ?></span>
            <strong class="arim-order-status-pill is-<?php echo esc_attr($status['key'] ?? 'pending'); ?>">
                <?php echo esc_html($status['label'] ?? __('İşleniyor', 'arim')); ?>
            </strong>
            <small><?php echo esc_html($metrics['itemCount'] ?? ''); ?></small>
            <small><?php echo esc_html($metrics['orderTotal'] ?? ''); ?></small>
        </div>
    </div>

    <?php if (!empty($summary)) : ?>
        <div class="arim-myaccount-view-order-summary">
            <?php foreach ($summary as $summary_item) : ?>
                <div class="arim-myaccount-view-order-stat">
                    <span><?php echo esc_html($summary_item['label'] ?? ''); ?></span>
                    <strong><?php echo esc_html($summary_item['value'] ?? '—'); ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="arim-myaccount-view-order-layout">
        <section class="arim-myaccount-view-order-panel">
            <div class="arim-myaccount-panel-head">
                <div>
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Ürün özeti', 'arim'); ?></span>
                    <h3><?php esc_html_e('Siparişteki ürünler', 'arim'); ?></h3>
                </div>
                <a href="<?php echo esc_url($links['orders'] ?? wc_get_account_endpoint_url('orders')); ?>">
                    <?php esc_html_e('Tüm siparişlere dön', 'arim'); ?>
                </a>
            </div>

            <?php if (!empty($items)) : ?>
                <div class="arim-myaccount-view-order-items">
                    <?php foreach ($items as $item) : ?>
                        <article class="arim-myaccount-view-order-item">
                            <div class="arim-myaccount-view-order-item-media">
                                <?php if (!empty($item['url'])) : ?>
                                    <a href="<?php echo esc_url($item['url']); ?>">
                                        <?php echo wp_kses_post($item['thumbnail'] ?? ''); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo wp_kses_post($item['thumbnail'] ?? ''); ?>
                                <?php endif; ?>
                            </div>

                            <div class="arim-myaccount-view-order-item-content">
                                <h4>
                                    <?php if (!empty($item['url'])) : ?>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['name'] ?? ''); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html($item['name'] ?? ''); ?>
                                    <?php endif; ?>
                                </h4>
                                <span><?php echo esc_html($item['quantity'] ?? ''); ?></span>
                            </div>

                            <strong class="arim-myaccount-view-order-item-total"><?php echo esc_html($item['total'] ?? ''); ?></strong>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside class="arim-myaccount-view-order-side">
            <div class="arim-myaccount-address-help">
                <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Sonraki adımlar', 'arim'); ?></span>
                <h3><?php esc_html_e('Sipariş ve hesap yönetimi', 'arim'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Teslimat, ödeme ve müşteri bilgileri aşağıdaki detay alanında görünür.', 'arim'); ?></li>
                    <li><?php esc_html_e('Sipariş tekrar veya ödeme aksiyonu varsa sağlanan butonlarla devam edebilirsin.', 'arim'); ?></li>
                    <li><?php esc_html_e('Adres ve iletişim bilgilerini güncel tutman teslimat sürecini hızlandırır.', 'arim'); ?></li>
                </ul>

                <div class="arim-myaccount-address-help-actions">
                    <?php foreach ($actions as $key => $action) : ?>
                        <a class="arim-myaccount-orders-link is-<?php echo esc_attr(sanitize_html_class($key)); ?>" href="<?php echo esc_url($action['url']); ?>">
                            <?php echo esc_html($action['name']); ?>
                        </a>
                    <?php endforeach; ?>
                    <a class="arim-myaccount-orders-link" href="<?php echo esc_url($links['account'] ?? wc_get_account_endpoint_url('edit-account')); ?>">
                        <?php esc_html_e('Hesabımı düzenle', 'arim'); ?>
                    </a>
                    <a class="arim-myaccount-orders-link" href="<?php echo esc_url($links['shop'] ?? arim_shop_url()); ?>">
                        <?php esc_html_e('Alışverişe dön', 'arim'); ?>
                    </a>
                </div>
            </div>

            <?php if (!empty($campaigns)) : ?>
                <div class="arim-myaccount-address-campaigns">
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Fırsat alanı', 'arim'); ?></span>
                    <?php foreach ($campaigns as $campaign) : ?>
                        <div class="arim-myaccount-orders-campaign">
                            <strong><?php echo esc_html($campaign['value']); ?></strong>
                            <span><?php echo esc_html($campaign['text']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </aside>
    </div>

    <div class="arim-myaccount-view-order-details">
        <?php do_action('woocommerce_view_order', $order_id); ?>
    </div>
</div>
