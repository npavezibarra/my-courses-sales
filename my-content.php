<?php

function my_courses_sales_content() {
    // Check if user is logged in
    if (is_user_logged_in()) {
        // Get the current user's ID
        $current_user_id = get_current_user_id();

        // Initialize variables
        $number_of_students = 0;
        $total_earnings = 0;

        // Get all completed orders
        $customer_orders = wc_get_orders(array(
            'status' => 'completed',
        ));

        if ($customer_orders) {
            // Count the number of students and calculate total earnings
            foreach ($customer_orders as $order) {
                foreach ($order->get_items() as $item_id => $item) {
                    // Retrieve the '_course_instructor_id' value from the product
                    $product_id = $item->get_product_id();
                    $course_instructor_id = get_post_meta($product_id, '_course_instructor_id', true);

                    // Check if the current user's ID matches the 'course_instructor' ID
                    if ($current_user_id == $course_instructor_id) {
                        // Increment number of students and total earnings
                        $number_of_students++;
                        $total_earnings += $item->get_total();
                    }
                }
            }

            // Calculate IVA taxes (19%) and final check amount
            $iva_taxes = $total_earnings * 0.19;
            $final_check = ($total_earnings - $iva_taxes) * 0.80;

            // Display content
            echo "<h2>" . __('Your Course Sales', 'text-domain') . "</h2>";
            echo '<h4>Summary of your account</h4>';
            echo '<div id="data-summary">';

            // Number of Students
            echo '<div id="instructor-students">';
            echo '<strong>Number of Students: ' . $number_of_students . '</strong>';
            echo '</div>';

            // IVA Taxes
            echo '<div id="fucking-taxes">';
            echo '<strong>IVA Taxes: ' . wc_price($iva_taxes) . '</strong>';
            echo '</div>';

            // Your Check
            echo '<div id="your-check">';
            echo '<strong>Your Check: ' . wc_price($final_check) . '</strong>';
            echo '</div>';

            // Close data-summary
            echo '</div>';

            // Display the table of latest students
            echo '<h4>Your latest students:</h4>';
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
                    $product_id = $item->get_product_id();
                    $course_instructor_id = get_post_meta($product_id, '_course_instructor_id', true);

                    if ($current_user_id == $course_instructor_id) {
                        $course_instructor = get_userdata($course_instructor_id);
                        $course_instructor_nickname = $course_instructor ? $course_instructor->display_name : '';

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
