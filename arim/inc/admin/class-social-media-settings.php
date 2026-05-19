<?php
defined('ABSPATH') || exit;

class ARIM_Social_Media_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct('arim-theme-options-social-media', __('Social Media Settings', 'arim'), __('Social Media', 'arim'));
    }

    protected function render_content() {
        $this->render_placeholder_message(__('Social Media modülü hazırlanıyor', 'arim'), __('Sosyal profil linkleri, paylaşım alanları ve marka hesap yönetimi bu bölümde toplanacak.', 'arim'));
    }
}
