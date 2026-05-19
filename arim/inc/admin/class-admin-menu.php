<?php
defined('ABSPATH') || exit;

class ARIM_Admin_Menu {
    const ROOT_SLUG = 'arim-theme-options';

    private $sections = [];

    public function __construct(array $sections) {
        $this->sections = $sections;
    }

    public function boot() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function register_menu() {
        add_menu_page(
            __('ARIM Theme Options', 'arim'),
            __('ARIM Theme Options', 'arim'),
            'manage_options',
            self::ROOT_SLUG,
            [$this, 'render_overview'],
            'dashicons-admin-generic',
            61
        );

        foreach ($this->sections as $section) {
            add_submenu_page(
                self::ROOT_SLUG,
                $section->get_page_title(),
                $section->get_menu_title(),
                'manage_options',
                $section->get_slug(),
                [$section, 'render']
            );
        }
    }

    public function enqueue_assets($hook) {
        if (0 !== strpos((string) $hook, 'toplevel_page_' . self::ROOT_SLUG) && 0 !== strpos((string) $hook, self::ROOT_SLUG . '_page_')) {
            return;
        }

        wp_add_inline_style(
            'wp-admin',
            '.arim-theme-options-wrap{max-width:1180px}.arim-theme-options-header{margin:20px 0 24px;padding:24px 28px;border-radius:18px;background:linear-gradient(135deg,#ff922e 0%,#ffb347 100%);color:#fff;box-shadow:0 14px 28px rgba(242,122,26,.18)}.arim-theme-options-header h1{margin:0 0 8px;color:#fff;font-size:28px}.arim-theme-options-header p{margin:0;max-width:840px;color:rgba(255,255,255,.92);line-height:1.7}.arim-theme-options-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px}.arim-theme-options-card,.arim-theme-options-panel,.arim-theme-options-form .form-table,.arim-theme-options-placeholder{background:#fff;border:1px solid #ececec;border-radius:16px;box-shadow:0 8px 18px rgba(0,0,0,.04)}.arim-theme-options-card{display:flex;flex-direction:column;gap:10px;padding:20px;text-decoration:none}.arim-theme-options-card h2{margin:0;color:#1d2327;font-size:18px}.arim-theme-options-card p{margin:0;color:#50575e}.arim-theme-options-card:hover{border-color:#ffb347;box-shadow:0 12px 24px rgba(242,122,26,.12)}.arim-theme-options-panel,.arim-theme-options-placeholder{padding:24px}.arim-theme-options-form .form-table{margin:0 0 20px;padding:8px 20px 20px;border-collapse:separate}.arim-theme-options-form .form-table th{width:260px;padding-top:24px}.arim-theme-options-form .form-table td{padding-top:20px}.arim-theme-options-checkbox{display:inline-flex;align-items:center;gap:10px;font-weight:600}.arim-theme-options-intro{margin:0 0 20px;padding:16px 18px;border-left:4px solid #ff922e;background:#fff7f0;border-radius:10px}.arim-theme-options-badge{display:inline-flex;align-items:center;min-height:28px;padding:0 12px;border-radius:999px;background:#fff2e8;color:#c45d00;font-size:12px;font-weight:700}.arim-theme-options-form .submit{margin-top:0}'
        );
    }

    public function render_overview() {
        ?>
        <div class="wrap arim-theme-options-wrap">
            <div class="arim-theme-options-header">
                <h1><?php esc_html_e('ARIM Theme Options', 'arim'); ?></h1>
                <p><?php esc_html_e('Tema yönetimini tek merkezde toplamak için hazırlanan modüler panel. Her bölüm ayrı sınıfta çalışır, native WordPress Settings API kullanır ve WooCommerce şablonlarıyla uyumlu genişlemeye açıktır.', 'arim'); ?></p>
            </div>

            <div class="arim-theme-options-grid">
                <?php foreach ($this->sections as $section) : ?>
                    <a class="arim-theme-options-card" href="<?php echo esc_url(admin_url('admin.php?page=' . $section->get_slug())); ?>">
                        <span class="arim-theme-options-badge"><?php esc_html_e('Section', 'arim'); ?></span>
                        <h2><?php echo esc_html($section->get_menu_title()); ?></h2>
                        <p><?php echo esc_html($section->get_page_title()); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
