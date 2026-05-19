<?php
defined('ABSPATH') || exit;

class ARIM_Header_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-header', __('Header Settings', 'arim'), __('Header', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Header modülü hazırlanıyor', 'arim'), __('Header varyasyonları, sticky davranışları, mega menu ve duyuru alanları bu bölümden yönetilecek.', 'arim'));
    }
}
