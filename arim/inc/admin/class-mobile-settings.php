<?php
defined('ABSPATH') || exit;

class ARIM_Mobile_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-mobile', __('Mobile Settings', 'arim'), __('Mobile Settings', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Mobile modülü hazırlanıyor', 'arim'), __('Mobil header, alt navigasyon, performans ve dokunmatik etkileşim seçenekleri bu bölümden yönetilecek.', 'arim'));
    }
}
