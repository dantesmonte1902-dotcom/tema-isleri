<?php
defined('ABSPATH') || exit;

$user = wp_get_current_user();
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
                <?php esc_html_e('Siparişlerini görüntüleyebilir, adres bilgilerini düzenleyebilir ve hesap detaylarını buradan yönetebilirsin.', 'arim'); ?>
            </p>
        </div>
    </div>

    <div class="arim-myaccount-cards">
        <a class="arim-myaccount-card" href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">
            <h3><?php esc_html_e('Siparişlerim', 'arim'); ?></h3>
            <p><?php esc_html_e('Geçmiş siparişlerini ve durumlarını görüntüle.', 'arim'); ?></p>
        </a>

        <a class="arim-myaccount-card" href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address')); ?>">
            <h3><?php esc_html_e('Adreslerim', 'arim'); ?></h3>
            <p><?php esc_html_e('Teslimat ve fatura adreslerini düzenle.', 'arim'); ?></p>
        </a>

        <a class="arim-myaccount-card" href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>">
            <h3><?php esc_html_e('Hesap Bilgilerim', 'arim'); ?></h3>
            <p><?php esc_html_e('Ad, e-posta ve şifre bilgilerini güncelle.', 'arim'); ?></p>
        </a>
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