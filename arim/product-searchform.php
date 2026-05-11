<?php
defined('ABSPATH') || exit;
?>
<form role="search" method="get" class="woocommerce-product-search arim-product-search-form" action="<?php echo esc_url(home_url('/')); ?>" data-arim-live-search>
    <div class="arim-search-wrap">
        <input
            type="search"
            class="search-field"
            placeholder="<?php echo esc_attr__('Ürün, kategori veya marka ara', 'arim'); ?>"
            value="<?php echo get_search_query(); ?>"
            name="s"
            aria-label="<?php echo esc_attr__('Ürün ara', 'arim'); ?>"
            autocomplete="off"
            data-arim-search-input
        />
        <input type="hidden" name="post_type" value="product" />
        <button type="submit" value="<?php echo esc_attr_x('Ara', 'submit button', 'arim'); ?>">
            <?php esc_html_e('Ara', 'arim'); ?>
        </button>
    </div>

    <div class="arim-search-suggestions" data-arim-search-suggestions hidden>
        <div class="arim-search-suggestions-head">
            <span><?php esc_html_e('Hızlı arama', 'arim'); ?></span>
        </div>
        <div class="arim-search-suggestions-body" data-arim-search-results></div>
    </div>
</form>
