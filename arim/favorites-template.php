<?php
defined('ABSPATH') || exit;

get_header();

$shop_url = function_exists('arim_shop_url') ? arim_shop_url() : home_url('/shop');
?>

<div class="arim-favorites-page">
    <div class="arim-container">
        <header class="arim-favorites-header">
            <h1><?php esc_html_e('Favorilerim', 'arim'); ?></h1>
            <p><?php esc_html_e('Beğendiğin ürünleri kaydet, öne çıkan fırsatları tek bakışta karşılaştır ve dilediğin zaman alışverişe geri dön.', 'arim'); ?></p>
        </header>

        <section class="arim-favorites-hero">
            <div class="arim-favorites-hero-copy">
                <span class="arim-favorites-hero-badge"><?php esc_html_e('Senin için hazırlandı', 'arim'); ?></span>
                <h2><?php esc_html_e('Kişisel vitrinini oluştur', 'arim'); ?></h2>
                <p><?php esc_html_e('Favorilediğin ürünler burada birikir; indirimdekileri öne çıkarır, avantaj toplamını gösterir ve hızlı geri dönüş sağlar.', 'arim'); ?></p>
            </div>

            <div class="arim-favorites-summary" data-arim-favorites-summary>
                <div class="arim-favorites-stat">
                    <span><?php esc_html_e('Ürün', 'arim'); ?></span>
                    <strong data-arim-favorites-count>0</strong>
                </div>
                <div class="arim-favorites-stat">
                    <span><?php esc_html_e('İndirimli', 'arim'); ?></span>
                    <strong data-arim-favorites-sale-count>0</strong>
                </div>
                <div class="arim-favorites-stat">
                    <span><?php esc_html_e('Toplam Avantaj', 'arim'); ?></span>
                    <strong data-arim-favorites-savings>₺0</strong>
                </div>
            </div>
        </section>

        <div class="arim-favorites-toolbar">
            <a class="arim-favorites-toolbar-link is-primary" href="<?php echo esc_url($shop_url); ?>">
                <?php esc_html_e('Yeni ürünler keşfet', 'arim'); ?>
            </a>
            <span class="arim-favorites-toolbar-note"><?php esc_html_e('Kalp ikonuyla eklediğin ürünler bu sayfada otomatik olarak görünür.', 'arim'); ?></span>
        </div>

        <div class="arim-favorites-dynamic" data-arim-favorites-page></div>

        <section id="compare" class="arim-favorites-secondary-section arim-compare-section" data-arim-compare-section>
            <div class="arim-favorites-section-head">
                <div>
                    <span class="arim-favorites-section-kicker"><?php esc_html_e('Karar verme hızını artır', 'arim'); ?></span>
                    <h2><?php esc_html_e('Karşılaştırma Listem', 'arim'); ?></h2>
                </div>
                <a class="arim-favorites-toolbar-link" href="<?php echo esc_url($shop_url); ?>">
                    <?php esc_html_e('Karşılaştırılacak ürün seç', 'arim'); ?>
                </a>
            </div>

            <div data-arim-compare-page></div>
        </section>

        <section class="arim-favorites-secondary-section" data-arim-recently-viewed-section>
            <div class="arim-favorites-section-head">
                <div>
                    <span class="arim-favorites-section-kicker"><?php esc_html_e('Alışverişe kaldığın yerden devam et', 'arim'); ?></span>
                    <h2><?php esc_html_e('Son görüntülenen ürünler', 'arim'); ?></h2>
                </div>
                <a class="arim-favorites-toolbar-link" href="<?php echo esc_url($shop_url); ?>">
                    <?php esc_html_e('Mağazaya dön', 'arim'); ?>
                </a>
            </div>

            <div data-arim-recently-viewed-page></div>
        </section>

        <section class="arim-favorites-secondary-section arim-recommendations-section" data-arim-recommendations-section>
            <div class="arim-favorites-section-head">
                <div>
                    <span class="arim-favorites-section-kicker"><?php esc_html_e('Senin alışveriş ritmine göre', 'arim'); ?></span>
                    <h2><?php esc_html_e('Sana Özel Öneriler', 'arim'); ?></h2>
                </div>
                <button class="arim-favorites-toolbar-link arim-recommendations-refresh" type="button" data-arim-refresh-recommendations>
                    <?php esc_html_e('Önerileri yenile', 'arim'); ?>
                </button>
            </div>

            <div data-arim-recommendations-page></div>
        </section>
    </div>
</div>

<?php
get_footer();
