<?php
defined('ABSPATH') || exit;

$orders_page_data = arim_myaccount_orders_page_data();
$order_stats      = isset($orders_page_data['stats']) && is_array($orders_page_data['stats']) ? $orders_page_data['stats'] : [];
$spotlight        = isset($orders_page_data['spotlight']) && is_array($orders_page_data['spotlight']) ? $orders_page_data['spotlight'] : [];
$guide            = isset($orders_page_data['guide']) && is_array($orders_page_data['guide']) ? $orders_page_data['guide'] : [];
$filters          = isset($orders_page_data['filters']) && is_array($orders_page_data['filters']) ? $orders_page_data['filters'] : [];
$active_filter    = !empty($orders_page_data['activeFilter']) ? $orders_page_data['activeFilter'] : 'all';
$campaigns        = isset($orders_page_data['campaigns']) && is_array($orders_page_data['campaigns']) ? $orders_page_data['campaigns'] : [];
$support_url      = !empty($orders_page_data['supportUrl']) ? $orders_page_data['supportUrl'] : wc_get_account_endpoint_url('edit-account');
$filtered_orders  = [];

if (!empty($customer_orders->orders) && is_array($customer_orders->orders)) {
    foreach ($customer_orders->orders as $customer_order) {
        $order = wc_get_order($customer_order);

        if ($order instanceof WC_Order && arim_myaccount_order_matches_filter($order, $active_filter)) {
            $filtered_orders[] = $order;
        }
    }
}

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

        <?php if (!empty($filters)) : ?>
            <div class="arim-myaccount-orders-filters" aria-label="<?php esc_attr_e('Sipariş filtreleri', 'arim'); ?>">
                <?php foreach ($filters as $filter) : ?>
                    <?php
                    $filter_key = isset($filter['key']) ? $filter['key'] : 'all';
                    $filter_url = wc_get_account_endpoint_url('orders');

                    if ('all' === $filter_key) {
                        $filter_url = remove_query_arg('order_filter', $filter_url);
                    } else {
                        $filter_url = add_query_arg('order_filter', $filter_key, $filter_url);
                    }
                    ?>
                    <a class="arim-myaccount-orders-filter<?php echo $active_filter === $filter_key ? ' is-active' : ''; ?>" href="<?php echo esc_url($filter_url); ?>">
                        <span><?php echo esc_html($filter['label'] ?? ''); ?></span>
                        <strong><?php echo esc_html(number_format_i18n((int) ($filter['count'] ?? 0))); ?></strong>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($filtered_orders)) : ?>
            <div class="arim-myaccount-orders-search" data-arim-orders-search>
                <div class="arim-myaccount-orders-search-field">
                    <label class="screen-reader-text" for="arim-myaccount-orders-search-input"><?php esc_html_e('Siparişlerde ara', 'arim'); ?></label>
                    <input
                        id="arim-myaccount-orders-search-input"
                        type="search"
                        value=""
                        placeholder="<?php esc_attr_e('Sipariş no, ürün adedi veya durum ara', 'arim'); ?>"
                        data-arim-orders-search-input
                    >
                </div>
                <div class="arim-myaccount-orders-search-meta">
                    <strong data-arim-orders-search-count><?php echo esc_html(number_format_i18n(count($filtered_orders))); ?></strong>
                    <span data-arim-orders-search-label><?php esc_html_e('sipariş bu sayfada listeleniyor', 'arim'); ?></span>
                </div>
            </div>
        <?php endif; ?>

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

            <?php if (!empty($guide)) : ?>
                <div class="arim-myaccount-orders-guide <?php echo esc_attr($guide['state'] ?? 'is-success'); ?>">
                    <span class="arim-myaccount-panel-kicker"><?php echo esc_html($guide['badge'] ?? __('Sipariş sağlığı', 'arim')); ?></span>
                    <h3><?php echo esc_html($guide['title'] ?? __('Sipariş rehberi', 'arim')); ?></h3>
                    <p><?php echo esc_html($guide['text'] ?? ''); ?></p>

                    <?php if (!empty($guide['items']) && is_array($guide['items'])) : ?>
                        <ul>
                            <?php foreach ($guide['items'] as $guide_item) : ?>
                                <li><?php echo esc_html($guide_item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

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

        <?php if (!empty($filtered_orders)) : ?>
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
                        <?php foreach ($filtered_orders as $order) : ?>
                            <?php
                            $item_count  = $order->get_item_count() - $order->get_item_count_refunded();
                            $status_note = arim_myaccount_order_status_note($order);
                            $searchable_text = implode(' ', array_filter([
                                '#' . $order->get_order_number(),
                                $order->get_date_created() ? wc_format_datetime($order->get_date_created()) : '',
                                wc_get_order_status_name($order->get_status()),
                                wp_strip_all_tags($order->get_formatted_order_total()),
                                sprintf(
                                    _n('%s ürün', '%s ürün', $item_count, 'arim'),
                                    number_format_i18n($item_count)
                                ),
                                $status_note,
                            ]));
                            ?>
                            <tr
                                class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr($order->get_status()); ?> order"
                                data-arim-order-search-row
                                data-arim-order-search-text="<?php echo esc_attr(wp_strip_all_tags($searchable_text)); ?>"
                            >
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
                                            <?php if ($status_note !== '') : ?>
                                                <small class="arim-orders-table-note"><?php echo esc_html($status_note); ?></small>
                                            <?php endif; ?>

                                        <?php elseif ('order-total' === $column_id) : ?>
                                            <?php
                                            printf(
                                                /* translators: 1: formatted total, 2: item count */
                                                _n('%1$s · %2$s ürün', '%1$s · %2$s ürün', $item_count, 'arim'),
                                                wp_kses_post($order->get_formatted_order_total()),
                                                number_format_i18n($item_count)
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
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="arim-myaccount-inline-empty arim-myaccount-orders-inline-empty arim-myaccount-orders-search-empty" data-arim-orders-search-empty hidden>
                <h3><?php esc_html_e('Aramana uygun sipariş bulunamadı', 'arim'); ?></h3>
                <p><?php esc_html_e('Farklı bir sipariş numarası, durum ifadesi veya genel filtre deneyerek sonuçlarını genişletebilirsin.', 'arim'); ?></p>
            </div>
        <?php else : ?>
            <div class="arim-myaccount-inline-empty arim-myaccount-orders-inline-empty">
                <h3><?php esc_html_e('Bu filtrede sipariş görünmüyor', 'arim'); ?></h3>
                <p><?php esc_html_e('Farklı bir filtre seçerek diğer sipariş durumlarını inceleyebilir veya tüm siparişlerini tekrar görüntüleyebilirsin.', 'arim'); ?></p>
                <div class="arim-myaccount-empty-actions">
                    <a class="button" href="<?php echo esc_url(remove_query_arg('order_filter', wc_get_account_endpoint_url('orders'))); ?>">
                        <?php esc_html_e('Tüm siparişleri göster', 'arim'); ?>
                    </a>
                    <a class="arim-myaccount-orders-link" href="<?php echo esc_url($support_url); ?>">
                        <?php esc_html_e('Hesap ayarlarına git', 'arim'); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php do_action('woocommerce_before_account_orders_pagination'); ?>

        <?php if (1 < $customer_orders->max_num_pages) : ?>
            <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination arim-myaccount-pagination">
                <?php if (1 !== $current_page) : ?>
                    <?php
                    $previous_url = wc_get_endpoint_url('orders', $current_page - 1);
                    if ('all' !== $active_filter) {
                        $previous_url = add_query_arg('order_filter', $active_filter, $previous_url);
                    }
                    ?>
                    <a class="woocommerce-button woocommerce-button--previous button" href="<?php echo esc_url($previous_url); ?>">
                        <?php esc_html_e('Previous', 'woocommerce'); ?>
                    </a>
                <?php endif; ?>

                <?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
                    <?php
                    $next_url = wc_get_endpoint_url('orders', $current_page + 1);
                    if ('all' !== $active_filter) {
                        $next_url = add_query_arg('order_filter', $active_filter, $next_url);
                    }
                    ?>
                    <a class="woocommerce-button woocommerce-button--next button" href="<?php echo esc_url($next_url); ?>">
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
