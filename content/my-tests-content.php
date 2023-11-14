<?php
/*
Template Name: My Knowledge
*/

// Add any custom content or queries you want for the "My Knowledge" page here

get_header();
?>

<div id="primary" class="content-area bb-grid-cell">
    <main id="main" class="site-main">

        <?php
        // Define the get_user_completed_quizzes() function
        function get_user_completed_quizzes() {
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

            // Display the results in a table
            echo '<div class="wrap">';
            echo '<h2>' . bp_get_displayed_user_fullname() . "'s Quizzes Result Table</h2>";
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>User ID</th>';
            echo '<th>Quiz ID</th>';
            echo '<th>Score</th>';
            echo '<th>Completed On</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($completed_quizzes as $user_id => $quizzes) {
                foreach ($quizzes as $quiz) {
                    echo '<tr>';
                    echo '<td>' . $user_id . '</td>';
                    echo '<td>' . $quiz['quiz_id'] . '</td>';
                    echo '<td>' . $quiz['score'] . '</td>';
                    echo '<td>' . $quiz['completed_on'] . '</td>';
                    echo '</tr>';
                }
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }

        // Call the get_user_completed_quizzes() function to display the Quizzes Result Table
        get_user_completed_quizzes();
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
if ( is_search() ) {
	get_sidebar( 'search' );
} else {
	get_sidebar( 'page' );
}
?>

<?php
get_footer();
?>
