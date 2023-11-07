<?php
// Add the metabox
function my_courses_sales_add_course_instructor_metabox() {
    add_meta_box(
        'course_instructor_metabox', // ID of the metabox
        __('Course Instructor', 'text-domain'), // Title of the metabox
        'my_courses_sales_course_instructor_metabox_callback', // Callback function
        'product', // Post type
        'side', // Context
        'default' // Priority
    );
}
add_action('add_meta_boxes', 'my_courses_sales_add_course_instructor_metabox');

// Callback function for the metabox content
function my_courses_sales_course_instructor_metabox_callback($post) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field('course_instructor_save_metabox_data', 'course_instructor_metabox_nonce');
    
    // Retrieve the current value of course instructor ID if it exists
    $selected_instructor = get_post_meta($post->ID, '_course_instructor_id', true);

    // Get all users with 'author' role.
    $args = [
        'role' => 'author',
        'orderby' => 'display_name'
    ];
    $users = get_users($args);

    echo '<select name="course_instructor_id" id="course_instructor_id" class="postbox">';
    // The option that displays by default
    echo '<option value="">' . __('-Select Instructor-', 'text-domain') . '</option>';
    foreach ($users as $user) {
        // Mark the selected instructor as the option that should be pre-selected
        echo '<option value="' . esc_attr($user->ID) . '"' . selected($selected_instructor, $user->ID, false) . '>' . esc_html($user->display_name) . '</option>';
    }
    echo '</select>';
}

// Save the metabox data
function my_courses_sales_save_course_instructor_metabox_data($post_id) {
    // Check if our nonce is set and if the action is authorized.
    if (!isset($_POST['course_instructor_metabox_nonce']) ||
        !wp_verify_nonce($_POST['course_instructor_metabox_nonce'], 'course_instructor_save_metabox_data') ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        !current_user_can('edit_product', $post_id)
    ) {
        return;
    }

    // If the course instructor ID is set, save it
    if (isset($_POST['course_instructor_id'])) {
        update_post_meta($post_id, '_course_instructor_id', sanitize_text_field($_POST['course_instructor_id']));
    }
}
add_action('save_post', 'my_courses_sales_save_course_instructor_metabox_data');
