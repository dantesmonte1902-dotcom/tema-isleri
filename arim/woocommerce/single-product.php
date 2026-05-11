<?php
defined('ABSPATH') || exit;

get_header('shop');
?>

<div class="arim-single-page">
    <div class="arim-container">
        <?php
        do_action('woocommerce_before_main_content');

        while (have_posts()) :
            the_post();
            wc_get_template_part('content', 'single-product');
        endwhile;

        do_action('woocommerce_after_main_content');
        ?>
    </div>
</div>

<?php
get_footer('shop');