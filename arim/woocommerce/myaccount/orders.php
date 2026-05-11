<?php
defined('ABSPATH') || exit;

$orders_page_data = arim_myaccount_orders_page_data();
$order_stats      = isset($orders_page_data['stats']) && is_array($orders_page_data['stats']) ? $orders_page_data['stats'] : [];
$spotlight        = isset($orders_page_data['spotlight']) && is_array($orders_page_data['spotlight']) ? $orders_page_data['spotlight'] : [];
$campaigns        = isset($orders_page_data['campaigns']) && is_array($orders_page_data['campaigns']) ? $orders_page_data['campaigns'] : [];
$support_url      = !empty($orders_page_data['supportUrl']) ? $orders_page_data['supportUrl'] : wc_get_account_endpoint_url('edit-account');

do_action('woocommerce_before_account_orders', $has_orders);

if ($has_orders) : ?>
    <div class="arim-myaccount-orders">
        <div class="arim-myaccount-section-head">
            <h2><?php esc_html_e('Siparişlerim', 'arim'); ?></h2>
            <p><?php esc_html_e('Teslimat ritmini, işlem adımlarını ve son sipariş hareketlerini tek ekrandan takip et.', 'arim'); ?></p>
        </div>

        <div class="arim-myaccount-orders-summary">
            <div class="arim-myaccount-orders-stat">
                <span><?php esc_html_e('Toplam sipariş', 'arim'); ?></span>
                <strong><?php echo esc_html(number_format_i18n((int) ($order_stats['orders'] ?? 0))); ?></strong>
            </div>
            <div class="arim-myaccount-orders-stat">
                <span><?php esc_html_e('Aktif akış', 'arim'); ?></span>
                <strong><?php echo esc_html(number_format_i18n((int) ($order_stats['active'] ?? 0))); ?></strong>
            </div>
            <div class="arim-myaccount-orders-stat">
                <span><?php esc_html_e('Tamamlanan', 'arim'); ?></span>
                <strong><?php echo esc_html(number_format_i18n((int) ($order_stats['completed'] ?? 0))); ?></strong>
            </div>
            <div class="arim-myaccount-orders-stat">
                <span><?php esc_html_e('Son sipariş tarihi', 'arim'); ?></span>
                <strong><?php echo esc_html($order_stats['lastOrderDate'] ?? '—'); ?></strong>
            </div>
        </div>

        <div class="arim-myaccount-orders-panels">
            <div class="arim-myaccount-orders-spotlight">
                <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Sipariş takibi', 'arim'); ?></span>
                <h3><?php echo esc_html($spotlight['title'] ?? __('Siparişlerini takip et', 'arim')); ?></h3>
                <p><?php echo esc_html($spotlight['text'] ?? __('Tüm teslimat ve işlem adımlarını bu ekrandan izleyebilirsin.', 'arim')); ?></p>

                <div class="arim-myaccount-orders-actions">
                    <a class="button" href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">
                        <?php esc_html_e('Sipariş detaylarını aç', 'arim'); ?>
                    </a>
                    <a class="arim-myaccount-orders-link" href="<?php echo esc_url($support_url); ?>">
                        <?php esc_html_e('Hesap ve destek ayarları', 'arim'); ?>
                    </a>
                </div>
            </div>

            <?php if (!empty($campaigns)) : ?>
                <div class="arim-myaccount-orders-campaigns">
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Sipariş öncesi fırsatlar', 'arim'); ?></span>
                    <?php foreach ($campaigns as $campaign) : ?>
                        <div class="arim-myaccount-orders-campaign">
                            <strong><?php echo esc_html($campaign['value']); ?></strong>
                            <span><?php echo esc_html($campaign['text']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="arim-myaccount-orders-table-wrap">
            <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table arim-orders-table">
                <thead>
                    <tr>
                        <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr($column_id); ?>">
                                <span class="nobr"><?php echo esc_html($column_name); ?></span>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    foreach ($customer_orders->orders as $customer_order) {
                        $order = wc_get_order($customer_order);
                        $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                        ?>
                        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr($order->get_status()); ?> order">
                            <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
                                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr($column_id); ?>" data-title="<?php echo esc_attr($column_name); ?>">
                                    <?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)) : ?>
                                        <?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

                                    <?php elseif ('order-number' === $column_id) : ?>
                                        <a href="<?php echo esc_url($order->get_view_order_url()); ?>">
                                            #<?php echo esc_html($order->get_order_number()); ?>
                                        </a>

                                    <?php elseif ('order-date' === $column_id) : ?>
                                        <time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>">
                                            <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?>
                                        </time>

                                     <?php elseif ('order-status' === $column_id) : ?>
                                         <span class="arim-order-status-pill is-<?php echo esc_attr(sanitize_html_class($order->get_status())); ?>">
                                             <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                                         </span>

                                     <?php elseif ('order-total' === $column_id) : ?>
                                         <?php
                                         printf(
                                            _n('%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce'),
                                            $order->get_formatted_order_total(),
                                            $item_count
                                        );
                                        ?>

                                    <?php elseif ('order-actions' === $column_id) : ?>
                                        <?php
                                        $actions = wc_get_account_orders_actions($order);

                                        if (!empty($actions)) {
                                            foreach ($actions as $key => $action) {
                                                echo '<a href="' . esc_url($action['url']) . '" class="woocommerce-button button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
                                            }
                                        }
                                        ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php do_action('woocommerce_before_account_orders_pagination'); ?>

        <?php if (1 < $customer_orders->max_num_pages) : ?>
            <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination arim-myaccount-pagination">
                <?php if (1 !== $current_page) : ?>
                    <a class="woocommerce-button woocommerce-button--previous button" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>">
                        <?php esc_html_e('Previous', 'woocommerce'); ?>
                    </a>
                <?php endif; ?>

                <?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
                    <a class="woocommerce-button woocommerce-button--next button" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>">
                        <?php esc_html_e('Next', 'woocommerce'); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

<?php else : ?>
    <div class="arim-myaccount-empty">
        <h2><?php esc_html_e('Henüz siparişin yok', 'arim'); ?></h2>
        <p><?php esc_html_e('Sipariş verdiğinde burada görünecek. Hemen alışverişe başlayabilirsin.', 'arim'); ?></p>

        <div class="arim-myaccount-empty-actions">
            <a class="button" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
                <?php esc_html_e('Alışverişe Başla', 'arim'); ?>
            </a>
            <a class="arim-myaccount-orders-link" href="<?php echo esc_url(arim_favorites_url()); ?>">
                <?php esc_html_e('Favorilere dön', 'arim'); ?>
            </a>
        </div>

        <?php if (!empty($campaigns)) : ?>
            <div class="arim-myaccount-empty-campaigns">
                <?php foreach ($campaigns as $campaign) : ?>
                    <div class="arim-myaccount-orders-campaign">
                        <strong><?php echo esc_html($campaign['value']); ?></strong>
                        <span><?php echo esc_html($campaign['text']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>
