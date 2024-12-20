<?php
if (!defined('ABSPATH')) {
    exit;
}

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 3,
    'paged' => $paged,
    'author' => get_current_user_id(),
    'post_status' => array('publish', 'pending', 'draft')
);

$products = new WP_Query($args);
?>
<div class="messages-container"></div>
<table class="woocommerce-orders-table shop_table shop_table_responsive my_account_orders">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($products->have_posts()) : while ($products->have_posts()) : $products->the_post();
        $product = wc_get_product(get_the_ID());
    ?>
        <tr class="product-item">
            <td><?php echo esc_html($product->get_name()); ?></td>
            <td><?php echo esc_html($product->get_stock_quantity()); ?></td>
            <td><?php echo wc_price($product->get_price()); ?></td>
            <td><?php echo esc_html(get_post_status()); ?></td>
            <td>
                <a href="<?php echo esc_url(get_edit_post_link()); ?>" class="button">Edit</a>
                <button class="button delete-product" data-product-id="<?php echo esc_attr(get_the_ID()); ?>">Delete</button>
            </td>
        </tr>
    <?php endwhile; endif; wp_reset_postdata(); ?>
    </tbody>
</table>

<?php
echo paginate_links(array(
    'total' => $products->max_num_pages,
    'current' => $paged,
));
?>