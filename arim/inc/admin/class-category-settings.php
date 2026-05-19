<?php
defined('ABSPATH') || exit;

class ARIM_Category_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-category-pages', __('Category Page Settings', 'arim'), __('Category Pages', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Category Pages modülü hazırlanıyor', 'arim'), __('Kategori bannerları, açıklama konumu, alt kategori düzeni, filtre sistemi ve mobil görünüm seçenekleri bu bölümde yer alacak.', 'arim'));
    }
}
