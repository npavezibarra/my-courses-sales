<?php
function my_courses_sales_register_settings() {
    // Register a new setting for "tax rate"
    register_setting('my-courses-sales-options', 'my_courses_sales_tax_rate');

    // Register a new setting for "instructor commission"
    register_setting('my-courses-sales-options', 'my_courses_sales_instructor_commission');
}

add_action('admin_init', 'my_courses_sales_register_settings');
