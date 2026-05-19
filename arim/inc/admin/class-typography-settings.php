<?php
defined('ABSPATH') || exit;

class ARIM_Typography_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-typography', __('Typography Settings', 'arim'), __('Typography', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Typography modülü hazırlanıyor', 'arim'), __('Yazı tipi aileleri, boyut skalaları ve heading/text stilleri bu bölümde yer alacak.', 'arim'));
    }
}
