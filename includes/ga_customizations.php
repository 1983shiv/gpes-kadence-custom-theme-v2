<?php

add_filter( 'gtm4wp_compile_datalayer', 'gtm4wp_woocommerce_datalayer_filter_items_remove_purchase', 99, 1 );
add_filter( 'gtm4wp_after_datalayer', 'gtm4wp_woocommerce_datalayer_filter_items_remove_purchase', 99, 1 );


function gtm4wp_woocommerce_datalayer_filter_items_remove_purchase($data_layer){
    if (isset($data_layer['ecommerce']['purchase'])) {
        unset($data_layer['ecommerce']['purchase']);
    }
    return $data_layer;
}

add_action('woocommerce_checkout_create_order_line_item', 'add_ga_client_id_to_order', 10, 4);
function add_ga_client_id_to_order($item, $cart_item_key, $values, $order)
{

    if (isset($_COOKIE["_ga"])) {
        $_GA_Client_ID = preg_replace("/^.+\.(.+?\..+?)$/", "\\1", @$_COOKIE['_ga']);
        if ($_GA_Client_ID) {
            $item->update_meta_data('_GA_Client_ID', $_GA_Client_ID);
        }
    }
    if (isset($_COOKIE["_ga_".GA4_ID])) {
        $session_parts = explode(".", @$_COOKIE['_ga_'.GA4_ID]);
        $_GA_Session_ID = isset($session_parts[2]) ? $session_parts[2] : ""; 
        if ($_GA_Session_ID) {
            $item->update_meta_data('_GA_Session_ID', $_GA_Session_ID);
        }
        $_GA_Session_No = isset($session_parts[3]) ? $session_parts[3] : ""; 
        if ($_GA_Session_No) {
            $item->update_meta_data('_GA_Session_No', $_GA_Session_No);
        }
    }

    if (isset($_COOKIE["gp_utm_source"])) {
        $item->update_meta_data('gp_utm_source',    $_COOKIE["gp_utm_source"]);
        $item->update_meta_data('gp_utm_medium',    $_COOKIE["gp_utm_medium"]);
        $item->update_meta_data('gp_utm_campaign',  $_COOKIE["gp_utm_campaign"]);
        $item->update_meta_data('gp_utm_term',      $_COOKIE["gp_utm_term"]);
        $item->update_meta_data('gp_utm_content',   $_COOKIE["gp_utm_content"]);
        $item->update_meta_data('gp_channel_group', $_COOKIE["gp_channel_group"]);
    }


}

add_action('wp_head', 'print_ga_values');
function print_ga_values(){
    if (isset($_GET['gavalues'])){
        if (isset($_COOKIE["_ga"])) {
            $_GA_Client_ID = preg_replace("/^.+\.(.+?\..+?)$/", "\\1", @$_COOKIE['_ga']);
            echo '<br/>$_GA_Client_ID == '.$_GA_Client_ID;
        }
        if (isset($_COOKIE["_ga_".GA4_ID])) {
            $session_parts = explode(".", @$_COOKIE['_ga_'.GA4_ID]);
            $_GA_Session_ID = isset($session_parts[2]) ? $session_parts[2] : ""; 
            echo '<br/>$_GA_Session_ID == '.$_GA_Session_ID;
        }    
    }    
}