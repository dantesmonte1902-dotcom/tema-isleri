<?php
defined('ABSPATH') || exit;

class ARIM_Homepage_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct(
            'arim-theme-options-homepage',
            __('Homepage Settings', 'arim'),
            __('Homepage', 'arim'),
            '',
            __('Mevcut anasayfa yönetimi bu panel yapısına taşınacak şekilde hazırlanıyor.', 'arim')
        );
    }

    protected function render_content() {
        $this->render_placeholder_message(
            __('Homepage modülü hazırlanıyor', 'arim'),
            __('Hero slider, kampanya alanları, kategori blokları, öne çıkan ürünler, marka slider ve banner yönetimi bu bölüm altında native yapı ile birleştirilecek.', 'arim')
        );
    }
}
