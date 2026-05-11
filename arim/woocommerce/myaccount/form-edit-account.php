<?php
/**
 * Edit account form
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.5.0
 */

defined('ABSPATH') || exit;

$account_page_data = arim_myaccount_account_page_data();
$account_stats     = isset($account_page_data['stats']) && is_array($account_page_data['stats']) ? $account_page_data['stats'] : [];
$identity          = isset($account_page_data['identity']) && is_array($account_page_data['identity']) ? $account_page_data['identity'] : [];
$security          = isset($account_page_data['security']) && is_array($account_page_data['security']) ? $account_page_data['security'] : [];
$campaigns         = isset($account_page_data['campaigns']) && is_array($account_page_data['campaigns']) ? $account_page_data['campaigns'] : [];
$orders_url        = !empty($account_page_data['ordersUrl']) ? $account_page_data['ordersUrl'] : wc_get_account_endpoint_url('orders');
$address_url       = !empty($account_page_data['addressUrl']) ? $account_page_data['addressUrl'] : wc_get_account_endpoint_url('edit-address');
$dashboard_url     = !empty($account_page_data['dashboardUrl']) ? $account_page_data['dashboardUrl'] : arim_account_url();

do_action('woocommerce_before_edit_account_form');
?>

<div class="arim-myaccount-account">
    <div class="arim-myaccount-account-hero">
        <div>
            <span class="arim-myaccount-badge"><?php esc_html_e('Hesap bilgileri', 'arim'); ?></span>
            <h2><?php esc_html_e('Profilini ve güvenlik ayarlarını yönet', 'arim'); ?></h2>
            <p><?php esc_html_e('Ad, e-posta ve şifre alanlarını güncel tutarak sipariş, teslimat ve destek akışını daha sorunsuz ilerletebilirsin.', 'arim'); ?></p>
        </div>

        <div class="arim-myaccount-account-hero-card">
            <span><?php esc_html_e('Profil görünümü', 'arim'); ?></span>
            <strong><?php echo esc_html($identity['fullName'] ?: ($identity['displayName'] ?? '—')); ?></strong>
            <small><?php echo esc_html($identity['email'] ?? ''); ?></small>
        </div>
    </div>

    <div class="arim-myaccount-account-summary">
        <div class="arim-myaccount-account-stat">
            <span><?php esc_html_e('Profil doluluk', 'arim'); ?></span>
            <strong><?php echo esc_html(number_format_i18n((int) ($account_stats['profileCompletion'] ?? 0))); ?>%</strong>
        </div>
        <div class="arim-myaccount-account-stat">
            <span><?php esc_html_e('Kayıtlı adres', 'arim'); ?></span>
            <strong><?php echo esc_html(number_format_i18n((int) ($account_stats['savedAddresses'] ?? 0))); ?></strong>
        </div>
        <div class="arim-myaccount-account-stat">
            <span><?php esc_html_e('Güvenlik durumu', 'arim'); ?></span>
            <strong><?php echo esc_html($account_stats['securityReady'] ?? '—'); ?></strong>
        </div>
        <div class="arim-myaccount-account-stat">
            <span><?php esc_html_e('İletişim hattı', 'arim'); ?></span>
            <strong><?php echo esc_html($account_stats['contactChannel'] ?? '—'); ?></strong>
        </div>
    </div>

    <div class="arim-myaccount-account-layout">
        <form class="woocommerce-EditAccountForm edit-account arim-myaccount-account-form" action="" method="post" <?php do_action('woocommerce_edit_account_form_tag'); ?>>
            <?php do_action('woocommerce_edit_account_form_start'); ?>

            <div class="arim-myaccount-panel-head">
                <div>
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Profil formu', 'arim'); ?></span>
                    <h3><?php esc_html_e('Temel hesap bilgileri', 'arim'); ?></h3>
                </div>
                <a href="<?php echo esc_url($dashboard_url); ?>">
                    <?php esc_html_e('Panele dön', 'arim'); ?>
                </a>
            </div>

            <div class="arim-myaccount-account-grid">
                <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                    <label for="account_first_name"><?php esc_html_e('Ad', 'arim'); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr($user->first_name); ?>" aria-required="true" />
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                    <label for="account_last_name"><?php esc_html_e('Soyad', 'arim'); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr($user->last_name); ?>" aria-required="true" />
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="account_display_name"><?php esc_html_e('Görünen ad', 'arim'); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" aria-describedby="account_display_name_description" value="<?php echo esc_attr($user->display_name); ?>" aria-required="true" />
                    <span id="account_display_name_description"><em><?php esc_html_e('Bu isim hesap alanında ve ürün yorumlarında görünecek.', 'arim'); ?></em></span>
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="account_email"><?php esc_html_e('E-posta adresi', 'arim'); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                    <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr($user->user_email); ?>" aria-required="true" />
                </p>
            </div>

            <?php do_action('woocommerce_edit_account_form_fields'); ?>

            <fieldset class="arim-myaccount-account-security">
                <legend><?php esc_html_e('Şifre yenileme', 'arim'); ?></legend>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="password_current"><?php esc_html_e('Mevcut şifre (değiştirmeyeceksen boş bırak)', 'arim'); ?></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="current-password" />
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="password_1"><?php esc_html_e('Yeni şifre (değiştirmeyeceksen boş bırak)', 'arim'); ?></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="new-password" />
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="password_2"><?php esc_html_e('Yeni şifre tekrar', 'arim'); ?></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="new-password" />
                </p>
            </fieldset>

            <?php do_action('woocommerce_edit_account_form'); ?>

            <p>
                <?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
                <button type="submit" class="woocommerce-Button button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="save_account_details" value="<?php esc_attr_e('Değişiklikleri kaydet', 'arim'); ?>"><?php esc_html_e('Değişiklikleri Kaydet', 'arim'); ?></button>
                <input type="hidden" name="action" value="save_account_details" />
            </p>

            <?php do_action('woocommerce_edit_account_form_end'); ?>
        </form>

        <aside class="arim-myaccount-account-side">
            <div class="arim-myaccount-address-help">
                <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Güvenlik özeti', 'arim'); ?></span>
                <h3><?php echo esc_html($security['title'] ?? __('Güvenlik özetin', 'arim')); ?></h3>
                <p class="arim-myaccount-account-side-copy"><?php echo esc_html($security['text'] ?? __('Hesap alanlarını güncel tut.', 'arim')); ?></p>
                <ul>
                    <li><?php esc_html_e('Şifre değişikliği yaptığında yeni giriş bilgilerini güvenli şekilde sakla.', 'arim'); ?></li>
                    <li><?php esc_html_e('Adres ve sipariş akışların için e-posta bilgisinin güncel olması önemlidir.', 'arim'); ?></li>
                    <li><?php esc_html_e('Telefon ve adres alanlarını kontrol ederek teslimat süreçlerini hızlandırabilirsin.', 'arim'); ?></li>
                </ul>
                <div class="arim-myaccount-address-help-actions">
                    <a class="arim-myaccount-orders-link" href="<?php echo esc_url($orders_url); ?>">
                        <?php esc_html_e('Siparişlerime git', 'arim'); ?>
                    </a>
                    <a class="arim-myaccount-orders-link" href="<?php echo esc_url($address_url); ?>">
                        <?php esc_html_e('Adreslerimi aç', 'arim'); ?>
                    </a>
                </div>
            </div>

            <?php if (!empty($campaigns)) : ?>
                <div class="arim-myaccount-address-campaigns">
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Ek fırsatlar', 'arim'); ?></span>
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

<?php do_action('woocommerce_after_edit_account_form'); ?>
