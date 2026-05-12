<?php
defined('ABSPATH') || exit;

$shop_url     = function_exists('arim_shop_url') ? arim_shop_url() : home_url('/shop');
$account_url  = function_exists('arim_account_url') ? arim_account_url() : wp_login_url();
$cart_url     = function_exists('arim_cart_url') ? arim_cart_url() : home_url('/cart');
$checkout_url = function_exists('arim_checkout_url') ? arim_checkout_url() : home_url('/checkout');
?>
    </main>

    <footer class="arim-footer">
        <div class="arim-footer-app-strip">
            <div class="arim-container arim-footer-app-inner">
                <div class="arim-footer-app-copy">
                    <span><?php esc_html_e('Mobil alışveriş deneyimi', 'arim'); ?></span>
                    <strong><?php esc_html_e('ARIM uygulama hissini web vitrininize taşıyan yoğun marketplace tasarımı', 'arim'); ?></strong>
                </div>

                <div class="arim-footer-app-badges">
                    <a href="#"><?php esc_html_e('App Store', 'arim'); ?></a>
                    <a href="#"><?php esc_html_e('Google Play', 'arim'); ?></a>
                    <a href="#"><?php esc_html_e('Hızlı Market', 'arim'); ?></a>
                </div>
            </div>
        </div>

        <div class="arim-footer-top">
            <div class="arim-container">
                <div class="arim-footer-features">
                    <div class="arim-footer-feature">
                        <h4><?php esc_html_e('Güvenli Ödeme', 'arim'); ?></h4>
                        <p><?php esc_html_e('Tüm siparişlerinde güvenli ödeme altyapısı.', 'arim'); ?></p>
                    </div>
                    <div class="arim-footer-feature">
                        <h4><?php esc_html_e('Hızlı Teslimat', 'arim'); ?></h4>
                        <p><?php esc_html_e('Siparişlerini kısa sürede kapına ulaştır.', 'arim'); ?></p>
                    </div>
                    <div class="arim-footer-feature">
                        <h4><?php esc_html_e('Kolay İade', 'arim'); ?></h4>
                        <p><?php esc_html_e('Müşteri dostu iade ve destek süreci.', 'arim'); ?></p>
                    </div>
                    <div class="arim-footer-feature">
                        <h4><?php esc_html_e('Canlı Destek', 'arim'); ?></h4>
                        <p><?php esc_html_e('İhtiyacın olduğunda hızlı destek al.', 'arim'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="arim-footer-middle">
            <div class="arim-container">
                <div class="arim-footer-grid">
                    <div class="arim-footer-col arim-footer-brand">
                        <h3>ARIM</h3>
                        <p><?php esc_html_e('Trendyol ilhamlı modern WooCommerce temasıyla hazırlanmış akıcı alışveriş deneyimi.', 'arim'); ?></p>

                        <div class="arim-footer-socials">
                            <a href="#" aria-label="Instagram">Instagram</a>
                            <a href="#" aria-label="Facebook">Facebook</a>
                            <a href="#" aria-label="X">X</a>
                        </div>
                    </div>

                    <div class="arim-footer-col">
                        <h3><?php esc_html_e('Kurumsal', 'arim'); ?></h3>
                        <ul>
                            <li><a href="#"><?php esc_html_e('Hakkımızda', 'arim'); ?></a></li>
                            <li><a href="#"><?php esc_html_e('İletişim', 'arim'); ?></a></li>
                            <li><a href="#"><?php esc_html_e('Kariyer', 'arim'); ?></a></li>
                            <li><a href="#"><?php esc_html_e('Gizlilik Politikası', 'arim'); ?></a></li>
                        </ul>
                    </div>

                    <div class="arim-footer-col">
                        <h3><?php esc_html_e('Müşteri Hizmetleri', 'arim'); ?></h3>
                        <ul>
                            <li><a href="<?php echo esc_url($account_url); ?>"><?php esc_html_e('Hesabım', 'arim'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/my-account/orders')); ?>"><?php esc_html_e('Siparişlerim', 'arim'); ?></a></li>
                            <li><a href="#"><?php esc_html_e('İade Koşulları', 'arim'); ?></a></li>
                            <li><a href="#"><?php esc_html_e('Sık Sorulan Sorular', 'arim'); ?></a></li>
                        </ul>
                    </div>

                    <div class="arim-footer-col">
                        <h3><?php esc_html_e('Mağaza', 'arim'); ?></h3>
                        <ul>
                            <li><a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Tüm Ürünler', 'arim'); ?></a></li>
                            <li><a href="<?php echo esc_url($cart_url); ?>"><?php esc_html_e('Sepetim', 'arim'); ?></a></li>
                            <li><a href="<?php echo esc_url($checkout_url); ?>"><?php esc_html_e('Ödeme', 'arim'); ?></a></li>
                            <li><a href="<?php echo esc_url($account_url); ?>"><?php esc_html_e('Üyelik', 'arim'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="arim-footer-bottom">
            <div class="arim-container arim-footer-bottom-inner">
                <div>© <?php echo date('Y'); ?> ARIM. <?php esc_html_e('Tüm hakları saklıdır.', 'arim'); ?></div>
                <div class="arim-footer-bottom-links">
                    <a href="#"><?php esc_html_e('Gizlilik', 'arim'); ?></a>
                    <a href="#"><?php esc_html_e('Şartlar', 'arim'); ?></a>
                    <a href="#"><?php esc_html_e('Çerezler', 'arim'); ?></a>
                </div>
            </div>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
