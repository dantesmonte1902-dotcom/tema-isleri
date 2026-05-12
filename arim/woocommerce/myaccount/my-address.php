<?php
/**
 * My Addresses
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined('ABSPATH') || exit;

$address_page_data = arim_myaccount_address_page_data();
$address_stats     = isset($address_page_data['stats']) && is_array($address_page_data['stats']) ? $address_page_data['stats'] : [];
$address_contact   = isset($address_page_data['contact']) && is_array($address_page_data['contact']) ? $address_page_data['contact'] : [];
$addresses_map     = isset($address_page_data['addresses']) && is_array($address_page_data['addresses']) ? $address_page_data['addresses'] : [];
$completion_items  = isset($address_page_data['completionItems']) && is_array($address_page_data['completionItems']) ? $address_page_data['completionItems'] : [];
$campaigns         = isset($address_page_data['campaigns']) && is_array($address_page_data['campaigns']) ? $address_page_data['campaigns'] : [];
$support_url       = !empty($address_page_data['supportUrl']) ? $address_page_data['supportUrl'] : wc_get_account_endpoint_url('edit-account');
$dashboard_url     = !empty($address_page_data['dashboardUrl']) ? $address_page_data['dashboardUrl'] : arim_account_url();

?>

<div class="arim-myaccount-addresses">
    <div class="arim-myaccount-section-head">
        <h2><?php esc_html_e('Adreslerim', 'arim'); ?></h2>
        <p><?php esc_html_e('Teslimat ve fatura bilgilerini güncel tut, sipariş ve ödeme adımlarını daha hızlı tamamla.', 'arim'); ?></p>
    </div>

    <div class="arim-myaccount-address-summary">
        <div class="arim-myaccount-address-stat">
            <span><?php esc_html_e('Kayıtlı adres', 'arim'); ?></span>
            <strong>
                <?php
                printf(
                    '%1$s / %2$s',
                    esc_html(number_format_i18n((int) ($address_stats['savedCount'] ?? 0))),
                    esc_html(number_format_i18n((int) ($address_stats['expectedCount'] ?? 0)))
                );
                ?>
            </strong>
        </div>
        <div class="arim-myaccount-address-stat">
            <span><?php esc_html_e('Hazırlık oranı', 'arim'); ?></span>
            <strong><?php echo esc_html(number_format_i18n((int) ($address_stats['completion'] ?? 0))); ?>%</strong>
        </div>
        <div class="arim-myaccount-address-stat">
            <span><?php esc_html_e('Hesap e-postası', 'arim'); ?></span>
            <strong><?php echo esc_html($address_contact['email'] ?? '—'); ?></strong>
        </div>
        <div class="arim-myaccount-address-stat">
            <span><?php esc_html_e('İletişim telefonu', 'arim'); ?></span>
            <strong><?php echo esc_html(!empty($address_contact['phone']) ? $address_contact['phone'] : '—'); ?></strong>
        </div>
    </div>

    <div class="arim-myaccount-completion-card">
        <div class="arim-myaccount-completion-head">
            <div>
                <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Adres hazırlık akışı', 'arim'); ?></span>
                <h3><?php esc_html_e('Teslimat için eksik alanları tamamla', 'arim'); ?></h3>
            </div>
            <strong><?php echo esc_html(number_format_i18n((int) ($address_stats['detailCompletion'] ?? 0))); ?>%</strong>
        </div>
        <div class="arim-myaccount-completion-progress" aria-hidden="true">
            <span style="width: <?php echo esc_attr(max(0, min(100, (int) ($address_stats['detailCompletion'] ?? 0)))); ?>%;"></span>
        </div>
        <?php if (!empty($completion_items)) : ?>
            <div class="arim-myaccount-completion-list">
                <?php foreach ($completion_items as $item) : ?>
                    <a class="arim-myaccount-completion-item <?php echo !empty($item['isReady']) ? 'is-ready' : 'is-pending'; ?>" href="<?php echo esc_url($item['actionUrl'] ?? $dashboard_url); ?>">
                        <span class="arim-myaccount-completion-state"><?php echo !empty($item['isReady']) ? '✓' : '•'; ?></span>
                        <span class="arim-myaccount-completion-copy">
                            <strong><?php echo esc_html($item['label'] ?? ''); ?></strong>
                            <small><?php echo esc_html($item['detail'] ?? ''); ?></small>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="arim-myaccount-address-layout">
        <div class="arim-myaccount-address-grid">
            <?php foreach ($addresses_map as $address_key => $address_item) : ?>
                <article class="arim-myaccount-address-card">
                    <div class="arim-myaccount-address-card-top">
                        <div>
                            <span class="arim-myaccount-panel-kicker"><?php echo esc_html($address_key === 'billing' ? __('Ödeme için', 'arim') : __('Teslimat için', 'arim')); ?></span>
                            <h3><?php echo esc_html($address_item['label']); ?></h3>
                        </div>
                        <span class="arim-myaccount-address-badge <?php echo !empty($address_item['isComplete']) ? 'is-ready' : 'is-empty'; ?>">
                            <?php echo !empty($address_item['isComplete']) ? esc_html__('Hazır', 'arim') : esc_html__('Eksik', 'arim'); ?>
                        </span>
                    </div>

                    <div class="arim-myaccount-address-card-body">
                        <?php if (!empty($address_item['address'])) : ?>
                            <address><?php echo wp_kses_post($address_item['address']); ?></address>
                        <?php else : ?>
                            <p><?php esc_html_e('Bu adres tipi henüz eklenmedi. Sipariş adımlarını hızlandırmak için bilgilerini tamamlayabilirsin.', 'arim'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="arim-myaccount-address-card-actions">
                        <a class="button" href="<?php echo esc_url($address_item['url']); ?>">
                            <?php echo !empty($address_item['isComplete']) ? esc_html__('Adresi düzenle', 'arim') : esc_html__('Adres ekle', 'arim'); ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <aside class="arim-myaccount-address-side">
            <div class="arim-myaccount-address-help">
                <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Adres rehberi', 'arim'); ?></span>
                <h3><?php esc_html_e('Teslimat akışını hızlandır', 'arim'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Teslimat adresini güncel tutarak kargo gecikmelerini azaltabilirsin.', 'arim'); ?></li>
                    <li><?php esc_html_e('Fatura adresi kayıtlı olduğunda ödeme adımı daha hızlı tamamlanır.', 'arim'); ?></li>
                    <li><?php esc_html_e('Telefon bilgini güncel tutmak kurye ve destek ekipleri için önemlidir.', 'arim'); ?></li>
                </ul>
                <div class="arim-myaccount-address-help-actions">
                    <a class="arim-myaccount-orders-link" href="<?php echo esc_url($support_url); ?>">
                        <?php esc_html_e('Hesap bilgilerini güncelle', 'arim'); ?>
                    </a>
                    <a class="arim-myaccount-orders-link" href="<?php echo esc_url($dashboard_url); ?>">
                        <?php esc_html_e('Panele dön', 'arim'); ?>
                    </a>
                </div>
            </div>

            <?php if (!empty($campaigns)) : ?>
                <div class="arim-myaccount-address-campaigns">
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Sipariş öncesi fırsatlar', 'arim'); ?></span>
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
</div>
