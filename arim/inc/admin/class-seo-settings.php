<?php
defined('ABSPATH') || exit;

class ARIM_SEO_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-seo', __('SEO Settings', 'arim'), __('SEO', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('SEO modülü hazırlanıyor', 'arim'), __('Meta yapılandırmaları, schema alanları ve içerik optimizasyon seçenekleri bu bölümden yönetilecek.', 'arim'));
    }
}
