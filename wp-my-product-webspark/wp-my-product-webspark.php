<?php
/**
 * Plugin Name: WP My Product Webspark
 * Description: Custom plugin for WooCommerce product management
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// WooCommerce availability check
function wpmpw_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>WP My Product Webspark requires WooCommerce to be installed and active.</p></div>';
        });
        deactivate_plugins(plugin_basename(__FILE__));
    }
}
add_action('admin_init', 'wpmpw_check_woocommerce');

// Add menu items to My Account
function wpmpw_add_menu_items($items) {
    $new_items = array();
    foreach ($items as $key => $value) {
        if ($key === 'customer-logout') {
            $new_items['add-product'] = 'Add Product';
            $new_items['my-products'] = 'My Products';
        }
        $new_items[$key] = $value;
    }
    return $new_items;
}
add_filter('woocommerce_account_menu_items', 'wpmpw_add_menu_items');

// Registering endpoints
function wpmpw_add_endpoints() {
    add_rewrite_endpoint('add-product', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('my-products', EP_ROOT | EP_PAGES);
}
add_action('init', 'wpmpw_add_endpoints');

// Activating and deactivating the plugin
function wpmpw_activate() {
    wpmpw_add_endpoints();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wpmpw_activate');


function wpmpw_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wpmpw_deactivate');

// Add content for pages
function wpmpw_add_product_content() {
    include plugin_dir_path(__FILE__) . 'templates/add-product-form.php';
}
add_action('woocommerce_account_add-product_endpoint', 'wpmpw_add_product_content');

function wpmpw_my_products_content() {
    include plugin_dir_path(__FILE__) . 'templates/my-products-list.php';
}
add_action('woocommerce_account_my-products_endpoint', 'wpmpw_my_products_content');

// adding styles
function wpmpw_enqueue_scripts() {
    if (is_account_page()) {
        wp_enqueue_media();
        wp_enqueue_editor();
        wp_enqueue_script('wpmpw-script', plugins_url('assets/js/script.js', __FILE__), array('jquery'), '1.0.0', true);
        wp_enqueue_style('wpmpw-style', plugins_url('assets/css/style.css', __FILE__));
        
        wp_localize_script('wpmpw-script', 'wpmpw_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpmpw_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'wpmpw_enqueue_scripts');

// Processing the product add/edit form
function wpmpw_handle_product_form() {
    if (!isset($_POST['wpmpw_product_nonce']) || !wp_verify_nonce($_POST['wpmpw_product_nonce'], 'wpmpw_add_product')) {
        return;
    }

    $product_data = array(
        'post_title'    => sanitize_text_field($_POST['product_name']),
        'post_content'  => wp_kses_post($_POST['product_description']),
        'post_status'   => 'pending',
        'post_type'     => 'product',
        'post_author'   => get_current_user_id() 
    );

    $product_id = wp_insert_post($product_data);

    if (!is_wp_error($product_id)) {
        update_post_meta($product_id, '_regular_price', floatval($_POST['product_price']));
        update_post_meta($product_id, '_price', floatval($_POST['product_price']));
        update_post_meta($product_id, '_stock', intval($_POST['product_quantity']));
        update_post_meta($product_id, '_stock_status', 'instock');
        update_post_meta($product_id, '_manage_stock', 'yes');

        if (!empty($_POST['product_image_id'])) {
            set_post_thumbnail($product_id, intval($_POST['product_image_id']));
        }

        wpmpw_send_admin_email($product_id);

        wp_redirect(wc_get_account_endpoint_url('my-products'));
        exit;
    }
}
add_action('admin_post_wpmpw_add_product', 'wpmpw_handle_product_form');

// Deleting a product
function wpmpw_delete_product() {
   
    if (!check_ajax_referer('wpmpw_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Product not found');
        return;
    }

    $current_user_id = get_current_user_id();
    $post = get_post($product_id);
    
    if ($current_user_id && $current_user_id === intval($post->post_author)) {
        $deleted = wp_delete_post($product_id, true);
        if ($deleted) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to delete product');
        }
    } else {
        wp_send_json_error('Unauthorized access');
    }
}
add_action('wp_ajax_wpmpw_delete_product', 'wpmpw_delete_product');


// Sending an email 
function wpmpw_send_admin_email($product_id) {
    if (get_option('wpmpw_enable_custom_email', 'no') !== 'yes') {
        return; 
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return;
    }

    $admin_email = get_option('admin_email');
    $subject = __('New Product Submission', 'woocommerce');
    
 
    $content = '<p>' . __('A new product has been submitted for review.', 'woocommerce') . '</p>';
    $content .= '<p><strong>' . __('Product Name:', 'woocommerce') . '</strong> ' . $product->get_name() . '</p>';
    $content .= '<p><strong>' . __('Edit Product:', 'woocommerce') . '</strong> <a href="' . admin_url('post.php?post=' . $product_id . '&action=edit') . '">' . __('Edit Product', 'woocommerce') . '</a></p>';
    $content .= '<p><strong>' . __('Author:', 'woocommerce') . '</strong> <a href="' . admin_url('user-edit.php?user_id=' . $product->get_post_data()->post_author) . '">' . __('View Author', 'woocommerce') . '</a></p>';

    $mailer = WC()->mailer();
    
    $email_body = $mailer->wrap_message(__('New Product Submission', 'woocommerce'), $content);
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    $mailer->send($admin_email, $subject, $email_body, $headers);
}

// Add new email setting to WooCommerce
function wpmpw_add_custom_email_setting($settings) {
    $custom_email_setting = array(
        'title'         => 'Enable Custom Order Email',
        'id'            => 'wpmpw_enable_custom_email',
        'type'          => 'checkbox',
        'desc'          => 'Enable custom email template for new orders and products.',
        'default'       => 'no',
        'desc_tip'      => true,
    );
    $settings[] = $custom_email_setting;
    return $settings;
}
add_filter('woocommerce_email_settings', 'wpmpw_add_custom_email_setting');

function wpmpw_send_custom_email($order_id) {

    if (get_option('wpmpw_enable_custom_email', 'no') !== 'yes') {
        return; 
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        return; 
    }

    $mailer = WC()->mailer();

    $message = $mailer->wrap_message(
        'Custom Order Email',
        'Your custom email message here.'
    );

    $mailer->send(
        $order->get_billing_email(),
        'Custom Order Email Subject',
        $message
    );
}

add_action('woocommerce_order_status_pending', 'wpmpw_send_custom_email');