<?php
defined('ABSPATH') || exit;

class ARIM_Checkout_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-checkout', __('Checkout Settings', 'arim'), __('Checkout', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Checkout modülü hazırlanıyor', 'arim'), __('Checkout akışı, alan görünürlüğü ve güven öğeleri bu bölümde yapılandırılacak.', 'arim'));
    }
}
