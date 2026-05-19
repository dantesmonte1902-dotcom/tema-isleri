<?php
defined('ABSPATH') || exit;

$product_archive = get_template_directory() . '/woocommerce/archive-product.php';

if (file_exists($product_archive)) {
    include $product_archive;
    return;
}

require get_template_directory() . '/taxonomy.php';
