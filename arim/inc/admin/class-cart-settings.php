<?php
defined('ABSPATH') || exit;

class ARIM_Cart_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-cart', __('Cart Settings', 'arim'), __('Cart', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Cart modülü hazırlanıyor', 'arim'), __('Sepet adımları, upsell alanları ve bilgilendirme blokları bu sayfada yönetilecek.', 'arim'));
    }
}
