<?php
function wbfd_settings_data(){
  $save_settings = array(
                        'btn_src'  =>  array('bg_btn' => WVSD_URL.'/includes/images/background.png', 'logo_btn' => WVSD_URL.'/includes/images/logo.png', 'more_text_btn' => WVSD_URL.'/includes/images/more-text-frontend.png', 'save_btn' => WVSD_URL.'/includes/images/save-design-frontend.png', 'add_to_cart_btn' => WVSD_URL.'/includes/images/add-to-cart.png'),
                        'btn_visibility' => array('bg_btn' => 1, 'logo_btn' => 1, 'more_text_btn' => 1, 'save_btn' => 1)
                        
                        
  );
  if ( get_option( 'wbfd_new_settings_data' ) === false ) {
    add_option( 'wbfd_new_settings_data', $save_settings );
  }
}

function wbfd_plugin_table_install(){
  global $wpdb;
  $table =                        $wpdb->prefix."wbfd_custom_data_save";
  $table_name_temp_design_save =  $wpdb->prefix ."wbfd_temp_design_save_by_user";
  $table_name_design_save =       $wpdb->prefix ."wbfd_design_save_by_user";
  
  $sql = "CREATE TABLE IF NOT EXISTS $table(
                                            id int(11) NOT NULL auto_increment,                                                                 
                                            design_url longtext,
                                            logo_url longtext,
                                            bg_url longtext,
                                            uniqid VARCHAR(150),
                                            PRIMARY KEY  (id)
  );";
  
  $wpdb->query( $sql );
  
  $sql_temp_design_save = "CREATE TABLE IF NOT EXISTS $table_name_temp_design_save(
                                                    id int(11) NOT NULL auto_increment,                                                        
                                                    design_front longtext,
                                                    design_back longtext,
                                                    logo_url longtext,
                                                    bg_url longtext,
                                                    uniqid VARCHAR(150),
                                                    PRIMARY KEY  (id)
    );";
   $wpdb->query($sql_temp_design_save);
   
   $sql_design_save = "CREATE TABLE IF NOT EXISTS $table_name_design_save(
                                                    id int(11) NOT NULL auto_increment,                                                        
                                                    design_front longtext,
                                                    design_back longtext,
                                                    logo_url longtext,
                                                    bg_url longtext,
                                                    product_id int(11),  
                                                    login_user_id int(11),
                                                    PRIMARY KEY  (id)
    );";
   $wpdb->query($sql_design_save);
   
}
function wbfd_plugin_install(){
  wbfd_plugin_table_install();
  wbfd_settings_data();
}

function wbfd_admin_page(){
  add_submenu_page( 'woocommerce', __('WBFD-Settings', 'prowbfd'),  __('WBFD-Settings', 'prowbfd') , 'manage_woocommerce', 'wbfd-btn-upload', 'wbfd_btn_upload');
}
add_action('admin_menu', 'wbfd_admin_page');
?>