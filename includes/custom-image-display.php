<?php 
function wbfd_custom_image_display_on_cart(){
  if(is_cart()){
    global $woocommerce;
    $cart_url = $woocommerce->cart->get_cart_url();
    $uploads = wp_upload_dir();
    $upload_path = $uploads['baseurl'];
?>
  <style type="text/css">
  .wbfd_custom_design_style
  {
    background: none repeat scroll 0 0 #0099FF;
    color: #FFFFFF !important;
    display: block;
    font-size: 10px !important;
    margin-bottom: 10px;
    height: 20px !important;
    /*margin-left:-25px;*/
    text-align: center;
    text-decoration: none;
    width:90px !important;
    font-weight: bold;
  }
  .wbfd_product_custom_image a:hover{
   color: #FFFFFF !important;
   text-decoration: none !important;
  }

  .wbfd_edit_link
  {
    padding-right:10px;
    color: #0099FF !important;
  }
  </style>
  <div id="wbfdToDisplayCustomImage">
    <div>
      <div style="float: left; color:#0099FF;font-size:15px;font-weight:bold;height: 20px;text-align: center;width:95%; margin-top: 10px;"><?php echo _e('Custom Design', 'prowbfd');?></div>
      <div style="float: left; margin-top:3px; font-size:22px; cursor:pointer;" onclick="wbfd_close_dialog();">x</div>
      <div style="clear:both;"></div>
    </div>
    <div id="wcp_custom_img_content"></div>
  </div>
  <div class="wbfd_overlay"></div>
  <script type="text/javascript">
  jQuery(document).ready(function(){
    if(jQuery('table tr').hasClass('cart_table_item')){
      jQuery('.cart_table_item dd').each(function() {
        var session_id = jQuery(this).html();
        var design_type = jQuery(this).prev('dt').html();
        var getids = session_id.toString().split('_');
        var design_name = design_type.toString().split(':');

        if(getids[0] == 'custom-type' && design_name[0] == 'design'){
          jQuery(this).parent(".variation").parent(".product-name").prev('td.product-thumbnail').addClass('img_'+session_id);
          jQuery('.img_'+session_id).append('<div class="wbfd_product_custom_image" style="margin-top:10px;"><a href="javascript:void(0);" class="wbfd_custom_design_style" onclick=wbfd_custom_image_display("'+ getids[1] +'");>Custom Design</a></div>');
        }	
      });   
    }
    if(jQuery('table tr').hasClass('cart_item')){
      jQuery('.shop_table .cart_item .variation dd').each(function() {
        var design_type = jQuery(this).find('p').html();
        var design_name = design_type.toString().split('_');
        
        if( design_name[0] === 'custom-type' ){
          var session_id = design_name[1];
          jQuery(this).parents("td.product-name").prev('td.product-thumbnail').addClass('img_'+session_id);
          
          if(jQuery('.img_' + session_id).find('.wbfd_product_custom_image').length === 0){
            jQuery.ajax({
              type: "POST",
              url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
              data : {
                'action':    'wbfd_get_customize_img',
                'uids':       session_id
                 },
              success: function(response){
                if(response != ''){
                  var base_url = '<?php echo $upload_path;?>';
                  
                  jQuery('.img_'+session_id).find('img').attr('src',  base_url + response);
                  jQuery('.img_'+session_id).find('img').attr('srcset', base_url + response);
                  
                  jQuery('.img_'+ session_id).append('<div class="wbfd_product_custom_image" style="margin-top:10px;"><a href="javascript:void(0);" class="wbfd_custom_design_style" onclick=wbfd_custom_image_display("'+ session_id +'","fromCart");>Custom Design</a></div>');
                }
              }
            });
          }
        }	
      });   
    }
  });

  function wbfd_custom_image_display( uids ){
    var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    jQuery.ajax({
      type: "POST",
      url:ajax_url,
      data : {
              action:    'wbfd_get_custom_design_with_html',
              uids:       uids
      },
      success: function(response){
        if(response!=''){
          jQuery('.wbfd_overlay').show();  
          jQuery("#wbfdToDisplayCustomImage").show();
          jQuery("#wcp_custom_img_content").html(response);
        }
      }
    }); 
  }

  function wbfd_close_dialog(){
    jQuery('.wbfd_overlay').hide();  
    jQuery("#wbfdToDisplayCustomImage").hide();
  }
  </script>
<?php
  }
}
  
function wbfd_custom_image_display_on_order_admin(){
		$uploads = wp_upload_dir();
  $upload_path = $uploads['baseurl'];
?>
    <style type="text/css">
    .wbfd_custom_design_style
    {
      background: none repeat scroll 0 0 #0099FF;
      color: #FFFFFF !important;
      display: block;
      font-size: 10px !important;
      height: 25px !important;
      margin-left:-25px;
      text-align: center;
      text-decoration: none;
      width:90px !important;
      font-weight: bold;
      margin-top:10px;
    }
    .wbfd_product_custom_image a:hover{
      color: #FFFFFF !important;
    }
    </style>
    
<script type="text/javascript">
jQuery(document).ready(function(){
		var getValName = '';
  if(jQuery('div').hasClass('woocommerce_order_items_wrapper')){ 
    if(jQuery('table').hasClass('woocommerce_order_items')){     
						jQuery('#order_line_items').find('tr.item .thumb').each(function() {
								if(jQuery(this).next('.name').find('div.view').length>0){
										jQuery(this).next('.name').find('table.display_meta td p').each(function(){
												if(jQuery(this).html().split('_')[0] == 'custom-type'){
														getValName = jQuery(this).html().split('_')[1];
														return false;
												}
										});
								}

								if(getValName !='' ){
										var getsessionsid = getValName;

										jQuery(this).attr('id', getsessionsid);
										jQuery('#'+ getsessionsid ).append('<div class="wbfd_product_custom_image"><a data-id="'+ getsessionsid +'" data-source="fromAdmin" style="color:#FF0000;" class="wbfd_custom_design_style" href="javascript:void(0);" data-reveal-id="myModal" data-animation="none">Custom Design</a></div>');
										
										jQuery('.wbfd_product_custom_image a').on('click', function(){
												wbfd_popupCustom(jQuery(this).data('id'), jQuery(this).data('source'));
										});

//										var ajax_url = '<?php //echo admin_url( 'admin-ajax.php' ); ?>';
//										jQuery.ajax({
//												type: "POST",
//												url:ajax_url,
//												data : {
//														'action':      'wbfd_get_customize_img',
//														'uids':        getsessionsid
//															},
//												success: function(response){
//														if(response!=''){
//																var base_url = '<?php //echo $upload_path;?>';
//																jQuery('#'+ getsessionsid ).find('img').attr("src", base_url + response);
//																jQuery('#'+ getsessionsid ).append('<div class="wbfd_product_custom_image"><a id="'+ getsessionsid +'" style="color:#FF0000;" class="wbfd_custom_design_style" href="javascript:void(0);" data-reveal-id="myModal" data-animation="none" onclick=wbfd_popupCustom("'+ getsessionsid +'", "fromAdmin");>Custom Design</a></div>'); 
//														}
//												}
//										}); 
								}
						}); 
				}    
		}  
});

function wbfd_popupCustom(id, from){
  var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
  jQuery.ajax({
    type: "POST",
    url:ajax_url,
    data : {
            action:    'wbfd_get_custom_design_with_html',
            uids:       id,
            from:       from  
    },
    success: function(response){
      if(response!=''){
        jQuery('.wbfd_overlay').show();  
        jQuery("#wbfdToDisplayCustomImage").show();
        jQuery("#wcp_custom_img_content").html(response);
      }
    }
  }); 
}

function wbfd_close_dialog(){
  jQuery('.wbfd_overlay').hide();  
  jQuery("#wbfdToDisplayCustomImage").hide();
}
</script>
<div id="wbfdToDisplayCustomImage">
  <div>
    <div style="float: left; color:#0099FF;font-size:15px;font-weight:bold;height: 20px;text-align: center;width:95%; margin-top: 10px;"><?php echo _e('Custom Design', 'prowbfd');?></div>
    <div style="float: left; margin-top:3px; font-size:22px; cursor:pointer;" onclick="wbfd_close_dialog();">x</div>
    <div style="clear:both;"></div>
  </div>
  <div id="wcp_custom_img_content"></div>
</div>
<div class="wbfd_overlay"></div>
<?php
  }
//}
  
function wbfd_get_custom_design_with_html(){
  global $wpdb;
  $str = '';
  $upload_dir = wp_upload_dir();
  
  $sql = "SELECT * FROM ".$wpdb->prefix."wbfd_custom_data_save WHERE uniqid='".$_REQUEST['uids']."'";
  $results = $wpdb->get_row($sql);
  $parseUrl = explode(',', trim($results->design_url, ','));
  
  if($parseUrl){
    foreach($parseUrl as $url){
      $str .= '<div style="display:inline-block;"><img style="width:400px;" src="'. $upload_dir['baseurl'].$url .'"></div>';
    }
  }
  
  if(isset($_REQUEST['from']) && $_REQUEST['from'] == 'fromAdmin'){
    $str .= '<div>'; 
    if($results->bg_url){
      $str .= '<div style="display:inline-block;margin-right:10px;"><a href="'.WVSD_URL.'/includes/design-print.php?type=bg&id='. $_REQUEST['uids'] .'"  target="_blank">'. __('Print/Save BG', 'prowbfd'). '</a></div>';
    }
    if($results->logo_url){
      $str .= '<div style="display:inline-block;margin-right:10px;"><a href="'.WVSD_URL.'/includes/design-print.php?type=logo&id='. $_REQUEST['uids'] .'"  target="_blank">'.__('Print/Save Logo', 'prowbfd').'</a></div>';
    }
    
    if( file_exists(WVSD_PATH.'svg-output/'.$_REQUEST['uids'].'.zip')){
      $str .= '<div style="display:inline-block;margin-right:10px;"><a href="'.WVSD_URL.'/svg-output/'.$_REQUEST['uids'].'.zip" download>'. __('Download SVG', 'prowbfd'). '</a></div>';
    }
    $str .= '<div style="display:inline-block;margin-right:10px;"><a href="'.WVSD_URL.'/includes/design-print.php?type=design&id='. $_REQUEST['uids'] .'"  target="_blank">'.__('Design Print/Save', 'prowbfd').'</a></div>';
				$str .= '<div style="display:inline-block;margin-right:10px;"><a href="'.WVSD_URL.'/includes/download-pdf.php?id='. $_REQUEST['uids'] .'"  target="_blank">'.__('Download PDF', 'prowbfd').'</a></div>';
    $str .= '</div>'; 
  }
  echo $str;
  exit;
}

function wbfd_get_customize_img(){
  global $wpdb;
  $sql = "SELECT * FROM ".$wpdb->prefix."wbfd_custom_data_save WHERE uniqid='". $_REQUEST['uids'] ."'";
  $results = $wpdb->get_row($sql);
  $parse = explode(',', trim($results->design_url, ','));
  if(count($parse) > 0){
    echo $parse[0]; 
  }
  exit;
}

   
add_action( 'wp_ajax_nopriv_wbfd_get_custom_design_with_html','wbfd_get_custom_design_with_html' );
add_action( 'wp_ajax_wbfd_get_custom_design_with_html', 'wbfd_get_custom_design_with_html' );
add_action( 'wp_ajax_nopriv_wbfd_get_customize_img','wbfd_get_customize_img' );
add_action( 'wp_ajax_wbfd_get_customize_img', 'wbfd_get_customize_img' );
?>