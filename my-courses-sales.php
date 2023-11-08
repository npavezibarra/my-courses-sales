<?php
/**
 * Plugin Name: My Courses Sales
 * Description: Adds a custom tab in My Account page for displaying user's course sales.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: text-domain
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// INCLUDE FILES
include_once plugin_dir_path(__FILE__) . 'course-instructor-metabox.php';

// CUSTOM ENDPOINT FOR MY ACCOUNT PAGE
function my_courses_sales_endpoint() {
    add_rewrite_endpoint('my-courses-sales', EP_ROOT | EP_PAGES);
}
add_action('init', 'my_courses_sales_endpoint');

function my_courses_sales_activate() {
    my_courses_sales_endpoint();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'my_courses_sales_activate');

function my_courses_sales_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'my_courses_sales_deactivate');

// ADD LINK TO MY ACCOUNT MENU
function my_courses_sales_link_my_account($items) {
    // Add an icon (e.g., a money symbol) before the text
    $icon = '&#x1F4B0;'; // You can replace this with the desired icon or HTML entity

    // Add the icon and text
    $items['my-courses-sales'] = $icon . __('My Courses Sales', 'text-domain');
    return $items;
}

add_filter('woocommerce_account_menu_items', 'my_courses_sales_link_my_account');

// ADD CONTENT TO THE NEW TAB IN MY ACCOUNT
require_once plugin_dir_path(__FILE__) . 'my-content.php';

add_action('woocommerce_account_my-courses-sales_endpoint', 'my_courses_sales_content');

?>
