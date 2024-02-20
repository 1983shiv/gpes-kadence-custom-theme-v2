// Step 1: Add Custom Fields to Product
function add_custom_product_options() {
    global $woocommerce, $post;

    echo '<div class="options_group">';

    // Replace 'custom_print_area' with the desired meta key for your custom field.
    woocommerce_wp_select(
        array(
            'id'          => 'custom_print_area',
            'label'       => __( 'Print Area', 'woocommerce' ),
            'description' => '',
            'desc_tip'    => 'true',
            'options'     => array(
                ''          => __( 'Select Print Area', 'woocommerce' ),
                'front'     => __( 'Front', 'woocommerce' ),
                'back'      => __( 'Back', 'woocommerce' ),
                'sleeve'    => __( 'Sleeve', 'woocommerce' ),
            ),
        )
    );

    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'add_custom_product_options');

// Step 2: Save Custom Field Data
function save_custom_product_options($post_id) {
    $custom_print_area = isset($_POST['custom_print_area']) ? sanitize_text_field($_POST['custom_print_area']) : '';
    update_post_meta($post_id, 'custom_print_area', $custom_print_area);
}
add_action('woocommerce_process_product_meta', 'save_custom_product_options');

// Step 3: Display Custom Fields on the Product Page
function display_custom_product_options() {
    global $product;

    echo '<div class="custom-options">';
    echo '<label for="custom_print_area">' . __('Print Area', 'woocommerce') . '</label>';
    echo '<select name="custom_print_area" id="custom_print_area">';
    echo '<option value="">' . __('Select Print Area', 'woocommerce') . '</option>';
    echo '<option value="front">' . __('Front', 'woocommerce') . '</option>';
    echo '<option value="back">' . __('Back', 'woocommerce') . '</option>';
    echo '<option value="sleeve">' . __('Sleeve', 'woocommerce') . '</option>';
    echo '</select>';
    echo '</div>';
}
add_action('woocommerce_before_add_to_cart_button', 'display_custom_product_options');

// Step 4: Update Cart and Checkout
function add_custom_option_to_cart_item($cart_item_data, $product_id) {
    $custom_print_area = isset($_POST['custom_print_area']) ? sanitize_text_field($_POST['custom_print_area']) : '';
    $cart_item_data['custom_print_area'] = $custom_print_area;

    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'add_custom_option_to_cart_item', 10, 2);

function calculate_cart_item_price($cart_object) {
    foreach ($cart_object->cart_contents as $cart_item_key => $cart_item) {
        if (isset($cart_item['custom_print_area'])) {
            // Define your custom pricing rules based on the selected print area.
            $price_increase = 0;
            $selected_option = $cart_item['custom_print_area'];
            if ($selected_option === 'front') {
                $price_increase = 5.00; // Adjust the price based on your requirements.
            } elseif ($selected_option === 'back') {
                $price_increase = 8.00; // Adjust the price based on your requirements.
            } elseif ($selected_option === 'sleeve') {
                $price_increase = 3.00; // Adjust the price based on your requirements.
            }

            $cart_item['data']->set_price($cart_item['data']->get_price() + $price_increase);
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'calculate_cart_item_price');