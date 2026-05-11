<?php
defined('ABSPATH') || exit;

$navigation_data = arim_myaccount_navigation_data();
$nav_user        = isset($navigation_data['user']) && is_array($navigation_data['user']) ? $navigation_data['user'] : [];
$nav_summary     = isset($navigation_data['summary']) && is_array($navigation_data['summary']) ? $navigation_data['summary'] : [];
$nav_items       = isset($navigation_data['items']) && is_array($navigation_data['items']) ? $navigation_data['items'] : [];
?>

<nav class="woocommerce-MyAccount-navigation arim-myaccount-nav">
    <div class="arim-myaccount-nav-box">
        <div class="arim-myaccount-nav-head">
            <span class="arim-myaccount-badge"><?php esc_html_e('Hesabım', 'arim'); ?></span>
            <h3 class="arim-myaccount-nav-title"><?php echo esc_html($nav_user['name'] ?? __('ARIM Hesabı', 'arim')); ?></h3>
            <p class="arim-myaccount-nav-copy"><?php echo esc_html($nav_user['email'] ?? ''); ?></p>

            <div class="arim-myaccount-nav-summary">
                <div class="arim-myaccount-nav-summary-item">
                    <strong><?php echo esc_html(number_format_i18n((int) ($nav_summary['orders'] ?? 0))); ?></strong>
                    <span><?php esc_html_e('sipariş', 'arim'); ?></span>
                </div>
                <div class="arim-myaccount-nav-summary-item">
                    <strong><?php echo esc_html(number_format_i18n((int) ($nav_summary['addresses'] ?? 0))); ?></strong>
                    <span><?php esc_html_e('adres', 'arim'); ?></span>
                </div>
            </div>
        </div>

        <ul>
            <?php foreach ($nav_items as $endpoint => $item) : ?>
                <li class="<?php echo esc_attr($item['classes']); ?>">
                    <a href="<?php echo esc_url($item['url']); ?>">
                        <span class="arim-myaccount-nav-item-badge"><?php echo esc_html($item['badge']); ?></span>
                        <span class="arim-myaccount-nav-item-content">
                            <strong><?php echo esc_html($item['label']); ?></strong>
                            <small><?php echo esc_html($item['description']); ?></small>
                        </span>
                        <?php if (!empty($item['meta'])) : ?>
                            <span class="arim-myaccount-nav-item-meta"><?php echo esc_html($item['meta']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
