<?php

define('GARMENT_PRINTING_VERSION' , time());

add_action('wp_enqueue_scripts', 'wp_enqueue_garment_printing_style', PHP_INT_MAX);
function wp_enqueue_garment_printing_style()
{  
    wp_enqueue_style('gp-menu', get_stylesheet_directory_uri() . '/assets/css/gp-header-menu.css', array(), GARMENT_PRINTING_VERSION, 'all');  
}