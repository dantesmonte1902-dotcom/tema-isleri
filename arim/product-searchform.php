<?php
defined('ABSPATH') || exit;
?>
<form role="search" method="get" class="woocommerce-product-search arim-product-search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="arim-search-wrap">
        <input
            type="search"
            class="search-field"
            placeholder="<?php echo esc_attr__('Ürün, kategori veya marka ara', 'arim'); ?>"
            value="<?php echo get_search_query(); ?>"
            name="s"
            aria-label="<?php echo esc_attr__('Ürün ara', 'arim'); ?>"
        />
        <input type="hidden" name="post_type" value="product" />
        <button type="submit" value="<?php echo esc_attr_x('Ara', 'submit button', 'arim'); ?>">
            <?php esc_html_e('Ara', 'arim'); ?>
        </button>
    </div>
</form>