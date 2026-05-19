<?php
defined('ABSPATH') || exit;

class ARIM_Custom_Code_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-custom-code', __('Custom Code Settings', 'arim'), __('Custom Code', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Custom Code modülü hazırlanıyor', 'arim'), __('Header/footer script alanları ve tema bazlı custom CSS/JS girişleri bu bölümde yer alacak.', 'arim'));
    }
}
