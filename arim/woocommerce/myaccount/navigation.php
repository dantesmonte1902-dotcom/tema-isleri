<?php
defined('ABSPATH') || exit;
?>

<nav class="woocommerce-MyAccount-navigation arim-myaccount-nav">
    <div class="arim-myaccount-nav-box">
        <h3 class="arim-myaccount-nav-title"><?php esc_html_e('Hesabım', 'arim'); ?></h3>

        <ul>
            <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
                <li class="<?php echo wc_get_account_menu_item_classes($endpoint); ?>">
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>