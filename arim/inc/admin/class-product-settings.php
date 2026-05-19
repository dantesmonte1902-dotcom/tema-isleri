<?php
defined('ABSPATH') || exit;

class ARIM_Product_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-product-page', __('Product Page Settings', 'arim'), __('Product Page', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Product Page modülü hazırlanıyor', 'arim'), __('Gallery style, sticky add to cart, tabs, related products, upsells, breadcrumb ve trust badges ayarları bu bölümde toplanacak.', 'arim'));
    }
}
