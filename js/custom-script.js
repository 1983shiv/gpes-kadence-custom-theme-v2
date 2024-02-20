jQuery(document).ready(function($) {
    var productPrice = parseFloat(customData.productPrice);

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
		console.log('custom area', customPrice);
        $('#product_price').html('<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + woocommerce_params.currency_format_symbol + '</span>' + customPrice.toFixed(2) + '</span>');
    });
});
