<?php do_action('nsn_export_CSV'); ?>

<?php
if ( ! defined( 'ABSPATH' ) ) { exit; };
if( !is_admin() ) { exit; }
if ( ! current_user_can( 'manage_options' ) ) { wp_die( __( 'You are not allowed to access this part of the site' ) ); };
?>


 <style>
.page-numbers {
	margin: 4px;
	padding: 2px;
	font-size: 16px;
	font-weight: bold;
}

#brisi:disabled {
	cursor: not-allowed
}
</style>
 


<div class="wrap">
	
	<div>
	<h3 style="display:inline-block">SUBSCRIBERS</h3>
	<img style="float:right;display:inline-block;opacity: 0.8;" alt="NORD" src="<?php echo plugin_dir_url( __FILE__ ).'Nord.png' ?>"  />
	</div>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-1">
		
		
		
			<!-- main content -->
			<div id="post-body-content">
			
			
			
			<div class="meta-box-sortables">
                <div class="postbox">
                        	
                       	  <h3><span>Search Email</span></h3>
                    <div class="inside">
					
						 <form id="search-email" method="post" action="?page=<?=esc_js(esc_html($_GET['page']));?>">
						 <?php wp_nonce_field( 'nsn_search_mail', 'nsn_search_mail_form' ); ?>
							<input type="email" name="nsn_mail_serch" value="" class="all-options" placeholder="email to search" required />
							<input class="button-primary" name="nsn_search_email" type="submit" value="Search email" />
						</form>
					
					</div>
				</div>
			</div>
			
			<?php	
			// table pagination stuff
			if (current_user_can('manage_options')) {
				$customPagHTML     = "";
				$query             = "SELECT * FROM ".$wpdb->prefix."nsn";
				$total_query     = "SELECT COUNT(1) FROM (${query}) AS combined_table";
				$total           = $wpdb->get_var( $total_query );
				$items_per_page = 20;
				$page             = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
				$offset         = ( $page * $items_per_page ) - $items_per_page;
				$result         = $wpdb->get_results( $query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}" );
							
							//var_dump($result);
				$totalPage         = ceil($total / $items_per_page);
							
							
			if($totalPage > 1){
				$customPagHTML =  '<div class="nsn_sort"><span>Page '.$page.' of '.$totalPage.'</span>'.paginate_links( array(
							'base' => add_query_arg( 'cpage', '%#%' ),
							'format' => '',
							'prev_text' => __('&laquo;'),
							'next_text' => __('&raquo;'),
							'total' => $totalPage,
							'current' => $page
						)).'</div>';
					}
					
			}
			
			if ( !$_POST['nsn_search_email'] ) {
					echo $customPagHTML; // echo links and nubers....
					}
            			?>
					
						<table cellspacing="0" class="wp-list-table widefat fixed subscribers">
                          <thead>
                            <tr>
                                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
                                <th style="" class="manage-column column-name" id="name" scope="col">Name<span class="sorting-indicator"></span></th>
                                <th style="" class="manage-column column-email" id="email" scope="col"><span>Email Address</span><span class="sorting-indicator"></span></th>
								<th style="" class="manage-column column-email" id="uuid" scope="col"><span>UUID</span><span class="sorting-indicator"></span></th>
                            </thead>
                        
                            <tfoot>
                            <tr>
                                <th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
                                <th style="" class="manage-column column-name" scope="col"><span>Name</span><span class="sorting-indicator"></span></th>
                                <th style="" class="manage-column column-email" scope="col"><span>Email Address</span><span class="sorting-indicator"></span></th>
								<th style="" class="manage-column column-email" scope="col"><span>UUID</span><span class="sorting-indicator"></span></th>
                            </tfoot>
                        
                            <tbody id="the-list">
			
			
				
			<form method="post" action="?page=<?php echo esc_js(esc_html($_GET['page'])); ?>">
			
			
							
			<!--
            <input name="nsn_remove" value="1" type="hidden" />
			-->         
            <?php
				if ($_SERVER['REQUEST_METHOD']=="POST" and $_POST['nsn_search_email']) {
							
				if (current_user_can('manage_options')) {				
								
					global $nsn_searched_email_result;
								
				echo '<tr>
						<th class="check-column" style="padding:5px 0 2px 0">
							<input type="checkbox" name="rem[]" value="'.esc_js(esc_html($nsn_searched_email_result['id'])).'">
						</th>
								<td>'.esc_js(esc_html($nsn_searched_email_result['name'])).'</td>
  								<td>'.esc_js(esc_html($nsn_searched_email_result['mail'])).'</td>
								<td>'.esc_js(esc_html($nsn_searched_email_result['uid'])).'</td>
					</tr>';
							}
				}
					if ( !$_POST['nsn_search_email'] || $_POST['nsn_reload']) {
				if (current_user_can('manage_options')) {
					if (count($result) < 1) echo '<tr class="no-items"><td colspan="3" class="colspanchange">No mailing list subscribers have been added.</td></tr>';
								else {
									foreach($result as $row) {
	
									echo '<tr>
										<th class="check-column" style="padding:5px 0 2px 0"><input type="checkbox" name="rem[]" value="'.esc_js(esc_html($row->id)).'"></th>
											<td>'.esc_js(esc_html($row->nsn_name)).'</td>
  											<td>'.esc_js(esc_html($row->nsn_email)).'</td>
											<td>'.esc_js(esc_html($row->nsn_uuid)).'</td>
										</tr>';
											  
									}
								}
				}

	}//if not nsn_search
							?>
                            
                                
                            </tbody>
                        </table>
						
						<?php
					if ( !$_POST['nsn_search_email'] ) {
						echo $customPagHTML;
					}
					?>
					
						
						<br class="clear">
						<?php echo "<br/><div style='float:right'><b>".nsn_DB_Tables_Rows()."</b></div>"; ?>
						
                        <br class="clear">
						<input class="button" name="nsn_remove" type="submit" value="Remove Selected" />
					
					
						
						<a id="nsncsvd" class="button" href="#" >Export as CSV</a>
					
					
					
					<input class="button" name="nsn_reload" type="submit" value="Load / Reload List" />
						<br class="clear">
				</form>
				<br class="clear">

		
				
			<div class="meta-box-sortables">
                <div class="postbox">
                        	
                       	  <h3><span>Insert data to Subscribers list</span></h3>
                    <div class="inside">
					
						 <form id="insert-manual" method="post" action="?page=<?=esc_js(esc_html($_GET['page']));?>">
							<input type="text" name="nsn_ime" value="" class="all-options" placeholder="Name" />
							<input type="email" name="nsn_mail" value="" class="all-options" placeholder="email" required />
							<input class="button-primary" name="nsnaddtolist" type="submit" value="Add to list" />
						</form>
					
					</div>
				</div>
			</div>
				
				
				
				
				
                
                
                <div class="meta-box-sortables">
                        <div class="postbox">
                        	
                       	  <h3><span>Import your own CSV File</span></h3>
                          <div class="inside">
                
                <p>This feature allows you to import your own csv (comma seperated values) file into &quot;Mail Subscribe List&quot;.</p>

                <form id="import-csv" method="post" enctype="multipart/form-data" action="?page=<?=esc_js(esc_html($_GET['page']));?>">
                <input name="nsn_import" value="1" type="hidden" />
                <p><label><input name="file" type="file" value="" /> CSV File</label></p>
                <p class="description">File must contain no header row, each record on its own line and only two comma seperated collumns in the order of email address followed by name. The name field is optional.</p>
                <p>Example: joe@blogs.com,Joe Blogs</p>
                
                <br class="clear">
                
                <p class="submit"><input type="submit" value="Upload and Import CSV File" class="button-secondary" id="submit" name="submit"></p>
				</form>
                </div></div></div>
                
         <br class="clear">
                
				
			<div class="meta-box-sortables">
                <div class="postbox">
                        	
                       	  <h3><span>Clear List (delete all in Subscribers list ...)</span></h3>
                    <div class="inside">
					
						 <form id="clear-list" method="post" action="?page=<?=esc_js(esc_html($_GET['page']));?>">
	<input id="brisi" type="submit" value="Delete All from Subscribers list" class="button-secondary" name="nsndelall" style="background-color:#DC3232;color:white" disabled>
	<span id="dozvola-icon" class="dashicons dashicons-lock" style="margin:4px;margin-right:-4px;margin-left:32px"></span>
	<input id="dozvola" type="checkbox" style="margin: 6px;" title="enable / disable delete all option">
						</form>
					
					</div>
				</div>
			</div>
				
				
				
				
				
				
			</div> <!-- div-content -->
			
			 <br class="clear">
            </div>
           
	</div>
	
</div> 

<script>
(function($) {
	
	// $ Works! You can test it with next line if you like
	// console.log($);
	
	$('#dozvola').on('click',function(){
		
	var is_on = $('#dozvola').is(':checked');
	
		if ( is_on ) {
			$('#dozvola-icon').removeClass('dashicons-lock');
			$('#dozvola-icon').addClass('dashicons-unlock');
			$('#brisi').prop("disabled",false);
		} else {
			$('#dozvola-icon').removeClass('dashicons-unlock');
			$('#dozvola-icon').addClass('dashicons-lock');
			$('#brisi').prop("disabled",true);
		}

		});
	
})( jQuery );
</script>

