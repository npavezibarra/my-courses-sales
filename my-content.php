<?php

function my_courses_sales_content() {
    if (is_user_logged_in()) {
        $current_user_id = get_current_user_id();

        $number_of_students = 0;
        $total_earnings = 0;

        $tax_rate = floatval(get_option('my_courses_sales_tax_rate', 0.19));
        $instructor_commission = floatval(get_option('my_courses_sales_instructor_commission', 0.80));

        $customer_orders = wc_get_orders(array(
            'status' => 'completed',
        ));

        // Default date values
        $end_date = date('Y-m-d'); // Today's date
        $start_date = date('Y-m-d', strtotime('-3 days')); // 3 days ago

        // Check if the form is submitted and update dates accordingly
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_date']) && isset($_POST['end_date'])) {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
        }

        $customer_orders = wc_get_orders(array(
            'status' => 'completed',
            'date_created' => $start_date . '...'. $end_date, // Filter orders within the date range
        ));        

        if ($customer_orders) {
            // Pagination setup
            $per_page = 10;
            $current_page = isset($_GET['page_num']) ? max(1, intval($_GET['page_num'])) : 1;
            $start = ($current_page - 1) * $per_page;
            $end = $start + $per_page;
            $total_orders = count($customer_orders);
            $total_pages = ceil($total_orders / $per_page);

            foreach ($customer_orders as $index => $order) {
                if ($index >= $start && $index < $end) {
                    foreach ($order->get_items() as $item_id => $item) {
                        $product_id = $item->get_product_id();
                        $course_instructor_id = get_post_meta($product_id, '_course_instructor_id', true);

                        if ($current_user_id == $course_instructor_id) {
                            $number_of_students++;
                            $total_earnings += $item->get_total();
                        }
                    }
                }
            }

            $iva_taxes = $total_earnings * $tax_rate;
            $final_check = ($total_earnings - $iva_taxes) * $instructor_commission;

            // Display content
            echo "<h2>" . __('Your Course Sales', 'text-domain') . "</h2>";
            echo '<h4>Summary of your account</h4>';
            echo '<form method="post" id="date-filter">';
            echo '<label for="start-date">Start Date:</label>';
            echo '<input type="date" id="start-date" name="start_date" value="' . $start_date . '">';
            echo '<label for="end-date">End Date:</label>';
            echo '<input type="date" id="end-date" name="end_date" value="' . $end_date . '">';
            echo '<input type="submit" value="Filter">';
            echo '</form>';
            echo '<div id="data-summary">';
            echo '<div id="instructor-students">';
            echo '<strong>Number of Students: ' . $number_of_students . '</strong>';
            echo '</div>';
            echo '<div id="fucking-taxes">';
            echo '<strong>IVA Taxes: ' . wc_price($iva_taxes) . '</strong>';
            echo '</div>';
            echo '<div id="your-check">';
            echo '<strong>Your Check: ' . wc_price($final_check) . '</strong>';
            echo '</div>';
            echo '</div>';

            // Display the table of latest students
            echo '<h4>Your latest students:</h4>';
            echo '<table id="tablaOrdenes">';
            echo '<tr>';
            echo '<th>' . __('Course', 'text-domain') . '</th>';
            echo '<th>' . __('Course Instructor', 'text-domain') . '</th>';
            echo '<th>' . __('Price', 'text-domain') . '</th>';
            echo '<th>' . __('Date', 'text-domain') . '</th>';
            echo '<th>' . __('Student', 'text-domain') . '</th>';
            echo '</tr>';

            // Display orders within the current page range
            foreach ($customer_orders as $index => $order) {
                if ($index >= $start && $index < $end) {
                    foreach ($order->get_items() as $item_id => $item) {
                        $product_id = $item->get_product_id();
                        $course_instructor_id = get_post_meta($product_id, '_course_instructor_id', true);

                        if ($current_user_id == $course_instructor_id) {
                            $course_instructor = get_userdata($course_instructor_id);
                            $course_instructor_nickname = $course_instructor ? $course_instructor->display_name : '';
                            echo '<tr>';
                            echo '<td><a href="' . get_permalink($product_id) . '">' . $item->get_name() . '</a></td>';
                            echo '<td>' . esc_html($course_instructor_nickname) . '</td>';
                            echo '<td>' . wc_price($item->get_total()) . '</td>';
                            echo '<td>' . wc_format_datetime($order->get_date_created()) . '</td>';
                            echo '<td>' . $order->get_billing_first_name() . '</td>';
                            echo '</tr>';
                        }
                    }
                }
            }
            echo '</table>';

            // Display pagination
            echo '<div class="pagination">';
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                // Highlight the current page
                        echo '<a href="?page_num=' . $i . '" style="color: #385dff;">' . $i . '</a> ';
                    } else {
                        echo '<a href="?page_num=' . $i . '">' . $i . '</a> ';
                    }
}   
echo '</div>';
} else {
    // Display the section title and the three bubbles with respective data
    echo "<h2>" . __('Your Course Sales', 'text-domain') . "</h2>";
    echo '<div id="data-summary">';
    echo '<div id="instructor-students">';
    echo '<strong>Number of Students: 0</strong>'; // No students as there are no orders
    echo '</div>';
    echo '<div id="fucking-taxes">';
    echo '<strong>IVA Taxes: ' . wc_price(0) . '</strong>'; // No taxes as there are no orders
    echo '</div>';
    echo '<div id="your-check">';
    echo '<strong>Your Check: ' . wc_price(0) . '</strong>'; // No earnings as there are no orders
    echo '</div>';
    echo '</div>';

    // Since there are no orders, display a message instead of the table
    echo '<h3>' . __('No completed orders found.', 'text-domain') . '</h3>';
    echo '<p>' . __('If you want to find out how to sell more <a>click here</a>.', 'text-domain') . '</p>';
}

    } else {
        echo '<p>' . __('Please log in to view your orders.', 'text-domain') . '</p>';
    }
}
