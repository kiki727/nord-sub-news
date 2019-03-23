<?php 
/*
Plugin Name: NorD Sub News
Plugin URI: http://www.google.com/
Description: This plugin helps you to get subscriptions through email and you can get email for every user signup for news letters.
Users are in DB ,aranged by lists.
Author: Dejan Jovanovic
Version: 1.0
Author URI: http://www.google.com/
*/
if ( ! defined( 'ABSPATH' ) ) { exit; };
// Plugin Activation ...  nsn

if( !defined( 'NORD_PLUGIN_URL' ) ) {
	define( 'NORD_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}

if( !defined( 'NORD_VERSION' ) ) {
	define( 'NORD_VERSION', '1.0' ); // plugin version
}

if( !defined( 'NORD_PLUGIN_DIR' ) ) {
	define( 'NORD_PLUGIN_DIR', dirname( __FILE__ ) ); // plugin dir
}

if( !defined( 'NORD_ADMIN_DIR' ) ) {
	define( 'NORD_ADMIN_DIR', NORD_PLUGIN_DIR . '/admin' ); // plugin admin dir
}




function nsn_install() {
    global $wpdb;
    $table = $wpdb->prefix."nsn";
    $structure = "CREATE TABLE $table (
        id INT(9) NOT NULL AUTO_INCREMENT,
        nsn_name VARCHAR(200) NOT NULL,
        nsn_email VARCHAR(200) NOT NULL,
		nsn_uuid VARCHAR(200) NOT NULL,
	UNIQUE KEY id (id)
    );";
    $wpdb->query($structure);
	
}
register_activation_hook( __FILE__, 'nsn_install' );

// Plugin Deactivation
function nsn_uninstall() {
    global $wpdb;
	
}
register_deactivation_hook( __FILE__, 'nsn_uninstall' );

require_once ('nsn_common.php');



add_action( 'init', 'nsn_register_shortcode_for_newsletter');

function nsn_register_shortcode_for_newsletter(){
	
	add_shortcode('nsn_email_subscriptions', 'nsn_email_subscription_fnc' );
}

class NSN_Subscription_widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 
			'classname' => 'nsn_email_subscription',
			'description' => 'A Simple Email Subscription Widget to get subscribers info',
		);
		parent::__construct( 'my_widget', 'NSN Subscriptions', $widget_ops );
	}

	public function widget( $args, $instance ) {
		echo '<aside>'; 
		do_action('nsn_email_subscription');
		echo '</aside>';
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'NSN_Subscription_widget' );
});
	

if(!function_exists('nsn_email_subscription_fnc')) {
	add_action('nsn_email_subscription' , 'nsn_email_subscription_fnc' );

	function nsn_email_subscription_fnc() {
		
		
if ( isset( $_POST['nsn_cform_generate_nonce'] ) &&
			wp_verify_nonce( $_POST['nsn_cform_generate_nonce'], 'nsn_contact_form_submit' ) ) {

		if('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['nsn_submit_subscription'])) {
			
				$nsn_name = sanitize_text_field($_POST['subscriber_name']);
				$nsn_email = sanitize_email($_POST['subscriber_email']);
			

			if( filter_var($_POST['subscriber_email'], FILTER_VALIDATE_EMAIL) ){
				
				 $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
				 
				 $subject = sprintf(__('New Subscription on %s','nsn'), $blogname);
				 
				 $to = get_option('admin_email'); 
				 
				 $headers = 'From: '. sprintf(__('%s Admin', 'nsn'), $blogname) .' <No-repy@'.$_SERVER['SERVER_NAME'] .'>' . PHP_EOL;
				 
				$message  = sprintf(__('Hi ,', 'nsn')) . PHP_EOL . PHP_EOL;
				$message .= sprintf(__('You have a new subscription on your %s website.', 'kvc'), $blogname) . PHP_EOL . PHP_EOL;
				$message .= __('Email Details', 'nsn') . PHP_EOL;
				$message .= __('-----------------') . PHP_EOL;
				$message .= __('User E-mail: ', 'nsn') . stripslashes($_POST['subscriber_email']) . PHP_EOL;
				$message .= __('Regards,', 'nsn') . PHP_EOL . PHP_EOL;
				$message .= sprintf(__('Your %s Team', 'nsn'), $blogname) . PHP_EOL;
				$message .= trailingslashit(get_option('home')) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
			
			
				if (wp_mail($to, $subject, $message, $headers)){
				
					echo '<p style="color:#A0A5AA">Your e-mail (' . esc_js(esc_html($_POST['subscriber_email'])) . ') has been added to our mailing list!</p>';

				global $wpdb;	

				if (is_email($nsn_email)) {
					$exists = $wpdb->get_row($wpdb->prepare("SELECT COUNT(`id`) as 'count' FROM ".$wpdb->prefix."nsn WHERE nsn_email = %s LIMIT 1", $nsn_email));

					if ((int) $exists->count === 0) {
						$nsn_uuid = nsn_guidv4();
						$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."nsn (nsn_name, nsn_email,nsn_uuid) VALUES (%s, %s, %s)", $nsn_name, $nsn_email,$nsn_uuid));
					}
				};// end of insert in list / db	
					
				}	else	{
				   echo '<p style="color:#A0A5AA">There was a problem with your e-mail 1 (' . esc_js(esc_html($_POST['subscriber_email'])) . ')</p>';   
				}
					
			}else{
			   echo '<p style="color:#A0A5AA">There was a problem with your e-mail 2 (' . esc_js(esc_html($_POST['subscriber_email'])) . ')</p>';   
			}
		}
	}?>

		<div>
		<!-- class="col-lg-4 col-md-4 col-sm-4 twitter-widget-area animate-onscroll" -->
								
			<form id="newsletter-footer" action="" method="POST">
									
				<h5><strong>Sign up</strong> for email updates</h5>
				<div class="newsletter-form">
				
					<div class="newsletter-email" style="margin-bottom:10px; " >
						<input type="text" name="subscriber_name" placeholder="Name">
					</div>
							
					<div class="newsletter-email" style="margin-bottom:10px; " >	
						<input type="email" name="subscriber_email" placeholder="Email address" required>
					</div>
					<div class="newsletter-submit">
					<?php wp_nonce_field( 'nsn_contact_form_submit', 'nsn_cform_generate_nonce' ); ?>
							<input type="hidden" name="nsn_submit_subscription" value="Submit">
							<input type="submit" name="submit_form" value="Submit">							
					</div>
				</div>
			</form>
		</div>							
	<?php }

} ?>
