jQuery(document).ready(function($) {
    // Image upload
    $('#upload_image_button').click(function(e) {
        e.preventDefault();
        var image = wp.media({
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            $('#product_image_id').val(uploaded_image.id);
            $('#image_preview').html('<img src="' + image_url + '" style="max-width: 150px;">');
        });
    });

    // Product deletion 
    $(document).on('click', '.delete-product', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }
        
        const productId = $(this).data('product-id');
        const $productElement = $(this).closest('.product-item');
        const $productContainer = $('.products-container');
        
        $.ajax({
            url: wpmpw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpmpw_delete_product',
                product_id: productId,
                nonce: wpmpw_ajax.nonce
            },
            beforeSend: function() {
                $productElement.addClass('deleting').css('opacity', '0.5');
            },
            success: function(response) {
                if (response.success) {
                    $productElement.fadeOut(400, function() {
                        $(this).remove();
                
                        const $productCount = $('.product-count');
                        if ($productCount.length) {
                            const currentCount = parseInt($productCount.text(), 10);
                            $productCount.text(Math.max(0, currentCount - 1));
                        }
                        
                        if ($productContainer.children('.product-item').length === 0) {
                            $productContainer.html('<p class="no-products">No products found.</p>');
                        }
                        
                        const $messageContainer = $('.messages-container');
                        if ($messageContainer.length) {
                            $messageContainer.html('<div class="success-message">Product successfully deleted!</div>');
                            setTimeout(() => {
                                $messageContainer.empty();
                            }, 3000);
                        }
                    });
                } else {
                    const errorMessage = response.data || 'Failed to delete product';
                    alert('Error: ' + errorMessage);
                    $productElement.removeClass('deleting').css('opacity', '');
                }
            },
            error: function(xhr, status, error) {
                alert('Error occurred while deleting the product: ' + error);
                $productElement.removeClass('deleting').css('opacity', '');
            }
        });
    });

    $('#add-product-form').on('submit', function(e) {
        const productName = $('#product_name').val().trim();
        const productPrice = $('#product_price').val().trim();
        const productQuantity = $('#product_quantity').val().trim();
        
        if (!productName || !productPrice || !productQuantity) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
        
        if (isNaN(productPrice) || productPrice <= 0) {
            e.preventDefault();
            alert('Please enter a valid price');
            return false;
        }
        
        if (isNaN(productQuantity) || productQuantity < 0) {
            e.preventDefault();
            alert('Please enter a valid quantity');
            return false;
        }
    });
});