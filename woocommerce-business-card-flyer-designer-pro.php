<?php
/*
Plugin Name: Woocommerce business card and flyer designer pro
Plugin URI: http://www.solvercircle.com
Description:This plugin is used to design or customize cards by users.  
Version: 1.1
Author: SolverCircle
Author URI: http://www.solvercircle.com
*/

if(!defined('ABSPATH')) exit;
define('WVSD_URL', plugins_url('',__FILE__));
define('WVSD_PATH', plugin_dir_path( __FILE__ ));

include ( 'includes/ajax.php' );
include ( 'includes/setup.php' );
include ( 'includes/zipArchive.php' );
include ( 'includes/custom-image-display.php' );
include ( 'includes/wbfd-settings.php' );

add_action( 'admin_init', 'wbfd_custom_stickers_design_metabox');

function wbfd_custom_stickers_design_metabox() {
  add_meta_box('wbfd_custom_metabox', __('Custom Business Card Design Option', 'prowbfd'), 'wbfd_designer_custom_metabox_content', 'product', 'normal', 'high');
}

function wbfd_designer_custom_metabox_content($product){
  $get_custon_width = 400;
  $get_custon_height = 200;
  
  $get_id =                    get_post_custom($product->ID);
  
  
  if(isset($get_id['_wbfd_designer_enable_checkbox_on_metabox'])){
    $metabox_checkbox_status = 	$get_id['_wbfd_designer_enable_checkbox_on_metabox'][0];
  }
  else{
    $metabox_checkbox_status = false;
  }
  
  $get_custon_price=           esc_html( get_post_meta($product->ID, '_wbfd_design_custom_price', true ));
  
  if(esc_html( get_post_meta($product->ID, '_wbfd_design_width', true ))){
    $get_custon_width=           esc_html( get_post_meta($product->ID, '_wbfd_design_width', true ));
  }
  
  if(esc_html( get_post_meta($product->ID, '_wbfd_design_height', true ))){
    $get_custon_height=          esc_html( get_post_meta($product->ID, '_wbfd_design_height', true ));
  }
  
  $get_front_bg_img=           esc_html( get_post_meta($product->ID, '_wbfd_front_background_image', true ) );
  $get_back_bg_img=            esc_html( get_post_meta($product->ID, '_wbfd_back_background_image', true ) );
  
  $disableFrontUploader = '';
  $disableBackUploader = '';
  
  if($get_front_bg_img){
    $disableFrontUploader = 'pointer-events:none;';
  }
 else {
    $disableFrontUploader = 'cursor:pointer;';
  }
  
  if($get_back_bg_img){
    $disableBackUploader = 'pointer-events:none;';
  }
  else {
    $disableBackUploader = 'cursor:pointer;';
  }
  
?>
<table>
  <tr>
    <td><?php echo _e('Enable for Custom Design?', 'prowbfd');?>:</td>
    <td><input type="checkbox" name="custom_stickers_design_enable_checkbox" id="custom_stickers_design_enable_checkbox" <?php if( $metabox_checkbox_status == true ) { ?>checked="checked"<?php }?> /></td>
  </tr>
  <tr>
    <td><?php echo _e('Custom Price', 'prowbfd');?>:</td>
    <td><input type="number" name="stickers_design_custom_price" id="stickers_design_custom_price" value="<?php echo $get_custon_price;?>" style="width:80px;"/></td>
  </tr>
  <tr>
    <td><?php echo _e('Design Width', 'prowbfd');?>:</td>
    <td><input type="number" name="wbfd_design_width" id="wbfd_design_width" value="<?php echo $get_custon_width;?>" style="width:80px;"/></td>
  </tr>
  <tr>
    <td><?php echo _e('Design Height', 'prowbfd');?>:</td>
    <td><input type="number" name="wbfd_design_height" id="wbfd_design_height" value="<?php echo $get_custon_height;?>" style="width:80px;"/></td>
  </tr>
  <?php if(!get_post_meta($product->ID, '_wbfd_front_background_image', true ) ||  !get_post_meta($product->ID, '_wbfd_back_background_image', true )){?>
  <tr>
    <td><?php echo _e('Upload Front Background Image', 'prowbfd');?>:</td>
    <td><input type="text" readonly="true" name="front_background_img_upload_url" id="front_background_img_upload_url"  style="width:200px;" value="" /></td>  
    <td><input data-side="front" id="front_background_img_uploader" name="front_background_img_uploader" class="button-primary" style="<?php echo $disableFrontUploader;?>" type="button" value="Browse"/></td>
  </tr>
 
  <tr>
    <td><?php echo _e('Upload Back Background Image', 'prowbfd');?>:</td>
    <td><input type="text" readonly="true" name="back_background_img_upload_url" id="back_background_img_upload_url"  style="width:200px;" value="" /></td>  
    <td><input data-side="back" id="back_background_img_uploader" name="back_background_img_uploader" class="button-primary" style="<?php echo $disableBackUploader;?>" type="button" value="Browse"/></td>
  </tr>
  <?php }?>
  
</table>

<?php if(get_post_meta($product->ID, '_wbfd_front_background_image', true ) ||  get_post_meta($product->ID, '_wbfd_back_background_image', true )){?>
<div class="wbfd_design_popup"><input id="wbfd_design_popup_open" name="wbfd_design_popup_open" class="button-primary" style="width:100px; cursor:pointer;" type="button" value="Design Popup" /></div>
<div class="wbfd_overlay"></div>
<div class="wbfd_design_popup_content">
    <div class="wbfd_close_popup"></div>
    <iframe name="iframe_popup" id="iframe_popup" width="950" height="638" scrolling="no"></iframe>
</div>
<input type="hidden" name="plugin_url" id="plugin_url" value="<?php echo WVSD_URL;?>">
<input type="hidden" name="front_bg" id="front_bg" value="<?php echo $get_front_bg_img;?>">
<input type="hidden" name="back_bg" id="back_bg" value="<?php echo $get_back_bg_img;?>">
<input type="hidden" name="product_id" id="product_id" value="<?php echo $product->ID;?>">
<?php }?>
<?php 
}

add_action( 'save_post','wbfd_metabox_content_save', 10, 2 );

function wbfd_metabox_content_save( $post_id ){
  $front_background_img=             esc_html( get_post_meta($post_id,'_wbfd_front_background_image', true ));
  $back_background_img=              esc_html( get_post_meta($post_id,'_wbfd_back_background_image', true ));
  
  if(isset($_POST['custom_stickers_design_enable_checkbox'])){
    update_post_meta($post_id,  '_wbfd_designer_enable_checkbox_on_metabox', $_POST['custom_stickers_design_enable_checkbox'] );
  }
  else{
    update_post_meta($post_id,  '_wbfd_designer_enable_checkbox_on_metabox', false );
  }
  
  if(isset($_POST['stickers_design_custom_price'])){
    update_post_meta($post_id,  '_wbfd_design_custom_price', $_POST['stickers_design_custom_price'] );
  }
  else{
    update_post_meta($post_id,  '_wbfd_design_custom_price', '' );
  }
  
  if(isset($_POST['wbfd_design_width'])){
    update_post_meta($post_id,  '_wbfd_design_width', $_POST['wbfd_design_width'] );
  }
 else {
    update_post_meta($post_id,  '_wbfd_design_width', '' );
  }
  
  if(isset($_POST['wbfd_design_height'])){
    update_post_meta($post_id,  '_wbfd_design_height', $_POST['wbfd_design_height'] );
  }
  else{
    update_post_meta($post_id,  '_wbfd_design_height', '' );
  }

  if(!empty($_POST["front_background_img_upload_url"])){
    update_post_meta($post_id,  '_wbfd_front_background_image', $_POST["front_background_img_upload_url"]);
  }
  else{
    update_post_meta($post_id,  '_wbfd_front_background_image', $front_background_img);
  }
  
  if(!empty($_POST["back_background_img_upload_url"])){
    update_post_meta($post_id,  '_wbfd_back_background_image', $_POST["back_background_img_upload_url"]);
  }
  else{
    update_post_meta($post_id,  '_wbfd_back_background_image', $back_background_img);
  } 
}

function wbfd_plugin_assets_load(){
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-core ');
  wp_enqueue_script( 'jquery-ui-tooltip' );
  wp_enqueue_script('fabric-js', plugins_url( '/includes/js/fabric-1.5.0.min.js', __FILE__ ));
  wp_enqueue_script('customiseControls-js', plugins_url( '/includes/js/customiseControls.min.js', __FILE__ ));
  wp_enqueue_script('wbfdAjaxUploadJs', plugins_url( '/includes/js/ajaxupload.3.5.js', __FILE__ ));
  wp_enqueue_script('wbfdColorJs', plugins_url( '/includes/js/jscolor/jscolor.js', __FILE__ ));
  wp_enqueue_style('wbfd-stickers-design-style',   plugins_url( '/includes/css/stickers-design.css', __FILE__ ));
  wp_enqueue_style('wbfd-stickers-design-ui-style',   plugins_url( '/includes/css/stickers-design-ui.css', __FILE__ ));
  
  if( !session_id() ){
    session_start();
  }
  
  global $wpdb;
  if (isset($_SESSION['wbfd_save_id']) && isset($_SESSION['wbfd_timeout'])){
				if($_SESSION['wbfd_timeout']<time()){
								$wpdb->delete($wpdb->prefix.'wbfd_temp_design_save_by_user', array( 'uniqid' =>$_SESSION['wbfd_save_id']) );

								unset($_SESSION['wbfd_save_id']);
								unset($_SESSION['wbfd_timeout']);
				}
  }
  
  wbfd_load_plugin_textdomain_urls();
}

function wbfd_load_custom_wp_admin_script(){
  wp_enqueue_script('stickers-design-js', plugins_url( '/includes/js/stickers-design.js', __FILE__ ));
  wp_enqueue_script('wbfdDesignJs', plugins_url( '/includes/js/design.js', __FILE__ ));
}

function wbfd_load_design_js_frontend(){
  wp_enqueue_script('wbfdDesignFrontendJs', plugins_url( '/includes/js/design.js', __FILE__ ));
  wp_localize_script( 'wbfdDesignFrontendJs', 'wbfdAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}

function wbfd_use_custom_template( $template ){
  $template_slug = basename(rtrim( $template, '.php' ));
  
  if( ($template_slug === 'single-product' || $template_slug === 'woocommerce') && isset($_GET['type']) && $_GET['type'] == 'sc-customize-product'){
    $template = WVSD_PATH . 'includes/template.php';
  }
  else if($template_slug === 'page' && isset($_REQUEST['user'])){
    $template = WVSD_PATH . 'includes/user-saved-custom-design-list.php';
  }
  
  return $template;
}

add_filter('woocommerce_login_redirect', 'wbfd_login_redirect');
add_filter('woocommerce_registration_redirect','wbfd_login_redirect_after_registration');
 
function wbfd_login_redirect( $redirect_to ) {  
  if (isset($_SESSION['wbfd_save_id']) && isset($_SESSION['wbfd_product_id'])){
    $redirect_to = get_permalink( $_SESSION['wbfd_product_id'] );
  }
  else{
    $redirect_to = $redirect_to;
  }

  return $redirect_to;
}

function wbfd_login_redirect_after_registration(){
  if (isset($_SESSION['wbfd_save_id']) && isset($_SESSION['wbfd_product_id'])){
    $reg_redirect_to = get_permalink( $_SESSION['wbfd_product_id'] );
  }
  return $reg_redirect_to;
}

function wbfd_remove_custom_data( $meta_value ){
  return array(
       '_qty',
       '_tax_class',
       '_product_id',
       '_variation_id',
       '_line_subtotal',
       '_line_subtotal_tax',
       '_line_total',
       '_line_tax',
       'Type',
       'wbfd_custom_price'
      );
}

function wbfd_curPageURL() {
  $pageURL = 'http';
  if(isset($_SERVER["HTTPS"])){
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
  }
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

function wbfd_design_edit_link_show(){
  $login_success_page = get_permalink(get_option('woocommerce_myaccount_page_id'));
  $current_url = wbfd_curPageURL();
  
  if(is_user_logged_in() && !is_super_admin() && is_account_page()){
    $current_user_id = get_current_user_id();
    $shop_page_url = esc_url( add_query_arg( 'user', $current_user_id, get_permalink(get_option('woocommerce_myaccount_page_id'))));
?>
<script type="text/javascript">
    var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    jQuery(document).ready(function(){
						if(jQuery('.woocommerce-MyAccount-content').length>0){
								jQuery.ajax({
										type: "POST",
										url:ajax_url,
										data : {
																		'action': 'wbfd_check_is_design_html_by_user'
													},
										success: function(response){
												if(response>0){
														jQuery('.woocommerce-MyAccount-content').append('<div style="margin-bottom:15px;width:100%;text-align:right;"><a class="button" href="<?php echo WVSD_URL;?>/includes/user-saved-custom-design-list.php?page=wbfd_sc_designer_design_list&type=wbfd_design&user=<?php echo $current_user_id;?>">Edit your design</a></div>');
												}
										}
								});
      }
    });
</script>
<?php 
  }
}

function wbfd_button_view_on_details_page(){
  if( is_product() ){
    global $product;
    $id                =  $product->post->ID;
    $hasCustomization  =  get_post_meta( $id,'_wbfd_designer_enable_checkbox_on_metabox', true );
    $url               =  get_permalink( $id );
    
    if($hasCustomization == 'on'){
						echo '<div style="width: 100%;"><a class="button button-primary button-large" href="'.WVSD_URL.'/includes/template.php?product_id='.$id.'&page=wbfd_design_page&type=wbfd_design">Customize</a></div>';
    }
  }
}

function wbfd_load_plugin_textdomain_urls() {
		load_plugin_textdomain( 'prowbfd', false, 'woocommerce-business-card-flyer-designer-pro/languages');
}

add_action( 'woocommerce_hidden_order_itemmeta','wbfd_remove_custom_data' );
add_action( 'init', 'wbfd_plugin_assets_load');
add_action( 'admin_footer', 'wbfd_load_custom_wp_admin_script');
add_action( 'wp_enqueue_scripts', 'wbfd_load_design_js_frontend' );
add_action( 'wp_footer', 'wbfd_custom_image_display_on_cart' );
add_action( 'wp_footer', 'wbfd_design_edit_link_show');
add_action( 'admin_footer', 'wbfd_custom_image_display_on_order_admin' );
add_action( 'woocommerce_after_add_to_cart_form','wbfd_button_view_on_details_page' );
//add_filter( 'template_include', 'wbfd_use_custom_template', 999);
register_activation_hook( __FILE__, 'wbfd_plugin_install' );
?>