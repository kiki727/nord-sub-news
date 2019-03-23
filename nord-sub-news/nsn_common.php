<?php
if ( !defined( 'ABSPATH' ) ) { exit; };


function nsn_msg() {
	global $nsn_message;
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( $nsn_message, 'wordpress' ); ?></p>
    </div>
    <?php
};

add_action( 'init', 'nsn_db_manage' );

function nsn_db_manage(){
	
//	if ( ! current_user_can( 'manage_options' ) ) { wp_die( __( 'You are not allowed to access this part of the site' ) ); };
	
	global $wpdb;
	global $nsn_message;
	global $nsn_searched_email_result;
	
if (current_user_can('manage_options')) {
	
	if ($_SERVER['REQUEST_METHOD']=="POST" and $_POST['nsndelall']) {
		$q = "TRUNCATE TABLE ".$wpdb->prefix."nsn";
		$wpdb->query($q);
		$nsn_message = 'All Emails are deleted from DB!';
		add_action( 'admin_notices','nsn_msg',10 );
	};

	if ($_SERVER['REQUEST_METHOD']=="POST" and $_POST['nsnaddtolist']) {
							 
		$uuid = nsn_guidv4();
		$ime = esc_js(esc_html($_POST['nsn_ime']));
		$mail = esc_js(esc_html($_POST['nsn_mail']));
		
		$is_exist = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."nsn where nsn_email LIKE '".$mail."' limit 1", ARRAY_A);
			
		
		if (!is_array($is_exist)) {
			if (is_email($mail)) {
				 $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."nsn (nsn_name, nsn_email,nsn_uuid) VALUES (%s, %s, %s)", $ime, $mail,$uuid));
				 $nsn_message = 'Email address added to list';
				 add_action( 'admin_notices','nsn_msg',10 );
			} else {
				echo '<script> alert("insert valid email adress"); </script>';
			};
		} else { echo '<script> alert("this email adress alredy exist"); </script>'; };
	};
	
	
	
	if ($_SERVER['REQUEST_METHOD']=="POST" and $_POST['nsn_remove']) {
							if ($_GET['rem']) $_POST['rem'][] = $_GET['rem'];
							$count = 0;
							if (is_array($_POST['rem'])) {
								foreach ($_POST['rem'] as $id) { 
								//$wpdb->query( $wpdb->prepare( "delete from ".$wpdb->prefix."nsn where id = '%s' limit 1",$wpdb->$id ) );
								$wpdb->query("delete from ".$wpdb->prefix."nsn where id = '".$wpdb->escape($id)."' limit 1"); // use prepare no escape
									$count++; 
								}
								$nsn_message = $count." subscribers have been removed successfully.";
							}
						};
						
						

						
	if ($_SERVER['REQUEST_METHOD']=="POST" and $_POST['nsn_import']) {
							$correct = 0;
							if($_FILES['file']['tmp_name']) {
								if(!$_FILES['file']['error'])  {
									$file = file_get_contents ($_FILES['file']['tmp_name']);
									$lines = preg_split('/\r\n|\r|\n/', $file);
									if (count($lines)) {
										$sql = array();
										foreach ($lines as $data) {
											$data = explode(',', $data);
											$num = count($data);
											$row++;
											
							if (is_email(trim($data[0]))) {
				$c = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."nsn where nsn_email LIKE '".$wpdb->escape(trim($data[0]))."' limit 1", ARRAY_A);// use prepare no escape
	if (!is_array($c)) {
												$uuid = nsn_guidv4();
	$wpdb->query("INSERT INTO ".$wpdb->prefix."nsn (nsn_email, nsn_name,nsn_uuid) VALUES ('".$wpdb->escape(trim($data[0]))."', '".$wpdb->escape(trim($data[1]))."','".$wpdb->escape($uuid)."')");// use prepare no escape
													$correct++;
												} else { $exists++; }
											} else { $invalid++; }
										}
										
									} else { $nsn_message = 'Oh no! Your CSV file does not apear to be valid, please check the format and upload again.'; }
								
									if (!$nsn_message) {
										$nsn_message = $correct.' records have been imported. '.($invalid?$invalid.' could not be imported due to invalid email addresses. ':'').($exists?$exists.' already exists. ':'');
									}
								
								} else {
									$nsn_message = 'Ooops! There seems to of been a problem uploading your csv';
								}
							}								
				
	}
						//echo $sql;
	if ($nsn_message) { 
	add_action( 'admin_notices','nsn_msg',10 );
	}
	
}
	
};

function nsn_searchEmail(){

	
	global $wpdb;
	global $nsn_message;
	global $nsn_searched_email_result;
			// search for email
	if ($_SERVER['REQUEST_METHOD']=="POST" and $_POST['nsn_search_email']) {
		

		
	if (current_user_can('manage_options')) {
		$nsn_searched_email_result= array();
		
		$to_look = sanitize_email($_POST['nsn_mail_serch']);
		if($to_look) {
		//$q = "SELECT * FROM test.test1  WHERE nsn_email =".$to_look;
		$res = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."nsn WHERE nsn_email = '$to_look'" );
		if (count($res)) {
			foreach($res as $re){
				$nsn_searched_email_result['id'] = esc_js(esc_html($re->id));
				$nsn_searched_email_result['name'] = esc_js(esc_html($re->nsn_name));
				$nsn_searched_email_result['mail'] = esc_js(esc_html($re->nsn_email));
				$nsn_searched_email_result['uid'] = esc_js(esc_html($re->nsn_uuid));
			};
			
		ob_start();
		//var_dump($nsn_searched_email_result);
		echo "<b>Email id:</b> ". esc_html($nsn_searched_email_result['id']).'<br/>';
		echo "<b>Name:</b> ".esc_html($nsn_searched_email_result['name']).'<br/>';
		echo "<b>Email:</b> ".esc_html($nsn_searched_email_result['mail']).'<br/>';
		echo "<b>uuid:</b> ".esc_html($nsn_searched_email_result['uid']);
		$rt = ob_get_clean();
		$nsn_message = $rt;
		 if ($nsn_message) { 
			add_action( 'admin_notices','nsn_msg',10 );
		}
			
			
		} else {
			$nsn_message = 'Email not found!';
			add_action( 'admin_notices','nsn_msg',10 );
		}
	}
	
		
	}
}
	

};

add_action( 'init', 'nsn_searchEmail' );


// Left Menu Button
function register_nsn_menu() {

	add_menu_page('Subscribers', 'Subscribers', 'manage_options', NORD_ADMIN_DIR . '/nsn_index.php','','dashicons-carrot', 58.122);
	add_submenu_page( NORD_ADMIN_DIR . '/nsn_index.php', 'List', 'List','manage_options', NORD_ADMIN_DIR . '/nsn_index.php');
	//add_submenu_page( NORD_ADMIN_DIR . '/nsn_index.php', 'Letter', 'Letter' ,'manage_options', NORD_ADMIN_DIR .'/nsn_letter.php');
	
}
add_action('admin_menu', 'register_nsn_menu');

// uuid
function nsn_guidv4()
{
    if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');

    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function nsn_DB_Tables_Rows()
{
		global $wpdb;
		$table_name = $wpdb->prefix . 'nsn';
		$my_query = $wpdb->get_results( "SELECT * FROM $table_name" );
		//echo $wpdb->num_rows;

    echo  '<b style="background-color:#0073AA;color:white;padding:4px 16px">Number of records: '  . $wpdb->num_rows . '</b>';
}

// CSV AJAX stuff
//=====================================================================

add_action( 'admin_footer', 'nsn_download_csv' ); // Write our JS below here

function nsn_download_csv() { ?>
	<script type="text/javascript" >
function download(content, filename, contentType)
{
    if(!contentType) contentType = 'application/octet-stream';
        var a = document.createElement('a');
        var blob = new Blob([content], {'type':contentType});
        a.href = window.URL.createObjectURL(blob);
        a.download = filename;
        a.click();
}
	
	
	
	jQuery(document).ready(function($) {

		var data = {
			'action': 'nsncsv',
			'nsn_nonce':'<?php echo wp_create_nonce('nsn_csv_nonce'); ?>'
		};

	jQuery('#nsncsvd').on('click', function(){
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
		//	alert('Got this from the server: ' + response);
			let filename = "Subsribers-" + new Date().toISOString().slice(0,10) + ".csv";
			download(response, filename, "text/csv");
		});
	});
		
		
		
		
	});
	</script> <?php
}

add_action( 'wp_ajax_nsncsv', 'nsncsv' );

function nsncsv() {

	$nonce =  $_POST['nsn_nonce'] ;
		
		if ( wp_verify_nonce( $_REQUEST['nsn_nonce'], 'nsn_csv_nonce' ) ) {
			
			if (current_user_can('manage_options')) {
	
				global $wpdb;
					
					$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."nsn");
				
			ob_start();
					echo "First Name,Last Name,Email Address\r\n";
						if (count($results))  {
							foreach($results as $row) {
							$n = nsn_doSplitName($row->nsn_name);
							//echo $n['first'].','.$n['last'].','.$row->nsn_email."\r\n";
							echo $row->nsn_email . ','. $n['first'] . "\r\n";
				}
				
				
			}
			$csv = ob_get_clean();
		}//manage options
			
			
		}//nonce
		
		echo $csv;
       // ' > '. wp_verify_nonce( $_REQUEST['nsn_nonce'], 'nsn_csv_nonce' );

	wp_die(); // this is required to terminate immediately and return a proper response
} // end of fnc


// this is helper fnc
function nsn_doSplitName($name) {
    $results = array();

    $r = explode(' ', $name);
    $size = count($r);

    if (mb_strpos($r[0], '.') === false) {
        $results['salutation'] = '';
        $results['first'] = $r[0];
    } else {
        $results['salutation'] = $r[0];
        $results['first'] = $r[1];
    }

    if (mb_strpos($r[$size - 1], '.') === false) {
        $results['suffix'] = '';
    } else {
        $results['suffix'] = $r[$size - 1];
    }

    $start = ($results['salutation']) ? 2 : 1;
    $end = ($results['suffix']) ? $size - 2 : $size - 1;

    $last = '';
    for ($i = $start; $i <= $end; $i++) {
        $last .= ' '.$r[$i];
    }
    $results['last'] = trim($last);

    return $results;
}










?>