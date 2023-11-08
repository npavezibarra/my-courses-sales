<?php

function my_courses_sales_content() {
    // Check if user is logged in
    if (is_user_logged_in()) {
        // Get the current user's ID
        $current_user_id = get_current_user_id();

        // Initialize total earnings variable
        $total_earnings = 0;

        // Get all completed orders
        $customer_orders = wc_get_orders(array(
            'status' => 'completed',
        ));

        if ($customer_orders) {
            // Calculate total earnings first
            foreach ($customer_orders as $order) {
                foreach ($order->get_items() as $item_id => $item) {
                    // Retrieve the '_course_instructor_id' value from the product
                    $product_id = $item->get_product_id();
                    $course_instructor_id = get_post_meta($product_id, '_course_instructor_id', true);
                    
                    // Check if the current user's ID matches the 'course_instructor' ID
                    if ($current_user_id == $course_instructor_id) {
                        // Update total earnings
                        $total_earnings += $item->get_total();
                    }
                }
            }

            echo "<h2>" . __('Your Course Sales', 'text-domain') . "</h2>";

            // Display the total earnings above the table
            echo '<div id="data-summary">';
    
            // Display the total earnings inside data-summary
            echo '<div id="instructor-earnings">';
            echo '<strong>Total Earnings: ' . wc_price($total_earnings) . '</strong>';
            echo '</div>';

            // Create two additional divs for horizontal spacing
            $fucking_taxes = $total_earnings - $total_earnings / 1.19;
            echo '<div id="fucking-taxes"><strong>Taxes: ' . wc_price($fucking_taxes) . '</strong></div>';
            echo '<div id="your-check"><strong>Your check: ' . wc_price($total_earnings - $fucking_taxes) . '</strong></div>';
            
            // Close the data-summary div
            echo '</div>'; // Close #data-summary

            // Start the table outside of data-summary
            echo '<table id="tablaOrdenes">';
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

            // Close the table
            echo '</table>';
        } else {
            echo '<p>' . __('No completed orders found.', 'text-domain') . '</p>';
        }
    } else {
        echo '<p>' . __('Please log in to view your orders.', 'text-domain') . '</p>';
    }
}
