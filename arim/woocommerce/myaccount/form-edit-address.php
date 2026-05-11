<?php
/**
 * Edit address form
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined('ABSPATH') || exit;

$page_title        = ('billing' === $load_address) ? esc_html__('Fatura adresi', 'arim') : esc_html__('Teslimat adresi', 'arim');
$address_page_data = arim_myaccount_address_page_data();
$address_contact   = isset($address_page_data['contact']) && is_array($address_page_data['contact']) ? $address_page_data['contact'] : [];
$campaigns         = isset($address_page_data['campaigns']) && is_array($address_page_data['campaigns']) ? $address_page_data['campaigns'] : [];
$dashboard_url     = !empty($address_page_data['dashboardUrl']) ? $address_page_data['dashboardUrl'] : arim_account_url();
$addresses_url     = wc_get_account_endpoint_url('edit-address');

do_action('woocommerce_before_edit_account_address_form');
?>

<?php if (!$load_address) : ?>
    <?php wc_get_template('myaccount/my-address.php'); ?>
<?php else : ?>
    <div class="arim-myaccount-address-edit">
        <div class="arim-myaccount-address-edit-hero">
            <div>
                <span class="arim-myaccount-badge"><?php esc_html_e('Adres düzenleme', 'arim'); ?></span>
                <h2><?php echo esc_html(apply_filters('woocommerce_my_account_edit_address_title', $page_title, $load_address)); ?></h2>
                <p><?php esc_html_e('Sipariş teslimatını ve ödeme akışını hızlandırmak için aşağıdaki bilgileri güncel tut.', 'arim'); ?></p>
            </div>

            <div class="arim-myaccount-address-edit-contact">
                <span><?php esc_html_e('Hesap sahibi', 'arim'); ?></span>
                <strong><?php echo esc_html($address_contact['name'] ?? ''); ?></strong>
                <small><?php echo esc_html($address_contact['email'] ?? ''); ?></small>
            </div>
        </div>

        <div class="arim-myaccount-address-edit-layout">
            <form method="post" novalidate class="arim-myaccount-address-form">
                <div class="arim-myaccount-panel-head">
                    <div>
                        <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Adres formu', 'arim'); ?></span>
                        <h3><?php echo esc_html($page_title); ?></h3>
                    </div>
                    <a href="<?php echo esc_url($addresses_url); ?>">
                        <?php esc_html_e('Tüm adreslere dön', 'arim'); ?>
                    </a>
                </div>

                <div class="woocommerce-address-fields">
                    <?php do_action("woocommerce_before_edit_address_form_{$load_address}"); ?>

                    <div class="woocommerce-address-fields__field-wrapper">
                        <?php
                        foreach ($address as $key => $field) {
                            woocommerce_form_field($key, $field, wc_get_post_data_by_key($key, $field['value']));
                        }
                        ?>
                    </div>

                    <?php do_action("woocommerce_after_edit_address_form_{$load_address}"); ?>

                    <p>
                        <button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="save_address" value="<?php esc_attr_e('Adresi kaydet', 'arim'); ?>"><?php esc_html_e('Adresi Kaydet', 'arim'); ?></button>
                        <?php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); ?>
                        <input type="hidden" name="action" value="edit_address" />
                    </p>
                </div>
            </form>

            <aside class="arim-myaccount-address-edit-side">
                <div class="arim-myaccount-address-help">
                    <span class="arim-myaccount-panel-kicker"><?php esc_html_e('Hızlı notlar', 'arim'); ?></span>
                    <h3><?php esc_html_e('Doğru teslimat için dikkat et', 'arim'); ?></h3>
                    <ul>
                        <li><?php esc_html_e('Mahalle, sokak ve kapı numarası bilgilerini eksiksiz gir.', 'arim'); ?></li>
                        <li><?php esc_html_e('Telefon alanını aktif kurye iletişimi için güncel tut.', 'arim'); ?></li>
                        <li><?php esc_html_e('Adreslerini güncelledikten sonra sipariş ve sepet akışı daha hızlı ilerler.', 'arim'); ?></li>
                    </ul>
                    <div class="arim-myaccount-address-help-actions">
                        <a class="arim-myaccount-orders-link" href="<?php echo esc_url($dashboard_url); ?>">
                            <?php esc_html_e('Panele dön', 'arim'); ?>
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
<?php endif; ?>

<?php do_action('woocommerce_after_edit_account_address_form'); ?>
