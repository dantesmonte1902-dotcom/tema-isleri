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
        $full_url = wp_get_attachment_image_url($gallery_id, 'large');

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
$brand = arim_product_brand_name($product_id);
$rating = (float) $product->get_average_rating();
$review_count = (int) $product->get_review_count();
$short_description = $product->get_short_description();
$full_description = $product->get_description();
$price_html = $product->get_price_html();
$price_text = arim_product_price_text($product);
$current_price_value = (float) $product->get_price();
$regular_price_value = (float) $product->get_regular_price();
$store_name = arim_product_store_name($product_id);
$delivery_details = arim_single_product_delivery_details($product);
$campaigns = arim_single_product_campaigns(3);
$campaign_count = count($campaigns);
$store_review_count = max(0, $review_count);
$store_review_text = $store_review_count > 0
    ? sprintf(_n('%s değerlendirme', '%s değerlendirme', $store_review_count, 'arim'), number_format_i18n($store_review_count))
    : __('İlk yorumu sen bırak', 'arim');
$store_rating = $rating > 0
    ? sprintf(__('%s puan', 'arim'), number_format($rating, 1))
    : __('Henüz puan yok', 'arim');
$store_shipping_text = $product->is_in_stock() ? __('Bugün kargoda', 'arim') : __('Siparişe göre hazırlanır', 'arim');
$stock_state_text = $product->is_in_stock() ? __('Stokta var', 'arim') : __('Stokta yok', 'arim');
$discount_amount_value = max(0, $regular_price_value - $current_price_value);
$discount_percent = ($regular_price_value > 0 && $discount_amount_value > 0)
    ? (int) round(($discount_amount_value / $regular_price_value) * 100)
    : 0;
$discount_amount_text = $discount_amount_value > 0 ? wp_strip_all_tags(wc_price($discount_amount_value)) : '';
$product_badge = '';

if ($product->is_on_sale()) {
    $product_badge = __('Flaş Fiyat', 'arim');
} elseif ($product->is_featured()) {
    $product_badge = __('Öne Çıkan', 'arim');
}

$promotion_badges = [];
if ($discount_percent > 0) {
    $promotion_badges[] = '%' . $discount_percent . ' ' . __('indirim', 'arim');
}
if ($campaign_count > 0) {
    $promotion_badges[] = sprintf(_n('%s kampanya', '%s kampanya', $campaign_count, 'arim'), number_format_i18n($campaign_count));
}
if ($product->is_in_stock()) {
    $promotion_badges[] = __('Hızlı teslimat', 'arim');
}
if ($product_badge) {
    $promotion_badges[] = $product_badge;
}

$summary_highlights = [
    [
        'label' => __('Tahmini teslimat', 'arim'),
        'value' => $delivery_details['date'],
        'note'  => $delivery_details['badge'],
    ],
    [
        'label' => __('Satıcı', 'arim'),
        'value' => $store_name,
        'note'  => $store_review_text,
    ],
    [
        'label' => __('İade', 'arim'),
        'value' => __('Kolay iade', 'arim'),
        'note'  => __('Güvenli sipariş desteği', 'arim'),
    ],
];

$purchase_journey = [
    [
        'title' => __('Sipariş onayı', 'arim'),
        'text'  => __('Ödeme sonrası siparişin hemen işleme alınır.', 'arim'),
    ],
    [
        'title' => __('Hazırlık süreci', 'arim'),
        'text'  => $product->is_in_stock()
            ? __('Ürün stoktan hazırlanır ve hızlıca kargoya verilir.', 'arim')
            : __('Ürün hazırlanır hazırlanmaz öncelikli gönderime alınır.', 'arim'),
    ],
    [
        'title' => __('Teslimat', 'arim'),
        'text'  => sprintf(__('Tahmini teslim günü: %s', 'arim'), $delivery_details['date']),
    ],
];

$product_attributes = [];
foreach ($product->get_attributes() as $attribute) {
    if (!method_exists($attribute, 'get_visible') || !$attribute->get_visible()) {
        continue;
    }

    if ($attribute->is_taxonomy()) {
        $values = wc_get_product_terms($product_id, $attribute->get_name(), ['fields' => 'names']);
    } else {
        $values = $attribute->get_options();
    }

    $values = array_filter(array_map('trim', array_map('wp_strip_all_tags', (array) $values)));
    if (empty($values)) {
        continue;
    }

    $product_attributes[] = [
        'label' => wc_attribute_label($attribute->get_name()),
        'value' => implode(', ', $values),
    ];
}

$product_attributes_preview = array_slice($product_attributes, 0, 8);
$tabs = apply_filters('woocommerce_product_tabs', []);
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
        <section class="arim-single-gallery" data-arim-product-gallery>
            <div class="arim-single-gallery-card">
                <div class="arim-single-gallery-topbar">
                    <span class="arim-single-gallery-count">
                        <strong data-arim-gallery-current-index>1</strong>
                        <span>/ <?php echo esc_html(number_format_i18n($gallery_total)); ?></span>
                    </span>

                    <button
                        class="arim-single-gallery-zoom"
                        type="button"
                        data-arim-gallery-open
                        aria-label="<?php esc_attr_e('Ürün görsellerini tam ekranda aç', 'arim'); ?>"
                    >
                        <?php esc_html_e('Büyüt', 'arim'); ?>
                    </button>
                </div>

                <div class="arim-single-gallery-stage">
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

                    <div class="arim-single-main-image">
                        <img
                            src="<?php echo esc_url($gallery_items[0]['full'] ?? $main_image_url); ?>"
                            alt="<?php echo esc_attr($gallery_items[0]['alt'] ?? get_the_title()); ?>"
                            data-arim-gallery-main-image
                            data-arim-gallery-open
                            role="button"
                            tabindex="0"
                            aria-label="<?php esc_attr_e('Ürün görselini büyük aç', 'arim'); ?>"
                        >

                        <?php if (!empty($promotion_badges)) : ?>
                            <div class="arim-single-gallery-badges">
                                <?php foreach (array_slice($promotion_badges, 0, 2) as $badge) : ?>
                                    <span><?php echo esc_html($badge); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($gallery_total > 1) : ?>
                            <button class="arim-single-gallery-nav arim-single-gallery-nav-prev" type="button" data-arim-gallery-prev aria-label="<?php esc_attr_e('Önceki görsel', 'arim'); ?>">‹</button>
                            <button class="arim-single-gallery-nav arim-single-gallery-nav-next" type="button" data-arim-gallery-next aria-label="<?php esc_attr_e('Sonraki görsel', 'arim'); ?>">›</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="arim-single-gallery-lightbox" hidden aria-hidden="true" data-arim-gallery-lightbox>
                <div class="arim-single-gallery-dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Ürün görsel önizlemesi', 'arim'); ?>" tabindex="-1" data-arim-gallery-dialog>
                    <div class="arim-single-gallery-lightbox-topbar">
                        <div class="arim-single-gallery-lightbox-copy">
                            <span class="arim-single-gallery-lightbox-status" aria-live="polite">
                                <strong data-arim-gallery-lightbox-current-index>1</strong>
                                <span>/ <?php echo esc_html(number_format_i18n($gallery_total)); ?></span>
                            </span>
                            <p><?php esc_html_e('Kapatmak için çarpı butonunu veya koyu alanı kullan.', 'arim'); ?></p>
                        </div>
                        <button class="arim-single-gallery-close" type="button" data-arim-gallery-close aria-label="<?php esc_attr_e('Galeriyi kapat', 'arim'); ?>">×</button>
                    </div>

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
                            <strong><?php echo esc_html($brand ? $brand : __('ARIM', 'arim')); ?></strong>
                            <span data-arim-gallery-caption><?php echo esc_html($gallery_items[0]['alt'] ?? get_the_title()); ?></span>
                        </figcaption>
                    </figure>

                    <?php if ($gallery_total > 1) : ?>
                        <button class="arim-single-gallery-lightbox-nav arim-single-gallery-lightbox-next" type="button" data-arim-gallery-next aria-label="<?php esc_attr_e('Sonraki görsel', 'arim'); ?>">›</button>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="arim-single-summary">
            <div class="arim-single-summary-panel">
                <div class="arim-single-summary-header">
                    <div>
                        <?php if ($brand) : ?>
                            <div class="arim-single-brand"><?php echo esc_html($brand); ?></div>
                        <?php endif; ?>
                        <h1 class="arim-single-title"><?php the_title(); ?></h1>
                    </div>

                    <div class="arim-single-action-pills">
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
                </div>

                <?php if ($rating > 0 || $review_count > 0) : ?>
                    <div class="arim-single-rating">
                        <span class="arim-rating-stars" aria-hidden="true">★★★★★</span>
                        <?php if ($rating > 0) : ?>
                            <strong class="arim-rating-score"><?php echo esc_html(number_format($rating, 1)); ?></strong>
                        <?php endif; ?>
                        <span class="arim-rating-count"><?php echo esc_html($store_review_text); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($promotion_badges)) : ?>
                    <div class="arim-single-promo-row">
                        <?php foreach ($promotion_badges as $badge) : ?>
                            <span class="arim-single-promo-badge"><?php echo esc_html($badge); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="arim-single-price-box">
                    <div class="arim-single-price"><?php echo wp_kses_post($price_html); ?></div>

                    <?php if ($discount_percent > 0) : ?>
                        <div class="arim-single-discount-note">
                            <strong>%<?php echo esc_html($discount_percent); ?></strong>
                            <span><?php echo esc_html(sprintf(__('Yaklaşık %s avantaj', 'arim'), $discount_amount_text)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="arim-single-summary-highlights" aria-label="<?php esc_attr_e('Ürün kısa özet bilgileri', 'arim'); ?>">
                    <?php foreach ($summary_highlights as $highlight) : ?>
                        <article class="arim-single-summary-highlight">
                            <span><?php echo esc_html($highlight['label']); ?></span>
                            <strong><?php echo esc_html($highlight['value']); ?></strong>
                            <small><?php echo esc_html($highlight['note']); ?></small>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($campaigns)) : ?>
                    <section class="arim-single-coupon-box">
                        <div class="arim-single-coupon-head">
                            <div>
                                <span class="arim-single-section-kicker"><?php esc_html_e('Kampanyalar', 'arim'); ?></span>
                                <h2><?php esc_html_e('Sepette uygulanacak fırsatlar', 'arim'); ?></h2>
                            </div>
                            <span class="arim-single-coupon-note"><?php esc_html_e('Aynı siparişte ek avantaj yakala', 'arim'); ?></span>
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

                <section class="arim-single-seller-card">
                    <div class="arim-single-seller-card-top">
                        <div>
                            <span class="arim-single-section-kicker"><?php esc_html_e('Satıcı', 'arim'); ?></span>
                            <h2><?php echo esc_html($store_name); ?></h2>
                        </div>
                        <span class="arim-single-seller-score"><?php echo esc_html($store_rating); ?></span>
                    </div>

                    <div class="arim-single-seller-meta">
                        <div>
                            <strong><?php esc_html_e('Değerlendirme', 'arim'); ?></strong>
                            <span><?php echo esc_html($store_review_text); ?></span>
                        </div>
                        <div>
                            <strong><?php esc_html_e('Gönderim', 'arim'); ?></strong>
                            <span><?php echo esc_html($store_shipping_text); ?></span>
                        </div>
                    </div>

                    <a class="arim-single-store-link" href="<?php echo esc_url(arim_shop_url()); ?>">
                        <?php esc_html_e('Satıcının diğer ürünleri', 'arim'); ?>
                    </a>
                </section>

                <?php if (!empty($short_description)) : ?>
                    <div class="arim-single-short-description">
                        <?php echo wp_kses_post(wpautop($short_description)); ?>
                    </div>
                <?php endif; ?>

                <div class="arim-single-cart-box">
                    <?php woocommerce_template_single_add_to_cart(); ?>
                </div>

                <section class="arim-single-purchase-guide">
                    <div class="arim-single-purchase-guide-head">
                        <span class="arim-single-section-kicker"><?php esc_html_e('Sipariş süreci', 'arim'); ?></span>
                        <h2><?php esc_html_e('Teslimata kadar tüm adımlar', 'arim'); ?></h2>
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
                        <strong><?php esc_html_e('Stok', 'arim'); ?></strong>
                        <span><?php echo esc_html($stock_state_text); ?></span>
                    </div>

                    <?php if ($product->get_sku()) : ?>
                        <div class="arim-single-meta-item">
                            <strong><?php esc_html_e('SKU', 'arim'); ?></strong>
                            <span><?php echo esc_html($product->get_sku()); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="arim-single-meta-item">
                        <strong><?php esc_html_e('Kategori', 'arim'); ?></strong>
                        <span><?php echo wc_get_product_category_list($product_id, ', '); ?></span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section class="arim-single-product-info">
        <div class="arim-single-product-info-header">
            <div>
                <span class="arim-single-section-kicker"><?php esc_html_e('Ürün detayları', 'arim'); ?></span>
                <h2><?php esc_html_e('Ürün Bilgileri', 'arim'); ?></h2>
            </div>
        </div>

        <div class="arim-single-product-info-grid">
            <div class="arim-single-product-info-media">
                <img src="<?php echo esc_url($gallery_items[0]['full'] ?? $main_image_url); ?>" alt="<?php echo esc_attr($gallery_items[0]['alt'] ?? get_the_title()); ?>">
            </div>

            <div class="arim-single-product-info-content">
                <?php if (!empty($product_attributes_preview)) : ?>
                    <div class="arim-single-attributes-grid">
                        <?php foreach ($product_attributes_preview as $attribute) : ?>
                            <article class="arim-single-attribute-item">
                                <span><?php echo esc_html($attribute['label']); ?></span>
                                <strong><?php echo esc_html($attribute['value']); ?></strong>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($full_description)) : ?>
                    <div class="arim-single-description-box">
                        <h3><?php esc_html_e('Ürün Açıklaması', 'arim'); ?></h3>
                        <div class="arim-single-description-content"><?php echo wp_kses_post(wpautop($full_description)); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($tabs)) : ?>
                    <div class="arim-single-tabs">
                        <?php foreach ($tabs as $key => $tab) : ?>
                            <section class="arim-tab-block">
                                <h3 class="arim-tab-title"><?php echo esc_html($tab['title']); ?></h3>
                                <div class="arim-tab-content">
                                    <?php
                                    if (isset($tab['callback'])) {
                                        call_user_func($tab['callback'], $key, $tab);
                                    }
                                    ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="arim-single-recently-viewed" data-arim-recently-viewed-shell>
        <div class="arim-single-section-head">
            <div>
                <span class="arim-single-section-kicker"><?php esc_html_e('Alışverişe devam et', 'arim'); ?></span>
                <h2><?php esc_html_e('Son baktığın ürünler', 'arim'); ?></h2>
            </div>
            <a class="arim-single-store-link" href="<?php echo esc_url(arim_favorites_url()); ?>">
                <?php esc_html_e('Favorilerde görüntüle', 'arim'); ?>
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
                <span class="arim-single-section-kicker"><?php esc_html_e('Sana özel', 'arim'); ?></span>
                <h2><?php esc_html_e('Önerilen ürünler', 'arim'); ?></h2>
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
