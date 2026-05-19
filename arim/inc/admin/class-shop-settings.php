<?php
defined('ABSPATH') || exit;

class ARIM_Shop_Settings extends ARIM_Base_Settings_Page {
    public function __construct() {
        parent::__construct(
            'arim-theme-options-shop-archive',
            __('Shop Archive Settings', 'arim'),
            __('Shop Archive', 'arim'),
            'arim_shop_archive_settings',
            __('WooCommerce shop archive görünümü, kolon düzeni, sidebar, kart stili ve pagination davranışını bu panelden yönetin.', 'arim')
        );
    }

    public function boot() {
        add_action('admin_init', [$this, 'register_settings']);
    }

    public static function get_defaults() {
        return [
            'columns'         => 4,
            'show_sidebar'    => '1',
            'show_filters'    => '1',
            'default_view'    => 'grid',
            'card_style'      => 'default',
            'infinite_scroll' => '0',
            'pagination_type' => 'numbers',
            'hover_effect'    => 'lift',
        ];
    }

    public static function get_settings() {
        $settings = get_option('arim_shop_archive_settings', []);

        return wp_parse_args(is_array($settings) ? $settings : [], self::get_defaults());
    }

    public static function get_setting($key, $default = null) {
        $settings = self::get_settings();

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        return null === $default && array_key_exists($key, self::get_defaults()) ? self::get_defaults()[$key] : $default;
    }

    public function register_settings() {
        register_setting(
            $this->get_settings_group(),
            $this->get_option_name(),
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize_settings'],
                'default'           => self::get_defaults(),
            ]
        );

        add_settings_section(
            'arim_shop_archive_layout_section',
            __('Layout & Visibility', 'arim'),
            [$this, 'render_layout_section_intro'],
            $this->get_slug()
        );

        add_settings_field(
            'columns',
            __('Ürün sütun sayısı', 'arim'),
            [$this, 'render_number_field'],
            $this->get_slug(),
            'arim_shop_archive_layout_section',
            [
                'key'         => 'columns',
                'default'     => 4,
                'min'         => 2,
                'max'         => 6,
                'description' => __('Shop arşivinde ürünlerin aynı satırda kaç sütunla görüneceğini belirler.', 'arim'),
            ]
        );

        add_settings_field(
            'show_sidebar',
            __('Sidebar aç / kapat', 'arim'),
            [$this, 'render_checkbox_field'],
            $this->get_slug(),
            'arim_shop_archive_layout_section',
            [
                'key'         => 'show_sidebar',
                'label'       => __('Sidebar alanını göster', 'arim'),
                'description' => __('Kapatıldığında arşiv içeriği tam genişliğe yayılır.', 'arim'),
                'default'     => '1',
            ]
        );

        add_settings_field(
            'show_filters',
            __('Filtre alanı', 'arim'),
            [$this, 'render_checkbox_field'],
            $this->get_slug(),
            'arim_shop_archive_layout_section',
            [
                'key'         => 'show_filters',
                'label'       => __('Filtre bileşenlerini göster', 'arim'),
                'description' => __('Hızlı filtreler, aktif filtre chipleri ve filtre formu birlikte kontrol edilir.', 'arim'),
                'default'     => '1',
            ]
        );

        add_settings_section(
            'arim_shop_archive_cards_section',
            __('Product Cards & Interaction', 'arim'),
            [$this, 'render_cards_section_intro'],
            $this->get_slug()
        );

        add_settings_field(
            'default_view',
            __('Grid / List görünüm', 'arim'),
            [$this, 'render_select_field'],
            $this->get_slug(),
            'arim_shop_archive_cards_section',
            [
                'key'         => 'default_view',
                'default'     => 'grid',
                'description' => __('Shop arşivinin varsayılan ürün listeleme görünümünü belirler.', 'arim'),
                'options'     => [
                    'grid' => __('Grid görünüm', 'arim'),
                    'list' => __('List görünüm', 'arim'),
                ],
            ]
        );

        add_settings_field(
            'card_style',
            __('Ürün kart stili', 'arim'),
            [$this, 'render_select_field'],
            $this->get_slug(),
            'arim_shop_archive_cards_section',
            [
                'key'         => 'card_style',
                'default'     => 'default',
                'description' => __('Kart yoğunluğu ve yüzey karakterini ayarlar.', 'arim'),
                'options'     => [
                    'default'  => __('Default premium card', 'arim'),
                    'compact'  => __('Compact dense card', 'arim'),
                    'elevated' => __('Elevated card', 'arim'),
                ],
            ]
        );

        add_settings_field(
            'hover_effect',
            __('Ürün hover efekti', 'arim'),
            [$this, 'render_select_field'],
            $this->get_slug(),
            'arim_shop_archive_cards_section',
            [
                'key'         => 'hover_effect',
                'default'     => 'lift',
                'description' => __('Kart hover davranışını seçin.', 'arim'),
                'options'     => [
                    'lift' => __('Lift', 'arim'),
                    'zoom' => __('Zoom', 'arim'),
                    'none' => __('No hover animation', 'arim'),
                ],
            ]
        );

        add_settings_section(
            'arim_shop_archive_pagination_section',
            __('Pagination & Scrolling', 'arim'),
            [$this, 'render_pagination_section_intro'],
            $this->get_slug()
        );

        add_settings_field(
            'pagination_type',
            __('Pagination tipi', 'arim'),
            [$this, 'render_select_field'],
            $this->get_slug(),
            'arim_shop_archive_pagination_section',
            [
                'key'         => 'pagination_type',
                'default'     => 'numbers',
                'description' => __('Klasik pagination veya AJAX load more davranışını seçin.', 'arim'),
                'options'     => [
                    'numbers'   => __('Numbered pagination', 'arim'),
                    'load_more' => __('Load more button', 'arim'),
                ],
            ]
        );

        add_settings_field(
            'infinite_scroll',
            __('Sonsuz scroll', 'arim'),
            [$this, 'render_checkbox_field'],
            $this->get_slug(),
            'arim_shop_archive_pagination_section',
            [
                'key'         => 'infinite_scroll',
                'label'       => __('Scroll ile otomatik yükleme aktif olsun', 'arim'),
                'description' => __('Açıldığında sonraki sayfalar otomatik olarak yüklenir ve pagination görünümü AJAX tabanlı hale gelir.', 'arim'),
                'default'     => '0',
            ]
        );
    }

    public function sanitize_settings($input) {
        $input          = is_array($input) ? $input : [];
        $defaults       = self::get_defaults();
        $allowed_views  = ['grid', 'list'];
        $allowed_cards  = ['default', 'compact', 'elevated'];
        $allowed_hover  = ['lift', 'zoom', 'none'];
        $allowed_pagers = ['numbers', 'load_more'];

        $sanitized = [
            'columns'         => min(6, max(2, (int) (isset($input['columns']) ? $input['columns'] : $defaults['columns']))),
            'show_sidebar'    => !empty($input['show_sidebar']) ? '1' : '0',
            'show_filters'    => !empty($input['show_filters']) ? '1' : '0',
            'default_view'    => sanitize_text_field(isset($input['default_view']) ? (string) $input['default_view'] : $defaults['default_view']),
            'card_style'      => sanitize_text_field(isset($input['card_style']) ? (string) $input['card_style'] : $defaults['card_style']),
            'infinite_scroll' => !empty($input['infinite_scroll']) ? '1' : '0',
            'pagination_type' => sanitize_text_field(isset($input['pagination_type']) ? (string) $input['pagination_type'] : $defaults['pagination_type']),
            'hover_effect'    => sanitize_text_field(isset($input['hover_effect']) ? (string) $input['hover_effect'] : $defaults['hover_effect']),
        ];

        if (!in_array($sanitized['default_view'], $allowed_views, true)) {
            $sanitized['default_view'] = $defaults['default_view'];
        }

        if (!in_array($sanitized['card_style'], $allowed_cards, true)) {
            $sanitized['card_style'] = $defaults['card_style'];
        }

        if (!in_array($sanitized['hover_effect'], $allowed_hover, true)) {
            $sanitized['hover_effect'] = $defaults['hover_effect'];
        }

        if (!in_array($sanitized['pagination_type'], $allowed_pagers, true)) {
            $sanitized['pagination_type'] = $defaults['pagination_type'];
        }

        add_settings_error(
            $this->get_option_name(),
            'arim_shop_archive_settings_saved',
            esc_html__('Shop Archive ayarları kaydedildi.', 'arim'),
            'updated'
        );

        return $sanitized;
    }

    protected function has_settings_form() {
        return true;
    }

    public function render_layout_section_intro() {
        echo '<p class="arim-theme-options-intro">' . esc_html__('Sidebar, filtre alanı ve ürün kolon yapısı gibi temel arşiv düzenini buradan yönetin.', 'arim') . '</p>';
    }

    public function render_cards_section_intro() {
        echo '<p class="arim-theme-options-intro">' . esc_html__('Grid/List görünümü, kart yoğunluğu ve hover hareketleri ürün kartlarının davranışını belirler.', 'arim') . '</p>';
    }

    public function render_pagination_section_intro() {
        echo '<p class="arim-theme-options-intro">' . esc_html__('Pagination tipi ve infinite scroll ayarları shop arşivinin gezinme deneyimini belirler.', 'arim') . '</p>';
    }
}
