<?php
defined('ABSPATH') || exit;

class ARIM_Footer_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-footer', __('Footer Settings', 'arim'), __('Footer', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Footer modülü hazırlanıyor', 'arim'), __('Footer kolon yapısı, widget alanları, trust alanları ve alt bilgi içerikleri bu bölümden yönetilecek.', 'arim'));
    }
}
