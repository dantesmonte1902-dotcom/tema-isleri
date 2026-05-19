<?php
defined('ABSPATH') || exit;

abstract class ARIM_Base_Settings_Page {
    protected $slug;
    protected $page_title;
    protected $menu_title;
    protected $option_name;
    protected $description;

    public function __construct($slug, $page_title, $menu_title, $option_name = '', $description = '') {
        $this->slug        = $slug;
        $this->page_title  = $page_title;
        $this->menu_title  = $menu_title;
        $this->option_name = $option_name;
        $this->description = $description;
    }

    public function boot() {
    }

    public function get_slug() {
        return $this->slug;
    }

    public function get_page_title() {
        return $this->page_title;
    }

    public function get_menu_title() {
        return $this->menu_title;
    }

    public function get_option_name() {
        return $this->option_name;
    }

    public function get_settings_group() {
        return $this->option_name ? $this->option_name . '_group' : '';
    }

    public function render() {
        ?>
        <div class="wrap arim-theme-options-wrap">
            <div class="arim-theme-options-header">
                <h1><?php echo esc_html($this->get_page_title()); ?></h1>
                <?php if ($this->description) : ?>
                    <p><?php echo esc_html($this->description); ?></p>
                <?php endif; ?>
            </div>

            <?php
            if ($this->get_option_name()) {
                settings_errors($this->get_option_name());
            }
            ?>

            <?php if ($this->has_settings_form()) : ?>
                <form action="options.php" method="post" class="arim-theme-options-form">
                    <?php
                    settings_fields($this->get_settings_group());
                    do_settings_sections($this->get_slug());
                    submit_button(__('Ayarları Kaydet', 'arim'));
                    ?>
                </form>
            <?php else : ?>
                <div class="arim-theme-options-panel">
                    <?php $this->render_content(); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    protected function has_settings_form() {
        return false;
    }

    protected function render_content() {
        echo '<p>' . esc_html__('Bu bölüm için ayarlar hazırlanıyor.', 'arim') . '</p>';
    }

    protected function get_defaults() {
        return [];
    }

    protected function get_option_value($key, $default = '') {
        $options = $this->option_name ? get_option($this->option_name, []) : [];
        $options = wp_parse_args(is_array($options) ? $options : [], $this->get_defaults());

        return isset($options[$key]) ? $options[$key] : $default;
    }

    public function render_checkbox_field($args) {
        $key         = isset($args['key']) ? (string) $args['key'] : '';
        $label       = isset($args['label']) ? (string) $args['label'] : '';
        $description = isset($args['description']) ? (string) $args['description'] : '';
        $checked     = '1' === (string) $this->get_option_value($key, isset($args['default']) ? $args['default'] : '0');
        ?>
        <label class="arim-theme-options-checkbox">
            <input type="checkbox" name="<?php echo esc_attr($this->option_name . '[' . $key . ']'); ?>" value="1" <?php checked($checked); ?>>
            <span><?php echo esc_html($label); ?></span>
        </label>
        <?php if ($description) : ?>
            <p class="description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php
    }

    public function render_select_field($args) {
        $key         = isset($args['key']) ? (string) $args['key'] : '';
        $options     = isset($args['options']) && is_array($args['options']) ? $args['options'] : [];
        $description = isset($args['description']) ? (string) $args['description'] : '';
        $value       = (string) $this->get_option_value($key, isset($args['default']) ? $args['default'] : '');
        ?>
        <select name="<?php echo esc_attr($this->option_name . '[' . $key . ']'); ?>" class="regular-text">
            <?php foreach ($options as $option_value => $option_label) : ?>
                <option value="<?php echo esc_attr($option_value); ?>" <?php selected($value, (string) $option_value); ?>>
                    <?php echo esc_html($option_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($description) : ?>
            <p class="description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php
    }

    public function render_number_field($args) {
        $key         = isset($args['key']) ? (string) $args['key'] : '';
        $description = isset($args['description']) ? (string) $args['description'] : '';
        $value       = (int) $this->get_option_value($key, isset($args['default']) ? $args['default'] : 0);
        $min         = isset($args['min']) ? (int) $args['min'] : 0;
        $max         = isset($args['max']) ? (int) $args['max'] : 100;
        ?>
        <input
            type="number"
            class="small-text"
            min="<?php echo esc_attr($min); ?>"
            max="<?php echo esc_attr($max); ?>"
            name="<?php echo esc_attr($this->option_name . '[' . $key . ']'); ?>"
            value="<?php echo esc_attr($value); ?>"
        >
        <?php if ($description) : ?>
            <p class="description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php
    }

    protected function render_placeholder_message($title, $text) {
        ?>
        <div class="arim-theme-options-placeholder">
            <h2><?php echo esc_html($title); ?></h2>
            <p><?php echo esc_html($text); ?></p>
        </div>
        <?php
    }
}
