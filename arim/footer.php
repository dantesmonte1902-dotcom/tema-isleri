<?php
defined('ABSPATH') || exit;
?>
    </main>

    <footer class="arim-footer">
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
                        <p><?php esc_html_e('Trendyol ilhamlı modern WooCommerce temasıyla hazırlanmış premium alışveriş deneyimi.', 'arim'); ?></p>

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
                            <li><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php esc_html_e('Hesabım', 'arim'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/my-account/orders')); ?>"><?php esc_html_e('Siparişlerim', 'arim'); ?></a></li>
                            <li><a href="#"><?php esc_html_e('İade Koşulları', 'arim'); ?></a></li>
                            <li><a href="#"><?php esc_html_e('Sık Sorulan Sorular', 'arim'); ?></a></li>
                        </ul>
                    </div>

                    <div class="arim-footer-col">
                        <h3><?php esc_html_e('Mağaza', 'arim'); ?></h3>
                        <ul>
                            <li><a href="<?php echo esc_url(home_url('/shop')); ?>"><?php esc_html_e('Tüm Ürünler', 'arim'); ?></a></li>
                            <li><a href="<?php echo esc_url(wc_get_cart_url()); ?>"><?php esc_html_e('Sepetim', 'arim'); ?></a></li>
                            <li><a href="<?php echo esc_url(wc_get_checkout_url()); ?>"><?php esc_html_e('Ödeme', 'arim'); ?></a></li>
                            <li><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php esc_html_e('Üyelik', 'arim'); ?></a></li>
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