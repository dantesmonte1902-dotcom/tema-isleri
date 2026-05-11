<?php
defined('ABSPATH') || exit;

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