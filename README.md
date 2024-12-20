# wp-my-product-webspark Plugin

This plugin extends WooCommerce functionality by enabling CRUD operations for products through the "My Account" section. The plugin allows users to create, update, delete, and view their products, complete with email notifications to administrators.

## Installation and Setup Instructions

Follow the steps below to deploy and activate the `wp-my-product-webspark` plugin:

### Prerequisites

1. Ensure WooCommerce is installed and activated on your WordPress site.
2. Verify your WordPress installation meets the minimum requirements:
   - PHP 8.2 or higher
   - WordPress 6.7.1 or higher
   - WooCommerce  9.5.1 or higher

### Steps to Deploy the Plugin

1. **Download the Plugin**
   - Clone or download the plugin from the repository.
   - Ensure the folder is named `wp-my-product-webspark`.

2. **Upload the Plugin**
   - Log in to the WordPress admin panel.
   - Navigate to **Plugins > Add New > Upload Plugin**.
   - Upload the `wp-my-product-webspark.zip` file.

3. **Activate the Plugin**
   - After uploading, click **Activate Plugin**.
   - The plugin will verify that WooCommerce is active. If WooCommerce is not installed or activated, the plugin will display a warning and deactivate itself.

### Plugin Features

#### 1. **Add Product Page**
   - A new menu item, **Add Product**, is added under the My Account section.
   - This page includes a form with the following fields:
     - Product Name
     - Price
     - Quantity
     - WYSIWYG Description
     - Product Image (only displays images uploaded by the current user)
     - Save Product button
   - Products created through this form are assigned the status `Pending Review`.

#### 2. **My Products Page**
   - A second menu item, **My Products**, is added under the My Account section.
   - This page displays a paginated table with the following columns:
     - Product Name
     - Quantity
     - Price
     - Status
     - Edit Link
     - Delete Button

#### 3. **Email Notifications**
   - Upon creating or updating a product, an email notification is sent to the admin.
   - The email includes:
     - Product Name
     - Link to the author’s page in the admin panel
     - Link to the product edit page in the admin panel
   - Email sending can be toggled on/off in WooCommerce email settings.

### Configuration


1. **Toggle Email Notifications**
   - Go to **WooCommerce > Settings > Notifications.
   - Locate the notification for new product submissions.
   - Enable or disable the email as per your preference.

### Advanced Features

- **Restrict Media Library Access**:
  - Users only see images they have uploaded when selecting a product image.

- **Pending Review Status**:
  - All products created or edited through the plugin are marked as `Pending Review` for admin approval.

### Troubleshooting

1. **WooCommerce Not Installed**:
   - Ensure WooCommerce is installed and activated before activating the plugin.

2. **Email Not Sent**:
   - Check your WooCommerce email settings.
   - Verify your WordPress site is correctly configured to send emails (use an SMTP plugin if necessary).

3. **Permission Errors**:
   - Ensure the user role has permissions to access the My Account section and manage products.

### Uninstallation

1. Deactivate the plugin from **Plugins > Installed Plugins**.
2. Optionally, delete the plugin folder from `/wp-content/plugins/wp-my-product-webspark/`.

---

For further assistance, refer to the official documentation or contact support.
