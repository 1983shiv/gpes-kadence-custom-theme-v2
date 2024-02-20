<div class="product-attribute-filter" id="attribute-filter" data-category-id="<?php echo get_queried_object_id(); ?>">
    <?php

    $category_id = get_queried_object_id();
    $product_ids = get_posts(array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        ),
    ));
    
    // Initialize an empty array to store attribute terms
    $attribute_terms = array();

    // Loop through each product ID
    foreach ($product_ids as $product_id) {
        // Get product object
        $product = wc_get_product($product_id);

        // Get product attributes
        $product_attributes = $product->get_attributes();
        
        // Loop through each attribute
        foreach ($product_attributes as $attribute_name => $attribute) {
            // Check if the attribute is not variations attribute
            // if (!$attribute->get_variation()) {
                // Get the terms for the attribute
                $terms = $product->get_attribute($attribute_name);
                if (strpos($terms, '|') !== false) {
                    // | is present
                    // Now you can split the string into an array using explode
                    $terms = explode('|', $terms);
                }
                
                // Ensure $terms is an array
                $terms = is_array($terms) ? $terms : array($terms);

                // Add terms to the attribute_terms array
                if (!empty($terms)) {
                    $attribute_terms[$attribute_name] = array_merge($attribute_terms[$attribute_name] ?? [], $terms);
                }
            // }
        }
    }

    // // Remove duplicates and re-index arrays
    foreach ($attribute_terms as $attribute_name => $terms) {
        $attribute_terms[$attribute_name] = array_values(array_unique($terms));
    }
    

    foreach ($attribute_terms as $attribute_name => $terms) {
        // $attribute_terms[$attribute_name] = array_values(array_unique($terms));
        // Check if the attribute has products in the current category
        $filtered_terms = array_filter($terms, function ($term) use ($attribute_name, $product_ids) {
            // Exclude empty or null values
            if (empty($term) && !is_numeric($term)) {
                return false;
            }
            $term_product_ids = wc_get_products(array(
                'limit'        => -1,
                'include'      => $product_ids,
                'meta_query'   => array(
                    array(
                        'key'     => $attribute_name,
                        'value'   => $term,
                        'compare' => 'IN',
                    ),
                ),
            ));
    
            return !empty($term_product_ids);
        });
    
        // If there are filtered terms, render the dropdown
        if (!empty($filtered_terms)) {
            $attribute_label = wc_attribute_label(wc_attribute_taxonomy_name($attribute_name));
            $list_id = 'list-pa_' . sanitize_title($attribute_name);
            echo '<div id="' . esc_attr($list_id) . '" class="dropdown-check-list" tabindex="100">';
            echo '<span class="anchor">' . esc_html($attribute_label) . '</span>';
            echo '<ul class="items">';
    
            foreach ($filtered_terms as $term) {
                if(!empty($term)){
                    echo '<li><input type="checkbox" name="terms-ckbox" data-attribute-slug="pa_' . esc_attr($attribute_name) . '" value="' . esc_attr($term) . '"> ' . esc_html($term) . '</li>';
                }
            }
    
            echo '</ul>';
            echo '</div>';
        }
    }
    echo '<div class="active-filters-container"></div>';
    ?>
  	
</div>
