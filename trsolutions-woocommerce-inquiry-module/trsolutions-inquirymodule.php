<?php
/*
Plugin Name: TheRightSol | Woocommerce Inquiry Module
Plugin URI:  http://www.therightsol.com/
Description: This plugin will convert woocommerce shoping cart into inquiry module.
Version:     1.0 (Beta)
Author:      Ali Shan
Author URI:  http://www.therightsol.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: therightsol-inquirymodule


{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.

*/

function therightsol_get_woo_version_number() {
    // If get_plugins() isn't available, require it
    if ( ! function_exists( 'get_plugins' ) )
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    // Create the plugins folder and file variables
    $plugin_folder = get_plugins( '/' . 'woocommerce' );
    $plugin_file = 'woocommerce.php';

    // If the plugin version number is set, return it
    if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
        return $plugin_folder[$plugin_file]['Version'];

    } else {
        // Otherwise return null
        return NULL;
    }
}

// ---------------------------------------------------------
// Changing add to cart button text
function woo_custom_cart_button_text() {
    return __( 'Add to basket', 'woocommerce' );
}

// change view cart button text
function therightsol_changeViewCartButtonText( $translated_text, $text, $domain ){
    /**
     * Change text strings
     *
     * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
     */

    switch ( $translated_text ) {
        case 'View Cart' :
            $translated_text = __( 'View Basket', 'woocommerce' );
            break;

        case 'Update Cart' :
            $translated_text = __( 'Update Basket', 'woocommerce' );
            break;

        case 'Proceed to Checkout' :
        case 'Place order' :
            $translated_text = __( 'Send Inquiry', 'woocommerce' );
            break;

        case 'Your cart is currently empty' :
            $translated_text = __( 'Your basket is empty.', 'woocommerce' );
            break;

        case 'Return To Shop' :
            $translated_text = __( 'Return to home', 'woocommerce' );
            break;

        case 'Thank you. Your order has been received.':
            $translated_text = __( 'Thank you. Your inquiry has been received.', 'woocommerce' );
            break;

        case 'Your cart is currently empty.':
            $translated_text = __( 'Your basket is currently empty.', 'woocommerce' );
            break;

        case 'Order Number:':
            $translated_text = __( 'Inquiry Number', 'woocommerce' );
            break;

        case 'Order Received':
            $translated_text = __( 'Inquiry Received', 'woocommerce' );
            break;

        case 'Order Details':
            $translated_text = __( 'Inquiry Details', 'woocommerce' );
            break;

        case 'Your order':
            $translated_text = __( 'Your Inquiry', 'woocommerce' );
            break;

        case 'Billing Details':
            $translated_text = __( 'Your Details', 'woocommerce' );
            break;


        case 'Order Notes':
            $translated_text = __( 'Inquiry additional note', 'woocommerce' );
            break;

        case 'Notes about your order, e.g. special notes for delivery.':
            $translated_text = __( 'Notes about your inquiry, e.g. some extra points.', 'woocommerce' );
            break;

    }
    return $translated_text;
}


function change_add_to_cart_msg($message, $product_id = null){

    $titles[] = get_the_title( $product_id );

    $titles = array_filter( $titles );
    $added_text = sprintf( _n( '%s has been added to your basket.', '%s have been added to your basket.', sizeof( $titles ), 'woocommerce' ), wc_format_list_of_items( $titles ) );

    $message = sprintf( '%s <a href="%s" class="button">%s</a>&nbsp;<a href="%s" class="button">%s</a>',
        esc_html( $added_text ),
        esc_url( wc_get_page_permalink( 'checkout' ) ),
        esc_html__( 'Send Inquiry', 'woocommerce' ),
        esc_url( wc_get_page_permalink( 'cart' ) ),
        esc_html__( 'View Basket', 'woocommerce' ));

    return $message;
}


function updating_url_for_gotoshop_btn(){
    /**
     * Changes the redirect URL for the Return To Shop button in the cart.
     *
     * @return string
     */
    return bloginfo('wpurl');

}


function creatingPages() {

    $the_page_title = 'Inquiry';


    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[woocommerce_cart][woocommerce_checkout]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

        return $the_page_id;

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

        return $the_page_id;

    }
}

// Updating WooCommerce Cart and Checkout pages
function therightsol_setInquiryPages($inquiryPageID = false){
    if($inquiryPageID){
        update_option( 'woocommerce_cart_page_id', $inquiryPageID );
        update_option( 'woocommerce_checkout_page_id', $inquiryPageID );
        update_option('woocommerce_email_footer_text', 'Inquiry Module - Powered by WooCommerce | Extended by TheRightSol.com');
    }
}


function therightsol_init(){

    // Changing Add to Cart Button Text
    $version = therightsol_get_woo_version_number();
    if($version){
        $version = substr($version, 0, -2);

        if($version < 2.1){

            add_filter( 'add_to_cart_text', 'woo_custom_cart_button_text' );    // < 2.1

        }else if ($version > 2.1){
            add_filter( 'woocommerce_product_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +
            add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +
        }
    }


    // Changing text of View Cart button.
    add_filter( 'gettext', 'therightsol_changeViewCartButtonText', 20, 3 );


    // Creating page
    $pageID = creatingPages();

    // updating Go To Home button after inquiry
    add_filter( 'woocommerce_return_to_shop_redirect', 'updating_url_for_gotoshop_btn' );

    // changing Added to cart text
    add_filter ( 'wc_add_to_cart_message', 'change_add_to_cart_msg', 10, 2 );

    // Setting Up Inquiry pages to WooCommerce
    if($pageID)
        therightsol_setInquiryPages($pageID);

    enablecod();

    // Hiding Price and other things by css
    wp_enqueue_style('therightsol-inquirymodule', plugin_dir_url(__FILE__) . '/css/style.css');


    // scripts
    wp_enqueue_script('therightsol-inquirymodule-js', plugin_dir_url(__FILE__) . '/js/custom.js', array('jquery'), '', true);



}

// ---------------------------------------------------------



function enablecod(){
    $paypall_disble = 'a:16:{s:7:"enabled";s:3:"no";s:5:"title";s:6:"PayPal";s:11:"description";s:85:"Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.";s:5:"email";s:16:"someone@fake.com";s:8:"testmode";s:2:"no";s:5:"debug";s:2:"no";s:14:"receiver_email";s:16:"someone@fake.com";s:14:"identity_token";s:0:"";s:14:"invoice_prefix";s:3:"WC-";s:13:"send_shipping";s:2:"no";s:16:"address_override";s:2:"no";s:13:"paymentaction";s:4:"sale";s:10:"page_style";s:0:"";s:12:"api_username";s:0:"";s:12:"api_password";s:0:"";s:13:"api_signature";s:0:"";}';
    $cheque_disable = 'a:4:{s:7:"enabled";s:2:"no";s:5:"title";s:14:"Cheque Payment";s:11:"description";s:102:"Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.";s:12:"instructions";s:102:"Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.";}';
    $cod_enable = 'a:6:{s:7:"enabled";s:3:"yes";s:5:"title";s:16:"Cash on Delivery";s:11:"description";s:28:"Pay with cash upon delivery.";s:12:"instructions";s:28:"Pay with cash upon delivery.";s:18:"enable_for_methods";s:0:"";s:18:"enable_for_virtual";s:3:"yes";}';


    /*update_option( 'woocommerce_paypal_settings', $paypall_disble );
    update_option( 'woocommerce_cheque_settings', $cheque_disable );
    update_option( 'woocommerce_cod_settings', $cod_enable );*/
}







function therightsol_printversion(){
    $result = therightsol_get_woo_version_number();

    if($result){
        echo $result;
    }

}

add_action('init', 'therightsol_init');