<?php
defined('ABSPATH') || exit;

class ARIM_Colors_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-colors', __('Color Settings', 'arim'), __('Colors', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Colors modülü hazırlanıyor', 'arim'), __('Marka renkleri, durum renkleri ve tema yüzey paleti bu bölümde yönetilecek.', 'arim'));
    }
}
