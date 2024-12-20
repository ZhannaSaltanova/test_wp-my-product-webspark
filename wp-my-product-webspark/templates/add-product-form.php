<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="woocommerce-form">
    <?php wp_nonce_field('wpmpw_add_product', 'wpmpw_product_nonce'); ?>
    <input type="hidden" name="action" value="wpmpw_add_product">

    <p class="woocommerce-form-row">
        <label><?php esc_html_e('Product Name', 'woocommerce'); ?> *</label>
        <input type="text" name="product_name" required class="woocommerce-Input">
    </p>

    <p class="woocommerce-form-row">
        <label><?php esc_html_e('Price', 'woocommerce'); ?> *</label>
        <input type="number" step="0.01" name="product_price" required class="woocommerce-Input">
    </p>

    <p class="woocommerce-form-row">
        <label><?php esc_html_e('Quantity', 'woocommerce'); ?> *</label>
        <input type="number" name="product_quantity" required class="woocommerce-Input">
    </p>

    <p class="woocommerce-form-row">
        <label><?php esc_html_e('Description', 'woocommerce'); ?> *</label>
        <?php
        wp_editor('', 'product_description', array(
            'media_buttons' => false,
            'textarea_rows' => 6
        ));
        ?>
    </p>

    <p class="woocommerce-form-row">
        <label><?php esc_html_e('Product Image', 'woocommerce'); ?></label>
        <input type="hidden" name="product_image_id" id="product_image_id">
        <button type="button" class="button" id="upload_image_button">Upload Image</button>
        <div id="image_preview"></div>
    </p>

    <p>
        <button type="submit" class="woocommerce-Button button"><?php esc_html_e('Submit Product', 'woocommerce'); ?></button>
    </p>
</form>