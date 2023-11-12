<?php
// Function to add the admin page
function my_courses_sales_add_admin_page() {
    add_menu_page(
        'My Courses Sales Settings', // Page title
        'Courses Sales', // Menu title
        'manage_options', // Capability
        'my-courses-sales-settings', // Menu slug
        'my_courses_sales_admin_page_callback', // Callback function
        'dashicons-welcome-learn-more', // Icon
        6 // Position
    );
}

// Callback function for the admin page content
function my_courses_sales_admin_page_callback() {
    ?>
    <div class="wrap">
        <h1>My Courses Sales Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('my-courses-sales-options'); ?>
            <?php do_settings_sections('my-courses-sales-options'); ?>

            <div id="tax-rate">
                <h2>Tax Rate Settings</h2>
                <label for="tax-rate-input">Tax Rate (%):</label>
                <input type="number" id="tax-rate-input" name="my_courses_sales_tax_rate" value="<?php echo esc_attr(get_option('my_courses_sales_tax_rate')); ?>" step="0.01" min="0" max="100">
            </div>

            <div id="instructor-commission">
                <h2>Instructor Commission Settings</h2>
                <label for="instructor-commission-input">Instructor Commission (%):</label>
                <input type="number" id="instructor-commission-input" name="my_courses_sales_instructor_commission" value="<?php echo esc_attr(get_option('my_courses_sales_instructor_commission')); ?>" step="0.01" min="0" max="100">
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}



