<?php
defined('ABSPATH') || exit;

$user           = wp_get_current_user();
$dashboard_data = arim_myaccount_dashboard_data();
$dashboard_stats = isset($dashboard_data['stats']) && is_array($dashboard_data['stats']) ? $dashboard_data['stats'] : [];
$recent_orders  = isset($dashboard_data['recentOrders']) && is_array($dashboard_data['recentOrders']) ? $dashboard_data['recentOrders'] : [];
$readiness      = isset($dashboard_data['readiness']) && is_array($dashboard_data['readiness']) ? $dashboard_data['readiness'] : [];
$readiness_items = isset($readiness['items']) && is_array($readiness['items']) ? $readiness['items'] : [];
$campaigns      = isset($dashboard_data['campaigns']) && is_array($dashboard_data['campaigns']) ? $dashboard_data['campaigns'] : [];

$quick_actions = [
    [
        'title' => __('Siparişlerim', 'arim'),
        'text'  => __('Geçmiş siparişlerini, kargo akışını ve aktif teslimatları görüntüle.', 'arim'),
        'url'   => wc_get_account_endpoint_url('orders'),
        'meta'  => sprintf(
            _n('%s sipariş', '%s sipariş', (int) ($dashboard_stats['orders'] ?? 0), 'arim'),
            number_format_i18n((int) ($dashboard_stats['orders'] ?? 0))
        ),
    ],
    [
        'title' => __('Adreslerim', 'arim'),
        'text'  => __('Teslimat ve fatura adreslerini tek yerden düzenle.', 'arim'),
        'url'   => wc_get_account_endpoint_url('edit-address'),
        'meta'  => sprintf(
            _n('%s kayıtlı adres', '%s kayıtlı adres', (int) ($dashboard_stats['addressCount'] ?? 0), 'arim'),
            number_format_i18n((int) ($dashboard_stats['addressCount'] ?? 0))
        ),
    ],
    [
        'title' => __('Hesap Bilgilerim', 'arim'),
        'text'  => __('Ad, e-posta ve güvenlik bilgilerini güncelle.', 'arim'),
        'url'   => wc_get_account_endpoint_url('edit-account'),
        'meta'  => __('Profil ve güvenlik', 'arim'),
    ],
    [
        'title' => __('Favorilerim', 'arim'),
        'text'  => __('Kaydettiğin ürünleri, karşılaştırmalarını ve önerileri yeniden keşfet.', 'arim'),
        'url'   => arim_favorites_url(),
        'meta'  => __('Favori ve karşılaştırma alanı', 'arim'),
    ],
];
?>

<div class="arim-myaccount-dashboard">
    <div class="arim-myaccount-hero">
        <div class="arim-myaccount-hero-content">
            <span class="arim-myaccount-badge"><?php esc_html_e('ARIM Hesabı', 'arim'); ?></span>
            <h2>
                <?php
                printf(
                    esc_html__('Merhaba %s', 'arim'),
                    esc_html($user->display_name)
                );
                ?>
            </h2>
            <p>
                <?php esc_html_e('Siparişlerini görüntüleyebilir, teslimat ritmini takip edebilir, adreslerini güncelleyebilir ve fırsat alanlarını tek panelden yönetebilirsin.', 'arim'); ?>
            </p>
        </div>

        <div class="arim-myaccount-hero-stats">
            <div class="arim-myaccount-hero-stat">
                <strong><?php echo esc_html(number_format_i18n((int) ($dashboard_stats['orders'] ?? 0))); ?></strong>
                <span><?php esc_html_e('toplam sipariş', 'arim'); ?></span>
            </div>
            <div class="arim-myaccount-hero-stat">
                <strong><?php echo esc_html(number_format_i18n((int) ($dashboard_stats['active'] ?? 0))); ?></strong>
                <span><?php esc_html_e('aktif akış', 'arim'); ?></span>
            </div>
            <div class="arim-myaccount-hero-stat">
                <strong><?php echo esc_html(number_format_i18n((int) ($dashboard_stats['addressCount'] ?? 0))); ?></strong>
                <span><?php esc_html_e('kayıtlı adres', 'arim'); ?></span>
            </div>
            <div class="arim-myaccount-hero-stat">
                <strong><?php echo esc_html(number_format_i18n((int) ($dashboard_stats['campaignCount'] ?? 0))); ?></strong>
                <span><?php esc_html_e('kampanya alanı', 'arim'); ?></span>
            </div>
        </div>
    </div>

    <div class="arim-myaccount-status-strip">
        <div class="arim-myaccount-status-card">
            <span><?php esc_html_e('Tamamlanan sipariş', 'arim'); ?></span>
            <strong><?php echo esc_html(number_format_i18n((int) ($dashboard_stats['completed'] ?? 0))); ?></strong>
        </div>
        <div class="arim-myaccount-status-card">
            <span><?php esc_html_e('Hazırlanan sipariş', 'arim'); ?></span>
            <strong><?php echo esc_html(number_format_i18n((int) ($dashboard_stats['processing'] ?? 0))); ?></strong>
        </div>
        <div class="arim-myaccount-status-card">
            <span><?php esc_html_e('Canlı destek', 'arim'); ?></span>
            <strong><?php esc_html_e('7/24', 'arim'); ?></strong>
        </div>
    </div>

    <?php if (!empty($readiness)) : ?>
        <section class="arim-myaccount-readiness">
            <div class="arim-myaccount-readiness-head">
                <div>
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Hazırlık merkezi', 'arim'); ?></span>
                    <h3><?php echo esc_html($readiness['title'] ?? __('Hesabını tamamla', 'arim')); ?></h3>
                    <p><?php echo esc_html($readiness['text'] ?? ''); ?></p>
                </div>
                <strong><?php echo esc_html(number_format_i18n((int) ($readiness['percent'] ?? 0))); ?>%</strong>
            </div>

            <div class="arim-myaccount-readiness-progress" aria-hidden="true">
                <span style="width: <?php echo esc_attr(max(0, min(100, (int) ($readiness['percent'] ?? 0)))); ?>%;"></span>
            </div>

            <?php if (!empty($readiness_items)) : ?>
                <div class="arim-myaccount-readiness-grid">
                    <?php foreach ($readiness_items as $item) : ?>
                        <a class="arim-myaccount-readiness-item <?php echo !empty($item['isReady']) ? 'is-ready' : 'is-pending'; ?>" href="<?php echo esc_url($item['url'] ?? wc_get_account_endpoint_url('edit-account')); ?>">
                            <span class="arim-myaccount-readiness-state"><?php echo !empty($item['isReady']) ? '✓' : '•'; ?></span>
                            <span class="arim-myaccount-readiness-copy">
                                <strong><?php echo esc_html($item['label'] ?? ''); ?></strong>
                                <em><?php echo esc_html($item['value'] ?? ''); ?></em>
                                <small><?php echo esc_html($item['detail'] ?? ''); ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <div class="arim-myaccount-cards">
        <?php foreach ($quick_actions as $action) : ?>
            <a class="arim-myaccount-card" href="<?php echo esc_url($action['url']); ?>">
                <span class="arim-myaccount-card-meta"><?php echo esc_html($action['meta']); ?></span>
                <h3><?php echo esc_html($action['title']); ?></h3>
                <p><?php echo esc_html($action['text']); ?></p>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="arim-myaccount-dashboard-grid">
        <section class="arim-myaccount-panel">
            <div class="arim-myaccount-panel-head">
                <div>
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Son hareketler', 'arim'); ?></span>
                    <h3><?php esc_html_e('Sipariş ritmin', 'arim'); ?></h3>
                </div>
                <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">
                    <?php esc_html_e('Tüm siparişleri gör', 'arim'); ?>
                </a>
            </div>

            <?php if (!empty($recent_orders)) : ?>
                <div class="arim-myaccount-order-list">
                    <?php foreach ($recent_orders as $order) : ?>
                        <article class="arim-myaccount-order-card">
                            <div class="arim-myaccount-order-top">
                                <div>
                                    <strong>#<?php echo esc_html($order['id']); ?></strong>
                                    <span><?php echo esc_html($order['date']); ?></span>
                                </div>
                                <span class="arim-myaccount-order-status is-<?php echo esc_attr($order['statusKey']); ?>">
                                    <?php echo esc_html($order['status']); ?>
                                </span>
                            </div>

                            <div class="arim-myaccount-order-bottom">
                                <div class="arim-myaccount-order-metric">
                                    <span><?php esc_html_e('Toplam', 'arim'); ?></span>
                                    <strong><?php echo esc_html($order['total']); ?></strong>
                                </div>
                                <div class="arim-myaccount-order-metric">
                                    <span><?php esc_html_e('İçerik', 'arim'); ?></span>
                                    <strong><?php echo esc_html($order['itemLabel']); ?></strong>
                                </div>
                                <a href="<?php echo esc_url($order['url']); ?>" class="arim-myaccount-order-link">
                                    <?php esc_html_e('Detayı aç', 'arim'); ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="arim-myaccount-inline-empty">
                    <h3><?php esc_html_e('İlk siparişini bekliyoruz', 'arim'); ?></h3>
                    <p><?php esc_html_e('Sipariş verdiğinde teslimat akışın ve güncel durum kartların burada görünür.', 'arim'); ?></p>
                    <a href="<?php echo esc_url(arim_shop_url()); ?>" class="button"><?php esc_html_e('Mağazaya dön', 'arim'); ?></a>
                </div>
            <?php endif; ?>
        </section>

        <aside class="arim-myaccount-panel arim-myaccount-panel-side">
            <div class="arim-myaccount-panel-head">
                <div>
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Sana özel alanlar', 'arim'); ?></span>
                    <h3><?php esc_html_e('Fırsat ve destek merkezi', 'arim'); ?></h3>
                </div>
            </div>

            <?php if (!empty($campaigns)) : ?>
                <div class="arim-myaccount-campaign-list">
                    <?php foreach ($campaigns as $campaign) : ?>
                        <div class="arim-myaccount-campaign-item">
                            <strong><?php echo esc_html($campaign['value']); ?></strong>
                            <span><?php echo esc_html($campaign['text']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="arim-myaccount-support-box">
                <h4><?php esc_html_e('Hızlı aksiyonlar', 'arim'); ?></h4>
                <ul>
                    <li><?php esc_html_e('Kargo hareketleri sipariş detayında anlık görünür.', 'arim'); ?></li>
                    <li><?php esc_html_e('Adres değişikliklerini ödeme öncesi hızlıca güncelleyebilirsin.', 'arim'); ?></li>
                    <li><?php esc_html_e('Favorilere döndüğünde öneriler ve karşılaştırmalar seni bekler.', 'arim'); ?></li>
                </ul>
            </div>
        </aside>
    </div>

    <div class="arim-myaccount-personal-grid">
        <section class="arim-myaccount-panel arim-myaccount-personal-panel">
            <div class="arim-myaccount-panel-head">
                <div>
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Karar alanın', 'arim'); ?></span>
                    <h3><?php esc_html_e('Karşılaştırma listem', 'arim'); ?></h3>
                </div>
                <a href="<?php echo esc_url(arim_favorites_url() . '#compare'); ?>">
                    <?php esc_html_e('Karşılaştırmaya git', 'arim'); ?>
                </a>
            </div>

            <div class="arim-myaccount-personal-body" data-arim-compare-page></div>
        </section>

        <section class="arim-myaccount-panel arim-myaccount-personal-panel">
            <div class="arim-myaccount-panel-head">
                <div>
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Geri dönüş alanın', 'arim'); ?></span>
                    <h3><?php esc_html_e('Son görüntülenen ürünler', 'arim'); ?></h3>
                </div>
                <a href="<?php echo esc_url(arim_shop_url()); ?>">
                    <?php esc_html_e('Mağazaya dön', 'arim'); ?>
                </a>
            </div>

            <div class="arim-myaccount-personal-body" data-arim-recently-viewed-page></div>
        </section>

        <section class="arim-myaccount-panel arim-myaccount-personal-panel">
            <div class="arim-myaccount-panel-head">
                <div>
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Sana özel', 'arim'); ?></span>
                    <h3><?php esc_html_e('Öneri vitrini', 'arim'); ?></h3>
                </div>
                <button class="arim-myaccount-inline-link" type="button" data-arim-refresh-recommendations>
                    <?php esc_html_e('Yenile', 'arim'); ?>
                </button>
            </div>

            <div class="arim-myaccount-personal-body" data-arim-recommendations-page></div>
        </section>
    </div>

    <div class="arim-myaccount-note">
        <?php
        do_action('woocommerce_account_dashboard');

        /* translators: 1: Orders URL 2: Address URL 3: Account URL */
        $dashboard_message = wp_kses(
            __('Kontrol panelinden <a href="%1$s">siparişlerini</a>, <a href="%2$s">adreslerini</a> ve <a href="%3$s">hesap bilgilerini</a> yönetebilirsin.', 'arim'),
            [
                'a' => [
                    'href' => [],
                ],
            ]
        );

        printf(
            $dashboard_message,
            esc_url(wc_get_account_endpoint_url('orders')),
            esc_url(wc_get_account_endpoint_url('edit-address')),
            esc_url(wc_get_account_endpoint_url('edit-account'))
        );
        ?>
    </div>
</div>
