<?php
/**
 * Custom Product Template for quotation
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @author Shiv Srivastava
 * @package Astra Child
 * @since 1.0.0
 */


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
function display_custom_product_options_select() {
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

function display_custom_product_options_checkbox() {
    global $product;

    echo '<div class="custom-options">';
    echo '<h4>' . __('Print Area', 'woocommerce') . '</h4> <br>';
    echo '<label><input type="checkbox" name="custom_print_area" value="front"> ' . __('Front', 'woocommerce') . '</label>';
    echo '<label><input type="checkbox" name="custom_print_area" value="back"> ' . __('Back', 'woocommerce') . '</label>';
    echo '<label><input type="checkbox" name="custom_print_area" value="lsleeve"> ' . __('Left Sleeve', 'woocommerce') . '</label>';
    echo '<label><input type="checkbox" name="custom_print_area" value="rsleeve"> ' . __('Right Sleeve', 'woocommerce') . '</label>';
    echo '<label><input type="checkbox" name="custom_print_area" value="lchest"> ' . __('Left Chest', 'woocommerce') . '</label>';
    echo '<label><input type="checkbox" name="custom_print_area" value="rchest"> ' . __('Right Chest', 'woocommerce') . '</label>';
    echo '</div>';
}

add_action('woocommerce_before_add_to_cart_button', 'display_custom_product_options_checkbox');

// Step 4: Update Cart and Checkout
function add_custom_option_to_cart_item($cart_item_data, $product_id) {
    $custom_print_area = isset($_POST['custom_print_area']) ? sanitize_text_field($_POST['custom_print_area']) : '';
    $cart_item_data['custom_print_area'] = $custom_print_area;

    // Set a flag to indicate that the custom price needs to be recalculated.
    $cart_item_data['custom_price_updated'] = false;

    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'add_custom_option_to_cart_item', 10, 2);

function calculate_cart_item_price_v1($cart_object) {
    foreach ($cart_object->cart_contents as $cart_item_key => $cart_item) {
        if (isset($cart_item['custom_print_area']) && !$cart_item['custom_price_updated']) {
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

            // Update the custom price and set the flag to indicate it has been updated.
            $cart_item['custom_price'] = $cart_item['data']->get_price();
            $cart_item['custom_price_updated'] = true;

            // Update the cart item data with the new custom price.
            $cart_object->cart_contents[$cart_item_key] = $cart_item;
        }
    }
}

function calculate_cart_item_price($cart_object) {
    foreach ($cart_object->cart_contents as $cart_item_key => $cart_item) {
        if (isset($cart_item['custom_print_area']) && is_array($cart_item['custom_print_area']) && !$cart_item['custom_price_updated']) {
            $price_increase = 0;
            $selected_options = $cart_item['custom_print_area'];

            // Loop through the selected print areas and calculate the price increase.
            foreach ($selected_options as $selected_option) {
                if ($selected_option === 'front') {
                    $price_increase += 5.00; // Adjust the price based on your requirements.
                } elseif ($selected_option === 'back') {
                    $price_increase += 8.00; // Adjust the price based on your requirements.
                } elseif ($selected_option === 'sleeve') {
                    $price_increase += 3.00; // Adjust the price based on your requirements.
                }
            }

            // Apply the price increase to the product price.
            $new_price = $cart_item['data']->get_price() + $price_increase;
            $cart_item['data']->set_price($new_price);

            // Update the custom price and set the flag to indicate it has been updated.
            $cart_item['custom_price'] = $new_price;
            $cart_item['custom_price_updated'] = true;

            // Update the cart item data with the new custom price.
            $cart_object->cart_contents[$cart_item_key] = $cart_item;
        }
    }
}

add_action('woocommerce_before_calculate_totals', 'calculate_cart_item_price');

// Step 5: Update the Cart Item Price Display
function display_custom_cart_item_price($item_price, $cart_item) {
    if (isset($cart_item['custom_price'])) {
        $item_price = wc_price($cart_item['custom_price']);
    }
    return $item_price;
}
add_filter('woocommerce_cart_item_price', 'display_custom_cart_item_price', 10, 2);

// Step 6: Update the Product Price Display
function display_custom_product_price($price, $product) {
    if (is_cart() || is_checkout()) {
        $cart = WC()->cart->get_cart();
        foreach ($cart as $cart_item_key => $cart_item) {
            if ($product->get_id() === $cart_item['product_id'] && isset($cart_item['custom_price'])) {
                $price = wc_price($cart_item['custom_price']);
                break;
            }
        }
    }
    return $price;
}
add_filter('woocommerce_get_price_html', 'display_custom_product_price', 10, 2);

// Step 7: Enqueue JavaScript to Handle Price Update

/*	
function enqueue_custom_js() {
    if (is_product()) {
        global $product;
        $product_price = $product->get_price();

        wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/custom-script.js', array('jquery'), '', true);

        wp_localize_script('custom-script', 'customData', array(
            'productPrice' => $product_price,
        ));
    }
}
*/


// Step 7: Enqueue JavaScript to Handle Price Update
function enqueue_custom_js2() {
    if (is_product()) {
        global $product;
        $product_price = $product->get_price();
        
        wc_enqueue_js("
            jQuery(document).ready(function($) {
                var productPrice = parseFloat($product_price);

                $('#custom_print_area').change(function() {
                    var customPrice = productPrice;
                    var selectedOption = $(this).val();

                    // Define your custom pricing rules based on the selected print area.
                    if (selectedOption === 'front') {
                        customPrice = productPrice + 5.00; // Adjust the price based on your requirements.
                    } else if (selectedOption === 'back') {
                        customPrice = productPrice + 8.00; // Adjust the price based on your requirements.
                    } else if (selectedOption === 'sleeve') {
                        customPrice = productPrice + 3.00; // Adjust the price based on your requirements.
                    }
					console.log('custom tshirt area', customPrice);
                      $('#product_price').html('<span class=\"woocommerce-Price-amount amount\"><span class=\"woocommerce-Price-currencySymbol\">' + woocommerce_params.currency_format_symbol + '</span>' + customPrice.toFixed(2) + '</span>');
                });
            });
        ");
    }
}
function enqueue_custom_js() {
    if (is_product()) {
        global $product;
        $product_price = $product->get_price();

        // Enqueue the accounting.js library
        wp_enqueue_script('accounting', get_stylesheet_directory_uri() . '/js/accounting.js', array(), '0.4.1', true);

        // Enqueue the custom script
        wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/custom-script.js', array('jquery'), '', true);


        wp_localize_script('custom-script', 'customData', array(
            'productPrice' => $product_price,
        ));
    }
}

add_action('wp_enqueue_scripts', 'enqueue_custom_js');

function custom_price_update_script() {
    if (is_product()) {
        /* ?>
        <script>
            jQuery(document).ready(function($) {
                var productPrice = parseFloat(customData.productPrice);
                var priceContainer = $('.woocommerce-Price-amount');
              	var productPrice = parseFloat(customData.productPrice);
    			var currencySymbol = customData.currencySymbol;


                // Initial display of the original product price
                var originalPriceHtml = priceContainer.html();

                $('#custom_print_area').change(function() {
                    var customPrice = productPrice;
                    var selectedOption = $(this).val();

                    // Define your custom pricing rules based on the selected print area.
                    if (selectedOption === 'front') {
                        customPrice = productPrice + 5.00; // Adjust the price based on your requirements.
                    } else if (selectedOption === 'back') {
                        customPrice = productPrice + 8.00; // Adjust the price based on your requirements.
                    } else if (selectedOption === 'sleeve') {
                        customPrice = productPrice + 3.00; // Adjust the price based on your requirements.
                    }

                    // Format the custom price as a currency value (using WooCommerce formatting)
                    // var formattedPrice = accounting.formatMoney(customPrice, woocommerce_params.currency_format_symbol, 2, woocommerce_params.mon_decimal_point, woocommerce_params.mon_thousands_sep);
                  var formattedPrice = accounting.formatMoney(customPrice, {
                      symbol: '€',
                      precision: 2,
                      thousand: ',',
                      decimal: '.',
                  });


                    // Display the updated price
                    var updatedPriceHtml = originalPriceHtml.replace(productPrice.toFixed(2), customPrice);
                    priceContainer.html(updatedPriceHtml);

                    // Update the hidden input field that stores the product price for WooCommerce
                    $('.single_add_to_cart_button').attr('data-price', customPrice.toFixed(2));
                  	// Display the updated price
            		$('.price').html('<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + '' + '</span>' +  formattedPrice + '</span>');
                });
            });
        </script>
        <?php */
      ?>
      <script>
      jQuery(document).ready(function($) {
        var productPrice = parseFloat(customData.productPrice);
        var currencySymbol = customData.currencySymbol;
        var priceContainer = $('.woocommerce-Price-amount');

        // Initial display of the original product price
        var originalPriceHtml = priceContainer.html();

        $('input[name="custom_print_area"]').on('change', function() {
            var selectedOptions = $('input[name="custom_print_area"]:checked').map(function() {
              	console.log('checked');
                return this.value;
            }).get();

            var customPrice = productPrice;

            // Define your custom pricing rules based on the selected print areas.
            if (selectedOptions.includes('front')) {
                customPrice += 9.00; // Adjust the price based on your requirements.
            }
            if (selectedOptions.includes('back')) {
                customPrice += 8.00; // Adjust the price based on your requirements.
            }
            if (selectedOptions.includes('lsleeve')) {
                customPrice += 3.00; // Adjust the price based on your requirements.
            }
          
           if (selectedOptions.includes('rsleeve')) {
                customPrice += 3.00; // Adjust the price based on your requirements.
            }
          
           if (selectedOptions.includes('lchest')) {
                customPrice += 4.00; // Adjust the price based on your requirements.
            }
          
           if (selectedOptions.includes('rchest')) {
                customPrice += 4.00; // Adjust the price based on your requirements.
            }

            // Format the custom price as a currency value (using accounting.js)
            var formattedPrice = accounting.formatMoney(customPrice, {
                symbol: '€',
                precision: 2,
                thousand: ',',
                decimal: '.',
            });

            // Display the updated price
            var updatedPriceHtml = originalPriceHtml.replace(productPrice.toFixed(2), formattedPrice);
            priceContainer.html(updatedPriceHtml);

            // Update the hidden input field that stores the product price for WooCommerce
            $('.single_add_to_cart_button').attr('data-price', customPrice.toFixed(2));
          	$('.price').html('<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + '' + '</span>' +  formattedPrice + '</span>');
        });
    });
	</script>
<?php
    }
}

add_action('wp_footer', 'custom_price_update_script');

// Override the product price display in the "Add to Cart" section
function custom_product_price_html($price, $product) {
    if (is_product()) {
        global $woocommerce;
        $cart = $woocommerce->cart->get_cart();

        foreach ($cart as $cart_item_key => $cart_item) {
            if ($product->get_id() === $cart_item['product_id'] && isset($cart_item['custom_price'])) {
                $price = wc_price($cart_item['custom_price']);
                break;
            }
        }
    }
    return $price;
}
//add_filter('woocommerce_get_price_html', 'custom_product_price_html', 10, 2);


// Replace '123' with the Gravity Form ID you obtained earlier.
function display_gravity_form_on_product_page() {
    global $product;

    // Check if it's a single product page and the product has the required form ID.
    if (is_product() && $product && has_term('tshirts', 'product_cat', $product->get_id()) && function_exists('gravity_form')) {
        // Replace '123' with the Gravity Form ID you obtained earlier.
        echo gravity_form( '5', false, false, false, null, true );
    }
}
add_action('woocommerce_single_product_summary', 'display_gravity_form_on_product_page', 30);

