<?php
defined('ABSPATH') || exit;

class ARIM_Performance_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-performance', __('Performance Settings', 'arim'), __('Performance', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Performance modülü hazırlanıyor', 'arim'), __('Lazy load, asset davranışı ve performans optimizasyon seçenekleri bu panelde toplanacak.', 'arim'));
    }
}
