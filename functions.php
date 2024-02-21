<?php
/**
 * Enqueue child styles.
 */

// require_once 'includes/assets.php';
// require_once 'includes/header-menu.php';
// require_once 'includes/ga_customizations.php';

// https://docs.google.com/spreadsheets/d/1xfdnMEn3OFopgHFHEnrE6vhy10wzHp1XXxN_lg_Og2c/edit#gid=0
// wholeseller	Custom ordering	Falk and Ross, Makito, ROLY, STAMINA, Stanley Stella
// https://www.garmentprinting.es/wp-admin/tools.php?page=permalink-manager&section=uri_editor&subsection=tax_pa_wholeseller
// https://v3.garmentprinting.es/wp-admin/tools.php?page=permalink-manager&section=uri_editor&subsection=tax_pa_wholeseller

// Add this to your theme's functions.php file or a custom plugin


function replace_jquery() {
    if (!is_admin()) {
        // Replace the default WordPress jQuery script with a CDN version
        wp_deregister_script('jquery');
        wp_register_script('jquery', 'https://code.jquery.com/jquery-3.7.1.min.js', array(), null, true);
        wp_enqueue_script('jquery');
    }
}
// add_action('wp_enqueue_scripts', 'replace_jquery');


require_once 'includes/header-menu.php';
require_once 'includes/ga_customizations.php';

function child_enqueue_styles() {
	wp_enqueue_style( 'child-theme', get_stylesheet_directory_uri() . '/style.css', array(), filemtime(get_stylesheet_directory() . '/style.css') ); 
}

// var_dump(get_stylesheet_directory_uri() . '/custom.js');
// Add this to your functions.php file
function enqueue_custom_scripts() {
  	$cache_buster = wp_rand();
    wp_enqueue_style( 'child-theme', get_stylesheet_directory_uri() . '/style.css', array(), filemtime(get_stylesheet_directory() . '/style.css') ); 
    // Replace 'path-to-your-styles.css' and 'path-to-your-scripts.js' with the actual paths of your CSS and JS files.
  	wp_enqueue_style( 'custom-theme-style', get_stylesheet_directory_uri() . '/custom.css', array(), $cache_buster, true);
    wp_enqueue_script('custom-scripts', get_stylesheet_directory_uri() . '/custom.js', array('jquery'), $cache_buster, true);
    wp_enqueue_script('global', get_stylesheet_directory_uri() . '/js/global.js', array('jquery'), $cache_buster, true);
  	wp_localize_script('custom-scripts', 'custom_pricing_data', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
  	wp_localize_script('custom-scripts', 'fiu_upload_file', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
  	if (is_product_category()) {
        wp_enqueue_script('filter-script', get_stylesheet_directory_uri() . '/filter-script.js', array('jquery'), $cache_buster, true);

        // Pass AJAX URL to script
        wp_localize_script('filter-script', 'ajax_params', array('ajax_url' => admin_url('admin-ajax.php')));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function enqueue_custom_admin_styles() {
    wp_enqueue_style('custom-admin-styles', get_stylesheet_directory_uri() . '/custom-admin-styles.css', array(), filemtime(get_stylesheet_directory() . '/custom-admin-styles.css') ); 
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_styles');

function fiu_upload_file() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['file'])) {
            $uploadedFile = $_FILES['file'];
            
            // Sanitize and generate a unique file name
            $originalFileName = sanitize_file_name($uploadedFile['name']);
            $uniqueFileName = md5(uniqid() . time()) . '_' . $originalFileName;

            // Define allowed file extensions and max file size
            $allowedExtensions = array('jpg', 'jpeg', 'png', 'pdf', 'eps', 'ai', 'svg', 'tiff');
            $maxFileSize = 64 * 1024 * 1024; // 1 MB

            // Get file extension and size
            $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

            // Validate file extension
            if (!in_array($fileExtension, $allowedExtensions)) {
                http_response_code(400);
                echo 'File extension is not allowed.';
                exit;
            }

            // Validate file size
            if ($uploadedFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo 'File size exceeds the allowed limit.';
                exit;
            }

            // Set destination path within WordPress uploads directory
            // $uploadDir = wp_upload_dir();
            $uploadPath = WP_CONTENT_DIR . '/uploads/customerDesigns/' . $uniqueFileName;
            // $uploadPath = $uploadDir['path'] . '/' . $uniqueFileName;

            // if (!file_exists($uploadPath) || !is_writable($uploadPath)) {
            //     http_response_code(500);
            //     echo 'Destination directory is not writable.';
            //     echo json_encode(array('Destination directory is not writable.' => ''));
            //     exit;
            // }

            if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
                http_response_code(500);
                echo 'File upload error: ' . $uploadedFile['error'];
                exit;
            } 

            // Move the uploaded file to the destination
            if (move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
                // Optionally, you can perform further processing here
                // $output = array('message' => 'File uploaded successfully');
                $output = json_encode(array('sanitized_file_name' => $uniqueFileName));
                wp_send_json($output);
                echo json_encode(array('sanitized_file_name' => $uniqueFileName));
                // Add JavaScript to disable the add to cart button
                
                exit;
            } else {
                http_response_code(500);
                // echo 'Error uploading file.';
                echo json_encode(array('Error uploading file.' => 'else blocked run.'));
                exit;
            }
        }
    }
}

add_action('wp_ajax_fiu_upload_file', 'fiu_upload_file');
add_action('wp_ajax_nopriv_fiu_upload_file', 'fiu_upload_file');

function add_inline_css_for_tshirt_products() {
    // $trace = debug_backtrace();
    // var_dump($trace);
    // (is_product() && has_term('camisetas-personalizadas', 'product_cat'))
    if (!get_transient('tshirt_products_css_executed')) {
        if (is_product()) {
            wp_enqueue_style('custom-styles', get_stylesheet_directory_uri() . '/custom-styles.css', array(), filemtime(get_stylesheet_directory() . '/custom-styles.css'));
        }
    }
}
add_action('woocommerce_before_single_product', 'add_inline_css_for_tshirt_products');

function get_product_category_slug($product_id) {
    $terms = wp_get_post_terms($product_id, 'product_cat');
    
    if (is_array($terms) && !empty($terms)) {
        return $terms[0]->slug;
    }
    
    // return '';
    return $terms;
}

function is_simple_or_variable_product($product_id) {
    $product_type = get_post_type($product_id);

    if ($product_type === 'product') {
        // Check if it's a variable product.
        $product = wc_get_product($product_id);
        if ($product->is_type('variable')) {
            return 'variable';
        }

        // It's a simple product if not a variable product.
        return 'simple';
    } else {
        // It's not a product.
        return 'other';
    }
}

function get_image_url_from_id($image_id) {
    $image_data = wp_get_attachment_image_src($image_id, 'full'); // 'full' size, you can change it to the desired size
    if ($image_data) {
        return $image_data[0]; // Return the URL of the image
    }
    return false; // Return false if the image ID is invalid or not found
}

function print_area_display_bag($child_theme_uri){
    // Print Area options with individual file upload fields
    echo '<h4>' . __('Área de impresión', 'woocommerce') . '</h4>';
    echo '<div class="custom-options-printarea">';
    
    echo '<label for="custom_print_area_centerfront"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerfront" value="centerfront"> 
    <img src="'. $child_theme_uri .'/img/bags/clothing-printing-areas_Bag-Front-Print-295x300.jpg" alt="Parte delantera central" width="100px" height="102px" >' . __('Parte delantera central', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_centerback"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerback" value="centerback"> 
    <img src="'. $child_theme_uri .'/img/bags/clothing-printing-areas_Bag-Back-Print-295x300.jpg" alt="Centro de la espalda" width="100px" height="102px" >' . __('Centro de la espalda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftsleeve" value="leftsleeve"> 
    <img src="'. $child_theme_uri .'/img/bags/clothing-printing-areas_Bag-Left-Print-295x300.jpg" alt="Lado izquierdo" width="100px" height="102px" >' . __('Lado izquierdo', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightsleeve" value="rightsleeve"> 
    <img src="'. $child_theme_uri .'/img/bags/clothing-printing-areas_Bag-Right-Print-295x300.jpg" alt="Lado derecho" width="100px" height="102px" >' . __('Lado derecho', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_customposition"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_customposition" value="customposition"> 
    <img src="'. $child_theme_uri .'/img/bags/clothing-printing-areas_Bag-custom@4x-295x300.png" alt="Personalizado" width="100px" height="102px" >' . __('Personalizado', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_allover"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_allover" value="allover"> 
    <img src="'. $child_theme_uri .'/img/bags/clothing-printing-areas_Bag-allover@4x-295x300.png" alt="Todo" width="100px" height="102px" >' . __('Todo', 'woocommerce') . '</label>';
    
    echo '</div>';
}

function print_area_display_cap($child_theme_uri){
    // Print Area options with individual file upload fields
    echo '<h4>' . __('Área de impresión', 'woocommerce') . '</h4>';
    echo '<div class="custom-options-printarea">';
    
    echo '<label for="custom_print_area_centerfront"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerfront" value="centerfront"> 
    <img src="'. $child_theme_uri .'/img/caps/clothing-printing-areas_Cap-Front-Print-295x300.jpg" alt="Parte delantera central" width="100px" height="102px" >' . __('Parte delantera central', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_centerback"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerback" value="centerback"> 
    <img src="'. $child_theme_uri .'/img/caps/clothing-printing-areas_Cap-Back-Print-295x300.jpg" alt="Centro de la espalda" width="100px" height="102px" >' . __('Centro de la espalda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftsleeve" value="leftsleeve"> 
    <img src="'. $child_theme_uri .'/img/caps/clothing-printing-areas_Cap-Left-Print-295x300.jpg" alt="Lado izquierdo" width="100px" height="102px" >' . __('Lado izquierdo', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightsleeve" value="rightsleeve"> 
    <img src="'. $child_theme_uri .'/img/caps/clothing-printing-areas_Cap-Right-Print-295x300.jpg" alt="Lado derecho" width="100px" height="102px" >' . __('Lado derecho', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_customposition"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_customposition" value="customposition"> 
    <img src="'. $child_theme_uri .'/img/caps/clothing-printing-areas_Cap-custom@4x-295x300.png" alt="Personalizado" width="100px" height="102px" >' . __('Personalizado', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_allover"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_allover" value="allover"> 
    <img src="'. $child_theme_uri .'/img/caps/clothing-printing-areas_Cap-all-over@4x-295x300.png" alt="Todo" width="100px" height="102px" >' . __('Todo', 'woocommerce') . '</label>';
    
    echo '</div>';
}

function upload_area_display_caps_bags($dynamic_value){
    // Print Area options with individual file upload fields
    echo '<div class="custom-options-printarea-upload hidden-uploads">';
    echo '<h4 class="custom-options-printarea-upload-h4">' . __('Cargue su obra de arte', 'woocommerce') . '</h4><br>';

    echo '<div class="custom_print_area_centerfront hidden-uploads">';
    echo '<label for="custom_print_area_centerfront_file">' . __('Parte delantera central', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_centerfront_file" id="custom_print_area_centerfront_file" /></div>';
    
    echo '<div class="custom_print_area_centerback hidden-uploads">';
    echo '<label for="custom_print_area_centerback_file">' . __('Centro de la espalda', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_centerback_file" id="custom_print_area_centerback_file" /></div>';
    
    echo '<div class="custom_print_area_leftsleeve hidden-uploads">';
    echo '<label for="custom_print_area_leftsleeve">' . __('Lado izquierdo', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_leftsleeve_file" id="custom_print_area_leftsleeve_file" /></div>';
    
    echo '<div class="custom_print_area_rightsleeve hidden-uploads">';
    echo '<label for="custom_print_area_rightsleeve">' . __('Lado derecho', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_rightsleeve_file" id="custom_print_area_rightsleeve_file" /></div>';
    
    echo '<div class="custom_print_area_customposition hidden-uploads">';
    echo '<label for="custom_print_area_customposition">' . __('Personalizado', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_customposition_file" id="custom_print_area_customposition_file" /></div>';
    
    echo '<div class="custom_print_area_allover hidden-uploads">';
    echo '<label for="custom_print_area_allover">' . __('Todo', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_allover_file" id="custom_print_area_allover_file" /></div>';
    echo '</div>';
    // Output the hidden input field
    echo '<input type="hidden" name="custom_hidden_field" id="custom_hidden_field" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_hidden_field_for_qty" id="custom_hidden_field_for_qty" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_centerfront_file_hidden" id="custom_print_area_centerfront_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_centerback_file_hidden" id="custom_print_area_centerback_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_leftsleeve_file_hidden" id="custom_print_area_leftsleeve_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_rightsleeve_file_hidden" id="custom_print_area_rightsleeve_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_customposition_file_hidden" id="custom_print_area_customposition_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_allover_file_hidden" id="custom_print_area_allover_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="varids[]" id="varids" value="' . esc_attr($dynamic_value) . '" />';
}

function print_area_display_jackets($child_theme_uri){
    // Print Area options with individual file upload fields
    echo '<h4>' . __('Área de impresión', 'woocommerce') . '</h4>';
    echo '<div class="custom-options-printarea">';
    
    echo '<label for="custom_print_area_centerback"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerback" value="centerback"> 
    <img src="'. $child_theme_uri .'/img/jackets/clothing-printing-areas_Jacket-Back-Print-295x300.jpg" alt="Centro de la espalda" width="120px" height="102px" >' . __('Centro de la espalda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftsleeve" value="leftsleeve"> 
    <img src="'. $child_theme_uri .'/img/jackets/clothing-printing-areas_Jacket-Left-Sleeve-Print-295x300.jpg" alt="Manga izquierda" width="120px" height="102px" >' . __('Manga izquierda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightsleeve" value="rightsleeve"> 
    <img src="'. $child_theme_uri .'/img/jackets/clothing-printing-areas_Jacket-Right-Sleeve-Print-295x300.jpg" alt="Manga derecha" width="120px" height="102px" >' . __('Manga derecha', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftchest"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftchest" value="leftchest"> 
    <img src="'. $child_theme_uri .'/img/jackets/clothing-printing-areas_Jacket-Left-Chest-Print-295x300.jpg" alt="Pecho izquierdo" width="120px" height="102px" >' . __('Pecho izquierdo', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightchest"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightchest" value="rightchest"> 
    <img src="'. $child_theme_uri .'/img/jackets/clothing-printing-areas_Jacket-Left-Chest-Print-295x300.jpg" alt="Pecho derecho" width="120px" height="102px" >' . __('Pecho derecho', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_customposition"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_customposition" value="customposition"> 
    <img src="'. $child_theme_uri .'/img/jackets/clothing-printing-areas_Jacket-custom@4x-295x300.png" alt="Personalizado" width="120px" height="102px" >' . __('Personalizado', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_allover"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_allover" value="allover"> 
    <img src="'. $child_theme_uri .'/img/jackets/clothing-printing-areas_Jacket-all-over@4x-295x300.png" alt="Todo" width="120px" height="102px" >' . __('Todo', 'woocommerce') . '</label>';
    
    echo '</div>';
}

function upload_area_display_jackets($dynamic_value){
    // Print Area options with individual file upload fields
    echo '<div class="custom-options-printarea-upload hidden-uploads">';
    echo '<h4 class="custom-options-printarea-upload-h4 hidden-uploads">' . __('Cargue su obra de arte', 'woocommerce') . '</h4><br>';

    echo '<div class="custom_print_area_centerback hidden-uploads">';
    echo '<label for="custom_print_area_centerback_file">' . __('Centro de la espalda', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_centerback_file" id="custom_print_area_centerback_file" /></div>';
    
    echo '<div class="custom_print_area_leftsleeve hidden-uploads">';
    echo '<label for="custom_print_area_leftsleeve">' . __('Manga izquierda', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_leftsleeve_file" id="custom_print_area_leftsleeve_file" /></div>';
    
    echo '<div class="custom_print_area_rightsleeve hidden-uploads">';
    echo '<label for="custom_print_area_rightsleeve">' . __('Manga derecha', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_rightsleeve_file" id="custom_print_area_rightsleeve_file" /></div>';
    
    echo '<div class="custom_print_area_leftchest hidden-uploads">';
    echo '<label for="custom_print_area_leftchest">' . __('Pecho izquierdo', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_leftchest_file" id="custom_print_area_leftchest_file" /></div>';
    
    echo '<div class="custom_print_area_rightchest hidden-uploads">';
    echo '<label for="custom_print_area_rightchest">' . __('Pecho derecho', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_rightchest_file" id="custom_print_area_rightchest_file" /></div>';
    
    echo '<div class="custom_print_area_customposition hidden-uploads">';
    echo '<label for="custom_print_area_customposition">' . __('Personalizado', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_customposition_file" id="custom_print_area_customposition_file" /></div>';
    
    echo '<div class="custom_print_area_allover hidden-uploads">';
    echo '<label for="custom_print_area_allover">' . __('Todo', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_allover_file" id="custom_print_area_allover_file" /></div>';
    echo '<div class="custom-product-notice hidden-uploads">Accepted file types: jpg, png, pdf, eps, ai, jpeg, Svg, Tiff and Maximum file size: 64 MB. <br>
        Artwork must be vectorized PDF and scaled to print size or additional artwork charges will apply.</div>';
    echo '</div>';
    // Output the hidden input field
    echo '<input type="hidden" name="custom_hidden_field" id="custom_hidden_field" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_hidden_field_for_qty" id="custom_hidden_field_for_qty" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_centerback_file_hidden" id="custom_print_area_centerback_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_leftsleeve_file_hidden" id="custom_print_area_leftsleeve_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_rightsleeve_file_hidden" id="custom_print_area_rightsleeve_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_leftchest_file_hidden" id="custom_print_area_leftchest_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_rightchest_file_hidden" id="custom_print_area_rightchest_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_customposition_file_hidden" id="custom_print_area_customposition_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_allover_file_hidden" id="custom_print_area_allover_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="varids[]" id="varids" value="' . esc_attr($dynamic_value) . '" />';
}

function print_area_display_hoodies($child_theme_uri){
    // Print Area options with individual file upload fields
    echo '<h4>' . __('Área de impresión', 'woocommerce') . '</h4>';
    echo '<div class="custom-options-printarea">';
    
    echo '<label for="custom_print_area_centerfront"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerfront" value="centerfront"> 
    <img src="'. $child_theme_uri .'/img/hoodies/clothing-printing-areas_Hoodie-Front-Print-295x300.jpg" alt="Parte delantera central" width="100px" height="102px" >' . __('Parte delantera central', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_centerback"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerback" value="centerback"> 
    <img src="'. $child_theme_uri .'/img/hoodies/clothing-printing-areas_Hoodie-Back-Print-295x300.jpg" alt="Centro de la espalda" width="100px" height="102px" >' . __('Centro de la espalda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftsleeve" value="leftsleeve"> 
    <img src="'. $child_theme_uri .'/img/hoodies/clothing-printing-areas_Hoodie-Left-Sleeve-Print-295x300.jpg" alt="Manga izquierda" width="100px" height="102px" >' . __('Manga izquierda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightsleeve" value="rightsleeve"> 
    <img src="'. $child_theme_uri .'/img/hoodies/clothing-printing-areas_Hoodie-Right-Sleeve-Print-295x300.jpg" alt="Manga derecha" width="100px" height="102px" >' . __('Manga derecha', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftchest"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftchest" value="leftchest"> 
    <img src="'. $child_theme_uri .'/img/hoodies/clothing-printing-areas_Hoodie-Left-Chest-Print-295x300.jpg" alt="Pecho izquierdo" width="100px" height="102px" >' . __('Pecho izquierdo', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightchest"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightchest" value="rightchest"> 
    <img src="'. $child_theme_uri .'/img/hoodies/clothing-printing-areas_Hoodie-Right-Chest-Print-295x300.jpg" alt="Pecho derecho" width="100px" height="102px" >' . __('Pecho derecho', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_customposition"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_customposition" value="customposition"> 
    <img src="'. $child_theme_uri .'/img/hoodies/clothing-printing-areas_Hoodie-custom@4x-295x300.png" alt="Personalizado" width="100px" height="102px" >' . __('Personalizado', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_allover"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_allover" value="allover"> 
    <img src="'. $child_theme_uri .'/img/hoodies/clothing-printing-areas_Hoodie-all-over@4x-295x300.png" alt="Todo" width="100px" height="102px" >' . __('Todo', 'woocommerce') . '</label>';
    
    echo '</div>';
}

function print_area_display_polos($child_theme_uri){
    // Print Area options with individual file upload fields
    echo '<h4>' . __('Área de impresión', 'woocommerce') . '</h4>';
    echo '<div class="custom-options-printarea">';
    
    echo '<label for="custom_print_area_centerfront"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerfront" value="centerfront"> 
    <img src="'. $child_theme_uri .'/img/polos/clothing-printing-areas_Polo-Shirt-Front-Print-295x300.jpg" alt="Parte delantera central" width="100px" height="102px" >' . __('Parte delantera central', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_centerback"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerback" value="centerback"> 
    <img src="'. $child_theme_uri .'/img/polos/clothing-printing-areas_Polo-Shirt-Back-Print-295x300.jpg" alt="Centro de la espalda" width="100px" height="102px" >' . __('Centro de la espalda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftsleeve" value="leftsleeve"> 
    <img src="'. $child_theme_uri .'/img/polos/clothing-printing-areas_Polo-Shirt-Left-Sleeve-Print-295x300.jpg" alt="Manga izquierda" width="100px" height="102px" >' . __('Manga izquierda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightsleeve" value="rightsleeve"> 
    <img src="'. $child_theme_uri .'/img/polos/clothing-printing-areas_Polo-Shirt-Right-Sleeve-Print-295x300.jpg" alt="Manga derecha" width="100px" height="102px" >' . __('Manga derecha', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftchest"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftchest" value="leftchest"> 
    <img src="'. $child_theme_uri .'/img/polos/clothing-printing-areas_Polo-Shirt-Left-Chest-Print-295x300.jpg" alt="Pecho izquierdo" width="100px" height="102px" >' . __('Pecho izquierdo', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightchest"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightchest" value="rightchest"> 
    <img src="'. $child_theme_uri .'/img/polos/clothing-printing-areas_Polo-Shirt-Right-Chest-Print-295x300.jpg" alt="Pecho derecho" width="100px" height="102px" >' . __('Pecho derecho', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_customposition"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_customposition" value="customposition"> 
    <img src="'. $child_theme_uri .'/img/polos/custom@4x-295x300.png" alt="Personalizado" width="100px" height="102px" >' . __('Personalizado', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_allover"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_allover" value="allover"> 
    <img src="'. $child_theme_uri .'/img/polos/all-over@4x-295x300.png" alt="Todo" width="100px" height="102px" >' . __('Todo', 'woocommerce') . '</label>';
    
    echo '</div>';
}

function print_area_display_tshirt($child_theme_uri){
    // Print Area options with individual file upload fields
    echo '<h4>' . __('Área de impresión', 'woocommerce') . '</h4>';
    echo '<div class="custom-options-printarea">';
    
    echo '<label for="custom_print_area_centerfront"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerfront" value="centerfront"> 
    <img src="'. $child_theme_uri .'/img/clothing-printing-areas_T-Shirt-Front-1.jpg" alt="Parte delantera central" width="50px" height="52px" >' . __('Parte delantera central', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_centerback"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_centerback" value="centerback"> 
    <img src="'. $child_theme_uri .'/img/clothing-printing-areas_T-Shirt-Back-Print.jpg" alt="Centro de la espalda" width="50px" height="52px" >' . __('Centro de la espalda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftsleeve" value="leftsleeve"> 
    <img src="'. $child_theme_uri .'/img/clothing-printing-areas_T-Shirt-Left-Sleeve-Print.jpg" alt="Manga izquierda" width="50px" height="52px" >' . __('Manga izquierda', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightsleeve"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightsleeve" value="rightsleeve"> 
    <img src="'. $child_theme_uri .'/img/clothing-printing-areas_T-Shirt-Right-Sleeve-Print.jpg" alt="Manga derecha" width="50px" height="52px" >' . __('Manga derecha', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_leftchest"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_leftchest" value="leftchest"> 
    <img src="'. $child_theme_uri .'/img/clothing-printing-areas_T-Shirt-Left-Chest-Print.jpg" alt="Pecho izquierdo" width="50px" height="52px" >' . __('Pecho izquierdo', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_rightchest"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_rightchest" value="rightchest"> 
    <img src="'. $child_theme_uri .'/img/clothing-printing-areas_T-Shirt-Right-Chest-Print.jpg" alt="Pecho derecho" width="50px" height="52px" >' . __('Pecho derecho', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_customposition"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_customposition" value="customposition"> 
    <img src="'. $child_theme_uri .'/img/clothing-printing-areas_T-Shirt-Custom2.png" alt="Personalizado" width="50px" height="52px" >' . __('Personalizado', 'woocommerce') . '</label>';
    
    echo '<label for="custom_print_area_allover"><input type="checkbox" name="custom_print_area[]" id="custom_print_area_allover" value="allover"> 
    <img src="'. $child_theme_uri .'/img/clothing-printing-areas_T-Shirt-All-Over2.png" alt="Todo" width="50px" height="52px" >' . __('Todo', 'woocommerce') . '</label>';
    
    echo '</div>';
}

function upload_area_display_tshirt($dynamic_value){
    // Print Area options with individual file upload fields
    echo '<div class="custom-options-printarea-upload hidden-uploads">';
    echo '<h4 class="custom-options-printarea-upload-h4 hidden-uploads">' . __('Cargue su obra de arte', 'woocommerce') . '</h4><br>';

    echo '<div class="custom_print_area_centerfront hidden-uploads">';
    echo '<label for="custom_print_area_centerfront_file">' . __('Parte delantera central', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_centerfront_file" id="custom_print_area_centerfront_file" /></div>';
    
    echo '<div class="custom_print_area_centerback hidden-uploads">';
    echo '<label for="custom_print_area_centerback_file">' . __('Centro de la espalda', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_centerback_file" id="custom_print_area_centerback_file" /></div>';
    
    echo '<div class="custom_print_area_leftsleeve hidden-uploads">';
    echo '<label for="custom_print_area_leftsleeve">' . __('Manga izquierda', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_leftsleeve_file" id="custom_print_area_leftsleeve_file" /></div>';
    
    echo '<div class="custom_print_area_rightsleeve hidden-uploads">';
    echo '<label for="custom_print_area_rightsleeve">' . __('Manga derecha', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_rightsleeve_file" id="custom_print_area_rightsleeve_file" /></div>';
    
    echo '<div class="custom_print_area_leftchest hidden-uploads">';
    echo '<label for="custom_print_area_leftchest">' . __('Pecho izquierdo', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_leftchest_file" id="custom_print_area_leftchest_file" /></div>';
    
    echo '<div class="custom_print_area_rightchest hidden-uploads">';
    echo '<label for="custom_print_area_rightchest">' . __('Pecho derecho', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_rightchest_file" id="custom_print_area_rightchest_file" /></div>';
    
    echo '<div class="custom_print_area_customposition hidden-uploads">';
    echo '<label for="custom_print_area_customposition">' . __('Personalizado', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_customposition_file" id="custom_print_area_customposition_file" /></div>';
    
    echo '<div class="custom_print_area_allover hidden-uploads">';
    echo '<label for="custom_print_area_allover">' . __('Todo', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_allover_file" id="custom_print_area_allover_file" /></div>';
    echo '<div class="custom-product-notice hidden-uploads">Accepted file types: jpg, png, pdf, eps, ai, jpeg, Svg, Tiff and  Maximum file size: 64 MB. <br>
        Artwork must be vectorized PDF and scaled to print size or additional artwork charges will apply.</div>';
    echo '</div>';
    // Output the hidden input field
    echo '<input type="hidden" name="custom_hidden_field" id="custom_hidden_field" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_hidden_field_for_qty" id="custom_hidden_field_for_qty" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_centerfront_file_hidden" id="custom_print_area_centerfront_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_centerback_file_hidden" id="custom_print_area_centerback_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_leftsleeve_file_hidden" id="custom_print_area_leftsleeve_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_rightsleeve_file_hidden" id="custom_print_area_rightsleeve_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_leftchest_file_hidden" id="custom_print_area_leftchest_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_rightchest_file_hidden" id="custom_print_area_rightchest_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_customposition_file_hidden" id="custom_print_area_customposition_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_allover_file_hidden" id="custom_print_area_allover_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="varids[]" id="varids" value="' . esc_attr($dynamic_value) . '" />';   
}

function render_general_form(){
    global $product;

    echo '<div class="custom-options-general">';

    echo '<label for="where_we_print">¿Dónde debemos imprimir su obra? : </label>';
    echo '<textarea id="where_we_print" name="where_we_print" rows="4" cols="50"></textarea>';

    // Print Area options with individual file upload fields
    echo '<div class="custom-options-printarea-upload">';
    echo '<h4 class="custom-options-printarea-upload-h4">' . __('Cargue su obra de arte (opcional)', 'woocommerce') . '</h4>';

    echo '<div class="custom_print_area_centerfront">';
    echo '<label for="custom_print_area_centerfront_file">' . __('Obra de arte 1', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_centerfront_file" id="custom_print_area_centerfront_file" /></div>';
    
    echo '<div class="custom_print_area_centerback">';
    echo '<label for="custom_print_area_centerback_file">' . __('Obra de arte 2', 'woocommerce') . '</label>';
    echo '<input type="file" name="custom_print_area_centerback_file" id="custom_print_area_centerback_file" /></div>';
    
    echo '</div>';
    $dynamic_value = '';
    
    echo '<input type="hidden" name="custom_hidden_field" id="custom_hidden_field" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_hidden_field_for_qty" id="custom_hidden_field_for_qty" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_centerfront_file_hidden" id="custom_print_area_centerfront_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
    echo '<input type="hidden" name="custom_print_area_centerback_file_hidden" id="custom_print_area_centerback_file_hidden" value="' . esc_attr($dynamic_value) . '" />';

    echo '</div>';
}

function render_misc_form(){
    global $product;

    echo '<div class="custom-options-general">';

        echo '<label for="where_we_print">¿Dónde debemos imprimir su obra? : </label>';
        echo '<textarea id="where_we_print" name="where_we_print" rows="4" cols="50"></textarea>';

        // Print Area options with individual file upload fields
        echo '<div class="custom-options-printarea-upload">';
            echo '<h4 class="custom-options-printarea-upload-h4">' . __('Cargue su obra de arte (opcional)', 'woocommerce') . '</h4>';

            echo '<div class="custom_print_area_centerfront">';
            echo '<label for="custom_print_area_centerfront_file">' . __('Obra de arte 1', 'woocommerce') . '</label>';
            echo '<input type="file" name="custom_print_area_centerfront_file" id="custom_print_area_centerfront_file" /></div>';
            
            echo '<div class="custom_print_area_centerback">';
            echo '<label for="custom_print_area_centerback_file">' . __('Obra de arte 2', 'woocommerce') . '</label>';
            echo '<input type="file" name="custom_print_area_centerback_file" id="custom_print_area_centerback_file" /></div>';
        
        echo '</div>';
        $dynamic_value = '';
        echo '<input type="hidden" name="custom_hidden_field" id="custom_hidden_field" value="' . esc_attr($dynamic_value) . '" />';
        echo '<input type="hidden" name="custom_hidden_field_for_qty" id="custom_hidden_field_for_qty" value="' . esc_attr($dynamic_value) . '" />';
        echo '<input type="hidden" name="custom_print_area_centerfront_file_hidden" id="custom_print_area_centerfront_file_hidden" value="' . esc_attr($dynamic_value) . '" />';
        echo '<input type="hidden" name="custom_print_area_centerback_file_hidden" id="custom_print_area_centerback_file_hidden" value="' . esc_attr($dynamic_value) . '" />';

        echo '<div class="custom-product-notice-misc">Accepted file types: jpg, png, pdf, eps, ai, jpeg, Svg, Tiff and  Maximum file size: 64 MB. <br>
        Artwork must be vectorized PDF and scaled to print size or additional artwork charges will apply.</div>';

    echo '</div>';
}

function render_printarea_basecolor_sizes($product_id, $product, $available_variations, $category_slugs){
    global $product;
    $child_theme_uri = get_stylesheet_directory_uri();
    $color_sizes_data = array(); // Associative array to store color and size data
    // $variations_ids = [];
    if (!empty($available_variations)) {
        foreach ($available_variations as $variation) {
            $variation_id = $variation['variation_id'];
            $variation_attributes = $variation['attributes'];
            // var_dump($variation_attributes, $variation_id);
            $color = '';
            $size = '';

            foreach ($variation_attributes as $attribute_name => $attribute_value) {
                if ($attribute_name == 'attribute_color') {
                    $color = $attribute_value;
                }
                if ($attribute_name == 'attribute_talla') {
                    $size = $attribute_value;
                }
            }

            if (!empty($color) && !empty($size)) {
                $Key = 'custom_size_' . $size . '_' . $color;
                // var_dump($Key);
                // Store data in the color_sizes_data array
                if (!isset($color_sizes_data[$color])) {
                    $color_sizes_data[$color] = array(
                        'label' => wc_attribute_label('pa_color', $color), // Replace 'pa_color' with your actual attribute slug
                        'image_url' => get_image_url_from_id($variation['image_id']),
                        'sizes' => array(),
                    );
                }
                
                $color_sizes_data[$color]['sizes'][] = array(
                    'size' => $size,
                    'Key' => $Key,
                    'var_id' => $variation_id,
                );
            }
            // var_dump($color_sizes_data['White-Grey']['sizes']);
        }
    }
    echo '<h4>' . __('Color de base', 'woocommerce') . '</h4>';
    echo '<div class="custom-options-basecolor">';
    // drawing the base color images
    /**
     * <input type="checkbox" name="custom_base_color[]" id="custom_base_color_' . $color . '" value="' . $color . '" data-variation-id="' . $variation_id . '">
     */
    foreach ($color_sizes_data as $color => $data) {            
        echo '<div class="label-container">';
        echo '<label for="custom_base_color_' . $color . '">
            <input type="checkbox" name="custom_base_color[]" id="custom_base_color_' . $color . '" value="' . $color . '">
            <div class="circle-container">
                <img src="'. $data['image_url'] .'" alt="custom_base_color_' . $color . '" width="50px" height="50px">' . $color . '</label>
            </div></div>';
        
    }
    echo '</div>';

    // drawing the size boxes
    foreach ($color_sizes_data as $color => $data) {
        echo '<div class="custom-options-color-container custom-options-sizes-' . $color . '">';
        echo '<div class="custom-options-sizes-heading custom-options-sizes-' . $color . '">Sizes for ' . ucwords(str_replace("-", " ", $color)) . '</div>';
        
        echo '<div class="custom-options-size-row">';
        foreach ($data['sizes'] as $size_data) {
            echo '<div class="custom-options-size-' . $color . '">';
            echo '<label for="' . $size_data['Key'] . '">' . __('Qty (' . strtoupper($size_data['size']) . ')', 'woocommerce') . '</label>';
            echo '<input type="number" name="' . $size_data['Key'] . '" id="' . $size_data['Key'] . '" step="1" min="0" value="0" data-variation-id= "' . $size_data['var_id'] . '"/>';
            echo '</div>';
        }
        echo '</div>';            
        echo '</div>';
    }

    $specified_slugs_hoodies = array('sudaderas-con-capucha', 'sudaderas-personalizadas', 'polares');
    $specified_slugs_caps = array('gorras-personalizadas');
    $specified_slugs_bags = array('bolsas');
    $specified_slugs_tshirts = array('camisetas-personalizadas');
    $specified_slugs_jackets = array('chaquetas');
    $specified_slugs_polos = array('polos-bordados-personalizados');
    $dynamic_value = [];

    // var_dump($category_slugs);
    if (is_array($category_slugs)) {
        $category_slugs_string = $category_slugs;
    } else {
        $category_slugs_string = $category_slugs;
    }
    // var_dump(array($category_slugs_string));
    // var_dump($specified_slugs_polos);
    // var_dump($category_slugs);
    if (count(array_intersect(($category_slugs_string), $specified_slugs_hoodies)) > 0) {
        print_area_display_hoodies($child_theme_uri);
        upload_area_display_tshirt($dynamic_value);
    } elseif (count(array_intersect(($category_slugs_string), $specified_slugs_tshirts)) > 0) {
        print_area_display_tshirt($child_theme_uri);
        upload_area_display_tshirt($dynamic_value);
    } elseif (count(array_intersect(($category_slugs_string), $specified_slugs_jackets)) > 0) {
        print_area_display_jackets($child_theme_uri);
        upload_area_display_jackets($dynamic_value);
    } elseif (count(array_intersect(($category_slugs_string), $specified_slugs_polos)) > 0) {
        print_area_display_polos($child_theme_uri);
        upload_area_display_tshirt($dynamic_value);
    } elseif (count(array_intersect(($category_slugs_string), $specified_slugs_caps)) > 0) {
        print_area_display_cap($child_theme_uri);
        upload_area_display_caps_bags($dynamic_value);
    } elseif (count(array_intersect(($category_slugs_string), $specified_slugs_bags)) > 0) {
        print_area_display_bag($child_theme_uri);
        upload_area_display_caps_bags($dynamic_value);
    } else {
        render_misc_form($child_theme_uri);
    }
}

function display_custom_product_options() {
  	if (is_customize_preview()) {
        // We are in the Customizer preview, handle accordingly
        return;
    }
	
  	$child_theme_uri = get_stylesheet_directory_uri();
    global $product;
    if (!empty($product) && is_a($product, 'WC_Product')) {
        $product_id = $product->get_id();
        $product_type = is_simple_or_variable_product($product_id);
        if ($product_type === 'simple') {
            render_general_form();
            // var_dump("Product is simple product");
        } elseif ($product_type === 'variable') {
            // $category_slugs = get_product_category_slug($product_id);
            $terms = wp_get_post_terms($product_id, 'product_cat');

            // Extract the slugs from the term objects
            $category_slugs = array_map(function ($term) {
                return $term->slug;
            }, $terms);
            // var_dump($category_slugs);
            $available_variations = $product->get_available_variations();
            // $selected_category = has_term('camisetas-personalizadas', 'product_cat', $product_id);
            // $is_print_area = TRUE;
            render_printarea_basecolor_sizes($product_id, $product, $available_variations, $category_slugs);
            
            // var_dump("Product has variables");
        } else {
            print_r("for other products");
        }
    }
}

add_action('woocommerce_before_add_to_cart_button', 'display_custom_product_options');

// add_action('woocommerce_before_cart', 'add_variations_to_cart');

// add_action('woocommerce_add_cart_item_data', 'add_variations_to_cart');



// WC_Cart->add_to_cart( $product_id = 31308, $quantity = 1, $variation_id = 31316, $variation = ['attribute_talla' => 'XS', 'attribute_color' => 'Navy-White'], $cart_item_data = ??? )
// Hook to add custom options to cart item data
add_filter('woocommerce_add_cart_item_data', 'add_custom_options_to_cart_item_data', 20, 3);

// add_filter('woocommerce_add_cart_item_data', 'add_data_to_cart', 20, 3);

// add_filter('woocommerce_add_to_cart', 'add_data_to_cart_v2', 10, 6);
function add_data_to_cart_v2($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data){
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

    // Log the values to the browser console
    echo '<script>';
    echo 'console.log("Variation ID: ' . $variation_id . ' | variation: ' . $variation. ' | cart item data: ' . $cart_item_data . '");';
    echo '</script>';

    // $quantity = isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 1;
    // $variation_id = 0; // Initialize the variation ID

    // For variable products, handle variations based on the selected checkboxes and quantities

    // $sizes = array('xs', 's', 'm', 'l', 'xl', '2xl', '3xl', '4xl');
    $sizes = [];

    $product = wc_get_product($product_id);
    
    // Loop through product variations
    foreach ($product->get_available_variations() as $variation) {
        $variation_attributes = $variation['attributes'];
        $variation_attributes = array_filter( $variation_attributes );
        foreach ($variation_attributes as $attribute_name => $attribute_value) {
            // Assuming the attribute name is 'attribute_talla'
            if ($attribute_name == 'attribute_talla' && !in_array($attribute_value, $sizes)) {
                // Add unique sizes to the $sizes array
                $sizes[] = $attribute_value;
            }
        }
    }

    if (!empty($_POST['custom_base_color'])) {
        foreach ($_POST['custom_base_color'] as $color) {
            foreach ($sizes as $size) {
                $qty_key = 'custom_size_' . $size . '_' . $color; 
                // Use a unique key for each size and color combination
    
                // Ensure the quantity is set and greater than 0
                if (isset($_POST[$qty_key]) && intval($_POST[$qty_key]) > 0) {
                    $attributes = array(
                        'attribute_color' => $color,
                        'attribute_talla' => $size,
                    );
    
                    $variation_id = get_variation_id_by_attributes($product_id, $attributes);
    
                    // Check if the variation ID is valid before adding to the cart
                    if ($variation_id) {
                        // Add the product to the cart with the specified quantity and variation
                        WC()->cart->add_to_cart($product_id, intval($_POST[$qty_key]), $variation_id);
                        
                    }
                }
            }
        }
    }
    
    return $cart_item_data;
}
function add_custom_options_to_cart_item_data($cart_item_data, $product_id, $variation_id) {
    // Process and store custom options here
    // var_dump($_POST['custom_base_color']);
    die('Function called by woocommerce_add_cart_item_data ');
    $custom_options = array(
        'print_area' => isset($_POST['custom_print_area']) ? $_POST['custom_print_area'] : array(),
        'base_color' => isset($_POST['custom_base_color']) ? $_POST['custom_base_color'] : array(),
        'where_we_print' => isset($_POST['where_we_print']) ? $_POST['where_we_print'] : '',
        'sizes' => array(),
      	'file_names' => array(),
    );
    //'file_name' => isset($_FILES['file']['name']) ? sanitize_file_name($_FILES['file']['name']) : '', // Add file name
    $sizes = array('xs', 's', 'm', 'l', 'xl', '2xl', '3xl', '4xl');
    
    // Check if there are multiple base colors
    // var_dump(count($custom_options['base_color']));
    if (count($custom_options['base_color']) > 0) {
        // Handle multiple base colors
        foreach ($custom_options['base_color'] as $color) {
            $custom_options['sizes'][$color] = array();
            
            foreach ($sizes as $size) {
                $qty_key = 'custom_size_' . $size . '_' . $color; // Use a unique key for each size and color combination
                $qty = isset($_POST[$qty_key]) ? intval($_POST[$qty_key]) : 0;
                $custom_options['sizes'][$color][$size] = $qty;
            }
        }
    } else {
        // If there's only one base color, proceed as before
        $custom_options['sizes'][$custom_options['base_color'][0]] = array(); // Initialize the sizes array for the single base color
        foreach ($sizes as $size) {
            $qty_key = 'custom_size_' . $size;
            $qty = isset($_POST[$qty_key]) ? intval($_POST[$qty_key]) : 0;
            $custom_options['sizes'][$custom_options['base_color'][0]][$size] = $qty; // Store size data under the single base color
        }
    }

    

    if (isset($_POST['custom_print_area_centerfront_file_hidden'])) {
        $custom_options['custom_print_area_centerfront_file'] = sanitize_text_field($_POST['custom_print_area_centerfront_file_hidden']);
    }
    if (isset($_POST['custom_print_area_centerback_file_hidden'])) {
        $custom_options['custom_print_area_centerback_file'] = sanitize_text_field($_POST['custom_print_area_centerback_file_hidden']);
    }
    if (isset($_POST['custom_print_area_leftsleeve_file_hidden'])) {
        $custom_options['custom_print_area_leftsleeve_file'] = sanitize_text_field($_POST['custom_print_area_leftsleeve_file_hidden']);
    }
    if (isset($_POST['custom_print_area_rightsleeve_file_hidden'])) {
        $custom_options['custom_print_area_rightsleeve_file'] = sanitize_text_field($_POST['custom_print_area_rightsleeve_file_hidden']);
    }
    if (isset($_POST['custom_print_area_leftchest_file_hidden'])) {
        $custom_options['custom_print_area_leftchest_file'] = sanitize_text_field($_POST['custom_print_area_leftchest_file_hidden']);
    }
    if (isset($_POST['custom_print_area_rightchest_file_hidden'])) {
        $custom_options['custom_print_area_rightchest_file'] = sanitize_text_field($_POST['custom_print_area_rightchest_file_hidden']);
    }

    if (isset($_POST['custom_print_area_customposition_file_hidden'])) {
        $custom_options['custom_print_area_customposition_file'] = sanitize_text_field($_POST['custom_print_area_customposition_file_hidden']);
    }

    if (isset($_POST['custom_print_area_allover_file_hidden'])) {
        $custom_options['custom_print_area_allover_file'] = sanitize_text_field($_POST['custom_print_area_allover_file_hidden']);
    }


    // var_dump($cart_item_data);
    // var_dump($custom_options);
    $cart_item_data = isset($cart_item_data) && is_array($cart_item_data) ? $cart_item_data : array();
    $cart_item_data['custom_options'] = array($custom_options);
    // Make sure $cart_item_data is initialized as an array
    

    // // var_dump($custom_options);
    // // Set custom options within $cart_item_data
    // $cart_item_data['custom_options'] = $custom_options;
    // if (isset($_POST['custom_hidden_field'])) {
    //     $cart_item_data['custom_temp_price'] = sanitize_text_field($_POST['custom_hidden_field']);
    // }
    // // Calculate and set the total quantity for the cart item based on selected sizes

    // if (isset($_POST['custom_hidden_field_for_qty'])) {
    //     $cart_item_data['quantity'] = sanitize_text_field($_POST['custom_hidden_field_for_qty']);
    // }
    
    // var_dump($custom_options);
    return $cart_item_data;
}

// add_filter('woocommerce_add_cart_item_data', 'add_custom_options_to_cart_item_data_v2', 10, 3);
function add_custom_options_to_cart_item_data_v2($cart_item_data, $product_id, $variation_id) {
    // Initialize an array to store the custom options for each combination of color and size
    $custom_options_per_combination = array();

    // Retrieve the product variations
    $product = wc_get_product($product_id);

    // Check if the product is a variable product
    if ($product->is_type('variable')) {
        $variation = wc_get_product($variation_id);

        // Get selected color and size from the variation attributes
        $color = $variation->get_attribute('pa_color');
        $size = $variation->get_attribute('pa_size');

        // Define a unique identifier for each combination of color and size
        $combination_identifier = $product_id . '-' . $variation_id . '-' . $color . '-' . $size;

        // Check if the combination identifier is already in the cart
        if (isset($cart_item_data['custom_options_per_combination'][$combination_identifier])) {
            // If it exists, increment the quantity for that combination
            $cart_item_data['custom_options_per_combination'][$combination_identifier]['quantity'] += 1;
        } else {
            // If it doesn't exist, create a new entry
            $custom_options_per_combination[$combination_identifier] = array(
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'quantity' => 1, // You may adjust the quantity as needed
                'data' => $product,
                'custom_options' => array(
                    'color' => $color,
                    'size' => $size,
                    // Add other custom options here
                ),
            );
        }

        // Store the custom options per combination in the cart item data
        $cart_item_data['custom_options_per_combination'] = $custom_options_per_combination;
    }
    
    return $cart_item_data;
}


add_action('woocommerce_add_to_cart_validation', 'add_variations_to_cart_v2', 20, 1);

// Get variation data using variation ID
function get_variation_data($variation_id) {
    // Get variation object
    $variation = wc_get_product_variation_attributes($variation_id);

    // Check if variation exists
    if ($variation) {
        // Extract variation attributes
        $attributes = array();
        foreach ($variation as $attribute => $value) {
            $attributes[$attribute] = $value;
        }
        return $attributes;
    } else {
        return false; // Variation not found
    }
}

// woocommerce_add_cart_item_data 
function add_variations_to_cart_v2($cart_item_data) {
    die('Function called by woocommerce_add_to_cart_validation ');
    var_dump($cart_item_data);
    // Check if the add to cart button is clicked
    if (isset($_POST['add-to-cart']) && isset($_POST['custom_hidden_field'])) {

        

        // Retrieve the variation data from the hidden input
        $variation_data = json_decode(stripslashes($_POST['custom_hidden_field']), true);

        // Loop through each variation data
        foreach ($variation_data as $variation_id => $qty) {
            // Ensure the quantity is greater than 0
            if ($qty > 0 && $variation_id !== 'undefined') {
                WC()->cart->add_to_cart($_POST['product_id'], $qty, $variation_id, get_variation_data($variation_id), $cart_item_data);
            }
        }

        

        // Add custom message
        wc_add_notice(__('Product successfully added to your cart.', 'your-text-domain'), 'success');
        wp_safe_redirect(wc_get_cart_url());
        exit; // Make sure to exit after redirecting
    }
}

function add_variations_to_cart() {
    // die('Function called');
    // Check if the add to cart button is clicked
    if (isset($_POST['add-to-cart']) && isset($_POST['custom_hidden_field'])) {
        // Retrieve the variation data from the hidden input
        $variation_data = json_decode(stripslashes($_POST['custom_hidden_field']), true);

        // Loop through each variation data
        foreach ($variation_data as $variation_id => $qty) {
            // Ensure the quantity is greater than 0
            if ($qty > 0 && $variation_id !== 'undefined') {
                // Add variation to the cart
                // var_dump($_POST['product_id'], $qty, $variation_id);
                WC()->cart->add_to_cart($_POST['product_id'], $qty, $variation_id, get_variation_data($variation_id), []);
            }
        }
    }
}


// Redirect to cart page after adding product to cart
add_filter('woocommerce_add_to_cart_redirect', 'custom_add_to_cart_redirect');

function custom_add_to_cart_redirect($url) {
    $url = wc_get_cart_url(); // Redirect to the cart page
    return $url;
}


function getDownloadlink($file_name, $area){
    // $file_path = wp_get_upload_dir()['basedir'] . '/customerDesign/' . $file_name; // Update with your upload directory
    $file_path = '/wp-content/uploads/customerDesigns/' . $file_name; // Update with your upload directory
    $download_link = '<a href="' . esc_url($file_path) . '" target="_blank">Download ' . $area . ' File</a>';
    return $download_link;
}

// Hook to display custom options in the cart
// add_filter('woocommerce_get_item_data', 'display_custom_options_in_cart_page', 10, 2);
function display_custom_options_in_cart_page($cart_data, $cart_item) {
    // var_dump($product);
    if (isset($cart_item['custom_options'])) {
        $custom_options = $cart_item['custom_options'];
        // var_dump($custom_options);
        // Display Print Area
        if (!empty($custom_options['print_area'])) {
            $cart_data[] = array(
                'name' => __('Print Area', 'woocommerce'),
                'value' => implode(', ', $custom_options['print_area']),
            );
        }
        

      	// Display Base Color
        if (isset($custom_options['base_color'])) {
            $value = $custom_options['base_color'];

            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (!empty($value)){
                $cart_data[] = array(
                    'name' => __('Base Color', 'woocommerce'),
                    'value' => $value,
                );
            }
        }
      	

        if (!empty($custom_options['sizes'])) {
            $size_data = array();
            $sizes = array('xs', 's', 'm', 'l', 'xl', '2xl', '3xl');
        
            if (count($custom_options['base_color']) > 0) {
                // Handle multiple base colors
                foreach ($custom_options['base_color'] as $color) {
                    $color_size_data = array();
                    foreach ($sizes as $size) {
                        $qty_key = 'custom_size_' . $size . '_' . $color; // Use the unique key for each size and color combination
                        if (isset($custom_options['sizes'][$color][$size]) && $custom_options['sizes'][$color][$size] > 0) {
                            $color_size_data[] = strtoupper($size) . ': ' . $custom_options['sizes'][$color][$size];
                        }
                    }
                    if (!empty($color_size_data)) {
                        $size_data[] = 'Color ' . $color . ': ' . implode(', ', $color_size_data);
                    }
                }
            } else {
                // Handle a single base color
                foreach ($sizes as $size) {
                    if (isset($custom_options['sizes'][$size]) && $custom_options['sizes'][$size] > 0) {
                        $size_data[] = strtoupper($size) . ': ' . $custom_options['sizes'][$size];
                    }
                }
            }
        
            if (!empty($size_data)) {
                $cart_data[] = array(
                    'name' => __('Sizes :', 'woocommerce'),
                    'value' => implode('<br>', $size_data), // Use <br> to separate color/size combinations
                );
            }
        }
        

        if (!empty($custom_options['custom_print_area_centerfront_file'])) {
            if (isset($custom_options['where_we_print']) && strlen($custom_options['where_we_print']) > 0) {
                $cart_data[] = array(
                    'name' => __('Obra de arte 1', 'woocommerce'),
                    'value' => getDownloadlink($custom_options['custom_print_area_centerfront_file'], 'Obra_de_arte_1'),
                );
            } else {    
                $cart_data[] = array(
                    'name' => __('Centerfront_file', 'woocommerce'),
                    'value' => getDownloadlink($custom_options['custom_print_area_centerfront_file'], 'centerfront'),
                );
            }
        }
        if (!empty($custom_options['custom_print_area_centerback_file'])) {
            if (isset($custom_options['where_we_print']) && strlen($custom_options['where_we_print']) > 0) {
                $cart_data[] = array(
                    'name' => __('Obra de arte 2', 'woocommerce'),
                    'value' => getDownloadlink($custom_options['custom_print_area_centerback_file'], 'Obra_de_arte_2'),
                );
            } else {
                $cart_data[] = array(
                    'name' => __('Centerback_file', 'woocommerce'),
                    'value' => getDownloadlink($custom_options['custom_print_area_centerback_file'], 'centerback'),
                );
            }
        }

        if (!empty($custom_options['custom_print_area_leftsleeve_file'])) {
            $cart_data[] = array(
                'name' => __('leftsleeve_file', 'woocommerce'),
                'value' => getDownloadlink($custom_options['custom_print_area_leftsleeve_file'], 'leftsleeve'),
            );
        }

        
        if (!empty($custom_options['custom_print_area_rightsleeve_file'])) {
            $cart_data[] = array(
                'name' => __('rightsleeve_file', 'woocommerce'),
                'value' => getDownloadlink($custom_options['custom_print_area_rightsleeve_file'], 'rightsleeve'),
            );
        }
        if (!empty($custom_options['custom_print_area_leftchest_file'])) {
            $cart_data[] = array(
                'name' => __('leftchest_file', 'woocommerce'),
                'value' => getDownloadlink($custom_options['custom_print_area_leftchest_file'], 'leftchest'),
            );
        }
        if (!empty($custom_options['custom_print_area_rightchest_file'])) {
            $cart_data[] = array(
                'name' => __('rightchest_file', 'woocommerce'),
                'value' => getDownloadlink($custom_options['custom_print_area_rightchest_file'], 'rightchest'),
            );
        }

        if (!empty($custom_options['custom_print_area_customposition_file'])) {
            $cart_data[] = array(
                'name' => __('custom_position_file', 'woocommerce'),
                'value' => getDownloadlink($custom_options['custom_print_area_customposition_file'], 'custom_position'),
            );
        }

        if (!empty($custom_options['custom_print_area_allover_file'])) {
            $cart_data[] = array(
                'name' => __('allover_position_file', 'woocommerce'),
                'value' => getDownloadlink($custom_options['custom_print_area_allover_file'], 'allover'),
            );
        }

        if (!empty($custom_options['total_qty'])) {
            $cart_data[] = array(
                'name' => __('Total Qty', 'woocommerce'),
                'value' => $custom_options['total_qty'],
            );
        } else {
            $cart_data[] = array(
                'name' => __('Total Qty', 'woocommerce'),
                'value' => $cart_item['quantity'],
            );
        }
        // $_POST['where_we_print'] $cart_item_data['quantity']; cart_item
        // if (isset($custom_options['where_we_print'])) {
        if (isset($custom_options['where_we_print']) && strlen($custom_options['where_we_print']) > 0) {
            $value = $custom_options['where_we_print'];
            $cart_data[] = array(
                'name' => __('Where We should Print', 'woocommerce'),
                'value' => $value,
            );
        }
      	
        if (isset($_POST['custom_hidden_field'])) {
          $product_id = isset($_POST['add-to-cart']) ? intval($_POST['add-to-cart']) : 0;

          if ($product_id > 0) {
              $new_price = sanitize_text_field($_POST['custom_hidden_field']);
              update_post_meta($product_id, '_price', $new_price);
            }
        }

    }  
    // var_dump($custom_options);
    return $cart_data;
}
// add_filter('woocommerce_add_cart_item_data', 'add_custom_qty_to_cart_item_data', 10, 3);
function add_custom_qty_to_cart_item_data($cart_item_data, $product_id, $variation_id) {
    // Check if custom quantity data is being sent
    if (isset($_POST['custom_hidden_field_for_qty'])) {
        // Get the cart item quantity from the posted data
        $new_qty = intval($_POST['custom_hidden_field_for_qty']);

        // Ensure the quantity is greater than 0
        if ($new_qty > 0) {
            $cart_item_data['quantity'] = $new_qty;
        }
    }

    return $cart_item_data;
}

// Function to add custom print area file if not blank
function add_custom_print_area_file($item_id, $file, $area_name) {
    if (!empty($file)) {
        wc_add_order_item_meta($item_id, 'Custom Print Area ' . ucfirst($area_name) . ' File', getDownloadlink($file, $area_name));
    }
}

// Add this to your theme's functions.php file or a custom plugin
// add_action('woocommerce_before_single_product', 'add_nonce_and_variation_fields');

function add_nonce_and_variation_fields() {
    // Add nonce field to the product form
    wp_nonce_field('add_to_cart_nonce', 'security', true, true);

    // Add hidden field for variation ID
    echo '<input type="hidden" name="variation_id" value="" />';
}


// Add this to your theme's functions.php file or a custom plugin


// Function to get the variation ID based on attributes
// function get_variation_id_by_attributes($product_id, $attributes) {
//     $variation_id = 0;
//     $product = wc_get_product($product_id);

//     foreach ($product->get_variation_attributes() as $attribute_name => $attribute_values) {
//         // Check if the attribute key exists in the provided attributes
//         if (isset($attributes[$attribute_name])) {
//             $attribute_value = $attributes[$attribute_name];
    
//             // Check if the attribute value matches the variation attribute
//             if ($attribute_value === $attribute_values) {
//                 continue; // Attribute matches, continue with the next attribute
//             } else {
//                 $match = false; // Attribute doesn't match, break the loop
//                 break;
//             }
//         } else {
//             // If the attribute key doesn't exist, consider it a mismatch
//             $match = false;
//             break;
//         }
//     }

//     if ($match) {
//         $variation_id = $variation['variation_id'];
//         break;
//     }

//     // var_dump($attributes);
//     // Loop through product variations
//     // foreach ($product->get_available_variations() as $variation) {
//     //     $match = true;

//     //     // Check if variation attributes match
//     //     foreach ($attributes as $attribute_name => $attribute_value) {
//     //         // var_dump($attribute_name);
//     //         // var_dump($attribute_value);
//     //         if (isset($attributes[$attribute_name])) {
//     //             $attribute_value = $attributes[$attribute_name];
//     //             if (in_array($attribute_value, $attribute_values)) {
//     //                 if ($variation['attributes'][$attribute_name] !== $attribute_value) {
//     //                     $match = false;
//     //                     break;
//     //                 }
//     //             } else {
//     //                 $match = false; // Attribute doesn't match, break the loop
//     //                 break;
//     //             }
//     //         } else {
//     //             // If the attribute key doesn't exist, consider it a mismatch
//     //             $match = false;
//     //             break;
//     //         }

//     //         // if (isset($variation['attributes'][$attribute_name])) {
//     //         //     if ($variation['attributes'][$attribute_name] !== $attribute_value) {
//     //         //         $match = false;
//     //         //         break;
//     //         //     }
//     //         // } else {
//     //         //     // If the attribute key doesn't exist, consider it a mismatch
//     //         //     $match = false;
//     //         //     break;
//     //         // }
//     //     }

//     //     // If attributes match, set the variation ID
//     //     if ($match) {
//     //         $variation_id = $variation['variation_id'];
//     //         break;
//     //     }
//     // }

//     return $variation_id;
// }

function get_variation_id_by_attributes($product_id, $attributes) {
    $variation_id = 0;
    $product = wc_get_product($product_id);

    // Loop through product variations
    foreach ($product->get_available_variations() as $variation) {
        $match = true;

        // Check if variation attributes match
        
        foreach ($attributes as $attribute_name => $attribute_value) {
            // Check if the attribute key exists in the provided attributes
            if (isset($variation['attributes'][$attribute_name])) {
                $variation_attribute_value = $variation['attributes'][$attribute_name];

                // Check if the attribute value matches the variation attribute
                if ($attribute_value === $variation_attribute_value) {
                    continue; // Attribute matches, continue with the next attribute
                } else {
                    $match = false; // Attribute doesn't match, break the loop
                    break;
                }
            } else {
                // If the attribute key doesn't exist, consider it a mismatch
                $match = false;
                break;
            }
        }

        // If attributes match, set the variation ID
        if ($match) {
            $variation_id = $variation['variation_id'];
            break;
        }
    }

    return $variation_id;
}


// Hook to store custom options with the order 
// wc_add_order_item_meta($item_id, 'Test Area', $values);
// function save_custom_options_to_order_item_meta($item_id, $cart_item_key, $values, $order) 
function save_custom_options_to_order_item_meta($item_id, $values, $cart_item_key) {
    // var_dump($values);
  	if (isset($values['custom_options'])) {
        $custom_options = $values['custom_options'];
        // var_dump($values);
      	error_log('Custom Options Data: ' . print_r($custom_options, true));
        // Save custom options data
      	
        if (!empty($custom_options)) {
            
            wc_add_order_item_meta($item_id, 'Print Area', implode(', ', $custom_options['print_area']));
            wc_add_order_item_meta($item_id, 'Base Color', implode(', ', $custom_options['base_color']));
            if(!empty($custom_options['where_we_print'])){
                wc_add_order_item_meta($item_id, 'Where should we print', $custom_options['where_we_print']);
            }
            
            // Add custom print area files if not blank
            if (!empty($custom_options['custom_print_area_centerfront_file'])) {
                if (isset($custom_options['where_we_print']) && strlen($custom_options['where_we_print']) > 0) {
                    add_custom_print_area_file($item_id, $custom_options['custom_print_area_centerfront_file'], 'Obra_de_arte_1');
                } else {    
                    add_custom_print_area_file($item_id, $custom_options['custom_print_area_centerfront_file'], 'centerfront');
                }
            }
            if (!empty($custom_options['custom_print_area_centerback_file'])) {
                if (isset($custom_options['where_we_print']) && strlen($custom_options['where_we_print']) > 0) {
                    add_custom_print_area_file($item_id, $custom_options['custom_print_area_centerback_file'], 'Obra_de_arte_2');
                } else {
                    add_custom_print_area_file($item_id, $custom_options['custom_print_area_centerback_file'], 'centerback');
                }
            }
            // add_custom_print_area_file($item_id, $custom_options['custom_print_area_centerfront_file'], 'centerfront');
            // add_custom_print_area_file($item_id, $custom_options['custom_print_area_centerback_file'], 'centerback');
            add_custom_print_area_file($item_id, $custom_options['custom_print_area_leftsleeve_file'], 'leftsleeve');
            add_custom_print_area_file($item_id, $custom_options['custom_print_area_rightsleeve_file'], 'rightsleeve');
            add_custom_print_area_file($item_id, $custom_options['custom_print_area_leftchest_file'], 'leftchest');
            add_custom_print_area_file($item_id, $custom_options['custom_print_area_rightchest_file'], 'rightchest');
            add_custom_print_area_file($item_id, $custom_options['custom_print_area_customposition_file'], 'custom');
            add_custom_print_area_file($item_id, $custom_options['custom_print_area_allover_file'], 'allover');

            // wc_add_order_item_meta($item_id, 'custom_print_area_centerfront_file', getDownloadlink($custom_options['custom_print_area_centerfront_file'], 'centerfront'));
            // wc_add_order_item_meta($item_id, 'custom_print_area_centerback_file', getDownloadlink($custom_options['custom_print_area_centerback_file'], 'centerback'));
            // wc_add_order_item_meta($item_id, 'custom_print_area_leftsleeve_file', getDownloadlink($custom_options['custom_print_area_leftsleeve_file'], 'leftsleeve'));
            // wc_add_order_item_meta($item_id, 'custom_print_area_rightsleeve_file', getDownloadlink($custom_options['custom_print_area_rightsleeve_file'], 'rightsleeve'));
            // wc_add_order_item_meta($item_id, 'custom_print_area_leftchest_file', getDownloadlink($custom_options['custom_print_area_leftchest_file'], 'leftchest'));
            // wc_add_order_item_meta($item_id, 'custom_print_area_rightchest_file', getDownloadlink($custom_options['custom_print_area_rightchest_file'], 'rightchest'));
          	// wc_add_order_item_meta($item_id, 'Final Price', $custom_options['custom_hidden_field']);

            // Loop through and save sizes/quantities
            if (isset($custom_options['sizes']) && is_array($custom_options['sizes'])) {
                foreach ($custom_options['sizes'] as $color => $color_sizes) {
                    if (!empty($color_sizes) && is_array($color_sizes)) {
                        foreach ($color_sizes as $size => $quantity) {
                            if (!empty($size) && is_numeric($quantity) && $quantity > 0) {
                                wc_add_order_item_meta($item_id, 'Size: ' . strtoupper($size) . ' (' . $color . ')', $quantity);
                            }
                        }
                    }
                }
            }
        }
        
        // $meta_keys_to_remove = array(
        //     'Color'
        // );
        // if (isset($values['Color'])) {
        //     unset($values['Color']);
        // }
        error_log('Print Area: ' . $print_area);
        error_log('Base Color: ' . $base_color);
		error_log('Size Data: ' . print_r($custom_options['sizes'], true));
    }
    
    $color_exists = wc_get_order_item_meta($item_id, 'Color', true);

    // Remove the "Color" meta key only if it exists
    if ($color_exists) {
        wc_remove_order_item_meta($item_id, 'Color');
    }
}
//add_action( 'woocommerce_checkout_create_order_line_item', 'save_custom_options_to_order_item_meta', 20, 3 );
add_action('woocommerce_add_order_item_meta', 'save_custom_options_to_order_item_meta', 20, 3);

function update_cart_item_price($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return; // Skip this in the admin

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (!empty($cart_item['custom_temp_price'])) {
            $cart_item['data']->set_price($cart_item['custom_temp_price']);
        }
    }
}

add_action('woocommerce_before_calculate_totals', 'update_cart_item_price', 10, 1);


// function for archieve category pages
function filter_products() {    
    $attribute_values = $_POST['attribute_value'];
    $data_attribute_slugs = $_POST['attribute_slug'];
    // $category_id = $_POST['category_id'];
    // $attribute_values = isset($_POST['attribute_value']) && is_array($_POST['attribute_value']) ? $_POST['attribute_value'] : array($_POST['attribute_value']);
    // $data_attribute_slugs = isset($_POST['attribute_slug']) && is_array($_POST['attribute_slug']) ? $_POST['attribute_slug'] : array($_POST['attribute_slug']);
    $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : 0;

    // echo 'Data type slug: <pre>' . print_r(($data_attribute_slugs), true) . '</pre>';
    // echo 'Data type value: <pre>' . print_r(($attribute_values), true) . '</pre>';
    // echo 'count: <pre>' . print_r(count($data_attribute_slugs), true) . '</pre>';
    // echo 'values: <pre>' . print_r($attribute_values, true) . '</pre>';

    // $args = array(
    //     'post_type'      => 'product',
    //     'posts_per_page' => 10,
    //     'product_cat'    => get_term($category_id)->slug,
    // );
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
        'product_cat'    => get_term($category_id)->slug,
    );
    $base_args = '';
    for ($i = 0; $i < count($attribute_values); $i++) {
        $base_args .= '&' . $data_attribute_slugs[$i] . '=' . $attribute_values[$i];
    }

    // $args['tax_query'] = array();
    if (count($data_attribute_slugs) > 3) {
        $args['tax_query'] = array(
            'relation' => 'AND', // Adjust the relation based on your needs (AND or OR)
            array(
                'taxonomy' => $data_attribute_slugs[0],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[0],
            ),
            array(
                'taxonomy' => $data_attribute_slugs[1],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[1],
            ),
            array(
                'taxonomy' => $data_attribute_slugs[2],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[2],
            ),
            array(
                'taxonomy' => $data_attribute_slugs[3],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[3],
            ),
        );
    } elseif (count($data_attribute_slugs) === 3) {
        $args['tax_query'] = array(
            'relation' => 'AND', // Adjust the relation based on your needs (AND or OR)
            array(
                'taxonomy' => $data_attribute_slugs[0],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[0],
            ),
            array(
                'taxonomy' => $data_attribute_slugs[1],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[1],
            ),
            array(
                'taxonomy' => $data_attribute_slugs[2],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[2],
            ),
        );
    } elseif(count($data_attribute_slugs) === 2){
        $args['tax_query'] = array(
            'relation' => 'AND', // Adjust the relation based on your needs (AND or OR)
            array(
                'taxonomy' => $data_attribute_slugs[0],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[0],
            ),
            array(
                'taxonomy' => $data_attribute_slugs[1],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[1],
            ),
        );
    } elseif(count($data_attribute_slugs) === 1){
        $args['tax_query'] = array(
            'relation' => 'AND', // Adjust the relation based on your needs (AND or OR)
            array(
                'taxonomy' => $data_attribute_slugs[0],
                'field' => 'slug', // Change to 'name' if your attribute values are names
                'terms' => $attribute_values[0],
            )
        );
    } else {
        echo "";
    }
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args['paged'] = $paged;

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
        wp_reset_postdata();

    } else {
        echo 'No products found.';
    }

    // 
    // 'base' => add_query_arg('paged', '%#%'), // Add this line to preserve other query parameters
    // if (!empty($attribute_values) && is_array($attribute_values) &&
    //     !empty($data_attribute_slugs) && is_array($data_attribute_slugs)) {
    //     $tax_queries = array('relation' => 'AND');
        
    //     $countt = 1;
    //     // Loop through each pair of attribute value and attribute slug
    //     foreach ($attribute_values as $key => $attribute_value) {
    //         echo 'loop count: <pre>' . print_r($countt, true) . '</pre>';
    //         $data_attribute_slug = $data_attribute_slugs[$key];            
    //         $tax_queries[] = array(
    //             'taxonomy' => $data_attribute_slug,
    //             'field'    => 'slug',
    //             'terms'    => $attribute_value,
    //         );
    //         $countt = $countt + 1;
    //     }
    //     echo 'query count: <pre>' . print_r(count($tax_queries), true) . '</pre>';
    //     // Add the tax_queries to the main $args
    //     $args['tax_query'] = $tax_queries;
    // }

    // *************************************************************************
    // $attribute_value = $_POST['attribute_value'];
    // $data_attribute_slug = $_POST['attribute_slug'];
    // $category_id = $_POST['category_id'];

    // echo 'taxonomy: <pre>' . print_r($data_attribute_slug, true) . '</pre>';
    // echo 'values: <pre>' . print_r($attribute_value, true) . '</pre>';

    // $args = array(
    //     'post_type' => 'product',
    //     'posts_per_page' => -1,
    //     'product_cat' => get_term($category_id)->slug,
    // );

    // if (!empty($attribute_value)) {
    //     $args['tax_query'] = array(
    //         'relation' => 'AND', // Adjust the relation based on your needs (AND or OR)
    //         array(
    //             'taxonomy' => $data_attribute_slug,
    //             'field' => 'slug', // Change to 'name' if your attribute values are names
    //             'terms' => $attribute_value,
    //         ),
    //     );
    // }

    // *************************************************************************
    // if (!empty($attribute_values) && is_array($attribute_values)) {
    //     // Initialize the tax_query array
    //     $args['tax_query'] = array(
    //         'relation' => 'AND', // Adjust the relation based on your needs (AND or OR)
    //     );
    
    //     foreach ($attribute_values as $attribute_value) {
    //         $args['tax_query'] = array(
    //             'taxonomy' => $data_attribute_slug,
    //             'field' => 'slug', // Change to 'name' if your attribute values are names
    //             'terms' => $attribute_value,
    //         );
    //     }
    // }

    // if (!empty($attribute_value)) {
    //     $args['tax_query'] = array(
    //         array(
    //             'taxonomy' => $data_attribute_slug, // Change to your desired attribute slug data_attribute_slug
    //             'field' => 'name',
    //             'terms' => $attribute_value,
    //         ),
    //     );
    // }

    // echo 'Args: <pre>' . print_r($args, true) . '</pre>';

    // $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    // $args['paged'] = $paged;

    

    // $query = new WP_Query($args);

    // if ($query->have_posts()) {
        
    //     while ($query->have_posts()) {
    //         $query->the_post();
    //         wc_get_template_part('content', 'product');
    //     }
    //     wp_reset_postdata();

    //     echo '<div style="display:block;">Total products found: ' . $query->found_posts . '</div>';
    // } else {
    //     echo 'No products found.';
    // }

    // $query = new WP_Query($args);

    // if ($query->have_posts()) {
    //     while ($query->have_posts()) {
    //         $query->the_post();
    //         wc_get_template_part('content', 'product');
    //     }
        // wp_reset_postdata();

        // // Display pagination
        // echo '<div style="display:block;">';
        // echo 'Total products found: ' . $query->found_posts;
        // echo '</div>';

        // // Pagination
        // echo '<div class="pagination">';
        // echo paginate_links(array(
        //     'total' => $query->max_num_pages,
        //     'current' => max(1, $paged),
        // ));
        // echo '</div>';
    // } else {
    //     echo 'No products found.';
    // }

}
add_action('wp_ajax_filter_products', 'filter_products');
add_action('wp_ajax_nopriv_filter_products', 'filter_products');


function change_order_received_message($message, $order_id) {
    // Get the order object
    $order = wc_get_order($order_id);

    // Check if this is an order or a quote (you may need to adjust the logic)
    if ($order->get_status() != 'failed') {
        $message = str_replace('Your order', 'Your Quote', $message);
    }

    return $message;
}
add_filter('woocommerce_thankyou_order_received_text', 'change_order_received_message', 10, 2);

// Remove order details from the WooCommerce order thank you page
function remove_order_details_from_thankyou_page($order_id) {
    // Get the order object
    $order = wc_get_order($order_id);

    // Remove pricing, total, subtotal, shipping cost, etc.
    remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
    remove_action('woocommerce_thankyou', 'woocommerce_thankyou_order_details', 20);

    // Optionally, you can add custom content here
    echo '<p>Thank you for your Quotation Request, We will get back to you very soon. !</p>';
}

add_action('woocommerce_thankyou', 'remove_order_details_from_thankyou_page', 9);

// remove billing_company fields from billing section
function remove_billing_company_field( $fields ) {
    // unset( $fields['billing']['billing_company'] );
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_country']);
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'remove_billing_company_field' );


function remove_shipping_address_checkout( $fields ) {
   
    if (isset($fields['shipping']['shipping_address_1'])) {
        $fields['shipping']['shipping_address_1']['label'] = '';
    }
    
    // Remove the order notes field
    // if (isset($fields['order']['order_comments'])) {
    //     unset($fields['order']['order_comments']);
    // }

    unset( $fields['shipping'] ); // Remove the entire shipping address section
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'remove_shipping_address_checkout' );

// remove order section on checkout page
function custom_remove_order_section_title($fields) {
    unset($fields['order']);
    return $fields;
}
// add_filter('woocommerce_checkout_fields', 'custom_remove_order_section_title');

// rename 'Billing details' text on checkout page
function custom_rename_billing_details($translated_text, $text, $domain) {
    if ($text === 'Billing details') {
        $translated_text = 'Contact Information'; // You can change this to your desired title
    }
    return $translated_text;
}
add_filter('gettext', 'custom_rename_billing_details', 20, 3);

// rename 'Your order' text on checkout page
function custom_rename_order_section_title($translated_text, $text, $domain) {
    if ($text === 'Your order') {
        $translated_text = ''; // Replace with your desired title
    }
    return $translated_text;
}
add_filter('gettext', 'custom_rename_order_section_title', 20, 3);

// rename the empty cart page text
function change_return_to_shop_text($translated_text, $text, $domain) {
    if ($text === 'Your cart is currently empty.' && $domain === 'woocommerce') {
        $translated_text = 'Your Quote bucket is currently empty.'; // Replace with your custom text
    }
    return $translated_text;
}

add_filter('gettext', 'change_return_to_shop_text', 20, 3);


add_action( 'after_setup_theme', 'gpes_remove_archive_titles', 99 );
function gpes_remove_archive_titles() {
	remove_action( 'woocommerce_before_main_content', 'gpes_archives_title', 20 );
}
 
// add_action( 'woocommerce_before_main_content', 'gpes_custom_product_category_title', 99 );
function gpes_custom_product_category_title() { 
	$term = get_queried_object();
	$category_title = get_field('category_heading', $term);
    // var_dump($category_title);
    $category_bg = get_field('category_bg', $term);
    $front_image = get_field('front_image', $term);
    $category_heading = get_field('category_heading', $term);
    $button_url = get_field('button_url', $term);
    $button_label = get_field('button_label', $term);

    if ($category_bg && $front_image && $category_heading && $button_url && $button_label) {
        echo '<div class="custom-container" style="background-image: url(' . $category_bg['url'] . ');">';
        echo '<img src="' . $front_image['url'] . '" class="front-image" alt="' . $front_image['alt'] . '">';

        echo '<div class="button-container">';
        echo '<a href="' . $button_url . '" class="custom-button">' . $button_label . '</a>';
        echo '</div>';

        echo '<div class="category-heading">' . $category_heading . '</div>';
        echo '</div>';
    }
}


function custom_change_quote_summary_text($translated_text, $text, $domain) {
    // Check if the text domain is 'woocommerce' and if the text matches 'Quote Summery'
    if ($domain === 'woocommerce' && $text === 'Quote Summery') {
        // Replace 'Quote Summery' with 'Quote Summary'
        $translated_text = 'Quote Summary';
    }
    
    return $translated_text;
}

// Hook the custom function into the gettext filter
add_filter('gettext', 'custom_change_quote_summary_text', 20, 3);


// Redirect to a custom page after WooCommerce checkout
function custom_redirect_after_checkout( $order_id ) {
    // Get the order object
    $order = wc_get_order( $order_id );

    // Get the order key
    $order_key = $order->get_order_key();

    // Define the custom redirect URL
    // $redirect_url = 'https://v3.garmentprinting.es/recuros/gracias/';
    // Get the page slug or text
    $page_slug_or_text = 'gracias';  // Change this to your page slug or text

    // Build the redirect URL dynamically
    $redirect_url = home_url( '/' . $page_slug_or_text . '/' );

    // Append the order key to the URL
    $redirect_url = add_query_arg( 'key', $order_key, $redirect_url );

    // Redirect to the custom page
    wp_redirect( $redirect_url );
    exit;
}

add_action( 'woocommerce_thankyou', 'custom_redirect_after_checkout', 10, 1 );







