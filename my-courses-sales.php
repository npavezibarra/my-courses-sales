<?php
/**
 * Plugin Name: My Courses Sales
 * Description: Adds a custom tab in My Account page for displaying user's course sales.
 * Version: 1.0
 * Author: Nicolás Pavez
 * Text Domain: my-courses-sales
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// INCLUDE FILES
include_once plugin_dir_path(__FILE__) . 'course-instructor-metabox.php';
include_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';

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
    $items['my-courses-sales'] = __('My Courses Sales', 'my-courses-sales'); // Use plugin text domain
    return $items;
}
add_filter('woocommerce_account_menu_items', 'my_courses_sales_link_my_account');

// ADD CONTENT TO THE NEW TAB IN MY ACCOUNT
require_once plugin_dir_path(__FILE__) . 'my-content.php';
add_action('woocommerce_account_my-courses-sales_endpoint', 'my_courses_sales_content');

// Enqueue your CSS file
function my_courses_sales_enqueue_styles() {
    wp_enqueue_style('my-plugin-style', plugin_dir_url(__FILE__) . 'my-plugin-style.css', array(), '1.0.0', 'all');
}
add_action('wp_enqueue_scripts', 'my_courses_sales_enqueue_styles');

// Hook the Admin Page Function
add_action('admin_menu', 'my_courses_sales_add_admin_page');

// Include Admin Page Settings
include_once plugin_dir_path(__FILE__) . 'admin/admin-settings.php';

// QUIZZES LIST
// A list that shows all the users' finished quizzes along with their results
add_action('admin_menu', 'register_user_completed_quizzes_menu');

function register_user_completed_quizzes_menu() {
    if (class_exists('SFWD_LMS')) {
        add_menu_page('Completed Quizzes', 'Completed Quizzes', 'manage_options', 'completed-quizzes', 'display_user_completed_quizzes');
    }
}

function display_user_completed_quizzes() {
    $users = get_users(); // Retrieve all users
    $completed_quizzes = array();

    foreach ($users as $user) {
        // Get user quiz data
        $user_quizzes = get_user_meta($user->ID, '_sfwd-quizzes', true);

        if (is_array($user_quizzes)) {
            foreach ($user_quizzes as $quiz) {
                if (isset($quiz['completed']) && $quiz['completed'] > 0) {
                    // Add completed quizzes to array
                    $completed_quizzes[$user->ID][] = array(
                        'quiz_id' => $quiz['quiz'],
                        'score' => $quiz['score'],
                        'completed_on' => date('Y-m-d H:i:s', $quiz['completed'])
                    );
                }
            }
        }
    }

    // Output the completed quizzes
    echo '<div class="wrap">';
    echo '<h2>Completed Quizzes</h2>';
    foreach ($completed_quizzes as $user_id => $quizzes) {
        echo '<h3>User ID: ' . $user_id . '</h3>';
        echo '<ul>';
        foreach ($quizzes as $quiz) {
            echo '<li>';
            echo 'Quiz ID: ' . $quiz['quiz_id'] . '<br>';
            echo 'Score: ' . $quiz['score'] . '<br>';
            echo 'Completed On: ' . $quiz['completed_on'] . '<br>';
            echo '</li>';
        }
        echo '</ul>';
    }
    echo '</div>';
}


function add_my_knowledge_button_to_profile_nav() {
    // Check if the user is logged in and on their profile page
    if (is_user_logged_in() && bp_is_my_profile()) {
        // Get the current user's ID
        $current_user_id = get_current_user_id();
        
        // Check if the user has the "My Knowledge" page
        $my_knowledge_page = get_page_by_title('My Knowledge');
        
        if ($my_knowledge_page) {
            // Get the URL of the "My Knowledge" page
            $my_knowledge_url = get_permalink($my_knowledge_page->ID);
            
            // Output the "My Knowledge" button HTML
            echo '<li id="my-knowledge-li" class="bp-personal-tab">';
            echo '<a href="' . esc_url($my_knowledge_url) . '"><div class="bb-single-nav-item-point">My Knowledge</div></a>';
            echo '</li>';
        }
    }
}
add_action('bp_nav_items', 'add_my_knowledge_button_to_profile_nav');


// Callback function to display content for the "My Knowledge" tab
function my_knowledge_content() {
    // Get the "My Knowledge" page by its title
    $page = get_page_by_title('My Knowledge');

    if ($page) {
        // Display the content of the "My Knowledge" page
        echo apply_filters('the_content', $page->post_content);
    } else {
        echo 'My Knowledge page content not found.';
    }
}

// TAB MY QUIZZES EN PROFILE PAGE

// Function to add custom profile nav item
function add_custom_profile_nav_item() {
    bp_core_new_nav_item( array( 
        'name' => __('My Tests', 'textdomain'), // Changed the tab name to "My Tests"
        'slug' => 'my-tests', // Changed the slug to match the tab name
        'position' => 50,
        'screen_function' => 'my_tests_tab_screen',
        'default_subnav_slug' => 'my-tests',
        'item_css_id' => 'my-tests-personal-li'
    ) );
}

// Screen function for the custom tab
function my_tests_tab_screen() {
    add_action( 'bp_template_content', 'my_tests_tab_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

// Content for the custom tab
function my_tests_tab_content() {
    include('content/my-tests-content.php');
}


// Hook to add the custom nav item
add_action( 'bp_setup_nav', 'add_custom_profile_nav_item' );


