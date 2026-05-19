<?php
defined('ABSPATH') || exit;

class ARIM_Blog_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-blog', __('Blog Settings', 'arim'), __('Blog', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Blog modülü hazırlanıyor', 'arim'), __('Blog listeleme, detay sayfası, kart yapısı ve sidebar öğeleri bu bölümde toplanacak.', 'arim'));
    }
}
