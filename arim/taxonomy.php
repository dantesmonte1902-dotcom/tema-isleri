<?php
defined('ABSPATH') || exit;

$queried_object  = get_queried_object();
$product_archive = get_template_directory() . '/woocommerce/archive-product.php';
$is_product_tax  = $queried_object instanceof WP_Term
    && (
        in_array($queried_object->taxonomy, ['product_cat', 'product_tag', 'product_brand', 'product_visibility'], true)
        || strpos($queried_object->taxonomy, 'pa_') === 0
    );

if ($is_product_tax && file_exists($product_archive)) {
    include $product_archive;
    return;
}

get_header();
?>

<div class="arim-container" style="padding:40px 0;">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?> style="margin-bottom:30px;">
                <h1><?php the_title(); ?></h1>
                <div><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p><?php esc_html_e('İçerik bulunamadı.', 'arim'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
