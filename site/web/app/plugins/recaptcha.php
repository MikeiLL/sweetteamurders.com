<?php
/**
 * @package Simple_ReCaptcha
 * @version 1.0
 */
/*
Plugin Name: Simple Recaptcha
Plugin URI: http://mzoo.org
Description: Add ReCaptcha to comments
	Author: Mike iLL
	Version: 1.0
	Author URI: http://mzoo.org/
	*/


// Enqueue reCAPTCHA script
function enqueue_recaptcha_script() {
  // Only load on single posts/pages (where comments are displayed)
  if (is_singular() && comments_open()) {
      wp_enqueue_script(
          'recaptcha',
          'https://www.google.com/recaptcha/api.js?render=6Let8zYtAAAAAJAS-48uW2ZpU6lvh8HeSQ-oTVyH', // Replace with your Site Key
          array(),
          '3.0',
          false // Load in footer
      );
  }
}
add_action('wp_enqueue_scripts', 'enqueue_recaptcha_script');

  // Add reCAPTCHA to comment form
function add_recaptcha_to_comment_form() {
  // Only show on single posts/pages with open comments

  if (is_singular() && comments_open()) {
      ?>
      <!-- Hidden input to store reCAPTCHA token -->
      <input type="hidden" id="recaptcha_response" name="recaptcha_response">

      <!-- JavaScript to get token and populate hidden input -->
      <script>
          grecaptcha.ready(function() {
              grecaptcha.execute('6Let8zYtAAAAAJAS-48uW2ZpU6lvh8HeSQ-oTVyH', {action: 'comment'}).then(function(token) {
                  document.getElementById('recaptcha_response').value = token;
              });
          });
      </script>
      <?php
  }
}
//if (!is_user_logged_in()) {
  add_action('comment_form_after_fields', 'add_recaptcha_to_comment_form');
//}


// Verify reCAPTCHA response before saving comment
function verify_recaptcha_comment($commentdata) {
  // Check if reCAPTCHA token exists
  if (!isset($_POST['recaptcha_response'])) {
      wp_die(__('reCAPTCHA verification failed. Please try again.'));
  }

  $token = $_POST['recaptcha_response'];
  $remote_ip = $_SERVER['REMOTE_ADDR'];

  // Send request to Google's verification API
  $url = 'https://www.google.com/recaptcha/api/siteverify';
  $args = array(
      'body' => array(
          'secret' => RECAPTCHA_SECRET_KEY,
          'response' => $token,
          'remoteip' => $remote_ip
      ),
      'timeout' => 10
  );

  $response = wp_remote_post($url, $args);
  // Check for API errors
  if (is_wp_error($response)) {
      wp_die(__('Failed to connect to reCAPTCHA server. Please try again later.'));
  }

  $response_body = wp_remote_retrieve_body($response);
  $result = json_decode($response_body);

  // Check if verification succeeded and score is ≥ 0.5 (adjust as needed)
  if (!$result->success || $result->score < 0.4) {
      wp_die(__('reCAPTCHA verification failed. You may be a bot (or worse).'));
  }

  return $commentdata; // Allow comment to post
}
add_filter('pre_comment_on_post', 'verify_recaptcha_comment');
