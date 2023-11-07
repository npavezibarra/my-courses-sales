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
    $items['my-courses-sales'] = __('My Courses Sales', 'text-domain');
    return $items;
}
add_filter('woocommerce_account_menu_items', 'my_courses_sales_link_my_account');

// ADD CONTENT TO THE NEW TAB IN MY ACCOUNT
function my_courses_sales_content() {
    echo "<h2>" . __('Your Course Sales', 'text-domain') . "</h2>";

    // Check if user is logged in
    if (is_user_logged_in()) {
        // Get the current user's ID
        $current_user_id = get_current_user_id();

        // Get all completed orders
        $customer_orders = wc_get_orders(array(
            'status' => 'completed',
        ));

        if ($customer_orders) {
            echo '<table>';
            echo '<tr>';
            echo '<th>' . __('Product Title', 'text-domain') . '</th>';
            echo '<th>' . __('Course Instructor', 'text-domain') . '</th>';
            echo '<th>' . __('Price', 'text-domain') . '</th>';
            echo '<th>' . __('Date', 'text-domain') . '</th>';
            echo '<th>' . __('First Name (Billing)', 'text-domain') . '</th>';
            echo '</tr>';

            foreach ($customer_orders as $order) {
                foreach ($order->get_items() as $item_id => $item) {
                    // Retrieve the '_course_instructor_id' value from the product
                    $product_id = $item->get_product_id();
                    $course_instructor_id = get_post_meta($product_id, '_course_instructor_id', true);
                    
                    // Get the user's data
                    $course_instructor = get_userdata($course_instructor_id);
                    $course_instructor_nickname = $course_instructor ? $course_instructor->display_name : '';

                    // Check if the current user's ID matches the 'course_instructor' ID
                    if ($current_user_id == $course_instructor_id) {
                        echo '<tr>';
                        echo '<td>' . $item->get_name() . '</td>';
                        echo '<td>' . esc_html($course_instructor_nickname) . '</td>';
                        echo '<td>' . wc_price($item->get_total()) . '</td>';
                        echo '<td>' . wc_format_datetime($order->get_date_created()) . '</td>';
                        echo '<td>' . $order->get_billing_first_name() . '</td>';
                        echo '</tr>';
                    }
                }
            }
            
            echo '</table>';
        } else {
            echo '<p>' . __('No completed orders found.', 'text-domain') . '</p>';
        }
    } else {
        echo '<p>' . __('Please log in to view your orders.', 'text-domain') . '</p>';
    }
}
add_action('woocommerce_account_my-courses-sales_endpoint', 'my_courses_sales_content');

?>
