<?php
defined('ABSPATH') || exit;

global $product;

if (!$product) {
    return;
}

$product_id = $product->get_id();
$main_image_id = $product->get_image_id();
$main_image_url = $main_image_id ? wp_get_attachment_image_url($main_image_id, 'large') : wc_placeholder_img_src();
$gallery_ids = $product->get_gallery_image_ids();
$gallery_items = [];

if ($main_image_url) {
    $gallery_items[] = [
        'full'  => $main_image_url,
        'thumb' => $main_image_id ? wp_get_attachment_image_url($main_image_id, 'woocommerce_thumbnail') : $main_image_url,
        'alt'   => trim((string) get_post_meta($main_image_id, '_wp_attachment_image_alt', true)) ?: $product->get_name(),
    ];
}

if (!empty($gallery_ids)) {
    foreach ($gallery_ids as $gallery_id) {
        if ((int) $gallery_id === (int) $main_image_id) {
            continue;
        }

        $thumb_url = wp_get_attachment_image_url($gallery_id, 'woocommerce_thumbnail');
        $full_url  = wp_get_attachment_image_url($gallery_id, 'large');

        if (!$thumb_url || !$full_url) {
            continue;
        }

        $gallery_items[] = [
            'full'  => $full_url,
            'thumb' => $thumb_url,
            'alt'   => trim((string) get_post_meta($gallery_id, '_wp_attachment_image_alt', true)) ?: $product->get_name(),
        ];
    }
}

$gallery_total = count($gallery_items);
$gallery_badge_text = $gallery_total > 1
    ? sprintf(_n('%s görsel', '%s görsel', $gallery_total, 'arim'), number_format_i18n($gallery_total))
    : __('Premium ürün sunumu', 'arim');
$gallery_hint_text = $gallery_total > 1
    ? __('Küçük önizlemeler arasında geçiş yap, detayları tam ekranda incele.', 'arim')
    : __('Ürünü tam boy inceleyip detaylara daha rahat odaklan.', 'arim');

$brand = arim_product_brand_name($product_id);
$rating = $product->get_average_rating();
$review_count = $product->get_review_count();
$short_description = $product->get_short_description();
$price_html = $product->get_price_html();
$price_text = arim_product_price_text($product);
$current_price_value = (float) $product->get_price();
$regular_price_value = (float) $product->get_regular_price();
$store_name = arim_product_store_name($product_id);
$delivery_details = arim_single_product_delivery_details($product);
$campaigns = arim_single_product_campaigns(3);
$campaign_count = count($campaigns);
$store_rating = $rating > 0 ? sprintf(__('%s / 5 mağaza puanı', 'arim'), number_format((float) $rating, 1)) : __('Henüz değerlendirilmedi', 'arim');
$store_review_count = max(0, (int) $review_count);
$store_review_text = $store_review_count > 0
    ? sprintf(_n('%s değerlendirme', '%s değerlendirme', $store_review_count, 'arim'), number_format_i18n($store_review_count))
    : __('İlk yorumu sen bırak', 'arim');
$store_shipping_text = $product->is_in_stock() ? __('Bugün kargoda fırsatı', 'arim') : __('Siparişe göre hazırlanır', 'arim');
$stock_state_text = $product->is_in_stock() ? __('Stokta var', 'arim') : __('Stokta yok', 'arim');
$discount_amount_value = max(0, $regular_price_value - $current_price_value);
$discount_percent = ($regular_price_value > 0 && $discount_amount_value > 0)
    ? (int) round(($discount_amount_value / $regular_price_value) * 100)
    : 0;
$discount_amount_text = $discount_amount_value > 0 ? wp_strip_all_tags(wc_price($discount_amount_value)) : '';
$price_insight_title = $discount_amount_value > 0
    ? sprintf(__('Sepette yaklaşık %s tasarruf', 'arim'), '%' . $discount_percent)
    : __('Güncel mağaza fiyatı', 'arim');
$price_insight_note = $discount_amount_value > 0
    ? sprintf(__('Bu üründe yaklaşık %s avantaj öne çıkıyor.', 'arim'), $discount_amount_text)
    : __('Fiyat düzenli olarak mağaza vitriniyle senkronize edilir.', 'arim');

$purchase_journey = [
    [
        'title' => __('Sipariş onayı', 'arim'),
        'text'  => __('Ödeme tamamlandığında siparişin hızla işleme alınır.', 'arim'),
    ],
    [
        'title' => __('Hazırlık süreci', 'arim'),
        'text'  => $product->is_in_stock()
            ? __('Ürün stoktan hazırlanır ve kargo etiketine düşer.', 'arim')
            : __('Ürün hazırlanınca öncelikli gönderim akışına alınır.', 'arim'),
    ],
    [
        'title' => __('Teslimat takibi', 'arim'),
        'text'  => sprintf(__('Tahmini teslimat günü: %s', 'arim'), $delivery_details['date']),
    ],
];

$product_badge = '';
if ($product->is_on_sale()) {
    $product_badge = __('Fırsat', 'arim');
} elseif ($product->is_featured()) {
    $product_badge = __('Öne Çıkan', 'arim');
}
?>
<div
    id="product-<?php the_ID(); ?>"
    <?php wc_product_class('arim-single-product', $product); ?>
    data-arim-recent-product
    data-product-id="<?php echo esc_attr($product_id); ?>"
    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
    data-product-price="<?php echo esc_attr($price_text); ?>"
    data-product-image="<?php echo esc_url($main_image_url); ?>"
    data-product-url="<?php echo esc_url(get_permalink($product_id)); ?>"
    data-product-brand="<?php echo esc_attr($brand ? $brand : __('ARIM', 'arim')); ?>"
    data-product-store="<?php echo esc_attr($store_name); ?>"
    data-product-badge="<?php echo esc_attr($product_badge); ?>"
    data-product-current-price="<?php echo esc_attr($current_price_value); ?>"
    data-product-regular-price="<?php echo esc_attr($regular_price_value); ?>"
>

    <div class="arim-single-grid">
        <div class="arim-single-gallery" data-arim-product-gallery>
            <div class="arim-single-gallery-head">
                <div>
                    <span class="arim-single-highlight-kicker"><?php echo esc_html($gallery_badge_text); ?></span>
                    <h2><?php esc_html_e('Premium ürün galeri alanı', 'arim'); ?></h2>
                    <p><?php echo esc_html($gallery_hint_text); ?></p>
                </div>

                <button
                    class="arim-single-gallery-zoom"
                    type="button"
                    data-arim-gallery-open
                    aria-label="<?php esc_attr_e('Ürün görsellerini tam ekranda aç', 'arim'); ?>"
                >
                    <?php esc_html_e('Yakınlaştır', 'arim'); ?>
                </button>
            </div>

            <div class="arim-single-main-image">
                <button
                    class="arim-single-gallery-open-area"
                    type="button"
                    data-arim-gallery-open
                    aria-label="<?php esc_attr_e('Ürün galerisini aç', 'arim'); ?>"
                >
                    <img
                        src="<?php echo esc_url($gallery_items[0]['full'] ?? $main_image_url); ?>"
                        alt="<?php echo esc_attr($gallery_items[0]['alt'] ?? get_the_title()); ?>"
                        data-arim-gallery-main-image
                    >
                </button>

                <div class="arim-single-gallery-overlay">
                    <span><?php esc_html_e('Tam ekran inceleme', 'arim'); ?></span>
                    <strong><?php esc_html_e('Dokun ve detayları büyüt', 'arim'); ?></strong>
                </div>

                <?php if ($gallery_total > 1) : ?>
                    <button class="arim-single-gallery-nav arim-single-gallery-nav-prev" type="button" data-arim-gallery-prev aria-label="<?php esc_attr_e('Önceki görsel', 'arim'); ?>">‹</button>
                    <button class="arim-single-gallery-nav arim-single-gallery-nav-next" type="button" data-arim-gallery-next aria-label="<?php esc_attr_e('Sonraki görsel', 'arim'); ?>">›</button>
                <?php endif; ?>

                <div class="arim-single-gallery-meta">
                    <span><?php echo esc_html($gallery_badge_text); ?></span>
                    <strong><?php esc_html_e('Yüksek çözünürlüklü önizleme', 'arim'); ?></strong>
                </div>
            </div>

            <?php if ($gallery_total > 1) : ?>
                <div class="arim-single-thumbs" role="list" aria-label="<?php esc_attr_e('Ürün galeri küçük önizlemeleri', 'arim'); ?>">
                    <?php foreach ($gallery_items as $gallery_index => $gallery_item) : ?>
                        <button
                            class="arim-single-thumb<?php echo 0 === $gallery_index ? ' is-active' : ''; ?>"
                            type="button"
                            role="listitem"
                            data-arim-gallery-thumb
                            data-index="<?php echo esc_attr($gallery_index); ?>"
                            data-full-url="<?php echo esc_url($gallery_item['full']); ?>"
                            data-alt="<?php echo esc_attr($gallery_item['alt']); ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Görsel %d', 'arim'), $gallery_index + 1)); ?>"
                        >
                            <img src="<?php echo esc_url($gallery_item['thumb']); ?>" alt="<?php echo esc_attr($gallery_item['alt']); ?>">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="arim-single-gallery-lightbox" hidden data-arim-gallery-lightbox>
                <div class="arim-single-gallery-dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Ürün görsel önizlemesi', 'arim'); ?>">
                    <button class="arim-single-gallery-close" type="button" data-arim-gallery-close aria-label="<?php esc_attr_e('Galeriyi kapat', 'arim'); ?>">×</button>

                    <?php if ($gallery_total > 1) : ?>
                        <button class="arim-single-gallery-lightbox-nav arim-single-gallery-lightbox-prev" type="button" data-arim-gallery-prev aria-label="<?php esc_attr_e('Önceki görsel', 'arim'); ?>">‹</button>
                    <?php endif; ?>

                    <figure class="arim-single-gallery-lightbox-figure">
                        <img
                            src="<?php echo esc_url($gallery_items[0]['full'] ?? $main_image_url); ?>"
                            alt="<?php echo esc_attr($gallery_items[0]['alt'] ?? get_the_title()); ?>"
                            data-arim-gallery-lightbox-image
                        >
                        <figcaption>
                            <strong><?php echo esc_html($gallery_badge_text); ?></strong>
                            <span data-arim-gallery-caption><?php echo esc_html($gallery_items[0]['alt'] ?? get_the_title()); ?></span>
                        </figcaption>
                    </figure>

                    <?php if ($gallery_total > 1) : ?>
                        <button class="arim-single-gallery-lightbox-nav arim-single-gallery-lightbox-next" type="button" data-arim-gallery-next aria-label="<?php esc_attr_e('Sonraki görsel', 'arim'); ?>">›</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="arim-single-summary">
            <?php if ($brand) : ?>
                <div class="arim-single-brand"><?php echo esc_html($brand); ?></div>
            <?php endif; ?>

            <h1 class="arim-single-title"><?php the_title(); ?></h1>

            <?php if ($rating > 0) : ?>
                <div class="arim-single-rating">
                    <span class="arim-rating-score"><?php echo esc_html(number_format((float)$rating, 1)); ?></span>
                    <span class="arim-rating-stars">★★★★★</span>
                    <span class="arim-rating-count">(<?php echo esc_html($review_count); ?>)</span>
                </div>
            <?php endif; ?>

            <div class="arim-single-price">
                <?php echo wp_kses_post($price_html); ?>
            </div>

            <section class="arim-single-price-insights" aria-label="<?php esc_attr_e('Fiyat ve satın alma özeti', 'arim'); ?>">
                <article class="arim-single-price-insight arim-single-price-insight-primary">
                    <strong><?php echo esc_html($price_insight_title); ?></strong>
                    <span><?php echo esc_html($price_insight_note); ?></span>
                </article>
                <article class="arim-single-price-insight">
                    <strong>
                        <?php
                        echo esc_html(
                            $campaign_count > 0
                                ? sprintf(_n('%s kampanya', '%s kampanya', $campaign_count, 'arim'), number_format_i18n($campaign_count))
                                : __('Ek kampanya yok', 'arim')
                        );
                        ?>
                    </strong>
                    <span><?php esc_html_e('Sepet adımında aktif avantajlar yeniden hesaplanır.', 'arim'); ?></span>
                </article>
                <article class="arim-single-price-insight">
                    <strong><?php echo esc_html($stock_state_text); ?></strong>
                    <span><?php echo esc_html($store_shipping_text); ?></span>
                </article>
            </section>

            <div class="arim-single-favorite-wrap">
                <button
                    class="arim-favorite-btn arim-single-favorite-btn"
                    type="button"
                    aria-label="<?php esc_attr_e('Favorilere ekle', 'arim'); ?>"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                    data-product-price="<?php echo esc_attr($price_text); ?>"
                    data-product-image="<?php echo esc_url($main_image_url); ?>"
                    data-product-url="<?php echo esc_url(get_permalink($product_id)); ?>"
                    data-product-brand="<?php echo esc_attr($brand ? $brand : __('ARIM', 'arim')); ?>"
                    data-product-store="<?php echo esc_attr($store_name); ?>"
                    data-product-badge="<?php echo esc_attr($product_badge); ?>"
                    data-product-current-price="<?php echo esc_attr($current_price_value); ?>"
                    data-product-regular-price="<?php echo esc_attr($regular_price_value); ?>"
                >♡</button>

                <button
                    class="arim-compare-btn arim-single-compare-btn"
                    type="button"
                    aria-label="<?php esc_attr_e('Karşılaştırmaya ekle', 'arim'); ?>"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                    data-product-price="<?php echo esc_attr($price_text); ?>"
                    data-product-image="<?php echo esc_url($main_image_url); ?>"
                    data-product-url="<?php echo esc_url(get_permalink($product_id)); ?>"
                    data-product-brand="<?php echo esc_attr($brand ? $brand : __('ARIM', 'arim')); ?>"
                    data-product-store="<?php echo esc_attr($store_name); ?>"
                    data-product-badge="<?php echo esc_attr($product_badge); ?>"
                    data-product-current-price="<?php echo esc_attr($current_price_value); ?>"
                    data-product-regular-price="<?php echo esc_attr($regular_price_value); ?>"
                >⇄</button>
            </div>

            <?php if (!empty($short_description)) : ?>
                <div class="arim-single-short-description">
                    <?php echo wp_kses_post(wpautop($short_description)); ?>
                </div>
            <?php endif; ?>

            <div class="arim-single-confidence-grid">
                <section class="arim-single-highlight-card arim-single-delivery-card">
                    <span class="arim-single-highlight-kicker"><?php echo esc_html($delivery_details['badge']); ?></span>
                    <h2><?php echo esc_html($delivery_details['date']); ?></h2>
                    <p><?php echo esc_html($delivery_details['note']); ?></p>

                    <div class="arim-single-highlight-points">
                        <span><?php esc_html_e('Kargo hareketleri canlı takip edilir', 'arim'); ?></span>
                        <span><?php esc_html_e('Kolay iade süreciyle desteklenir', 'arim'); ?></span>
                    </div>
                </section>

                <section class="arim-single-highlight-card arim-single-store-card">
                    <span class="arim-single-highlight-kicker"><?php esc_html_e('Satıcı bilgisi', 'arim'); ?></span>
                    <h2><?php echo esc_html($store_name); ?></h2>
                    <p><?php echo esc_html($store_rating); ?></p>

                    <div class="arim-single-store-metrics">
                        <div class="arim-single-store-metric">
                            <strong><?php esc_html_e('Yorum', 'arim'); ?></strong>
                            <span><?php echo esc_html($store_review_text); ?></span>
                        </div>
                        <div class="arim-single-store-metric">
                            <strong><?php esc_html_e('Hazırlık', 'arim'); ?></strong>
                            <span><?php echo esc_html($store_shipping_text); ?></span>
                        </div>
                    </div>

                    <a class="arim-single-store-link" href="<?php echo esc_url(arim_shop_url()); ?>">
                        <?php esc_html_e('Mağazadaki diğer ürünleri incele', 'arim'); ?>
                    </a>
                </section>
            </div>

            <?php if (!empty($campaigns)) : ?>
                <section class="arim-single-coupon-box">
                    <div class="arim-single-coupon-head">
                        <div>
                            <span class="arim-single-highlight-kicker"><?php esc_html_e('Sepette avantaj', 'arim'); ?></span>
                            <h2><?php esc_html_e('Kampanya ve kupon fırsatları', 'arim'); ?></h2>
                        </div>
                        <span class="arim-single-coupon-note"><?php esc_html_e('Aynı siparişte ek avantajları yakala', 'arim'); ?></span>
                    </div>

                    <div class="arim-single-coupon-grid">
                        <?php foreach ($campaigns as $campaign) : ?>
                            <div class="arim-single-coupon-item">
                                <strong><?php echo esc_html($campaign['value']); ?></strong>
                                <span><?php echo esc_html($campaign['text']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <div class="arim-single-cart-box">
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>

            <section class="arim-single-purchase-guide">
                <div class="arim-single-purchase-guide-head">
                    <span class="arim-single-highlight-kicker"><?php esc_html_e('Satın alma akışı', 'arim'); ?></span>
                    <h2><?php esc_html_e('Siparişten teslimata kısa yol haritası', 'arim'); ?></h2>
                </div>

                <div class="arim-single-purchase-steps">
                    <?php foreach ($purchase_journey as $step_index => $step) : ?>
                        <article class="arim-single-purchase-step">
                            <span class="arim-single-purchase-step-number"><?php echo esc_html($step_index + 1); ?></span>
                            <div>
                                <strong><?php echo esc_html($step['title']); ?></strong>
                                <span><?php echo esc_html($step['text']); ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="arim-single-meta-box">
                <div class="arim-single-meta-item">
                    <strong><?php esc_html_e('Stok Durumu:', 'arim'); ?></strong>
                    <span><?php echo esc_html($stock_state_text); ?></span>
                </div>

                <?php if ($product->get_sku()) : ?>
                    <div class="arim-single-meta-item">
                        <strong><?php esc_html_e('SKU:', 'arim'); ?></strong>
                        <span><?php echo esc_html($product->get_sku()); ?></span>
                    </div>
                <?php endif; ?>

                <div class="arim-single-meta-item">
                    <strong><?php esc_html_e('Kategori:', 'arim'); ?></strong>
                    <span><?php echo wc_get_product_category_list($product_id, ', '); ?></span>
                </div>
            </div>

            <div class="arim-single-benefits">
                <div class="arim-single-benefit"><?php esc_html_e('Güvenli ödeme', 'arim'); ?></div>
                <div class="arim-single-benefit"><?php esc_html_e('Kolay iade', 'arim'); ?></div>
                <div class="arim-single-benefit"><?php esc_html_e('Hızlı teslimat', 'arim'); ?></div>
            </div>
        </div>
    </div>

    <div class="arim-single-tabs">
        <?php
        $tabs = apply_filters('woocommerce_product_tabs', []);
        if (!empty($tabs)) :
        ?>
            <div class="arim-tabs-wrapper">
                <?php foreach ($tabs as $key => $tab) : ?>
                    <div class="arim-tab-block">
                        <h2 class="arim-tab-title">
                            <?php echo esc_html($tab['title']); ?>
                        </h2>
                        <div class="arim-tab-content">
                            <?php
                            if (isset($tab['callback'])) {
                                call_user_func($tab['callback'], $key, $tab);
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <section class="arim-single-recently-viewed" data-arim-recently-viewed-shell>
        <div class="arim-single-section-head">
            <div>
                <span class="arim-single-highlight-kicker"><?php esc_html_e('Alışverişe geri dön', 'arim'); ?></span>
                <h2><?php esc_html_e('Son baktığın ürünler', 'arim'); ?></h2>
            </div>
            <a class="arim-single-store-link" href="<?php echo esc_url(arim_favorites_url()); ?>">
                <?php esc_html_e('Tüm geçmişi favorilerde gör', 'arim'); ?>
            </a>
        </div>

        <div
            class="arim-single-recently-viewed-content"
            data-arim-recently-viewed-page
            data-arim-exclude-product-id="<?php echo esc_attr($product_id); ?>"
            data-arim-hide-empty="true"
        ></div>
    </section>

    <section class="arim-single-recommendations" data-arim-recommendations-shell>
        <div class="arim-single-section-head">
            <div>
                <span class="arim-single-highlight-kicker"><?php esc_html_e('Senin ritmine göre', 'arim'); ?></span>
                <h2><?php esc_html_e('Sana özel ürün önerileri', 'arim'); ?></h2>
            </div>
            <button class="arim-single-store-link arim-single-section-action" type="button" data-arim-refresh-recommendations>
                <?php esc_html_e('Önerileri yenile', 'arim'); ?>
            </button>
        </div>

        <div
            class="arim-single-recommendations-content"
            data-arim-recommendations-page
            data-arim-exclude-product-id="<?php echo esc_attr($product_id); ?>"
            data-arim-hide-empty="true"
        ></div>
    </section>

    <div class="arim-single-related">
        <?php woocommerce_output_related_products(); ?>
    </div>
</div>
